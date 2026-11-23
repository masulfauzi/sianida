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
        if ($tolak = $this->tolakJikaSecretSalah($request)) {
            return $tolak;
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

    /**
     * GET /api/ujian/bank-soal[?tanpa_soal=1]
     * Header: X-Ujian-Secret. Semua bank soal semester aktif (semua status).
     * tanpa_soal=1: hanya data bank (untuk daftar pilihan), tanpa isi soal.
     */
    public function bankSoal(Request $request)
    {
        if ($tolak = $this->tolakJikaSecretSalah($request)) {
            return $tolak;
        }

        $semester = Semester::get_semester_aktif();
        if (! $semester) {
            return response()->json(['message' => 'Semester aktif tidak ditemukan'], 404);
        }

        $banks = $this->queryBank()
            ->where('b.id_semester', $semester->id)
            ->orderBy('b.nama_bank_soal')
            ->get();

        if ($request->boolean('tanpa_soal')) {
            $jumlah = DB::table('ujian_soal')
                ->select('id_bank_soal', DB::raw('COUNT(*) AS jml'))
                ->whereIn('id_bank_soal', $banks->pluck('id'))
                ->whereNull('deleted_at')
                ->groupBy('id_bank_soal')
                ->pluck('jml', 'id_bank_soal');

            $data = $banks->map(fn ($b) => $this->formatBank($b, null, (int) ($jumlah[$b->id] ?? 0)))->values();
            $totalSoal = $data->sum('jumlah_soal');
        } else {
            $soalPerBank = $this->querySoal($banks->pluck('id'))->groupBy('id_bank_soal');
            $data = $banks->map(fn ($b) => $this->formatBank($b, $soalPerBank[$b->id] ?? collect()))->values();
            $totalSoal = $data->sum('jumlah_soal');
        }

        Log::info('ujian-sync: bank soal diambil', [
            'ip'         => $request->ip(),
            'tanpa_soal' => $request->boolean('tanpa_soal'),
            'total_bank' => $data->count(),
            'total_soal' => $totalSoal,
        ]);

        return response()->json([
            'semester'     => $this->semesterDetail($semester->id),
            'generated_at' => now()->toIso8601String(),
            'total_bank'   => $data->count(),
            'total_soal'   => $totalSoal,
            'bank_soal'    => $data,
        ]);
    }

    /**
     * GET /api/ujian/bank-soal/{id}
     * Header: X-Ujian-Secret. Satu bank soal (semua status, tidak dibatasi semester aktif) beserta soalnya.
     */
    public function bankSoalDetail(Request $request, string $id)
    {
        if ($tolak = $this->tolakJikaSecretSalah($request)) {
            return $tolak;
        }
        if (strlen($id) !== 36) {
            return response()->json(['message' => 'ID bank soal tidak valid'], 422);
        }

        $bank = $this->queryBank()->where('b.id', $id)->first();
        if (! $bank) {
            return response()->json(['message' => 'Bank soal tidak ditemukan'], 404);
        }

        $data = $this->formatBank($bank, $this->querySoal([$bank->id]));

        Log::info('ujian-sync: satu bank soal diambil', [
            'ip'         => $request->ip(),
            'id_bank'    => $bank->id,
            'total_soal' => $data['jumlah_soal'],
        ]);

        return response()->json([
            'semester'     => $this->semesterDetail($bank->id_semester),
            'generated_at' => now()->toIso8601String(),
            'bank_soal'    => $data,
        ]);
    }

    /** Query dasar bank soal (tidak terhapus) beserta nama relasinya. */
    private function queryBank()
    {
        return DB::table('ujian_bank_soal AS b')
            ->leftJoin('mapel AS m', 'm.id', '=', 'b.id_mapel')
            ->leftJoin('tingkat AS t', 't.id', '=', 'b.id_tingkat')
            ->leftJoin('jurusan AS j', 'j.id', '=', 'b.id_jurusan')
            ->leftJoin('guru AS g', 'g.id', '=', 'b.id_guru')
            ->whereNull('b.deleted_at')
            ->select(
                'b.id',
                'b.id_semester',
                'b.nama_bank_soal',
                'b.status',
                'b.deskripsi',
                'b.id_mapel',
                'm.mapel AS nama_mapel',
                't.tingkat',
                'j.jurusan AS nama_jurusan',
                'g.nama AS nama_guru'
            );
    }

    /** Semua soal (tidak terhapus) dari bank-bank yang diberikan, urut no_soal. */
    private function querySoal($idBank)
    {
        return DB::table('ujian_soal')
            ->whereIn('id_bank_soal', $idBank)
            ->whereNull('deleted_at')
            ->select(
                'id', 'id_bank_soal', 'no_soal', 'jenis', 'soal',
                'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'kunci',
                'gambar_soal', 'gambar_a', 'gambar_b', 'gambar_c', 'gambar_d', 'gambar_e'
            )
            ->orderBy('no_soal')
            ->get();
    }

    /**
     * Format satu bank untuk response.
     * $soal = collection soal (bank dengan soal), atau null (tanpa_soal) dengan $jumlahSoal dari COUNT.
     */
    private function formatBank($b, $soal, int $jumlahSoal = 0): array
    {
        $s = fn ($v) => $v === null ? '' : (string) $v;

        $data = [
            'id'             => $b->id,
            'nama_bank_soal' => $s($b->nama_bank_soal),
            'status'         => $s($b->status),
            'deskripsi'      => $s($b->deskripsi),
            'id_mapel'       => $b->id_mapel,
            'nama_mapel'     => $b->nama_mapel,
            'tingkat'        => $b->tingkat,
            'nama_jurusan'   => $b->nama_jurusan,
            'nama_guru'      => $b->nama_guru,
            'jumlah_soal'    => $jumlahSoal,
        ];

        if ($soal === null) {
            return $data;
        }

        $data['soal'] = $soal->map(function ($q) use ($s) {
            $bs = $q->jenis === 'bs';
            return [
                'id'          => $q->id,
                'no_soal'     => (int) $q->no_soal,
                'jenis'       => $s($q->jenis),
                'soal'        => $s($q->soal),
                'opsi_a'      => $s($q->opsi_a),
                'opsi_b'      => $s($q->opsi_b),
                'opsi_c'      => $s($q->opsi_c),
                'opsi_d'      => $bs ? '' : $s($q->opsi_d),
                'opsi_e'      => $bs ? '' : $s($q->opsi_e),
                'kunci'       => $s($q->kunci),
                'gambar_soal' => $s($q->gambar_soal),
                'gambar_a'    => $s($q->gambar_a),
                'gambar_b'    => $s($q->gambar_b),
                'gambar_c'    => $s($q->gambar_c),
                'gambar_d'    => $bs ? '' : $s($q->gambar_d),
                'gambar_e'    => $bs ? '' : $s($q->gambar_e),
            ];
        })->values();
        $data['jumlah_soal'] = $data['soal']->count();

        return $data;
    }

    private function semesterDetail($idSemester)
    {
        return DB::table('semester')
            ->select('id', 'semester', 'tgl_mulai', 'tgl_selesai')
            ->where('id', $idSemester)
            ->first();
    }

    /** Cek header X-Ujian-Secret. Mengembalikan response 401, atau null jika valid. */
    private function tolakJikaSecretSalah(Request $request)
    {
        $secret = (string) config('services.ujian_sync.secret');
        $given  = (string) $request->header('X-Ujian-Secret', '');

        if ($secret === '' || ! hash_equals($secret, $given)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return null;
    }
}
