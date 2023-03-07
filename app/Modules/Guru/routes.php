<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Guru\Controllers\GuruController;

Route::controller(GuruController::class)->middleware(['web','auth'])->name('guru.')->group(function(){
	Route::get('/guru', 'index')->name('index');
	Route::get('/guru/data', 'data')->name('data.index');
	Route::get('/guru/create', 'create')->name('create');
	Route::post('/guru', 'store')->name('store');
	Route::get('/guru/{guru}', 'show')->name('show');
	Route::get('/guru/{guru}/edit', 'edit')->name('edit');
	Route::patch('/guru/{guru}', 'update')->name('update');
	Route::get('/guru/{guru}/delete', 'destroy')->name('destroy');
});