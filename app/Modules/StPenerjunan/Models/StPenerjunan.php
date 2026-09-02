<?php

namespace App\Modules\StPenerjunan\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\PeriodeMagang\Models\PeriodeMagang;

class StPenerjunan extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'st_penerjunan';
	protected $fillable   = ['*'];	

	public function periode(){
		return $this->belongsTo(PeriodeMagang::class,"id_periode","id");
	}

}
