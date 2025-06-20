<?php

namespace App\Console\Commands;

use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AtualizarParcelasVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parcelas:atualizar-vencidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o status das parcelas vencidas automaticamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoje = Carbon::today();

        // Buscar parcelas pendentes que já venceram
        $parcelasVencidas = Parcela::where('status', 'pendente')
            ->where('data_vencimento', '<', $hoje)
            ->get();

        if ($parcelasVencidas->count() > 0) {
            // Atualizar status para vencida
            Parcela::where('status', 'pendente')
                ->where('data_vencimento', '<', $hoje)
                ->update(['status' => 'vencida']);

            $this->info("✅ {$parcelasVencidas->count()} parcelas foram marcadas como vencidas.");

            // Mostrar detalhes
            $this->table(
                ['Venda', 'Parcela', 'Valor', 'Vencimento', 'Dias Atraso'],
                $parcelasVencidas->map(function ($parcela) use ($hoje) {
                    return [
                        "#" . $parcela->venda_id,
                        $parcela->numero_parcela . "ª",
                        "R$ " . number_format($parcela->valor, 2, ',', '.'),
                        $parcela->data_vencimento->format('d/m/Y'),
                        $parcela->data_vencimento->diffInDays($hoje) . " dias"
                    ];
                })->toArray()
            );
        } else {
            $this->info("ℹ️ Nenhuma parcela vencida encontrada.");
        }

        return Command::SUCCESS;
    }
}
