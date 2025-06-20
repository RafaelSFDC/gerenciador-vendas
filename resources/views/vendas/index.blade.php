@extends('app')

@section('title', 'Vendas - DC Tecnologia')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-shopping-cart me-2"></i>
                Vendas
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

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>
            Filtros
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('vendas.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-select">
                        <option value="">Todos os clientes</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="forma_pagamento_id" class="form-label">Forma de Pagamento</label>
                    <select name="forma_pagamento_id" id="forma_pagamento_id" class="form-select">
                        <option value="">Todas as formas</option>
                        @foreach($formasPagamento as $forma)
                            <option value="{{ $forma->id }}"
                                {{ request('forma_pagamento_id') == $forma->id ? 'selected' : '' }}>
                                {{ $forma->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="paga" {{ request('status') == 'paga' ? 'selected' : '' }}>Paga</option>
                        <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="form-control"
                           value="{{ request('data_inicio') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" id="data_fim" class="form-control"
                           value="{{ request('data_fim') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Vendas -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>
            Lista de Vendas ({{ $vendas->total() }} {{ $vendas->total() == 1 ? 'registro' : 'registros' }})
        </h5>
    </div>
    <div class="card-body">
        @if($vendas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Forma Pagamento</th>
                            <th>Valor Total</th>
                            <th>Parcelas</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendas as $venda)
                            <tr>
                                <td>
                                    <strong>#{{ $venda->id }}</strong>
                                </td>
                                <td>
                                    @if($venda->cliente)
                                        {{ $venda->cliente->nome }}
                                        <br>
                                        <small class="text-muted">{{ $venda->cliente->email }}</small>
                                    @else
                                        <span class="text-muted">Cliente não informado</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $venda->data_venda->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $venda->created_at->format('H:i') }}</small>
                                </td>
                                <td>{{ $venda->formaPagamento->nome }}</td>
                                <td>
                                    <strong>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $venda->numero_parcelas }}x</span>
                                </td>
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
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('vendas.show', $venda) }}"
                                           class="btn btn-sm btn-outline-primary" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('relatorios.venda-pdf', $venda) }}"
                                           class="btn btn-sm btn-outline-success" title="PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('vendas.edit', $venda) }}"
                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                title="Excluir" onclick="confirmarExclusao({{ $venda->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Form de exclusão (oculto) -->
                                    <form id="form-excluir-{{ $venda->id }}"
                                          action="{{ route('vendas.destroy', $venda) }}"
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center">
                {{ $vendas->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma venda encontrada</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['cliente_id', 'forma_pagamento_id', 'status', 'data_inicio', 'data_fim']))
                        Tente ajustar os filtros ou
                        <a href="{{ route('vendas.index') }}">remover todos os filtros</a>.
                    @else
                        Comece criando sua primeira venda.
                    @endif
                </p>
                <a href="{{ route('vendas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Nova Venda
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarExclusao(vendaId) {
    if (confirm('Tem certeza que deseja excluir esta venda? Esta ação não pode ser desfeita.')) {
        document.getElementById('form-excluir-' + vendaId).submit();
    }
}
</script>
@endpush
