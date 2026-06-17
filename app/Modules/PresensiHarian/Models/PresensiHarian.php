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

    public static function rekap_kehadiran_per_kelas($tingkat, $id_semester)
    {
        return DB::table('presensi_harian as ph')
            ->join('siswa as s', 'ph.id_siswa', '=', 's.id')
            ->join('statuskehadiran as sk', 'ph.id_status_kehadiran', '=', 'sk.id')
            ->join('pesertadidik as pd', function ($join) use ($id_semester) {
                $join->on('pd.id_siswa', '=', 's.id')
                     ->where('pd.id_semester', '=', $id_semester);
            })
            ->join('kelas as k', 'pd.id_kelas', '=', 'k.id')
            ->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
            ->where('t.tingkat', $tingkat)
            ->whereNull('ph.deleted_at')
            ->groupBy('k.id', 'k.kelas', 'sk.id', 'sk.status_kehadiran')
            ->orderBy('k.kelas')
            ->select(
                'k.id as id_kelas',
                'k.kelas as nama_kelas',
                'sk.status_kehadiran',
                DB::raw('COUNT(ph.id) as jumlah')
            )
            ->get();
    }

}
