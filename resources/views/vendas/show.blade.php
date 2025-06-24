@extends('app')

@section('title', 'Venda #' . $venda->id . ' - DC Tecnologia')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-eye me-2"></i>
                Venda #{{ $venda->id }}
            </h1>
            <div>
                <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>
                    Editar
                </a>
                <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informações da Venda -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informações da Venda
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>ID:</strong></div>
                    <div class="col-sm-8">#{{ $venda->id }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Data:</strong></div>
                    <div class="col-sm-8">{{ $venda->data_venda->format('d/m/Y') }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Vendedor:</strong></div>
                    <div class="col-sm-8">{{ $venda->user->name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Cliente:</strong></div>
                    <div class="col-sm-8">
                        @if($venda->cliente)
                            {{ $venda->cliente->nome }}<br>
                            <small class="text-muted">{{ $venda->cliente->email }}</small>
                        @else
                            <span class="text-muted">Cliente não informado</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Forma de Pagamento:</strong></div>
                    <div class="col-sm-8">{{ $venda->formaPagamento->nome }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Status:</strong></div>
                    <div class="col-sm-8">
                        @if($venda->status === 'pendente')
                            <span class="badge bg-warning">Pendente</span>
                        @elseif($venda->status === 'paga')
                            <span class="badge bg-success">Paga</span>
                        @else
                            <span class="badge bg-danger">Cancelada</span>
                        @endif
                    </div>
                </div>

                @if($venda->observacoes)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Observações:</strong></div>
                        <div class="col-sm-8">{{ $venda->observacoes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Resumo Financeiro -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Resumo Financeiro
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Valor Total:</strong></div>
                    <div class="col-sm-6">
                        <h4 class="text-success mb-0">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</h4>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Número de Parcelas:</strong></div>
                    <div class="col-sm-6">
                        <span class="badge bg-info">{{ $venda->numero_parcelas }}x</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Valor por Parcela:</strong></div>
                    <div class="col-sm-6">
                        R$ {{ number_format($venda->valor_total / $venda->numero_parcelas, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Itens da Venda -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-shopping-basket me-2"></i>
            Itens da Venda ({{ $venda->itens->count() }} {{ $venda->itens->count() == 1 ? 'item' : 'itens' }})
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venda->itens as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->produto->nome }}</strong>
                                @if($item->produto->descricao)
                                    <br>
                                    <small class="text-muted">{{ $item->produto->descricao }}</small>
                                @endif
                            </td>
                            <td>{{ $item->quantidade }}</td>
                            <td>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                            <td><strong>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="3">Total Geral:</th>
                        <th>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Parcelas -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-credit-card me-2"></i>
            Parcelas ({{ $venda->parcelas->count() }} {{ $venda->parcelas->count() == 1 ? 'parcela' : 'parcelas' }})
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Parcela</th>
                        <th>Valor</th>
                        <th>Data Vencimento</th>
                        <th>Data Pagamento</th>
                        <th>Status</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venda->parcelas as $parcela)
                        <tr class="{{ $parcela->data_vencimento < today() && $parcela->status === 'pendente' ? 'table-danger' : '' }}
                                  {{ $parcela->data_vencimento == today() && $parcela->status === 'pendente' ? 'table-warning' : '' }}">
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
                                <div class="d-flex align-items-center gap-2">
                                    @if($parcela->status === 'pendente')
                                        <span class="badge bg-warning">Pendente</span>
                                        <form action="{{ route('parcelas.marcar-paga', $parcela) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Marcar esta parcela como paga?')"
                                                    title="Marcar como paga">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @elseif($parcela->status === 'paga')
                                        <span class="badge bg-success">Paga</span>
                                        <form action="{{ route('parcelas.marcar-pendente', $parcela) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-warning btn-sm"
                                                    onclick="return confirm('Desfazer o pagamento desta parcela?')"
                                                    title="Desfazer pagamento">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-danger">Vencida</span>
                                        <form action="{{ route('parcelas.marcar-paga', $parcela) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Marcar esta parcela como paga?')"
                                                    title="Marcar como paga">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($parcela->customizada)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-lock me-1"></i>Customizada
                                    </span>
                                    @if($parcela->valor_original)
                                        <br><small class="text-muted">
                                            Original: R$ {{ number_format($parcela->valor_original, 2, ',', '.') }}
                                        </small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Automática</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ações -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <div>
                <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-1"></i>
                    Listar Vendas
                </a>
            </div>

            <div>
                <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>
                    Editar Venda
                </a>

                <a href="{{ route('relatorios.venda-pdf', $venda) }}" class="btn btn-success me-2" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>
                    Gerar PDF
                </a>

                <button type="button" class="btn btn-danger" onclick="confirmarExclusao()">
                    <i class="fas fa-trash me-1"></i>
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form de exclusão (oculto) -->
<form id="form-excluir" action="{{ route('vendas.destroy', $venda) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmarExclusao() {
    if (confirm('Tem certeza que deseja excluir esta venda? Esta ação não pode ser desfeita.')) {
        document.getElementById('form-excluir').submit();
    }
}
</script>
@endpush
