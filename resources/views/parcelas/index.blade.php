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
                                        @php
                                            $dias = $parcela->data_vencimento->diffInDays(today());
                                        @endphp
                                        <br><small class="text-danger">
                                            Venceu há {{ $dias }} {{ $dias == 1 ? 'dia' : 'dias' }}
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
                                            <button type="button" class="btn btn-success btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalPagamento"
                                                    data-parcela-id="{{ $parcela->id }}"
                                                    data-parcela-numero="{{ $parcela->numero_parcela }}"
                                                    data-venda-id="{{ $parcela->venda_id }}"
                                                    data-valor="{{ number_format($parcela->valor, 2, ',', '.') }}">
                                                <i class="fas fa-check"></i>
                                            </button>
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

<!-- Modal para Marcar como Pago -->
<div class="modal fade" id="modalPagamento" tabindex="-1" aria-labelledby="modalPagamentoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagamentoLabel">
                    <i class="fas fa-check me-2"></i>
                    Marcar Parcela como Paga
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPagamento" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="mb-3">
                            <strong>Venda:</strong> #<span id="modalVendaId"></span><br>
                            <strong>Parcela:</strong> <span id="modalParcelaNumero"></span>ª<br>
                            <strong>Valor:</strong> R$ <span id="modalValor"></span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label for="dataPagamento" class="form-label">Data do Pagamento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dataPagamento" name="data_pagamento" required>
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
    const modalPagamento = document.getElementById('modalPagamento');
    const formPagamento = document.getElementById('formPagamento');
    const dataPagamentoInput = document.getElementById('dataPagamento');

    modalPagamento.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const parcelaId = button.getAttribute('data-parcela-id');
        const parcelaNumero = button.getAttribute('data-parcela-numero');
        const vendaId = button.getAttribute('data-venda-id');
        const valor = button.getAttribute('data-valor');

        // Atualizar informações no modal
        document.getElementById('modalVendaId').textContent = vendaId;
        document.getElementById('modalParcelaNumero').textContent = parcelaNumero;
        document.getElementById('modalValor').textContent = valor;

        // Configurar action do formulário
        formPagamento.action = `/parcelas/${parcelaId}/marcar-paga`;

        // Definir data atual como padrão
        const hoje = new Date().toISOString().split('T')[0];
        dataPagamentoInput.value = hoje;
    });
});
</script>
@endpush

@endsection
