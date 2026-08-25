<?php

namespace App\Modules\AnggotaEkskul\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Ekskul\Models\Ekskul;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Semester\Models\Semester;


class AnggotaEkskul extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'anggota_ekskul';
	protected $fillable   = ['*'];

	public function pd(){
		return $this->belongsTo(Pesertadidik::class,"id_pd","id");
	}
public function ekskul(){
		return $this->belongsTo(Ekskul::class,"id_ekskul","id");
	}
public function semester(){
		return $this->belongsTo(Semester::class,"id_semester","id");
	}

}
