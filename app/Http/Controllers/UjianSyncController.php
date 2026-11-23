<?php

namespace App\Http\Controllers;

use App\Modules\Semester\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Data siswa aktif untuk sinkronisasi ke Ujian Online (lihat issue-ujian-sync-siswa.md).
 * Server-to-server dari ujian-back. Secret tidak boleh ditulis ke log.
 */
class UjianSyncController extends Controller
{
    /**
     * GET /api/ujian/siswa
     * Header: X-Ujian-Secret. Query opsional: tingkat (X/XI/XII), id_kelas.
     */
    public function siswa(Request $request)
    {
        $secret = (string) config('services.ujian_sync.secret');
        $given  = (string) $request->header('X-Ujian-Secret', '');

        if ($secret === '' || ! hash_equals($secret, $given)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'tingkat'  => 'nullable|in:X,XI,XII',
            'id_kelas' => 'nullable|string|size:36',
        ]);

        $semester = Semester::get_semester_aktif();
        if (! $semester) {
            return response()->json(['message' => 'Semester aktif tidak ditemukan'], 404);
        }
        $semesterDetail = DB::table('semester')
            ->select('id', 'semester', 'tgl_mulai', 'tgl_selesai')
            ->where('id', $semester->id)
            ->first();

        // Satu akun per NIK. Jika ada lebih dari satu akun aktif untuk NIK yang sama, diambil satu
        // secara konsisten (MIN) agar baris siswa tidak dobel.
        $akun = DB::table('users')
            ->select('identitas', DB::raw('MIN(username) AS username'))
            ->whereNull('deleted_at')
            ->whereNotNull('identitas')
            ->where('identitas', '<>', '')
            ->groupBy('identitas');

        $query = DB::table('pesertadidik')
            ->join('siswa', 'siswa.id', '=', 'pesertadidik.id_siswa')
            ->join('kelas', 'kelas.id', '=', 'pesertadidik.id_kelas')
            ->join('tingkat', 'tingkat.id', '=', 'kelas.id_tingkat')
            ->join('jurusan', 'jurusan.id', '=', 'kelas.id_jurusan')
            ->leftJoinSub($akun, 'akun', function ($join) {
                $join->on('akun.identitas', '=', 'siswa.nik');
            })
            ->where('pesertadidik.id_semester', $semester->id)
            ->whereNull('pesertadidik.deleted_at')
            ->whereNull('siswa.deleted_at')
            ->whereNull('kelas.deleted_at')
            ->where(function ($q) {
                $q->whereNull('siswa.is_lulus')->orWhere('siswa.is_lulus', '<>', 1);
            })
            ->select(
                'siswa.id',
                'siswa.nisn',
                'siswa.nis',
                'siswa.nama_siswa',
                'akun.username',
                'pesertadidik.is_magang',
                'kelas.id AS id_kelas',
                'kelas.kelas AS nama_kelas',
                'kelas.nama_pendek',
                'tingkat.tingkat',
                'jurusan.id AS id_jurusan',
                'jurusan.jurusan AS nama_jurusan',
                'jurusan.singkatan'
            )
            ->orderByRaw("FIELD(tingkat.tingkat, 'X', 'XI', 'XII')")
            ->orderBy('kelas.kelas')
            ->orderBy('siswa.nama_siswa');

        if (! empty($validated['tingkat'])) {
            $query->where('tingkat.tingkat', $validated['tingkat']);
        }
        if (! empty($validated['id_kelas'])) {
            $query->where('kelas.id', $validated['id_kelas']);
        }

        $rows = $query->get();

        $jurusan = [];
        $kelas   = [];
        $siswa   = [];
        foreach ($rows as $r) {
            $jurusan[$r->id_jurusan] = [
                'id'           => $r->id_jurusan,
                'nama_jurusan' => $r->nama_jurusan,
                'singkatan'    => $r->singkatan,
            ];
            $kelas[$r->id_kelas] = [
                'id'          => $r->id_kelas,
                'nama_kelas'  => $r->nama_kelas,
                'nama_pendek' => $r->nama_pendek,
                'tingkat'     => $r->tingkat,
                'id_jurusan'  => $r->id_jurusan,
            ];
            $siswa[] = [
                'id'             => $r->id,
                'nisn'           => $r->nisn,
                'nis'            => $r->nis,
                'nama'           => $r->nama_siswa,
                'username'       => $r->username,
                'sudah_aktivasi' => $r->username !== null,
                'id_kelas'       => $r->id_kelas,
                'is_magang'      => (int) $r->is_magang === 1,
            ];
        }

        Log::info('ujian-sync: data siswa diambil', [
            'ip'      => $request->ip(),
            'tingkat' => $validated['tingkat'] ?? null,
            'total'   => count($siswa),
        ]);

        return response()->json([
            'semester'     => $semesterDetail,
            'generated_at' => now()->toIso8601String(),
            'total'        => count($siswa),
            'jurusan'      => array_values($jurusan),
            'kelas'        => array_values($kelas),
            'siswa'        => $siswa,
        ]);
    }
}
