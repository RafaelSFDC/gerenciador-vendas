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
        // Parcela vencendo hoje
        $parcelaHoje = Parcela::create([
            'venda_id' => 1,
            'numero_parcela' => 100,
            'valor' => 200.00,
            'data_vencimento' => Carbon::today(),
            'status' => 'pendente'
        ]);

        // Parcela vencida (ontem)
        $parcelaVencida = Parcela::create([
            'venda_id' => 1,
            'numero_parcela' => 101,
            'valor' => 150.00,
            'data_vencimento' => Carbon::yesterday(),
            'status' => 'vencida'
        ]);

        // Parcela vencida (há 5 dias)
        $parcelaVencida2 = Parcela::create([
            'venda_id' => 1,
            'numero_parcela' => 102,
            'valor' => 300.00,
            'data_vencimento' => Carbon::today()->subDays(5),
            'status' => 'vencida'
        ]);

        $this->info("✅ Parcelas de teste criadas:");
        $this->info("- Parcela {$parcelaHoje->id}: Vence hoje ({$parcelaHoje->data_vencimento->format('d/m/Y')})");
        $this->info("- Parcela {$parcelaVencida->id}: Vencida ontem ({$parcelaVencida->data_vencimento->format('d/m/Y')})");
        $this->info("- Parcela {$parcelaVencida2->id}: Vencida há 5 dias ({$parcelaVencida2->data_vencimento->format('d/m/Y')})");

        return Command::SUCCESS;
    }
}
