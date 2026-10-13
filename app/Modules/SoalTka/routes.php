<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SoalTka\Controllers\SoalTkaController;

Route::controller(SoalTkaController::class)->middleware(['web','auth'])->name('soaltka.')->group(function(){
	Route::get('/soaltka', 'index')->name('index');
	Route::get('/soaltka/data', 'data')->name('data.index');
	Route::get('/soaltka/create', 'create')->name('create');
	Route::post('/soaltka', 'store')->name('store');
	Route::get('/soaltka/{soaltka}', 'show')->name('show');
	Route::get('/soaltka/{soaltka}/edit', 'edit')->name('edit');
	Route::patch('/soaltka/{soaltka}', 'update')->name('update');
	Route::get('/soaltka/{soaltka}/delete', 'destroy')->name('destroy');
});