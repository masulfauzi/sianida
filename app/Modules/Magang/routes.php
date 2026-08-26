<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Magang\Controllers\MagangController;

Route::controller(MagangController::class)->middleware(['web','auth'])->name('magang.')->group(function(){
	Route::get('/magang', 'index')->name('index');
	Route::get('/magang/data', 'data')->name('data.index');
	Route::get('/magang/create', 'create')->name('create');
	Route::post('/magang', 'store')->name('store');
	Route::get('/magang/{magang}', 'show')->name('show');
	Route::get('/magang/{magang}/edit', 'edit')->name('edit');
	Route::patch('/magang/{magang}', 'update')->name('update');
	Route::get('/magang/{magang}/delete', 'destroy')->name('destroy');
});