<?php
namespace App\Modules\UjianSoal\Models;

use App\Helpers\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UjianSoal extends Model
{
    use SoftDeletes;
    use UsesUuid;

    public const JENIS_PG  = 'pg';   // pilihan ganda, kunci 1 huruf
    public const JENIS_PGK = 'pgk';  // pilihan ganda kompleks, kunci >= 2 huruf
    public const JENIS_BS  = 'bs';   // benar/salah, 3 pernyataan, kunci 3 karakter B/S

    public const LABEL_JENIS = [
        self::JENIS_PG  => 'Pilihan Ganda',
        self::JENIS_PGK => 'Pilihan Ganda Kompleks',
        self::JENIS_BS  => 'Benar/Salah',
    ];

    protected $dates    = ['deleted_at'];
    protected $table    = 'ujian_soal';
    protected $fillable = ['*'];

    public function bankSoal()
    {
        return $this->belongsTo(UjianBankSoal::class, 'id_bank_soal', 'id');
    }
}
