<?php

use App\Modules\Nilai\Controllers\NilaiController;
use Illuminate\Support\Facades\Route;

Route::controller(NilaiController::class)->middleware(['web', 'auth'])->name('nilai.')->group(function () {
    // SKL Routes
    Route::get('/nilai/skl', 'skl')->name('skl.index');
    Route::get('/nilai/skl/{id}/detail', 'sklDetail')->name('skl.show');

    // Transkrip Routes
    Route::get('/nilai/transkrip', 'transkrip')->name('transkrip.index');
    Route::get('/nilai/transkrip/{id}/detail', 'transkripDetail')->name('transkrip.show');
    Route::get('/nilai/transkrip/kelas/{idKelas}/siswa', 'transkripSiswaKelas')->name('transkrip.siswa.show');

    // custom role
    Route::get('/nilai/personal', 'index_siswa')->name('siswa.index');
    Route::get('/verif_nilai', 'verif_nilai')->name('verif_nilai.index');
    Route::get('/verif_nilai/siswa/{id_kelas}', 'daftar_siswa')->name('daftar_siswa.index');
    Route::get('/verif_nilai/nilai/{id_siswa}', 'daftar_nilai')->name('daftar_nilai.index');
    Route::post('/verif_nilai/nilai/verif', 'simpan_verif')->name('verif_nilai.store');

    // Leger
    Route::get('/nilai/leger', 'leger_nilai')->name('leger.index');
    Route::post('/nilai/leger/export', 'export_leger')->name('leger.show');
    Route::post('/nilai/leger/export/excel', 'export_leger_excel')->name('leger.export.show');

    Route::get('/nilai', 'index')->name('index');
    Route::get('/nilai/data', 'data')->name('data.index');
    Route::get('/nilai/create', 'create')->name('create');
    Route::post('/nilai', 'store')->name('store');
    Route::get('/nilai/{nilai}', 'show')->name('show');
    Route::get('/nilai/{nilai}/edit', 'edit')->name('edit');
    Route::patch('/nilai/{nilai}', 'update')->name('update');
    Route::get('/nilai/{nilai}/delete', 'destroy')->name('destroy');
});
