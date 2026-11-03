<?php

namespace App\Modules\HariLibur\Models;

use App\Helpers\UsesUuid;
use App\Modules\Kelas\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HariLibur extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $dates      = ['deleted_at'];
	protected $table      = 'hari_libur';
	protected $fillable   = ['*'];

	/**
	 * Kelas yang OFF pada hari libur ini.
	 * Jika kosong, hari libur berlaku untuk semua kelas.
	 */
	public function kelas()
	{
		return $this->belongsToMany(Kelas::class, 'hari_libur_kelas', 'id_hari_libur', 'id_kelas')->withTimestamps();
	}

	public function scopeBerlakuUntukKelas($query, $idKelas)
	{
		return $query->where(function ($q) use ($idKelas) {
			$q->whereDoesntHave('kelas')
				->orWhereHas('kelas', fn ($k) => $k->where('kelas.id', $idKelas));
		});
	}
}
