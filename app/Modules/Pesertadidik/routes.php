<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Pesertadidik\Controllers\PesertadidikController;

Route::controller(PesertadidikController::class)->middleware(['web','auth'])->name('pesertadidik.')->group(function(){
	Route::get('/pesertadidik', 'index')->name('index');
	Route::get('/pesertadidik/data', 'data')->name('data.index');
	Route::get('/pesertadidik/create', 'create')->name('create');
	Route::post('/pesertadidik', 'store')->name('store');
	Route::get('/pesertadidik/{pesertadidik}', 'show')->name('show');
	Route::get('/pesertadidik/{pesertadidik}/edit', 'edit')->name('edit');
	Route::patch('/pesertadidik/{pesertadidik}', 'update')->name('update');
	Route::get('/pesertadidik/{pesertadidik}/delete', 'destroy')->name('destroy');
});