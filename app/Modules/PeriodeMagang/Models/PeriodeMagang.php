<?php

namespace App\Modules\PeriodeMagang\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Semester\Models\Semester;
use App\Modules\Magang\Models\Magang;


class PeriodeMagang extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'periode_magang';
	protected $fillable   = ['*'];

	public function semester(){
		return $this->belongsTo(Semester::class,"id_semester","id");
	}

	public function magang(){
		return $this->hasMany(Magang::class,"id_periode_magang","id");
	}

}
