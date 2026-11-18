<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Kode SSO sekali pakai untuk login otomatis Ujian Online (lihat issue-ujian-sso.md).
 * Kode maupun secret tidak boleh ditulis ke log.
 */
class UjianSsoController extends Controller
{
    private function cacheKey(string $code): string
    {
        // Simpan hash, bukan kode mentah.
        return 'ujian_sso:' . hash('sha256', $code);
    }

    /**
     * POST /api/ujian/sso-code (auth:sanctum)
     * Dipanggil aplikasi siswa sesaat sebelum membuka situs ujian.
     */
    public function code(Request $request)
    {
        $user = $request->user();

        // Hanya akun siswa aktif (relasi sama seperti AuthController::login).
        $isSiswa = DB::table('siswa')
            ->where('nik', $user->identitas)
            ->whereNull('deleted_at')
            ->exists();
        if (! $isSiswa) {
            return response()->json(['message' => 'Hanya untuk akun siswa'], 403);
        }

        $ttl  = (int) config('services.ujian_sso.ttl', 60);
        $code = Str::random(48);

        Cache::put($this->cacheKey($code), [
            'username' => $user->username,
            'name'     => $user->name,
        ], now()->addSeconds($ttl));

        return response()->json([
            'code'       => $code,
            'expires_in' => $ttl,
        ]);
    }

    /**
     * POST /api/ujian/sso-verify (server-to-server dari ujian-back)
     * Header: X-SSO-Secret. Body: { "code": "..." }. Kode hanya bisa dipakai sekali.
     */
    public function verify(Request $request)
    {
        $secret = (string) config('services.ujian_sso.secret');
        $given  = (string) $request->header('X-SSO-Secret', '');

        if ($secret === '' || ! hash_equals($secret, $given)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:100',
        ]);

        $data = Cache::pull($this->cacheKey($validated['code']));
        if (! $data) {
            return response()->json(['message' => 'Kode tidak valid atau kedaluwarsa'], 404);
        }

        return response()->json($data); // { username, name }
    }
}
