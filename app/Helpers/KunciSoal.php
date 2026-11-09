<?php
namespace App\Helpers;

use App\Modules\UjianSoal\Models\UjianSoal;
use InvalidArgumentException;

/**
 * Validasi & normalisasi kunci jawaban soal ujian.
 *
 * Logikanya harus SAMA dengan validasi di aplikasi ujian (lihat
 * integrasi_soal.md bagian 6.1 - NormalizeKunci / ValidateOpsiSoal),
 * supaya soal yang lolos tersimpan di sini pasti lolos saat diimport.
 */
class KunciSoal
{
    /**
     * Normalisasi & validasi kunci sesuai jenis soal.
     *
     * @throws InvalidArgumentException
     */
    public static function normalize(string $jenis, string $kunci): string
    {
        $k = strtoupper(str_replace([' ', ',', ';'], '', $kunci));

        switch ($jenis) {
            case UjianSoal::JENIS_PGK:
                if ($k === '' || ! preg_match('/^[A-E]+$/', $k)) {
                    throw new InvalidArgumentException('kunci pilihan ganda kompleks harus minimal 2 huruf berbeda dari A-E, mis. AC');
                }
                $huruf = array_unique(str_split($k));
                sort($huruf);
                if (count($huruf) < 2) {
                    throw new InvalidArgumentException('kunci pilihan ganda kompleks harus minimal 2 huruf berbeda dari A-E, mis. AC');
                }
                return implode('', $huruf);

            case UjianSoal::JENIS_BS:
                if (! preg_match('/^[BS]{3}$/', $k)) {
                    throw new InvalidArgumentException('soal benar/salah harus berisi tepat 3 karakter B atau S, mis. BSB');
                }
                return $k;

            default: // pg
                if (! preg_match('/^[A-E]$/', $k)) {
                    throw new InvalidArgumentException('kunci harus A, B, C, D, atau E');
                }
                return $k;
        }
    }

    /**
     * Pastikan opsi sesuai jenis. $opsi & $adaGambar berindeks 0-4 (A-E).
     *
     * @throws InvalidArgumentException
     */
    public static function validateOpsi(string $jenis, string $kunci, array $opsi, array $adaGambar): void
    {
        $terisi = fn (int $i) => trim((string) ($opsi[$i] ?? '')) !== '' || ! empty($adaGambar[$i]);

        if ($jenis === UjianSoal::JENIS_BS) {
            for ($i = 0; $i < 3; $i++) {
                if (! $terisi($i)) {
                    throw new InvalidArgumentException('soal benar/salah wajib berisi 3 pernyataan di opsi_a, opsi_b, dan opsi_c');
                }
            }
            return;
        }

        // pg & pgk: setiap huruf kunci harus menunjuk opsi yang terisi.
        foreach (str_split($kunci) as $huruf) {
            if (! $terisi(ord($huruf) - ord('A'))) {
                throw new InvalidArgumentException($jenis === UjianSoal::JENIS_PGK
                    ? "opsi {$huruf} pada kunci pilihan ganda kompleks tidak boleh kosong"
                    : "opsi {$huruf} pada kunci tidak boleh kosong");
            }
        }
    }
}
