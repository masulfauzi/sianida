<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StPenerjunanSiswa\Controllers\StPenerjunanSiswaController;

Route::controller(StPenerjunanSiswaController::class)->middleware(['web','auth'])->name('stpenerjunansiswa.')->group(function(){
	Route::get('/stpenerjunansiswa', 'index')->name('index');
	Route::get('/stpenerjunansiswa/data', 'data')->name('data.index');
	Route::get('/stpenerjunansiswa/create', 'create')->name('create');
	Route::post('/stpenerjunansiswa', 'store')->name('store');
	Route::get('/stpenerjunansiswa/{stpenerjunansiswa}', 'show')->name('show');
	Route::get('/stpenerjunansiswa/{stpenerjunansiswa}/surat-tugas/{dudi}', 'suratTugas')->name('surat_tugas.show');
	Route::get('/stpenerjunansiswa/{stpenerjunansiswa}/edit', 'edit')->name('edit');
	Route::patch('/stpenerjunansiswa/{stpenerjunansiswa}', 'update')->name('update');
	Route::get('/stpenerjunansiswa/{stpenerjunansiswa}/delete', 'destroy')->name('destroy');
});