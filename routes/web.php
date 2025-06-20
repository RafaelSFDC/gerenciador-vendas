<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('vendas', VendaController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
