<?php

namespace App\Modules\Ijin\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\JenisIjin\Models\JenisIjin;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\StatusIjin\Models\StatusIjin;


class Ijin extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'ijin';
	protected $fillable   = ['*'];	

	public function jenisIjin(){
		return $this->belongsTo(JenisIjin::class,"id_jenis_ijin","id");
	}
public function siswa(){
		return $this->belongsTo(Siswa::class,"id_siswa","id");
	}
public function statusIjin(){
		return $this->belongsTo(StatusIjin::class,"id_status_ijin","id");
	}

}
