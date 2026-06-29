<?php

namespace App\Modules\FileKsp\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Ksp\Models\Ksp;


class FileKsp extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'file_ksp';
	protected $fillable   = ['*'];	

	public function ksp(){
		return $this->belongsTo(Ksp::class,"id_ksp","id");
	}

}
