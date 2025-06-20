<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RelatorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Gerar PDF de uma venda específica
     */
    public function vendaPdf(Venda $venda): Response
    {
        // Verificar se o usuário pode visualizar esta venda
        $this->authorize('view', $venda);

        // Carregar relacionamentos necessários
        $venda->load(['cliente', 'user', 'formaPagamento', 'itens.produto', 'parcelas']);

        // Gerar PDF
        $pdf = Pdf::loadView('relatorios.venda-pdf', compact('venda'));

        // Configurar papel e orientação
        $pdf->setPaper('A4', 'portrait');

        // Nome do arquivo
        $nomeArquivo = "venda-{$venda->id}-" . date('Y-m-d') . ".pdf";

        return $pdf->download($nomeArquivo);
    }

    /**
     * Gerar PDF com relatório de vendas por período
     */
    public function relatorioVendas(Request $request): Response
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'cliente_id' => 'nullable|exists:clientes,id',
            'forma_pagamento_id' => 'nullable|exists:formas_pagamento,id',
            'status' => 'nullable|in:pendente,paga,cancelada',
        ]);

        $userId = Auth::id();
        $dataInicio = Carbon::parse($request->data_inicio);
        $dataFim = Carbon::parse($request->data_fim);

        // Query base
        $query = Venda::with(['cliente', 'formaPagamento', 'itens.produto'])
            ->where('user_id', $userId)
            ->whereBetween('data_venda', [$dataInicio, $dataFim]);

        // Aplicar filtros
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('forma_pagamento_id')) {
            $query->where('forma_pagamento_id', $request->forma_pagamento_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vendas = $query->orderBy('data_venda', 'desc')->get();

        // Calcular totais
        $totalVendas = $vendas->count();
        $valorTotal = $vendas->sum('valor_total');
        $vendasPorStatus = $vendas->groupBy('status')->map->count();

        // Dados para o relatório
        $dados = [
            'vendas' => $vendas,
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim,
            ],
            'totais' => [
                'quantidade' => $totalVendas,
                'valor' => $valorTotal,
                'por_status' => $vendasPorStatus,
            ],
            'filtros' => $request->only(['cliente_id', 'forma_pagamento_id', 'status']),
            'usuario' => Auth::user(),
        ];

        // Gerar PDF
        $pdf = Pdf::loadView('relatorios.vendas-periodo-pdf', $dados);

        // Configurar papel e orientação
        $pdf->setPaper('A4', 'portrait');

        // Nome do arquivo
        $nomeArquivo = "relatorio-vendas-{$dataInicio->format('Y-m-d')}-a-{$dataFim->format('Y-m-d')}.pdf";

        return $pdf->download($nomeArquivo);
    }

    /**
     * Mostrar formulário para gerar relatório de vendas
     */
    public function formRelatorioVendas()
    {
        $clientes = \App\Models\Cliente::orderBy('nome')->get();
        $formasPagamento = \App\Models\FormaPagamento::ativo()->orderBy('nome')->get();

        return view('relatorios.form-vendas', compact('clientes', 'formasPagamento'));
    }

    /**
     * Gerar PDF de parcelas em aberto
     */
    public function parcelasAberto(): Response
    {
        $userId = Auth::id();

        $parcelas = \App\Models\Parcela::with(['venda.cliente', 'venda.formaPagamento'])
            ->whereHas('venda', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'pendente')
            ->orderBy('data_vencimento')
            ->get();

        // Agrupar por status (vencidas e a vencer)
        $hoje = Carbon::today();
        $parcelasVencidas = $parcelas->filter(function($parcela) use ($hoje) {
            return $parcela->data_vencimento < $hoje;
        });

        $parcelasAVencer = $parcelas->filter(function($parcela) use ($hoje) {
            return $parcela->data_vencimento >= $hoje;
        });

        $dados = [
            'parcelas_vencidas' => $parcelasVencidas,
            'parcelas_a_vencer' => $parcelasAVencer,
            'total_vencidas' => $parcelasVencidas->sum('valor'),
            'total_a_vencer' => $parcelasAVencer->sum('valor'),
            'usuario' => Auth::user(),
            'data_geracao' => Carbon::now(),
        ];

        // Gerar PDF
        $pdf = Pdf::loadView('relatorios.parcelas-aberto-pdf', $dados);

        // Configurar papel e orientação
        $pdf->setPaper('A4', 'portrait');

        // Nome do arquivo
        $nomeArquivo = "parcelas-aberto-" . date('Y-m-d') . ".pdf";

        return $pdf->download($nomeArquivo);
    }
}
