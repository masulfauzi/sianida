<?php

use Illuminate\Support\Facades\Route;
use App\Modules\FileKsp\Controllers\FileKspController;

Route::controller(FileKspController::class)->middleware(['web','auth'])->name('fileksp.')->group(function(){
	Route::get('/fileksp', 'index')->name('index');
	Route::get('/fileksp/data', 'data')->name('data.index');
	Route::get('/fileksp/create', 'create')->name('create');
	Route::post('/fileksp', 'store')->name('store');
	Route::get('/fileksp/{fileksp}', 'show')->name('show');
	Route::post('/ksp/{ksp}/fileksp/upload', 'upload')->name('upload.store');
	Route::get('/fileksp/{fileksp}/edit', 'edit')->name('edit');
	Route::patch('/fileksp/{fileksp}', 'update')->name('update');
	Route::get('/fileksp/{fileksp}/delete', 'destroy')->name('destroy');
});