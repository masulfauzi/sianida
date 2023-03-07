<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Siswa\Controllers\SiswaController;

Route::controller(SiswaController::class)->middleware(['web','auth'])->name('siswa.')->group(function(){
	Route::get('/siswa', 'index')->name('index');
	Route::get('/siswa/data', 'data')->name('data.index');
	Route::get('/siswa/create', 'create')->name('create');
	Route::post('/siswa', 'store')->name('store');
	Route::get('/siswa/{siswa}', 'show')->name('show');
	Route::get('/siswa/{siswa}/edit', 'edit')->name('edit');
	Route::patch('/siswa/{siswa}', 'update')->name('update');
	Route::get('/siswa/{siswa}/delete', 'destroy')->name('destroy');
});