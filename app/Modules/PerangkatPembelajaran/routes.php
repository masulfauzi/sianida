<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PerangkatPembelajaran\Controllers\PerangkatPembelajaranController;

Route::controller(PerangkatPembelajaranController::class)->middleware(['web','auth'])->name('perangkatpembelajaran.')->group(function(){
	//custome route
	Route::get('/perangkatpembelajaran/admin', 'index_admin')->name('admin.index');
	
	//route bawaan
	Route::get('/perangkatpembelajaran', 'index')->name('index');
	Route::get('/perangkatpembelajaran/data', 'data')->name('data.index');
	Route::get('/perangkatpembelajaran/create', 'create')->name('create');
	Route::post('/perangkatpembelajaran', 'store')->name('store');
	Route::get('/perangkatpembelajaran/{perangkatpembelajaran}', 'show')->name('show');
	Route::get('/perangkatpembelajaran/{perangkatpembelajaran}/edit', 'edit')->name('edit');
	Route::patch('/perangkatpembelajaran/{perangkatpembelajaran}', 'update')->name('update');
	Route::get('/perangkatpembelajaran/{perangkatpembelajaran}/delete', 'destroy')->name('destroy');


	
});