<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Pesan\Controllers\PesanController;

Route::controller(PesanController::class)->middleware(['web','auth'])->name('pesan.')->group(function(){
	Route::get('/pesan', 'index')->name('index');
	Route::get('/pesan/data', 'data')->name('data.index');
	Route::get('/pesan/create', 'create')->name('create');
	Route::post('/pesan', 'store')->name('store');
	Route::get('/pesan/{pesan}', 'show')->name('show');
	Route::get('/pesan/{pesan}/edit', 'edit')->name('edit');
	Route::patch('/pesan/{pesan}', 'update')->name('update');
	Route::get('/pesan/{pesan}/delete', 'destroy')->name('destroy');
});