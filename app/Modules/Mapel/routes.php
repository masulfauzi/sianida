<?php

use App\Modules\Mapel\Controllers\MapelController;
use Illuminate\Support\Facades\Route;

Route::controller(MapelController::class)->middleware(['web', 'auth'])->name('mapel.')->group(function () {
    Route::get('/mapel', 'index')->name('index');
    Route::get('/mapel/data', 'data')->name('data.index');
    Route::get('/mapel/create', 'create')->name('create');
    Route::post('/mapel', 'store')->name('store');
    Route::get('/mapel/{mapel}', 'show')->name('show');
    Route::get('/mapel/{mapel}/edit', 'edit')->name('edit');
    Route::patch('/mapel/{mapel}', 'update')->name('update');
    Route::get('/mapel/{mapel}/delete', 'destroy')->name('destroy');
    Route::get('/mapel/{mapel}/order/up', 'orderUp')->name('order.up.edit');
    Route::get('/mapel/{mapel}/order/down', 'orderDown')->name('order.down.edit');
});
