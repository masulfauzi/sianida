<?php
namespace App\Modules\Pengumuman\Models;

use App\Helpers\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Tabel "pengumuman" (dibuat manual, bukan migration). */
class Pengumuman extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $table   = 'pengumuman';
    protected $guarded = ['id'];
    protected $casts   = ['dikirim_at' => 'datetime'];
}
