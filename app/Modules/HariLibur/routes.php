<?php

use Illuminate\Support\Facades\Route;
use App\Modules\HariLibur\Controllers\HariLiburController;

Route::controller(HariLiburController::class)->middleware(['web','auth'])->name('harilibur.')->group(function(){
	Route::get('/harilibur', 'index')->name('index');
	Route::get('/harilibur/data', 'data')->name('data.index');
	Route::get('/harilibur/create', 'create')->name('create');
	Route::post('/harilibur', 'store')->name('store');
	Route::get('/harilibur/{harilibur}', 'show')->name('show');
	Route::get('/harilibur/{harilibur}/edit', 'edit')->name('edit');
	Route::patch('/harilibur/{harilibur}', 'update')->name('update');
	Route::get('/harilibur/{harilibur}/delete', 'destroy')->name('destroy');
});