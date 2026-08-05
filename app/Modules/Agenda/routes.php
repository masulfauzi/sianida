<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Agenda\Controllers\AgendaController;

Route::controller(AgendaController::class)->middleware(['web','auth'])->name('agenda.')->group(function(){
	Route::get('/agenda', 'index')->name('index');
	Route::get('/agenda/data', 'data')->name('data.index');
	Route::get('/agenda/create', 'create')->name('create');
	Route::post('/agenda', 'store')->name('store');
	Route::get('/agenda/{agenda}', 'show')->name('show');
	Route::get('/agenda/{agenda}/edit', 'edit')->name('edit');
	Route::patch('/agenda/{agenda}', 'update')->name('update');
	Route::get('/agenda/{agenda}/delete', 'destroy')->name('destroy');
});