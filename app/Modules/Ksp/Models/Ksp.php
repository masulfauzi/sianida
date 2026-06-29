<?php

namespace App\Modules\Ksp\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Semester\Models\Semester;


class Ksp extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'ksp';
	protected $fillable   = ['*'];	

	public function semester(){
		return $this->belongsTo(Semester::class,"id_semester","id");
	}

}
