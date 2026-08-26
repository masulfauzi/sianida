<?php

namespace App\Modules\Mou\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Dudi\Models\Dudi;


class Mou extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'mou';
	protected $fillable   = ['*'];	

	public function dudi(){
		return $this->belongsTo(Dudi::class,"id_dudi","id");
	}

}
