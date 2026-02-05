<?php

use Illuminate\Support\Facades\Route;
use App\Modules\IjinKeluarIjinKeluarKelas\Controllers\IjinKeluarIjinKeluarKelasController;

Route::controller(IjinKeluarKelasController::class)->middleware(['web','auth'])->name('ijinkeluarkelas.')->group(function(){
	Route::get('/ijinkeluarkelas', 'index')->name('index');
	Route::get('/ijinkeluarkelas/data', 'data')->name('data.index');
	Route::get('/ijinkeluarkelas/create', 'create')->name('create');
	Route::post('/ijinkeluarkelas', 'store')->name('store');
	Route::get('/ijinkeluarkelas/{ijinkeluarkelas}', 'show')->name('show');
	Route::get('/ijinkeluarkelas/{ijinkeluarkelas}/edit', 'edit')->name('edit');
	Route::patch('/ijinkeluarkelas/{ijinkeluarkelas}', 'update')->name('update');
	Route::get('/ijinkeluarkelas/{ijinkeluarkelas}/delete', 'destroy')->name('destroy');
});