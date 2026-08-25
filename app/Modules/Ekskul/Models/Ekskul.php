<?php

namespace App\Modules\Ekskul\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Pembimbing\Models\Pembimbing;


class Ekskul extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'ekskul';
	protected $fillable   = ['*'];	

	public function pembimbing(){
		return $this->belongsTo(Pembimbing::class,"id_pembimbing","id");
	}

}
