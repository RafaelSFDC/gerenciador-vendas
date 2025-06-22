@extends('app')

@section('title', 'Gerenciar Parcelas - DC Tecnologia')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-credit-card me-2"></i>
                Gerenciar Parcelas
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

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total_pendentes'] }}</h4>
                        <small>Pendentes</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total_vencidas'] }}</h4>
                        <small>Vencidas</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['vencendo_hoje'] }}</h4>
                        <small>Vencendo Hoje</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-day fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['vencendo_7_dias'] }}</h4>
                        <small>Próximos 7 dias</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-week fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="{{ route('parcelas.index', ['status' => 'todas']) }}" 
                       class="btn {{ $status === 'todas' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Todas
                    </a>
                    <a href="{{ route('parcelas.index', ['status' => 'pendentes']) }}" 
                       class="btn {{ $status === 'pendentes' ? 'btn-warning' : 'btn-outline-warning' }}">
                        Pendentes
                    </a>
                    <a href="{{ route('parcelas.index', ['status' => 'vencidas']) }}" 
                       class="btn {{ $status === 'vencidas' ? 'btn-danger' : 'btn-outline-danger' }}">
                        Vencidas
                    </a>
                    <a href="{{ route('parcelas.index', ['status' => 'vencendo']) }}" 
                       class="btn {{ $status === 'vencendo' ? 'btn-info' : 'btn-outline-info' }}">
                        Vencendo (7 dias)
                    </a>
                    <a href="{{ route('parcelas.index', ['status' => 'pagas']) }}" 
                       class="btn {{ $status === 'pagas' ? 'btn-success' : 'btn-outline-success' }}">
                        Pagas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Parcelas -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>
            Parcelas
            @if($status !== 'todas')
                - {{ ucfirst($status) }}
            @endif
        </h5>
    </div>
    <div class="card-body">
        @if($parcelas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Venda</th>
                            <th>Cliente</th>
                            <th>Parcela</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parcelas as $parcela)
                            <tr class="{{ $parcela->data_vencimento < today() && $parcela->status === 'pendente' ? 'table-danger' : '' }}
                                      {{ $parcela->data_vencimento == today() && $parcela->status === 'pendente' ? 'table-warning' : '' }}">
                                <td>
                                    <a href="{{ route('vendas.show', $parcela->venda) }}" class="text-decoration-none">
                                        #{{ $parcela->venda_id }}
                                    </a>
                                </td>
                                <td>
                                    @if($parcela->venda->cliente)
                                        {{ $parcela->venda->cliente->nome }}
                                    @else
                                        <em class="text-muted">Não informado</em>
                                    @endif
                                </td>
                                <td>{{ $parcela->numero_parcela }}ª</td>
                                <td>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                                <td>
                                    {{ $parcela->data_vencimento->format('d/m/Y') }}
                                    @if($parcela->data_vencimento < today() && $parcela->status === 'pendente')
                                        <br><small class="text-danger">
                                            {{ $parcela->data_vencimento->diffForHumans() }}
                                        </small>
                                    @elseif($parcela->data_vencimento == today() && $parcela->status === 'pendente')
                                        <br><small class="text-warning">Vence hoje!</small>
                                    @endif
                                </td>
                                <td>
                                    @if($parcela->data_pagamento)
                                        {{ $parcela->data_pagamento->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($parcela->status === 'pendente')
                                        <span class="badge bg-warning">Pendente</span>
                                    @elseif($parcela->status === 'paga')
                                        <span class="badge bg-success">Paga</span>
                                    @else
                                        <span class="badge bg-danger">Vencida</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($parcela->status !== 'paga')
                                            <form action="{{ route('parcelas.marcar-paga', $parcela) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                        onclick="return confirm('Marcar esta parcela como paga?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('parcelas.marcar-pendente', $parcela) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-warning btn-sm" 
                                                        onclick="return confirm('Desfazer o pagamento desta parcela?')">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('vendas.show', $parcela->venda) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <div class="d-flex justify-content-center">
                {{ $parcelas->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma parcela encontrada</h5>
                <p class="text-muted">
                    @if($status === 'todas')
                        Não há parcelas cadastradas no sistema.
                    @else
                        Não há parcelas com o status "{{ $status }}".
                    @endif
                </p>
                <a href="{{ route('vendas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Criar Nova Venda
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
