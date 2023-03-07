<?php

namespace App\Modules\Siswa\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Jeniskelamin\Models\Jeniskelamin;
use App\Modules\Agama\Models\Agama;


class Siswa extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'siswa';
	protected $fillable   = ['*'];	

	public function jeniskelamin(){
		return $this->belongsTo(Jeniskelamin::class,"id_jeniskelamin","id");
	}
public function agama(){
		return $this->belongsTo(Agama::class,"id_agama","id");
	}

	public static function cek_aktivasi_siswa($data)
	{
		return DB::table('siswa')
					->where('nis', $data->nis)
					->where('nisn', $data->nisn)
					->where('nik', $data->nik);
	}

}
