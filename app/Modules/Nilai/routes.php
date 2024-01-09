<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Nilai\Controllers\NilaiController;

Route::controller(NilaiController::class)->middleware(['web','auth'])->name('nilai.')->group(function(){
	// custom role
	Route::get('/nilai/personal', 'index_siswa')->name('siswa.index');



	Route::get('/nilai', 'index')->name('index');
	Route::get('/nilai/data', 'data')->name('data.index');
	Route::get('/nilai/create', 'create')->name('create');
	Route::post('/nilai', 'store')->name('store');
	Route::get('/nilai/{nilai}', 'show')->name('show');
	Route::get('/nilai/{nilai}/edit', 'edit')->name('edit');
	Route::patch('/nilai/{nilai}', 'update')->name('update');
	Route::get('/nilai/{nilai}/delete', 'destroy')->name('destroy');
});