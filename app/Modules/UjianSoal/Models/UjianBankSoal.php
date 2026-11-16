<?php
namespace App\Modules\UjianSoal\Models;

use App\Helpers\UsesUuid;
use App\Modules\Guru\Models\Guru;
use App\Modules\Jurusan\Models\Jurusan;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Semester\Models\Semester;
use App\Modules\Tingkat\Models\Tingkat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UjianBankSoal extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $dates    = ['deleted_at'];
    protected $table    = 'ujian_bank_soal';
    protected $fillable = ['*'];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id');
    }

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class, 'id_tingkat', 'id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id');
    }

    public function soal()
    {
        return $this->hasMany(UjianSoal::class, 'id_bank_soal')->orderBy('no_soal');
    }
}
