<?php

namespace App\Modules\JenisIjinKeluarKelas\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JenisIjinKeluarKelas extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'jenis_ijin_keluar_kelas';
	protected $fillable   = ['*'];	

	
}
