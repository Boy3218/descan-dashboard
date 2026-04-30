<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LkeController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('desa', \App\Http\Controllers\DesaController::class)->except(['show']);
Route::resource('lke-indicator', \App\Http\Controllers\LkeIndicatorController::class);

Route::get('/lke', [LkeController::class, 'index'])->name('lke.index');
Route::get('/lke-export', [LkeController::class, 'export'])->name('lke.export');
Route::get('/lke/{indicator}', [LkeController::class, 'show'])->name('lke.show');
Route::post('/lke/{indicator}', [LkeController::class, 'update'])->name('lke.update');
Route::delete('/lke/response/{response}', [LkeController::class, 'destroy'])->name('lke.destroy');
Route::post('/lke/response/{response}/status', [LkeController::class, 'updateStatus'])->name('lke.updateStatus');
