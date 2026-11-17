<?php

use App\Modules\UjianSoal\Controllers\UjianBankSoalController;
use App\Modules\UjianSoal\Controllers\UjianSoalController;
use Illuminate\Support\Facades\Route;

// Nama route di modul ini SELALU diakhiri salah satu dari
// index/show/create/store/edit/update/destroy, karena middleware
// AuthorizeRequest memetakan akhiran nama route ke hak akses lewat
// config('laralag.translate_action'). Akhiran lain = selalu 403.

Route::controller(UjianBankSoalController::class)->middleware(['web', 'auth'])->name('ujiansoal.')->group(function () {
    Route::get('/ujian-bank-soal', 'index')->name('index');
    Route::get('/ujian-bank-soal/create', 'create')->name('create');
    Route::post('/ujian-bank-soal', 'store')->name('store');
    Route::get('/ujian-bank-soal/{bankSoal}/edit', 'edit')->name('edit');
    Route::patch('/ujian-bank-soal/{bankSoal}', 'update')->name('update');
    Route::get('/ujian-bank-soal/{bankSoal}/delete', 'destroy')->name('destroy');
    Route::get('/ujian-bank-soal/{bankSoal}/final', 'setFinal')->name('final.update');
    Route::get('/ujian-bank-soal/{bankSoal}/export', 'export')->name('export.index');
});

// Monitoring upload bank soal. Hak aksesnya mengikuti menu "ujiansoal" (read), jadi Guru juga bisa
// membukanya; Admin diberi read di "ujiansoal" + menu sidebar "ujiansoalmonitoring" oleh
// command ujiansoal:setup-menu.
Route::get('/ujian-bank-soal-monitoring', [UjianBankSoalController::class, 'monitoring'])
    ->middleware(['web', 'auth'])
    ->name('ujiansoal.monitoring.index');

// Pratinjau soal dari halaman monitoring (bank soal milik guru mana pun). Prefix nama route
// "ujiansoalmonitoring." disengaja: AuthorizeRequest lalu memakai privilege menu
// "ujiansoalmonitoring" (Super Admin & Admin), bukan "ujiansoal" yang juga dimiliki Guru.
Route::get('/ujian-bank-soal-monitoring/{bankSoal}/preview', [UjianSoalController::class, 'previewMonitoring'])
    ->middleware(['web', 'auth'])
    ->name('ujiansoalmonitoring.preview.show');

Route::controller(UjianSoalController::class)->middleware(['web', 'auth'])->name('ujiansoal.soal.')->group(function () {
    Route::get('/ujian-bank-soal/{bankSoal}/soal', 'index')->name('index');
    Route::get('/ujian-bank-soal/{bankSoal}/soal/create', 'create')->name('create');
    Route::get('/ujian-bank-soal/{bankSoal}/soal/preview', 'preview')->name('preview.show');
    Route::post('/ujian-bank-soal/{bankSoal}/soal', 'store')->name('store');
    Route::get('/ujian-bank-soal/{bankSoal}/soal/{soal}/edit', 'edit')->name('edit');
    Route::patch('/ujian-bank-soal/{bankSoal}/soal/{soal}', 'update')->name('update');
    Route::get('/ujian-bank-soal/{bankSoal}/soal/{soal}/delete', 'destroy')->name('destroy');
    Route::get('/ujian-bank-soal/{bankSoal}/soal/{soal}/hapus_gambar/{field}', 'hapusGambar')->name('hapus_gambar.destroy');
});
