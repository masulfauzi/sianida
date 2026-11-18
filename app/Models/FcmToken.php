<?php

namespace App\Models;

use App\Helpers\UsesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Token perangkat FCM aplikasi siswa. Tabel "fcm_token" (dibuat manual, bukan migration).
 * Satu baris per perangkat (token unik); pemilik bisa berpindah jika HP dipakai bergantian.
 */
class FcmToken extends Model
{
    use UsesUuid;

    protected $table    = 'fcm_token';
    protected $fillable = ['user_id', 'id_siswa', 'token', 'platform'];
}
