<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Ijin\Controllers\IjinController;

Route::controller(IjinController::class)->middleware(['web','auth'])->name('ijin.')->group(function(){
	Route::get('/ijin', 'index')->name('index');
	Route::get('/ijin/data', 'data')->name('data.index');
	Route::get('/ijin/create', 'create')->name('create');
	Route::post('/ijin', 'store')->name('store');
	Route::get('/ijin/{ijin}', 'show')->name('show');
	Route::get('/ijin/{ijin}/edit', 'edit')->name('edit');
	Route::patch('/ijin/{ijin}', 'update')->name('update');
	Route::get('/ijin/{ijin}/delete', 'destroy')->name('destroy');
});