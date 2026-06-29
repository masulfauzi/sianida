<?php

use Illuminate\Support\Facades\Route;
use App\Modules\VerifikasiRpp\Controllers\VerifikasiRppController;

Route::controller(VerifikasiRppController::class)->middleware(['web','auth'])->name('verifikasirpp.')->group(function(){
	Route::get('/verifikasirpp', 'index')->name('index');
	Route::get('/verifikasirpp/data', 'data')->name('data.index');
	Route::get('/verifikasirpp/create', 'create')->name('create');
	Route::get('/verifikasirpp/export/pdf', 'exportPdf')->name('export.pdf.show');
	Route::post('/verifikasirpp', 'store')->name('store');
	Route::get('/verifikasirpp/{verifikasirpp}/detail', 'detail')->name('detail.show');
	Route::get('/verifikasirpp/{verifikasirpp}', 'show')->name('show');
	Route::get('/verifikasirpp/{verifikasirpp}/edit', 'edit')->name('edit');
	Route::patch('/verifikasirpp/{verifikasirpp}', 'update')->name('update');
	Route::get('/verifikasirpp/{verifikasirpp}/delete', 'destroy')->name('destroy');
});