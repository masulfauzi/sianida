<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AnggotaEkskul\Controllers\AnggotaEkskulController;

Route::controller(AnggotaEkskulController::class)->middleware(['web','auth'])->name('anggotaekskul.')->group(function(){
	Route::get('/anggotaekskul', 'index')->name('index');
	Route::get('/anggotaekskul/data', 'data')->name('data.index');
	Route::get('/anggotaekskul/create', 'create')->name('create');
	Route::post('/anggotaekskul', 'store')->name('store');
	Route::get('/anggotaekskul/{anggotaekskul}', 'show')->name('show');
	Route::get('/anggotaekskul/{anggotaekskul}/edit', 'edit')->name('edit');
	Route::patch('/anggotaekskul/{anggotaekskul}', 'update')->name('update');
	Route::get('/anggotaekskul/{anggotaekskul}/delete', 'destroy')->name('destroy');
});