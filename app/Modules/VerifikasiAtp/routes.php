<?php

use Illuminate\Support\Facades\Route;
use App\Modules\VerifikasiAtp\Controllers\VerifikasiAtpController;

Route::controller(VerifikasiAtpController::class)->middleware(['web','auth'])->name('verifikasiatp.')->group(function(){
	Route::get('/verifikasiatp', 'index')->name('index');
	Route::get('/verifikasiatp/data', 'data')->name('data.index');
	Route::get('/verifikasiatp/create', 'create')->name('create');
	Route::post('/verifikasiatp', 'store')->name('store');
	Route::get('/verifikasiatp/{verifikasiatp}/detail', 'detail')->name('detail.show');
	Route::get('/verifikasiatp/{verifikasiatp}', 'show')->name('show');
	Route::get('/verifikasiatp/{verifikasiatp}/edit', 'edit')->name('edit');
	Route::patch('/verifikasiatp/{verifikasiatp}', 'update')->name('update');
	Route::get('/verifikasiatp/{verifikasiatp}/delete', 'destroy')->name('destroy');
});