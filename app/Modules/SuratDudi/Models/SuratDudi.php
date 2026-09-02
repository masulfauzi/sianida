<?php

namespace App\Modules\SuratDudi\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\PeriodeMagang\Models\PeriodeMagang;

class SuratDudi extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'surat_dudi';
	protected $fillable   = ['*'];	

	public function periode(){
		return $this->belongsTo(PeriodeMagang::class,"id_periode","id");
	}

}
