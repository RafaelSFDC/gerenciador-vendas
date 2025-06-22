<?php

namespace App\Console\Commands;

use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CriarParcelaTeste extends Command
{
    protected $signature = 'parcela:criar-teste';
    protected $description = 'Criar uma parcela de teste com vencimento hoje';

    public function handle()
    {
        // Limpar parcelas de teste anteriores (numero_parcela >= 100)
        $parcelasRemovidas = Parcela::where('numero_parcela', '>=', 100)->delete();
        if ($parcelasRemovidas > 0) {
            $this->info("🗑️ Removidas {$parcelasRemovidas} parcelas de teste anteriores");
        }

        // Parcela vencendo hoje
        $parcelaHoje = Parcela::create([
            'venda_id' => 1,
            'numero_parcela' => 100,
            'valor' => 200.00,
            'data_vencimento' => Carbon::today(),
            'status' => 'pendente'
        ]);

        // Parcela vencida (há 5 dias)
        $parcelaVencida = Parcela::create([
            'venda_id' => 1,
            'numero_parcela' => 101,
            'valor' => 300.00,
            'data_vencimento' => Carbon::today()->subDays(5),
            'status' => 'vencida'
        ]);

        $this->info("✅ Parcelas de teste criadas:");
        $this->info("- Parcela {$parcelaHoje->id}: Vence hoje ({$parcelaHoje->data_vencimento->format('d/m/Y')})");
        $this->info("- Parcela {$parcelaVencida->id}: Vencida há 5 dias ({$parcelaVencida->data_vencimento->format('d/m/Y')})");

        return Command::SUCCESS;
    }
}
