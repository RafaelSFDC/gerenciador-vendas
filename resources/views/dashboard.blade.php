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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Parcelas Vencendo
                </h5>
                <a href="{{ route('parcelas.index') }}" class="btn btn-sm btn-outline-primary">
                    Ver todas
                </a>
            </div>
            <div class="card-body">
                @if(isset($parcelasVencendo) && $parcelasVencendo->count() > 0)
                    @foreach($parcelasVencendo as $parcela)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2
                             {{ $parcela->status === 'vencida' ? 'bg-danger bg-opacity-10' :
                                ($parcela->data_vencimento == today() ? 'bg-warning bg-opacity-10' : 'bg-light') }} rounded">
                            <div>
                                <small class="text-muted">
                                    <a href="{{ route('vendas.show', $parcela->venda) }}" class="text-decoration-none">
                                        Venda #{{ $parcela->venda_id }}
                                    </a>
                                </small><br>
                                <strong>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</strong><br>
                                <small class="{{ $parcela->status === 'vencida' ? 'text-danger' :
                                              ($parcela->data_vencimento == today() ? 'text-warning' : 'text-muted') }}">
                                    {{ $parcela->data_vencimento->format('d/m/Y') }}
                                    @if($parcela->data_vencimento == today())
                                        (Hoje!)
                                    @elseif($parcela->data_vencimento < today())
                                        @php
                                            $dias = $parcela->data_vencimento->diffInDays(today());
                                        @endphp
                                        (há {{ $dias }} {{ $dias == 1 ? 'dia' : 'dias' }})
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                @if($parcela->status === 'vencida')
                                    <span class="badge bg-danger">Vencida</span>
                                @elseif($parcela->data_vencimento == today())
                                    <span class="badge bg-warning">Vence Hoje</span>
                                @else
                                    <span class="badge bg-info">Vencendo</span>
                                @endif

                                @if($parcela->status !== 'paga')
                                    <form action="{{ route('parcelas.marcar-paga', $parcela) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-xs"
                                                onclick="return confirm('Marcar como paga?')"
                                                title="Marcar como paga">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($parcelasVencendo->count() >= 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('parcelas.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus me-1"></i>
                                Ver mais parcelas
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">Nenhuma parcela vencendo</p>
                        <small class="text-muted">Todas as parcelas estão em dia!</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Parcelas Vencidas -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-circle me-2 text-danger"></i>
                    Parcelas Vencidas
                </h5>
                <a href="{{ route('parcelas.index', ['status' => 'vencidas']) }}" class="btn btn-sm btn-outline-danger">
                    Ver todas
                </a>
            </div>
            <div class="card-body">
                @if(isset($parcelasVencidas) && $parcelasVencidas->count() > 0)
                    @foreach($parcelasVencidas as $parcela)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-danger bg-opacity-10 rounded">
                            <div>
                                <small class="text-muted">
                                    <a href="{{ route('vendas.show', $parcela->venda) }}" class="text-decoration-none">
                                        Venda #{{ $parcela->venda_id }}
                                    </a>
                                </small><br>
                                <strong>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</strong><br>
                                <small class="text-danger">
                                    {{ $parcela->data_vencimento->format('d/m/Y') }}
                                    @php
                                        $dias = $parcela->data_vencimento->diffInDays(today());
                                    @endphp
                                    (há {{ $dias }} {{ $dias == 1 ? 'dia' : 'dias' }})
                                </small>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                <span class="badge bg-danger">Vencida</span>
                                <button type="button" class="btn btn-success btn-xs"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalPagamentoDashboard"
                                        data-parcela-id="{{ $parcela->id }}"
                                        data-parcela-numero="{{ $parcela->numero_parcela }}"
                                        data-venda-id="{{ $parcela->venda_id }}"
                                        data-valor="{{ number_format($parcela->valor, 2, ',', '.') }}"
                                        title="Marcar como paga">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    @if($parcelasVencidas->count() >= 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('parcelas.index', ['status' => 'vencidas']) }}" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-plus me-1"></i>
                                Ver mais parcelas vencidas
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">Nenhuma parcela vencida</p>
                        <small class="text-muted">Parabéns! Todas as parcelas estão em dia!</small>
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
                        <a href="{{ route('parcelas.index') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-credit-card fa-2x mb-2 d-block"></i>
                            Gerenciar Parcelas
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('relatorios.index') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-file-pdf fa-2x mb-2 d-block"></i>
                            Relatórios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Marcar como Pago no Dashboard -->
<div class="modal fade" id="modalPagamentoDashboard" tabindex="-1" aria-labelledby="modalPagamentoDashboardLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagamentoDashboardLabel">
                    <i class="fas fa-check me-2"></i>
                    Marcar Parcela como Paga
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPagamentoDashboard" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="mb-3">
                            <strong>Venda:</strong> #<span id="modalVendaIdDashboard"></span><br>
                            <strong>Parcela:</strong> <span id="modalParcelaNumeroDashboard"></span>ª<br>
                            <strong>Valor:</strong> R$ <span id="modalValorDashboard"></span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label for="dataPagamentoDashboard" class="form-label">Data do Pagamento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dataPagamentoDashboard" name="data_pagamento" required>
                        <div class="form-text">Por padrão, será utilizada a data atual.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>
                        Marcar como Paga
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalPagamentoDashboard = document.getElementById('modalPagamentoDashboard');
    const formPagamentoDashboard = document.getElementById('formPagamentoDashboard');
    const dataPagamentoDashboardInput = document.getElementById('dataPagamentoDashboard');

    modalPagamentoDashboard.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const parcelaId = button.getAttribute('data-parcela-id');
        const parcelaNumero = button.getAttribute('data-parcela-numero');
        const vendaId = button.getAttribute('data-venda-id');
        const valor = button.getAttribute('data-valor');

        // Atualizar informações no modal
        document.getElementById('modalVendaIdDashboard').textContent = vendaId;
        document.getElementById('modalParcelaNumeroDashboard').textContent = parcelaNumero;
        document.getElementById('modalValorDashboard').textContent = valor;

        // Configurar action do formulário
        formPagamentoDashboard.action = `/parcelas/${parcelaId}/marcar-paga`;

        // Definir data atual como padrão
        const hoje = new Date().toISOString().split('T')[0];
        dataPagamentoDashboardInput.value = hoje;
    });
});
</script>
@endpush

@endsection
