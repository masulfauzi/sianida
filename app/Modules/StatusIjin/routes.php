<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StatusIjin\Controllers\StatusIjinController;

Route::controller(StatusIjinController::class)->middleware(['web','auth'])->name('statusijin.')->group(function(){
	Route::get('/statusijin', 'index')->name('index');
	Route::get('/statusijin/data', 'data')->name('data.index');
	Route::get('/statusijin/create', 'create')->name('create');
	Route::post('/statusijin', 'store')->name('store');
	Route::get('/statusijin/{statusijin}', 'show')->name('show');
	Route::get('/statusijin/{statusijin}/edit', 'edit')->name('edit');
	Route::patch('/statusijin/{statusijin}', 'update')->name('update');
	Route::get('/statusijin/{statusijin}/delete', 'destroy')->name('destroy');
});