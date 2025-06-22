<?php

namespace App\Console\Commands;

use App\Models\Parcela;
use Illuminate\Console\Command;

class LimparParcelasTeste extends Command
{
    protected $signature = 'parcela:limpar-teste';
    protected $description = 'Remove todas as parcelas de teste (numero_parcela >= 100)';

    public function handle()
    {
        $parcelasRemovidas = Parcela::where('numero_parcela', '>=', 100)->delete();
        
        if ($parcelasRemovidas > 0) {
            $this->info("🗑️ Removidas {$parcelasRemovidas} parcelas de teste");
        } else {
            $this->info("ℹ️ Nenhuma parcela de teste encontrada para remover");
        }
        
        return Command::SUCCESS;
    }
}
