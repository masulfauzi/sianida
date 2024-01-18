<?php

namespace App\Modules\Snbp\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Semester\Models\Semester;
use App\Modules\Siswa\Models\Siswa;


class Snbp extends Model
{
	// use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'snbp';
	protected $fillable   = ['*'];	

	public function semester(){
		return $this->belongsTo(Semester::class,"id_semester","id");
	}
public function siswa(){
		return $this->belongsTo(Siswa::class,"id_siswa","id");
	}

}
