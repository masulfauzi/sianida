<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\IjinController;
use App\Http\Controllers\IjinKeluarKelasController;
use App\Http\Controllers\JenisIjinKelasController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/revoke-all-tokens', [AuthController::class, 'revokeAllTokens']);

    // Siswa API endpoints
    Route::get('/siswa', [SiswaController::class, 'siswa']);

    // Guru API endpoints
    Route::get('/get_guru', [GuruController::class, 'index']);

    // Presensi API endpoints
    Route::post('/presensi', [PresensiController::class, 'store']);
    Route::get('/presensi/{userId}/{currentmonth}/{currentyear}', [PresensiController::class, 'index']);

    // Ijin API endpoints
    Route::get('/ijin', [IjinController::class, 'index']);
    Route::post('/ijin', [IjinController::class, 'store']);

    // Jenis Ijin Kelas API endpoints
    Route::get('/jenis-ijin-kelas', [JenisIjinKelasController::class, 'index']);

    // Ijin Keluar Kelas API endpoints
    Route::get('/riwayat-izin', [IjinKeluarKelasController::class, 'index']);
    Route::post('/keluar-kelas', [IjinKeluarKelasController::class, 'store']);
});
