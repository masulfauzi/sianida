<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Ksp\Controllers\KspController;

Route::controller(KspController::class)->middleware(['web','auth'])->name('ksp.')->group(function(){
	Route::get('/ksp', 'index')->name('index');
	Route::get('/ksp/data', 'data')->name('data.index');
	Route::get('/ksp/create', 'create')->name('create');
	Route::post('/ksp', 'store')->name('store');
	Route::get('/ksp/{ksp}', 'show')->name('show');
	Route::get('/ksp/{ksp}/edit', 'edit')->name('edit');
	Route::patch('/ksp/{ksp}', 'update')->name('update');
	Route::get('/ksp/{ksp}/delete', 'destroy')->name('destroy');
});

Route::get('/ksp/{ksp}/public', [KspController::class, 'publicShow'])->middleware(['web'])->name('ksp.public.show');