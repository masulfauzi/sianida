<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Ekskul\Controllers\EkskulController;

Route::controller(EkskulController::class)->middleware(['web','auth'])->name('ekskul.')->group(function(){
	Route::get('/ekskul', 'index')->name('index');
	Route::get('/ekskul/data', 'data')->name('data.index');
	Route::get('/ekskul/create', 'create')->name('create');
	Route::post('/ekskul', 'store')->name('store');
	Route::get('/ekskul/{ekskul}', 'show')->name('show');
	Route::get('/ekskul/{ekskul}/edit', 'edit')->name('edit');
	Route::patch('/ekskul/{ekskul}', 'update')->name('update');
	Route::get('/ekskul/{ekskul}/delete', 'destroy')->name('destroy');
});