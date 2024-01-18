<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Snbp\Controllers\SnbpController;

Route::controller(SnbpController::class)->middleware(['web','auth'])->name('snbp.')->group(function(){
	// ROUTE CUSTOM 
	Route::get('/snbp/jurusan/{jurusan}', 'index_jurusan')->name('jurusan.index');
	Route::get('/snbp/generate_jurusan/{jurusan}', 'generate_jurusan')->name('generate.create');
	
	
	
	Route::get('/snbp', 'index')->name('index');
	Route::get('/snbp/data', 'data')->name('data.index');
	Route::get('/snbp/create', 'create')->name('create');
	Route::post('/snbp', 'store')->name('store');
	Route::get('/snbp/{snbp}', 'show')->name('show');
	Route::get('/snbp/{snbp}/edit', 'edit')->name('edit');
	Route::patch('/snbp/{snbp}', 'update')->name('update');
	Route::get('/snbp/{snbp}/delete', 'destroy')->name('destroy');
});