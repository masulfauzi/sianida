<?php

use App\Http\Controllers\AuthController;
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
    Route::get('/siswa/{userId}', [SiswaController::class, 'siswa']);

    // Presensi API endpoints
    Route::post('/presensi', [PresensiController::class, 'store']);
    Route::get('/presensi/{userId}/{currentmonth}', [PresensiController::class, 'index']);
});
