<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();
        $hoje = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        // Estatísticas do dia
        $vendasHoje = Venda::where('user_id', $userId)
            ->whereDate('data_venda', $hoje)
            ->count();

        $faturamentoHoje = Venda::where('user_id', $userId)
            ->whereDate('data_venda', $hoje)
            ->sum('valor_total');

        // Estatísticas do mês
        $vendasMes = Venda::where('user_id', $userId)
            ->whereBetween('data_venda', [$inicioMes, $fimMes])
            ->count();

        $faturamentoMes = Venda::where('user_id', $userId)
            ->whereBetween('data_venda', [$inicioMes, $fimMes])
            ->sum('valor_total');

        // Vendas recentes
        $vendasRecentes = Venda::with(['cliente'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Parcelas vencendo (próximos 7 dias) e vencidas
        $proximosSete = Carbon::now()->addDays(7);
        $parcelasVencendo = Parcela::with(['venda'])
            ->whereHas('venda', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where(function($query) use ($hoje, $proximosSete) {
                $query->where(function($q) use ($hoje, $proximosSete) {
                    // Parcelas pendentes vencendo nos próximos 7 dias (incluindo hoje)
                    $q->where('status', 'pendente')
                      ->whereBetween('data_vencimento', [$hoje, $proximosSete]);
                })
                ->orWhere(function($q) {
                    // Parcelas vencidas
                    $q->where('status', 'vencida');
                });
            })
            ->orderBy('data_vencimento')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'vendasHoje',
            'faturamentoHoje',
            'vendasMes',
            'faturamentoMes',
            'vendasRecentes',
            'parcelasVencendo'
        ));
    }
}
