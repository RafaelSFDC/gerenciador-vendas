<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        // Parcelas vencendo (próximos 7 dias)
        $proximosSete = Carbon::now()->addDays(7);
        $parcelasVencendo = Parcela::whereHas('venda', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'pendente')
            ->whereBetween('data_vencimento', [$hoje, $proximosSete])
            ->orderBy('data_vencimento')
            ->limit(5)
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
