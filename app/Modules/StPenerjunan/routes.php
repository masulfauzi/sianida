<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StPenerjunan\Controllers\StPenerjunanController;

Route::controller(StPenerjunanController::class)->middleware(['web','auth'])->name('stpenerjunan.')->group(function(){
	Route::get('/stpenerjunan', 'index')->name('index');
	Route::get('/stpenerjunan/data', 'data')->name('data.index');
	Route::get('/stpenerjunan/create', 'create')->name('create');
	Route::post('/stpenerjunan', 'store')->name('store');
	Route::get('/stpenerjunan/{stpenerjunan}', 'show')->name('show');
	Route::get('/stpenerjunan/{stpenerjunan}/surat-tugas/{guru}', 'suratTugas')->name('surat_tugas.show');
	Route::get('/stpenerjunan/{stpenerjunan}/edit', 'edit')->name('edit');
	Route::patch('/stpenerjunan/{stpenerjunan}', 'update')->name('update');
	Route::get('/stpenerjunan/{stpenerjunan}/delete', 'destroy')->name('destroy');
});