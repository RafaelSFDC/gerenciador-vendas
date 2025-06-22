@extends('app')

@section('title', 'Produto: ' . $produto->nome)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-box me-2"></i>
            {{ $produto->nome }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>
                Editar
            </a>
            <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações do Produto -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações do Produto
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Nome:</strong></div>
                        <div class="col-sm-8">{{ $produto->nome }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Descrição:</strong></div>
                        <div class="col-sm-8">
                            @if($produto->descricao)
                                {{ $produto->descricao }}
                            @else
                                <span class="text-muted">Não informada</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Preço:</strong></div>
                        <div class="col-sm-8">
                            <span class="h5 text-success">
                                R$ {{ number_format($produto->preco, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Estoque:</strong></div>
                        <div class="col-sm-8">
                            @if($produto->estoque > 0)
                                <span class="badge bg-info fs-6">{{ $produto->estoque }} unidades</span>
                            @else
                                <span class="badge bg-warning fs-6">Sem estoque</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            @if($produto->ativo)
                                <span class="badge bg-success fs-6">Ativo</span>
                            @else
                                <span class="badge bg-secondary fs-6">Inativo</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4"><strong>Cadastrado em:</strong></div>
                        <div class="col-sm-8">{{ $produto->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de Vendas -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Histórico de Vendas
                    </h5>
                </div>
                <div class="card-body">
                    @if($produto->itensVenda->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Qtd</th>
                                        <th>Valor Unit.</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($produto->itensVenda->take(10) as $item)
                                        <tr>
                                            <td>{{ $item->venda->data_venda->format('d/m/Y') }}</td>
                                            <td>
                                                @if($item->venda->cliente)
                                                    {{ Str::limit($item->venda->cliente->nome, 20) }}
                                                @else
                                                    <span class="text-muted">Sem cliente</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->quantidade }}</td>
                                            <td>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                            <td>
                                                <a href="{{ route('vendas.show', $item->venda) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Ver Venda">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($produto->itensVenda->count() > 10)
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    Mostrando 10 de {{ $produto->itensVenda->count() }} vendas
                                </small>
                            </div>
                        @endif

                        <!-- Resumo -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Vendido</h6>
                                        <h4 class="text-primary">{{ $produto->itensVenda->sum('quantidade') }}</h4>
                                        <small class="text-muted">unidades</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Vendas</h6>
                                        <h4 class="text-info">{{ $produto->itensVenda->count() }}</h4>
                                        <small class="text-muted">transações</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Receita Total</h6>
                                        <h4 class="text-success">
                                            R$ {{ number_format($produto->itensVenda->sum('subtotal'), 2, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                            <h6 class="text-muted">Nenhuma venda realizada</h6>
                            <p class="text-muted mb-3">Este produto ainda não foi vendido.</p>
                            <a href="{{ route('vendas.create') }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Nova Venda
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
