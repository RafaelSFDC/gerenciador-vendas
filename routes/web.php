<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('vendas', VendaController::class);

    // Rotas de relatórios
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [RelatorioController::class, 'formRelatorioVendas'])->name('index');
        Route::get('vendas/{venda}/pdf', [RelatorioController::class, 'vendaPdf'])->name('venda-pdf');
        Route::post('vendas', [RelatorioController::class, 'relatorioVendas'])->name('vendas');
        Route::get('parcelas-aberto', [RelatorioController::class, 'parcelasAberto'])->name('parcelas-aberto');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
