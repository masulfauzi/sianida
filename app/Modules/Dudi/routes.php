<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dudi\Controllers\DudiController;

Route::controller(DudiController::class)->middleware(['web','auth'])->name('dudi.')->group(function(){
	Route::get('/dudi', 'index')->name('index');
	Route::get('/dudi/data', 'data')->name('data.index');
	Route::get('/dudi/create', 'create')->name('create');
	Route::post('/dudi', 'store')->name('store');
	Route::get('/dudi/{dudi}', 'show')->name('show');
	Route::get('/dudi/{dudi}/edit', 'edit')->name('edit');
	Route::patch('/dudi/{dudi}', 'update')->name('update');
	Route::get('/dudi/{dudi}/delete', 'destroy')->name('destroy');
});