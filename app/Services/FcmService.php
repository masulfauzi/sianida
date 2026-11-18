<?php

namespace App\Services;

use App\Models\FcmToken;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pengirim push notification lewat FCM HTTP v1 API.
 * Payload mengikuti kontrak aplikasi siswa (issue-push-notification.md):
 * nilai type, channel_id, dan topic JANGAN diubah.
 */
class FcmService
{
    /** Path absolut file service account, atau null bila belum dikonfigurasi / file tidak ada. */
    protected static function credentialsPath(): ?string
    {
        $path = config('services.fcm.credentials');
        if (! $path || ! config('services.fcm.project_id')) {
            return null;
        }

        $full = str_starts_with($path, '/') ? $path : base_path($path);
        return is_file($full) ? $full : null;
    }

    /** OAuth access token untuk FCM, di-cache sedikit di bawah masa berlakunya (1 jam). */
    protected static function accessToken(): string
    {
        return Cache::remember('fcm_access_token', now()->addMinutes(55), function () {
            $path = self::credentialsPath();
            if (! $path) {
                throw new \RuntimeException('Kredensial FCM belum dikonfigurasi (FIREBASE_PROJECT_ID / FIREBASE_CREDENTIALS)');
            }

            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                $path
            );
            $token = $credentials->fetchAuthToken();
            if (empty($token['access_token'])) {
                throw new \RuntimeException('Gagal mengambil access token FCM');
            }
            return $token['access_token'];
        });
    }

    /**
     * Kirim 1 pesan. $message = isi objek "message" FCM HTTP v1.
     * Return: response Laravel HTTP client.
     */
    public static function send(array $message)
    {
        // FCM mewajibkan semua nilai di "data" bertipe string.
        if (isset($message['data'])) {
            $message['data'] = array_map(fn ($v) => (string) $v, $message['data']);
        }

        $projectId = config('services.fcm.project_id');

        return Http::withToken(self::accessToken())
            ->timeout(10)
            ->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                ['message' => $message]
            );
    }

    /** Kirim ke semua perangkat milik satu siswa. Token yang sudah tidak valid dihapus. */
    public static function sendToSiswa(
        string $idSiswa,
        string $title,
        string $body,
        array $data,
        string $channelId
    ): void {
        $tokens = FcmToken::where('id_siswa', $idSiswa)->pluck('token');
        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $token) {
            try {
                $response = self::send(
                    self::buildMessage(['token' => $token], $title, $body, $data, $channelId)
                );

                if ($response->failed()) {
                    $status = $response->json('error.status');
                    // Token sudah tidak berlaku (app di-uninstall / logout offline)
                    if (in_array($status, ['UNREGISTERED', 'NOT_FOUND', 'INVALID_ARGUMENT'], true)
                        || $response->status() === 404) {
                        FcmToken::where('token', $token)->delete();
                    } else {
                        Log::warning('FCM gagal', ['status' => $response->status(), 'body' => $response->body()]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('FCM error: ' . $e->getMessage());
            }
        }
    }

    /** Kirim ke topic (mis. "siswa" = semua siswa yang login di aplikasi). */
    public static function sendToTopic(
        string $topic,
        string $title,
        string $body,
        array $data,
        string $channelId
    ): bool {
        try {
            $response = self::send(
                self::buildMessage(['topic' => $topic], $title, $body, $data, $channelId)
            );
            if ($response->failed()) {
                Log::warning('FCM topic gagal', ['status' => $response->status(), 'body' => $response->body()]);
            }
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('FCM topic error: ' . $e->getMessage());
            return false;
        }
    }

    protected static function buildMessage(
        array $target,
        string $title,
        string $body,
        array $data,
        string $channelId
    ): array {
        return $target + [
            'notification' => ['title' => $title, 'body' => $body],
            'data'         => $data,
            'android'      => [
                'priority'     => 'high',
                'notification' => ['channel_id' => $channelId],
            ],
            'apns' => [
                'payload' => ['aps' => ['sound' => 'default']],
            ],
        ];
    }
}
