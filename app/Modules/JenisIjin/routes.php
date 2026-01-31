<?php

use Illuminate\Support\Facades\Route;
use App\Modules\JenisIjin\Controllers\JenisIjinController;

Route::controller(JenisIjinController::class)->middleware(['web','auth'])->name('jenisijin.')->group(function(){
	Route::get('/jenisijin', 'index')->name('index');
	Route::get('/jenisijin/data', 'data')->name('data.index');
	Route::get('/jenisijin/create', 'create')->name('create');
	Route::post('/jenisijin', 'store')->name('store');
	Route::get('/jenisijin/{jenisijin}', 'show')->name('show');
	Route::get('/jenisijin/{jenisijin}/edit', 'edit')->name('edit');
	Route::patch('/jenisijin/{jenisijin}', 'update')->name('update');
	Route::get('/jenisijin/{jenisijin}/delete', 'destroy')->name('destroy');
});