<?php
namespace App\Modules\Presensi\Models;

use App\Helpers\UsesUuid;
use App\Modules\Jurnal\Models\Jurnal;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Statuskehadiran\Models\Statuskehadiran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Presensi extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $dates = ['deleted_at'];
    protected $table = 'presensi';
    // protected $fillable   = ['*'];

    protected $guarded = [];

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, "id_jurnal", "id");
    }
    public function pesertadidik()
    {
        return $this->belongsTo(Pesertadidik::class, "id_pesertadidik", "id");
    }
    public function statuskehadiran()
    {
        return $this->belongsTo(Statuskehadiran::class, "id_statuskehadiran", "id");
    }

    /**
     * Peta status presensi harian (kode pendek) -> status presensi jurnal.
     * Terlambat datang ke sekolah tetap dianggap hadir di jam pelajaran.
     */
    public const PETA_STATUS_HARIAN = ['H' => 'H', 'T' => 'H', 'S' => 'S', 'I' => 'I', 'A' => 'A'];

    /** Status jurnal untuk siswa yang belum punya presensi harian pada tanggal tsb. */
    public const STATUS_TANPA_PRESENSI_HARIAN = 'A';

    /**
     * Buat presensi jurnal untuk seluruh peserta didik kelas, dengan status
     * mengikuti presensi harian siswa pada tanggal pembelajaran.
     *
     * @return int jumlah baris presensi yang dibuat
     */
    public static function buatDariPresensiHarian(
        string $idJurnal,
        string $idKelas,
        string $idSemester,
        string $tgl,
        ?string $createdBy = null,
        bool $kecualikanMagang = false
    ): int {
        $idStatus = DB::table('statuskehadiran')
            ->whereNull('deleted_at')
            ->pluck('id', 'status_kehadiran_pendek');

        $pesertadidik = DB::table('pesertadidik')
            ->where('id_kelas', $idKelas)
            ->where('id_semester', $idSemester)
            ->whereNull('deleted_at')
            ->when($kecualikanMagang, fn ($q) => $q->where('is_magang', '0'))
            ->get(['id', 'id_siswa']);

        if ($pesertadidik->isEmpty()) {
            return 0;
        }

        // Rekaman terbaru menang (approve ijin bisa membuat lebih dari satu baris per hari).
        $statusHarian = DB::table('presensi_harian as ph')
            ->join('statuskehadiran as sk', 'ph.id_status_kehadiran', '=', 'sk.id')
            ->whereIn('ph.id_siswa', $pesertadidik->pluck('id_siswa')->filter()->unique()->values())
            ->whereDate('ph.tgl', $tgl)
            ->whereNull('ph.deleted_at')
            ->orderBy('ph.created_at')
            ->get(['ph.id_siswa', 'sk.status_kehadiran_pendek'])
            ->pluck('status_kehadiran_pendek', 'id_siswa');

        $now  = now()->toDateTimeString();
        $rows = [];
        foreach ($pesertadidik as $pd) {
            $kodeHarian = $statusHarian[$pd->id_siswa] ?? null;
            $kodeJurnal = self::PETA_STATUS_HARIAN[$kodeHarian] ?? self::STATUS_TANPA_PRESENSI_HARIAN;

            $rows[] = [
                'id'                 => (string) Str::uuid(),
                'id_jurnal'          => $idJurnal,
                'id_pesertadidik'    => $pd->id,
                'id_statuskehadiran' => $idStatus[$kodeJurnal] ?? null,
                'created_by'         => $createdBy,
                'created_at'         => $now,
                'updated_at'         => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('presensi')->insert($chunk);
        }

        return count($rows);
    }

    public static function get_presensi_by_idjurnal($id_jurnal)
    {
        return DB::table('presensi as a')
            ->select('a.*', 'c.nama_siswa', 'd.status_kehadiran', 'b.is_magang')
            ->join('pesertadidik as b', 'a.id_pesertadidik', '=', 'b.id')
            ->join('siswa as c', 'b.id_siswa', '=', 'c.id')
            ->join('statuskehadiran as d', 'a.id_statuskehadiran', '=', 'd.id')
            ->where('a.id_jurnal', $id_jurnal)

            ->orderBy('b.is_magang')
            ->orderBy('c.nama_siswa')
            ->get();
    }

    public static function insert_presensi($data)
    {
        return DB::table('presensi')
            ->insert($data);
    }

    public static function update_presensi($data)
    {
        return DB::table('presensi')
            ->where('id', $data['id'])
            ->update($data);
    }

    public static function get_kehadiran_siswa($status, $id_semester, $tanggal)
    {
        return DB::table('presensi as a')
            ->join('pesertadidik as b', 'a.id_pesertadidik', '=', 'b.id')
            ->join('jurnal as c', 'a.id_jurnal', '=', 'c.id')
            ->join('statuskehadiran as d', 'a.id_statuskehadiran', '=', 'd.id')
            ->join('siswa as e', 'b.id_siswa', '=', 'e.id')
            ->join('kelas as f', 'b.id_kelas', '=', 'f.id')
            ->where('b.id_semester', $id_semester)
            ->where('d.status_kehadiran_pendek', $status)
            ->where('c.tgl_pembelajaran', $tanggal)
            ->groupBy('a.id_pesertadidik')
            ->get();
    }

    public static function get_siswa_magang($id_semester)
    {
        return DB::table('pesertadidik')
            ->where('is_magang', '1')
            ->where('id_semester', $id_semester)
            ->get();
    }

}
