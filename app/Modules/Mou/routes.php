<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Mou\Controllers\MouController;

Route::controller(MouController::class)->middleware(['web','auth'])->name('mou.')->group(function(){
	Route::get('/mou', 'index')->name('index');
	Route::get('/mou/data', 'data')->name('data.index');
	Route::get('/mou/create', 'create')->name('create');
	Route::post('/mou', 'store')->name('store');
	Route::get('/mou/{mou}', 'show')->name('show');
	Route::get('/mou/{mou}/edit', 'edit')->name('edit');
	Route::patch('/mou/{mou}', 'update')->name('update');
	Route::get('/mou/{mou}/delete', 'destroy')->name('destroy');
});