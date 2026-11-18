<?php

use App\Modules\Pengumuman\Controllers\PengumumanController;
use Illuminate\Support\Facades\Route;

// Akhiran nama route dipetakan AuthorizeRequest ke privilege (config laralag.translate_action).
// "kirim.update" = kirim ulang notifikasi, memakai privilege update.
Route::controller(PengumumanController::class)->middleware(['web', 'auth'])->name('pengumuman.')->group(function () {
    Route::get('/pengumuman', 'index')->name('index');
    Route::get('/pengumuman/create', 'create')->name('create');
    Route::post('/pengumuman', 'store')->name('store');
    Route::get('/pengumuman/{pengumuman}', 'show')->name('show');
    Route::get('/pengumuman/{pengumuman}/edit', 'edit')->name('edit');
    Route::patch('/pengumuman/{pengumuman}', 'update')->name('update');
    Route::post('/pengumuman/{pengumuman}/kirim', 'kirim')->name('kirim.update');
    Route::get('/pengumuman/{pengumuman}/delete', 'destroy')->name('destroy');
});
