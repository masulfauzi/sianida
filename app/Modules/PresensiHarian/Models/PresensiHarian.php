<?php
namespace App\Modules\PresensiHarian\Models;

use App\Helpers\UsesUuid;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\StatusKehadiran\Models\StatusKehadiran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PresensiHarian extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $dates   = ['deleted_at'];
    protected $table   = 'presensi_harian';
    protected $guarded = ['id'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, "id_siswa", "id");
    }
    public function statusKehadiran()
    {
        return $this->belongsTo(StatusKehadiran::class, "id_status_kehadiran", "id");
    }

    public static function rekap_bulanan($id_kelas, $id_semester, $tahun, $bulan)
    {
        return DB::table('presensi_harian as ph')
            ->join('pesertadidik as pd', function ($join) use ($id_semester, $id_kelas) {
                $join->on('pd.id_siswa', '=', 'ph.id_siswa')
                     ->where('pd.id_semester', '=', $id_semester)
                     ->where('pd.id_kelas', '=', $id_kelas);
            })
            ->join('statuskehadiran as sk', 'ph.id_status_kehadiran', '=', 'sk.id')
            ->whereYear('ph.tgl', $tahun)
            ->whereMonth('ph.tgl', $bulan)
            ->whereNull('ph.deleted_at')
            ->whereNull('pd.deleted_at')
            ->select(
                'ph.id_siswa',
                DB::raw('DAY(ph.tgl) as tanggal'),
                'sk.status_kehadiran_pendek as status',
                'sk.status_kehadiran as status_lengkap'
            )
            ->get();
    }

    public static function rekap_kehadiran_per_kelas($tingkat, $id_semester, $tgl = null)
    {
        $tgl = $tgl ? \Carbon\Carbon::createFromFormat('Y-m-d', $tgl)->toDateString() : today()->toDateString();

        return DB::table('pesertadidik as pd')
            ->join('kelas as k', 'pd.id_kelas', '=', 'k.id')
            ->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
            ->leftJoin('presensi_harian as ph', function ($join) use ($tgl) {
                $join->on('ph.id_siswa', '=', 'pd.id_siswa')
                     ->whereDate('ph.tgl', $tgl)
                     ->whereNull('ph.deleted_at');
            })
            ->leftJoin('statuskehadiran as sk', 'ph.id_status_kehadiran', '=', 'sk.id')
            ->where('pd.id_semester', $id_semester)
            ->whereNull('pd.deleted_at')
            ->where('t.tingkat', $tingkat)
            ->groupBy('k.id', 'k.kelas', DB::raw('CASE WHEN ph.id IS NULL THEN \'Tidak Hadir\' ELSE sk.status_kehadiran END'))
            ->orderBy('k.kelas')
            ->select(
                'k.id as id_kelas',
                'k.kelas as nama_kelas',
                DB::raw('CASE WHEN ph.id IS NULL THEN \'Tidak Hadir\' ELSE sk.status_kehadiran END as status_kehadiran'),
                DB::raw('COUNT(pd.id) as jumlah')
            )
            ->get();
    }

}
