<?php

use Illuminate\Support\Facades\Route;
use App\Modules\JenisIjinKeluarJenisIjinKeluarKelas\Controllers\JenisIjinKeluarJenisIjinKeluarKelasController;

Route::controller(JenisIjinKeluarKelasController::class)->middleware(['web','auth'])->name('jenisijinkeluarkelas.')->group(function(){
	Route::get('/jenisijinkeluarkelas', 'index')->name('index');
	Route::get('/jenisijinkeluarkelas/data', 'data')->name('data.index');
	Route::get('/jenisijinkeluarkelas/create', 'create')->name('create');
	Route::post('/jenisijinkeluarkelas', 'store')->name('store');
	Route::get('/jenisijinkeluarkelas/{jenisijinkeluarkelas}', 'show')->name('show');
	Route::get('/jenisijinkeluarkelas/{jenisijinkeluarkelas}/edit', 'edit')->name('edit');
	Route::patch('/jenisijinkeluarkelas/{jenisijinkeluarkelas}', 'update')->name('update');
	Route::get('/jenisijinkeluarkelas/{jenisijinkeluarkelas}/delete', 'destroy')->name('destroy');
});