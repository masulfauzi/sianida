<?php

namespace App\Modules\Magang\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\PeriodeMagang\Models\PeriodeMagang;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Dudi\Models\Dudi;
use App\Modules\Guru\Models\Guru;

class Magang extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'magang';
	protected $fillable   = ['*'];	

	public function periodeMagang(){
		return $this->belongsTo(PeriodeMagang::class,"id_periode_magang","id");
	}
public function pesertadidik(){
		return $this->belongsTo(Pesertadidik::class,"id_pesertadidik","id");
	}
public function dudi(){
		return $this->belongsTo(Dudi::class,"id_dudi","id");
	}
public function pembimbing(){
		return $this->belongsTo(Guru::class,"id_pembimbing","id");
	}

}
