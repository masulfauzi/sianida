<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PresensiHarian\Controllers\PresensiHarianController;

Route::controller(PresensiHarianController::class)->middleware(['web','auth'])->name('presensiharian.')->group(function(){
	Route::get('/presensiharian', 'index')->name('index');
	Route::get('/presensiharian/data', 'data')->name('data.index');
	Route::get('/presensiharian/create', 'create')->name('create');
	Route::post('/presensiharian', 'store')->name('store');
	Route::get('/presensiharian/{presensiharian}', 'show')->name('show');
	Route::get('/presensiharian/{presensiharian}/edit', 'edit')->name('edit');
	Route::patch('/presensiharian/{presensiharian}', 'update')->name('update');
	Route::get('/presensiharian/{presensiharian}/delete', 'destroy')->name('destroy');
});