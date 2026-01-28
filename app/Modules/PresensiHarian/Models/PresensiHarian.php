<?php
namespace App\Modules\PresensiHarian\Models;

use App\Helpers\UsesUuid;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\StatusKehadiran\Models\StatusKehadiran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

}
