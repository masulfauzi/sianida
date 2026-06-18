<?php
namespace App\Modules\IjinKeluarKelas\Models;

use App\Helpers\UsesUuid;
use App\Modules\Guru\Models\Guru;
use App\Modules\Jampelajaran\Models\Jampelajaran;
use App\Modules\JenisIjinKeluarKelas\Models\JenisIjinKeluarKelas;
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
        return $this->belongsTo(JenisIjinKeluarKelas::class, "id_jenis_ijin_keluar", "id");
    }
    public function jamKeluar()
    {
        return $this->belongsTo(Jampelajaran::class, "jam_keluar", "id");
    }
    public function jamKembali()
    {
        return $this->belongsTo(Jampelajaran::class, "jam_kembali", "id");
    }

}
