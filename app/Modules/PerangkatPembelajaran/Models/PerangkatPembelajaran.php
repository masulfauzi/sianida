<?php

namespace App\Modules\PerangkatPembelajaran\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Guru\Models\Guru;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Tingkat\Models\Tingkat;
use App\Modules\Semester\Models\Semester;
use App\Modules\JenisPerangkat\Models\JenisPerangkat;


class PerangkatPembelajaran extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'perangkat_pembelajaran';
	protected $fillable   = ['*'];	

	public function guru(){
		return $this->belongsTo(Guru::class,"id_guru","id");
	}
public function mapel(){
		return $this->belongsTo(Mapel::class,"id_mapel","id");
	}
public function tingkat(){
		return $this->belongsTo(Tingkat::class,"id_tingkat","id");
	}
public function semester(){
		return $this->belongsTo(Semester::class,"id_semester","id");
	}
public function jenisPerangkat(){
		return $this->belongsTo(JenisPerangkat::class,"id_jenis_perangkat","id");
	}

}