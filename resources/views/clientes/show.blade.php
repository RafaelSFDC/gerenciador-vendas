@extends('app')

@section('title', 'Cliente: ' . $cliente->nome)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-user me-2"></i>
            {{ $cliente->nome }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>
                Editar
            </a>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações do Cliente -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações do Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Nome:</strong></div>
                        <div class="col-sm-8">{{ $cliente->nome }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8">
                            @if($cliente->email)
                                <a href="mailto:{{ $cliente->email }}" class="text-decoration-none">
                                    {{ $cliente->email }}
                                </a>
                            @else
                                <span class="text-muted">Não informado</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Telefone:</strong></div>
                        <div class="col-sm-8">
                            @if($cliente->telefone)
                                <a href="tel:{{ $cliente->telefone }}" class="text-decoration-none">
                                    {{ $cliente->telefone }}
                                </a>
                            @else
                                <span class="text-muted">Não informado</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>CPF/CNPJ:</strong></div>
                        <div class="col-sm-8">
                            {{ $cliente->cpf_cnpj ?? 'Não informado' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Endereço:</strong></div>
                        <div class="col-sm-8">
                            @if($cliente->endereco)
                                {{ $cliente->endereco }}
                            @else
                                <span class="text-muted">Não informado</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4"><strong>Cadastrado em:</strong></div>
                        <div class="col-sm-8">{{ $cliente->created_at->format('d/m/Y H:i') }}</div>
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
                    @if($cliente->vendas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cliente->vendas->take(10) as $venda)
                                        <tr>
                                            <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
                                            <td>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                                            <td>
                                                @if($venda->status === 'pendente')
                                                    <span class="badge bg-warning">Pendente</span>
                                                @elseif($venda->status === 'paga')
                                                    <span class="badge bg-success">Paga</span>
                                                @else
                                                    <span class="badge bg-danger">Cancelada</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('vendas.show', $venda) }}" 
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

                        @if($cliente->vendas->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('vendas.index', ['cliente_id' => $cliente->id]) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    Ver todas as vendas ({{ $cliente->vendas->count() }})
                                </a>
                            </div>
                        @endif

                        <!-- Resumo -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total de Vendas</h6>
                                        <h4 class="text-primary">{{ $cliente->vendas->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Valor Total</h6>
                                        <h4 class="text-success">
                                            R$ {{ number_format($cliente->vendas->sum('valor_total'), 2, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                            <h6 class="text-muted">Nenhuma venda realizada</h6>
                            <p class="text-muted mb-3">Este cliente ainda não possui vendas cadastradas.</p>
                            <a href="{{ route('vendas.create', ['cliente_id' => $cliente->id]) }}" 
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
