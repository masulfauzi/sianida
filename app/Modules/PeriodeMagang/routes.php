<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PeriodeMagang\Controllers\PeriodeMagangController;

Route::controller(PeriodeMagangController::class)->middleware(['web','auth'])->name('periodemagang.')->group(function(){
	Route::get('/periodemagang', 'index')->name('index');
	Route::get('/periodemagang/data', 'data')->name('data.index');
	Route::get('/periodemagang/create', 'create')->name('create');
	Route::post('/periodemagang', 'store')->name('store');
	Route::get('/periodemagang/{periodemagang}', 'show')->name('show');
	Route::get('/periodemagang/{periodemagang}/edit', 'edit')->name('edit');
	Route::patch('/periodemagang/{periodemagang}', 'update')->name('update');
	Route::get('/periodemagang/{periodemagang}/delete', 'destroy')->name('destroy');
});