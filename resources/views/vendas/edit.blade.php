@extends('app')

@section('title', 'Editar Venda #' . $venda->id . ' - DC Tecnologia')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-edit me-2"></i>
                Editar Venda #{{ $venda->id }}
            </h1>
            <div>
                <a href="{{ route('vendas.show', $venda) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('vendas.update', $venda) }}" method="POST" id="form-venda">
    @csrf
    @method('PUT')

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
                    <div class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente (Opcional)</label>
                        <select name="cliente_id" id="cliente_id" class="form-select">
                            <option value="">Selecione um cliente (opcional)</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}"
                                    {{ (old('cliente_id', $venda->cliente_id) == $cliente->id) ? 'selected' : '' }}>
                                    {{ $cliente->nome }} - {{ $cliente->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="data_venda" class="form-label">Data da Venda <span class="text-danger">*</span></label>
                        <input type="date" name="data_venda" id="data_venda" class="form-control"
                               value="{{ old('data_venda', $venda->data_venda->format('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="forma_pagamento_id" class="form-label">Forma de Pagamento <span class="text-danger">*</span></label>
                        <select name="forma_pagamento_id" id="forma_pagamento_id" class="form-select" required>
                            <option value="">Selecione uma forma de pagamento</option>
                            @foreach($formasPagamento as $forma)
                                <option value="{{ $forma->id }}"
                                    {{ (old('forma_pagamento_id', $venda->forma_pagamento_id) == $forma->id) ? 'selected' : '' }}>
                                    {{ $forma->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="numero_parcelas" class="form-label">Número de Parcelas <span class="text-danger">*</span></label>
                        <input type="number" name="numero_parcelas" id="numero_parcelas" class="form-control"
                               value="{{ old('numero_parcelas', $venda->numero_parcelas) }}" min="1" max="120" required>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea name="observacoes" id="observacoes" class="form-control" rows="3"
                                  placeholder="Observações sobre a venda...">{{ old('observacoes', $venda->observacoes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo da Venda -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Resumo da Venda
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6>Total de Itens:</h6>
                            <h4 id="total-itens">{{ $venda->itens->count() }}</h4>
                        </div>
                        <div class="col-6">
                            <h6>Valor Total:</h6>
                            <h4 id="total-venda" class="text-success">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div id="resumo-parcelas">
                        <h6>Parcelas:</h6>
                        <p class="text-muted">{{ $venda->numero_parcelas }}x de R$ {{ number_format($venda->valor_total / $venda->numero_parcelas, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Itens da Venda -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-basket me-2"></i>
                    Itens da Venda
                </h5>
                <button type="button" class="btn btn-success btn-sm" onclick="VendasApp.adicionarItem()">
                    <i class="fas fa-plus me-1"></i>
                    Adicionar Item
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="itens-container">
                @foreach($venda->itens as $index => $item)
                    <div class="row mb-3 item-venda" data-index="{{ $index }}">
                        <div class="col-md-4">
                            <select name="itens[{{ $index }}][produto_id]" class="form-select produto-select" required>
                                <option value="">Selecione um produto</option>
                                @foreach($produtos as $produto)
                                    <option value="{{ $produto->id }}" data-preco="{{ $produto->preco }}"
                                        {{ $item->produto_id == $produto->id ? 'selected' : '' }}>
                                        {{ $produto->nome }} - R$ {{ $produto->preco }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="itens[{{ $index }}][quantidade]" class="form-control quantidade-input"
                                   placeholder="Qtd" min="1" step="1" value="{{ $item->quantidade }}" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="itens[{{ $index }}][preco_unitario]" class="form-control preco-input"
                                   placeholder="Preço" min="0" step="0.01" value="{{ $item->preco_unitario }}" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="itens[{{ $index }}][subtotal]" class="form-control subtotal-input"
                                   placeholder="Subtotal" value="{{ $item->subtotal }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm" onclick="VendasApp.removerItem(this)">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Parcelas -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Parcelas
                </h5>
                <button type="button" class="btn btn-info btn-sm" onclick="VendasApp.gerarParcelas()">
                    <i class="fas fa-sync me-1"></i>
                    Gerar Parcelas
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="parcelas-container">
                @foreach($venda->parcelas as $index => $parcela)
                    <div class="row mb-2 parcela-row" data-index="{{ $index }}">
                        <div class="col-md-2">
                            <label class="form-label">Parcela {{ $index + 1 }}</label>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="parcelas[{{ $index }}][data_vencimento]"
                                   class="form-control" value="{{ $parcela->data_vencimento->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="parcelas[{{ $index }}][valor]"
                                   class="form-control parcela-valor" value="{{ $parcela->valor }}"
                                   min="0" step="0.01" required
                                   style="{{ $parcela->customizada ? 'background-color: #fff3cd; border-color: #ffc107;' : '' }}">
                        </div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input type="checkbox" name="parcelas[{{ $index }}][customizada]"
                                       class="form-check-input parcela-customizada"
                                       id="customizada_{{ $index }}" value="1"
                                       {{ $parcela->customizada ? 'checked' : '' }}
                                       onchange="VendasApp.toggleCustomizacao({{ $index }})">
                                <label class="form-check-label" for="customizada_{{ $index }}">
                                    <small>Customizar</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <span class="badge bg-secondary parcela-status" style="{{ $parcela->customizada ? 'display: inline-block;' : 'display: none;' }}">
                                <i class="fas fa-lock me-1"></i>Fixo
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('vendas.show', $venda) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </a>

                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Atualizar Venda
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Campo oculto para valor total -->
    <input type="hidden" name="valor_total" id="valor_total" value="{{ $venda->valor_total }}">
</form>
@endsection

@push('scripts')
<script>
// Disponibilizar produtos para JavaScript
window.produtos = @json($produtos->map(function($produto) {
    return [
        'id' => $produto->id,
        'nome' => $produto->nome,
        'preco' => $produto->preco
    ];
}));

// Configurar eventos existentes quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Configurar eventos para itens existentes
    document.querySelectorAll('.item-venda').forEach(item => {
        VendasApp.configurarEventosItem(item);
    });

    // Configurar eventos para parcelas customizadas existentes
    document.querySelectorAll('.parcela-customizada').forEach(checkbox => {
        if (checkbox.checked) {
            const row = checkbox.closest('.parcela-row');
            const valorInput = row.querySelector('.parcela-valor');
            const status = row.querySelector('.parcela-status');

            // Aplicar estilo visual para parcelas customizadas
            valorInput.style.backgroundColor = '#fff3cd';
            valorInput.style.borderColor = '#ffc107';
            status.style.display = 'inline-block';

            // Adicionar evento para recalcular quando valor customizado mudar
            valorInput.addEventListener('input', function() {
                VendasApp.atualizarParcelas(parseFloat(document.getElementById('valor_total').value) || 0);
            });
        }
    });

    // Calcular total inicial
    VendasApp.calcularTotal();
});

// Validação do formulário
document.getElementById('form-venda').addEventListener('submit', function(e) {
    const itens = document.querySelectorAll('.item-venda');
    if (itens.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um item à venda.');
        return false;
    }

    const parcelas = document.querySelectorAll('input[name*="[valor]"]');
    if (parcelas.length === 0) {
        e.preventDefault();
        alert('Configure as parcelas da venda.');
        return false;
    }
});
</script>
@endpush
