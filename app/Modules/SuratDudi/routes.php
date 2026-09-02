<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SuratDudi\Controllers\SuratDudiController;

Route::controller(SuratDudiController::class)->middleware(['web','auth'])->name('suratdudi.')->group(function(){
	Route::get('/suratdudi', 'index')->name('index');
	Route::get('/suratdudi/data', 'data')->name('data.index');
	Route::get('/suratdudi/create', 'create')->name('create');
	Route::post('/suratdudi', 'store')->name('store');
	Route::get('/suratdudi/{suratdudi}', 'show')->name('show');
	Route::get('/suratdudi/{suratdudi}/surat/{dudi}', 'cetakSurat')->name('surat.show');
	Route::get('/suratdudi/{suratdudi}/edit', 'edit')->name('edit');
	Route::patch('/suratdudi/{suratdudi}', 'update')->name('update');
	Route::get('/suratdudi/{suratdudi}/delete', 'destroy')->name('destroy');
});