<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\FormaPagamento;
use App\Models\ItemVenda;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Venda::with(['cliente', 'user', 'formaPagamento'])
            ->where('user_id', Auth::id());

        // Filtros
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('forma_pagamento_id')) {
            $query->where('forma_pagamento_id', $request->forma_pagamento_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_venda', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_venda', '<=', $request->data_fim);
        }

        $vendas = $query->orderBy('created_at', 'desc')->paginate(10);

        $clientes = Cliente::orderBy('nome')->get();
        $formasPagamento = FormaPagamento::ativo()->orderBy('nome')->get();

        return view('vendas.index', compact('vendas', 'clientes', 'formasPagamento'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::ativo()->orderBy('nome')->get();
        $formasPagamento = FormaPagamento::ativo()->orderBy('nome')->get();

        return view('vendas.create', compact('clientes', 'produtos', 'formasPagamento'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'forma_pagamento_id' => 'required|exists:formas_pagamento,id',
            'data_venda' => 'required|date',
            'numero_parcelas' => 'required|integer|min:1',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0',
            'parcelas' => 'required|array',
            'parcelas.*.data_vencimento' => 'required|date',
            'parcelas.*.valor' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calcular valor total
            $valorTotal = 0;
            foreach ($request->itens as $item) {
                $valorTotal += $item['quantidade'] * $item['preco_unitario'];
            }

            // Criar venda
            $venda = Venda::create([
                'user_id' => Auth::id(),
                'cliente_id' => $request->cliente_id ?: null,
                'forma_pagamento_id' => $request->forma_pagamento_id,
                'valor_total' => $valorTotal,
                'numero_parcelas' => $request->numero_parcelas,
                'data_venda' => $request->data_venda,
                'observacoes' => $request->observacoes,
                'status' => 'pendente',
            ]);

            // Criar itens da venda
            foreach ($request->itens as $item) {
                $subtotal = $item['quantidade'] * $item['preco_unitario'];

                ItemVenda::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Criar parcelas
            foreach ($request->parcelas as $index => $parcela) {
                $customizada = isset($parcela['customizada']) && $parcela['customizada'];

                Parcela::create([
                    'venda_id' => $venda->id,
                    'numero_parcela' => $index + 1,
                    'valor' => $parcela['valor'],
                    'valor_original' => $customizada ? ($valorTotal / count($request->parcelas)) : null,
                    'customizada' => $customizada,
                    'data_vencimento' => $parcela['data_vencimento'],
                    'status' => 'pendente',
                ]);
            }

            DB::commit();

            return redirect()->route('vendas.show', $venda)
                ->with('success', 'Venda criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Erro ao criar venda: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venda $venda)
    {
        $this->authorize('view', $venda);

        $venda->load(['cliente', 'user', 'formaPagamento', 'itens.produto', 'parcelas']);

        return view('vendas.show', compact('venda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venda $venda)
    {
        $this->authorize('update', $venda);

        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::ativo()->orderBy('nome')->get();
        $formasPagamento = FormaPagamento::ativo()->orderBy('nome')->get();

        $venda->load(['itens.produto', 'parcelas']);

        return view('vendas.edit', compact('venda', 'clientes', 'produtos', 'formasPagamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venda $venda)
    {
        $this->authorize('update', $venda);

        $request->validate([
            'forma_pagamento_id' => 'required|exists:formas_pagamento,id',
            'data_venda' => 'required|date',
            'numero_parcelas' => 'required|integer|min:1',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0',
            'parcelas' => 'required|array',
            'parcelas.*.data_vencimento' => 'required|date',
            'parcelas.*.valor' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calcular valor total
            $valorTotal = 0;
            foreach ($request->itens as $item) {
                $valorTotal += $item['quantidade'] * $item['preco_unitario'];
            }

            // Atualizar venda
            $venda->update([
                'cliente_id' => $request->cliente_id ?: null,
                'forma_pagamento_id' => $request->forma_pagamento_id,
                'valor_total' => $valorTotal,
                'numero_parcelas' => $request->numero_parcelas,
                'data_venda' => $request->data_venda,
                'observacoes' => $request->observacoes,
            ]);

            // Remover itens e parcelas existentes
            $venda->itens()->delete();
            $venda->parcelas()->delete();

            // Recriar itens da venda
            foreach ($request->itens as $item) {
                $subtotal = $item['quantidade'] * $item['preco_unitario'];

                ItemVenda::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Recriar parcelas
            foreach ($request->parcelas as $index => $parcela) {
                $customizada = isset($parcela['customizada']) && $parcela['customizada'];

                Parcela::create([
                    'venda_id' => $venda->id,
                    'numero_parcela' => $index + 1,
                    'valor' => $parcela['valor'],
                    'valor_original' => $customizada ? ($valorTotal / count($request->parcelas)) : null,
                    'customizada' => $customizada,
                    'data_vencimento' => $parcela['data_vencimento'],
                    'status' => 'pendente',
                ]);
            }

            DB::commit();

            return redirect()->route('vendas.show', $venda)
                ->with('success', 'Venda atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Erro ao atualizar venda: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venda $venda)
    {
        $this->authorize('delete', $venda);

        try {
            $venda->delete();
            return redirect()->route('vendas.index')
                ->with('success', 'Venda excluída com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir venda: ' . $e->getMessage());
        }
    }
}
