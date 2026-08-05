<?php

namespace App\Modules\Agenda\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Agenda extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'agenda';
	protected $fillable   = ['*'];	

	
}
