<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParcelaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista todas as parcelas do usuário
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $status = $request->get('status', 'todas');

        $query = Parcela::with(['venda.cliente', 'venda.formaPagamento'])
            ->whereHas('venda', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        // Filtrar por status
        if ($status === 'pendentes') {
            $query->where('status', 'pendente');
        } elseif ($status === 'vencidas') {
            $query->where('status', 'vencida');
        } elseif ($status === 'pagas') {
            $query->where('status', 'paga');
        } elseif ($status === 'vencendo') {
            $hoje = Carbon::today();
            $proximosSete = Carbon::today()->addDays(7);
            $query->where('status', 'pendente')
                  ->whereBetween('data_vencimento', [$hoje, $proximosSete]);
        }

        $parcelas = $query->orderBy('data_vencimento')->paginate(20);

        // Estatísticas
        $hoje = Carbon::today();
        $stats = [
            'total_pendentes' => Parcela::whereHas('venda', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'pendente')->count(),

            'total_vencidas' => Parcela::whereHas('venda', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'vencida')->count(),

            'vencendo_hoje' => Parcela::whereHas('venda', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'pendente')->whereDate('data_vencimento', $hoje)->count(),

            'vencendo_7_dias' => Parcela::whereHas('venda', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'pendente')->whereBetween('data_vencimento', [$hoje, $hoje->copy()->addDays(7)])->count(),
        ];

        return view('parcelas.index', compact('parcelas', 'status', 'stats'));
    }

    /**
     * Marcar parcela como paga
     */
    public function marcarComoPaga(Request $request, Parcela $parcela)
    {
        // Verificar se a parcela pertence ao usuário
        if ($parcela->venda->user_id !== Auth::id()) {
            abort(403, 'Acesso negado');
        }

        // Verificar se a parcela não está já paga
        if ($parcela->status === 'paga') {
            return back()->with('warning', 'Esta parcela já está marcada como paga.');
        }

        // Validar a data de pagamento se fornecida
        $dataPagamento = $request->data_pagamento ? Carbon::parse($request->data_pagamento) : Carbon::today();

        // Marcar como paga
        $parcela->update([
            'status' => 'paga',
            'data_pagamento' => $dataPagamento
        ]);

        return back()->with('success', 'Parcela marcada como paga com sucesso!');
    }

    /**
     * Marcar parcela como pendente (desfazer pagamento)
     */
    public function marcarComoPendente(Parcela $parcela)
    {
        // Verificar se a parcela pertence ao usuário
        if ($parcela->venda->user_id !== Auth::id()) {
            abort(403, 'Acesso negado');
        }

        // Verificar se a parcela está paga
        if ($parcela->status !== 'paga') {
            return back()->with('warning', 'Esta parcela não está marcada como paga.');
        }

        // Determinar o novo status baseado na data de vencimento
        $novoStatus = $parcela->data_vencimento < Carbon::today() ? 'vencida' : 'pendente';

        // Marcar como pendente/vencida
        $parcela->update([
            'status' => $novoStatus,
            'data_pagamento' => null
        ]);

        return back()->with('success', 'Pagamento da parcela foi desfeito com sucesso!');
    }

    /**
     * Atualizar data de pagamento
     */
    public function atualizarDataPagamento(Request $request, Parcela $parcela)
    {
        // Verificar se a parcela pertence ao usuário
        if ($parcela->venda->user_id !== Auth::id()) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'data_pagamento' => 'required|date'
        ]);

        $parcela->update([
            'status' => 'paga',
            'data_pagamento' => $request->data_pagamento
        ]);

        return back()->with('success', 'Data de pagamento atualizada com sucesso!');
    }
}
