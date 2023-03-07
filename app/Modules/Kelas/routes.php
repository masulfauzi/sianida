<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Kelas\Controllers\KelasController;

Route::controller(KelasController::class)->middleware(['web','auth'])->name('kelas.')->group(function(){
	Route::get('/kelas', 'index')->name('index');
	Route::get('/kelas/data', 'data')->name('data.index');
	Route::get('/kelas/create', 'create')->name('create');
	Route::post('/kelas', 'store')->name('store');
	Route::get('/kelas/{kelas}', 'show')->name('show');
	Route::get('/kelas/{kelas}/edit', 'edit')->name('edit');
	Route::patch('/kelas/{kelas}', 'update')->name('update');
	Route::get('/kelas/{kelas}/delete', 'destroy')->name('destroy');
});