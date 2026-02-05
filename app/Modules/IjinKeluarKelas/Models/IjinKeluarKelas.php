<?php
namespace App\Modules\IjinKeluarKelas\Models;

use App\Helpers\UsesUuid;
use App\Modules\Guru\Models\Guru;
use App\Modules\IsValbk\Models\IsValbk;
use App\Modules\IsValguru\Models\IsValguru;
use App\Modules\JenisIjinKeluar\Models\JenisIjinKeluar;
use App\Modules\Siswa\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IjinKeluarKelas extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $dates   = ['deleted_at'];
    protected $table   = 'ijin_keluar_kelas';
    protected $guarded = ['id'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, "id_siswa", "id");
    }
    public function guru()
    {
        return $this->belongsTo(Guru::class, "id_guru", "id");
    }
    public function jenisIjinKeluar()
    {
        return $this->belongsTo(JenisIjinKeluar::class, "id_jenis_ijin_keluar", "id");
    }
    public function isValguru()
    {
        return $this->belongsTo(IsValguru::class, "is_valid_guru", "id");
    }
    public function isValbk()
    {
        return $this->belongsTo(IsValbk::class, "is_valid_bk", "id");
    }

}
