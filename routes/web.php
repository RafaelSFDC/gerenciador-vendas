<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('vendas', VendaController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('produtos', ProdutoController::class);

    // Rotas de parcelas
    Route::prefix('parcelas')->name('parcelas.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ParcelaController::class, 'index'])->name('index');
        Route::patch('{parcela}/marcar-paga', [\App\Http\Controllers\ParcelaController::class, 'marcarComoPaga'])->name('marcar-paga');
        Route::patch('{parcela}/marcar-pendente', [\App\Http\Controllers\ParcelaController::class, 'marcarComoPendente'])->name('marcar-pendente');
        Route::patch('{parcela}/atualizar-data-pagamento', [\App\Http\Controllers\ParcelaController::class, 'atualizarDataPagamento'])->name('atualizar-data-pagamento');
    });

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
