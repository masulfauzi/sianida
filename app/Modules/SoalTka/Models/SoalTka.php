<?php

namespace App\Modules\SoalTka\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Guru\Models\Guru;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Semester\Models\Semester;
use App\Modules\Tingkat\Models\Tingkat;


class SoalTka extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'soal_tka';
	protected $fillable   = ['*'];	

	public function guru(){
		return $this->belongsTo(Guru::class,"id_guru","id");
	}
public function mapel(){
		return $this->belongsTo(Mapel::class,"id_mapel","id");
	}
public function semester(){
		return $this->belongsTo(Semester::class,"id_semester","id");
	}
public function tingkat(){
		return $this->belongsTo(Tingkat::class,"id_tingkat","id");
	}

}
