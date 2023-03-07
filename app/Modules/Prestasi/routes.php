<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Prestasi\Controllers\PrestasiController;

Route::controller(PrestasiController::class)->middleware(['web','auth'])->name('prestasi.')->group(function(){
	Route::get('/prestasi', 'index')->name('index');
	Route::get('/prestasi/data', 'data')->name('data.index');
	Route::get('/prestasi/create', 'create')->name('create');
	Route::post('/prestasi', 'store')->name('store');
	Route::get('/prestasi/{prestasi}', 'show')->name('show');
	Route::get('/prestasi/{prestasi}/edit', 'edit')->name('edit');
	Route::patch('/prestasi/{prestasi}', 'update')->name('update');
	Route::get('/prestasi/{prestasi}/delete', 'destroy')->name('destroy');
});