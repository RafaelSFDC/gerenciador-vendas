@extends('app')

@section('title', 'Relatórios de Vendas - DC Tecnologia')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                Relatórios de Vendas
            </h1>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Relatório de Vendas por Período -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Relatório de Vendas por Período
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Gere um relatório detalhado das vendas realizadas em um período específico, 
                    com opções de filtro por cliente, forma de pagamento e status.
                </p>
                
                <form action="{{ route('relatorios.vendas') }}" method="POST" target="_blank">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="data_inicio" class="form-label">Data Início <span class="text-danger">*</span></label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" 
                                   value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-6">
                            <label for="data_fim" class="form-label">Data Fim <span class="text-danger">*</span></label>
                            <input type="date" name="data_fim" id="data_fim" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente (Opcional)</label>
                        <select name="cliente_id" id="cliente_id" class="form-select">
                            <option value="">Todos os clientes</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="forma_pagamento_id" class="form-label">Forma de Pagamento (Opcional)</label>
                        <select name="forma_pagamento_id" id="forma_pagamento_id" class="form-select">
                            <option value="">Todas as formas</option>
                            @foreach($formasPagamento as $forma)
                                <option value="{{ $forma->id }}">{{ $forma->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status (Opcional)</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Todos os status</option>
                            <option value="pendente">Pendente</option>
                            <option value="paga">Paga</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf me-1"></i>
                            Gerar Relatório PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Relatório de Parcelas em Aberto -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Relatório de Parcelas em Aberto
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Visualize todas as parcelas pendentes, incluindo parcelas vencidas e 
                    aquelas que estão próximas do vencimento.
                </p>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Informações incluídas:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Parcelas vencidas com dias de atraso</li>
                        <li>Parcelas a vencer nos próximos dias</li>
                        <li>Valores totais por categoria</li>
                        <li>Detalhamento por cliente e venda</li>
                    </ul>
                </div>

                <div class="d-grid">
                    <a href="{{ route('relatorios.parcelas-aberto') }}" class="btn btn-warning" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i>
                        Gerar Relatório PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Relatórios Rápidos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Relatórios Rápidos
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <form action="{{ route('relatorios.vendas') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="data_inicio" value="{{ date('Y-m-d') }}">
                            <input type="hidden" name="data_fim" value="{{ date('Y-m-d') }}">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-calendar-day mb-2 d-block"></i>
                                Vendas de Hoje
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <form action="{{ route('relatorios.vendas') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="data_inicio" value="{{ date('Y-m-01') }}">
                            <input type="hidden" name="data_fim" value="{{ date('Y-m-d') }}">
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="fas fa-calendar-alt mb-2 d-block"></i>
                                Vendas do Mês
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <form action="{{ route('relatorios.vendas') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="data_inicio" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                            <input type="hidden" name="data_fim" value="{{ date('Y-m-d') }}">
                            <button type="submit" class="btn btn-outline-info w-100">
                                <i class="fas fa-calendar-week mb-2 d-block"></i>
                                Últimos 7 Dias
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <form action="{{ route('relatorios.vendas') }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="data_inicio" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                            <input type="hidden" name="data_fim" value="{{ date('Y-m-d') }}">
                            <button type="submit" class="btn btn-outline-warning w-100">
                                <i class="fas fa-calendar-times mb-2 d-block"></i>
                                Últimos 30 Dias
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dicas e Informações -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Dicas para Relatórios
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-line me-1"></i> Relatório de Vendas</h6>
                        <ul class="small">
                            <li>Use filtros para análises específicas</li>
                            <li>Períodos menores geram relatórios mais detalhados</li>
                            <li>Inclui resumo executivo com estatísticas</li>
                            <li>Mostra detalhamento dos itens vendidos</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle me-1"></i> Parcelas em Aberto</h6>
                        <ul class="small">
                            <li>Identifica parcelas vencidas com urgência</li>
                            <li>Mostra parcelas próximas do vencimento</li>
                            <li>Calcula dias de atraso automaticamente</li>
                            <li>Essencial para controle de fluxo de caixa</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Validação de datas
document.addEventListener('DOMContentLoaded', function() {
    const dataInicio = document.getElementById('data_inicio');
    const dataFim = document.getElementById('data_fim');
    
    function validarDatas() {
        if (dataInicio.value && dataFim.value) {
            if (dataInicio.value > dataFim.value) {
                dataFim.value = dataInicio.value;
            }
        }
    }
    
    dataInicio.addEventListener('change', validarDatas);
    dataFim.addEventListener('change', validarDatas);
});
</script>
@endpush
