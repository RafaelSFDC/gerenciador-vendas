<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\FormaPagamento;
use App\Models\ItemVenda;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GerarVendasTeste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendas:gerar-teste {quantidade=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera vendas de teste para demonstração do sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $quantidade = (int) $this->argument('quantidade');

        $this->info("🚀 Gerando {$quantidade} vendas de teste...");

        // Buscar dados necessários
        $vendedor = User::first();
        $clientes = Cliente::all();
        $produtos = Produto::all();
        $formasPagamento = FormaPagamento::all();

        if (!$vendedor || $clientes->isEmpty() || $produtos->isEmpty() || $formasPagamento->isEmpty()) {
            $this->error('❌ Execute primeiro: php artisan db:seed');
            return Command::FAILURE;
        }

        $vendasCriadas = 0;

        for ($i = 0; $i < $quantidade; $i++) {
            try {
                // Data aleatória nos últimos 30 dias
                $dataVenda = Carbon::now()->subDays(rand(0, 30));

                // Cliente aleatório (pode ser null)
                $cliente = rand(0, 10) > 2 ? $clientes->random() : null;

                // Forma de pagamento aleatória
                $formaPagamento = $formasPagamento->random();

                // Número de parcelas aleatório
                $numeroParcelas = rand(1, 6);

                // Criar venda
                $venda = Venda::create([
                    'user_id' => $vendedor->id,
                    'cliente_id' => $cliente?->id,
                    'forma_pagamento_id' => $formaPagamento->id,
                    'valor_total' => 0, // Será calculado depois
                    'numero_parcelas' => $numeroParcelas,
                    'data_venda' => $dataVenda,
                    'observacoes' => rand(0, 10) > 7 ? 'Venda de teste gerada automaticamente' : null,
                    'status' => $this->getStatusAleatorio(),
                ]);

                // Adicionar itens (1 a 4 itens por venda)
                $numeroItens = rand(1, 4);
                $valorTotal = 0;

                for ($j = 0; $j < $numeroItens; $j++) {
                    $produto = $produtos->random();
                    $quantidade = rand(1, 3);
                    $precoUnitario = $produto->preco * (rand(80, 120) / 100); // Variação de ±20%
                    $subtotal = $quantidade * $precoUnitario;

                    ItemVenda::create([
                        'venda_id' => $venda->id,
                        'produto_id' => $produto->id,
                        'quantidade' => $quantidade,
                        'preco_unitario' => $precoUnitario,
                        'subtotal' => $subtotal,
                    ]);

                    $valorTotal += $subtotal;
                }

                // Atualizar valor total da venda
                $venda->update(['valor_total' => $valorTotal]);

                // Criar parcelas
                $valorParcela = $valorTotal / $numeroParcelas;

                for ($k = 0; $k < $numeroParcelas; $k++) {
                    $dataVencimento = $dataVenda->copy()->addMonths($k + 1);

                    // Status da parcela baseado na data
                    $statusParcela = 'pendente';
                    $dataPagamento = null;

                    if ($venda->status === 'paga') {
                        $statusParcela = 'paga';
                        $dataPagamento = $dataVencimento->copy()->subDays(rand(0, 5));
                    } elseif ($dataVencimento < Carbon::now()) {
                        $statusParcela = rand(0, 10) > 3 ? 'vencida' : 'paga';
                        if ($statusParcela === 'paga') {
                            $dataPagamento = $dataVencimento->copy()->addDays(rand(0, 10));
                        }
                    }

                    Parcela::create([
                        'venda_id' => $venda->id,
                        'numero_parcela' => $k + 1,
                        'valor' => $valorParcela,
                        'data_vencimento' => $dataVencimento,
                        'data_pagamento' => $dataPagamento,
                        'status' => $statusParcela,
                    ]);
                }

                $vendasCriadas++;

                if ($vendasCriadas % 5 === 0) {
                    $this->info("✅ {$vendasCriadas} vendas criadas...");
                }

            } catch (\Exception $e) {
                $this->error("❌ Erro ao criar venda: " . $e->getMessage());
            }
        }

        $this->info("🎉 Concluído! {$vendasCriadas} vendas de teste foram criadas com sucesso.");
        $this->info("💡 Acesse o sistema para visualizar as vendas geradas.");

        return Command::SUCCESS;
    }

    private function getStatusAleatorio(): string
    {
        $statuses = ['pendente', 'paga', 'cancelada'];
        $pesos = [60, 35, 5]; // 60% pendente, 35% paga, 5% cancelada

        $random = rand(1, 100);

        if ($random <= $pesos[0]) {
            return $statuses[0];
        } elseif ($random <= $pesos[0] + $pesos[1]) {
            return $statuses[1];
        } else {
            return $statuses[2];
        }
    }
}
