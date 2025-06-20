@extends('app')

@section('title', 'Dashboard - DC Tecnologia')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </h1>
            <div>
                <a href="{{ route('vendas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Nova Venda
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Estatísticas -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Vendas Hoje</h6>
                        <h3 class="mb-0">{{ $vendasHoje ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Faturamento Hoje</h6>
                        <h3 class="mb-0">R$ {{ number_format($faturamentoHoje ?? 0, 2, ',', '.') }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Vendas Mês</h6>
                        <h3 class="mb-0">{{ $vendasMes ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Faturamento Mês</h6>
                        <h3 class="mb-0">R$ {{ number_format($faturamentoMes ?? 0, 2, ',', '.') }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Vendas Recentes -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Vendas Recentes
                </h5>
            </div>
            <div class="card-body">
                @if(isset($vendasRecentes) && $vendasRecentes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendasRecentes as $venda)
                                    <tr>
                                        <td>#{{ $venda->id }}</td>
                                        <td>{{ $venda->cliente->nome ?? 'Cliente não informado' }}</td>
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
                                            <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma venda encontrada</p>
                        <a href="{{ route('vendas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Criar primeira venda
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Parcelas Vencendo -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Parcelas Vencendo
                </h5>
            </div>
            <div class="card-body">
                @if(isset($parcelasVencendo) && $parcelasVencendo->count() > 0)
                    @foreach($parcelasVencendo as $parcela)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <small class="text-muted">Venda #{{ $parcela->venda_id }}</small><br>
                                <strong>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</strong><br>
                                <small class="text-danger">{{ $parcela->data_vencimento->format('d/m/Y') }}</small>
                            </div>
                            <div>
                                @if($parcela->status === 'vencida')
                                    <span class="badge bg-danger">Vencida</span>
                                @else
                                    <span class="badge bg-warning">Vencendo</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">Nenhuma parcela vencendo</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ações Rápidas -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('vendas.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-plus fa-2x mb-2 d-block"></i>
                            Nova Venda
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('vendas.index') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-list fa-2x mb-2 d-block"></i>
                            Listar Vendas
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('vendas.index', ['status' => 'pendente']) }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-clock fa-2x mb-2 d-block"></i>
                            Vendas Pendentes
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-file-pdf fa-2x mb-2 d-block"></i>
                            Relatórios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
