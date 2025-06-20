@extends('app')

@section('title', 'Nova Venda - DC Tecnologia')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-plus me-2"></i>
                Nova Venda
            </h1>
            <div>
                <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('vendas.store') }}" method="POST" id="form-venda">
    @csrf
    
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
                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }} - {{ $cliente->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="data_venda" class="form-label">Data da Venda <span class="text-danger">*</span></label>
                        <input type="date" name="data_venda" id="data_venda" class="form-control" 
                               value="{{ old('data_venda', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="forma_pagamento_id" class="form-label">Forma de Pagamento <span class="text-danger">*</span></label>
                        <select name="forma_pagamento_id" id="forma_pagamento_id" class="form-select" required>
                            <option value="">Selecione uma forma de pagamento</option>
                            @foreach($formasPagamento as $forma)
                                <option value="{{ $forma->id }}" {{ old('forma_pagamento_id') == $forma->id ? 'selected' : '' }}>
                                    {{ $forma->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="numero_parcelas" class="form-label">Número de Parcelas <span class="text-danger">*</span></label>
                        <input type="number" name="numero_parcelas" id="numero_parcelas" class="form-control" 
                               value="{{ old('numero_parcelas', 1) }}" min="1" max="12" required>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea name="observacoes" id="observacoes" class="form-control" rows="3" 
                                  placeholder="Observações sobre a venda...">{{ old('observacoes') }}</textarea>
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
                            <h4 id="total-itens">0</h4>
                        </div>
                        <div class="col-6">
                            <h6>Valor Total:</h6>
                            <h4 id="total-venda" class="text-success">R$ 0,00</h4>
                        </div>
                    </div>
                    <hr>
                    <div id="resumo-parcelas">
                        <h6>Parcelas:</h6>
                        <p class="text-muted">Configure os itens e parcelas para ver o resumo</p>
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
                <!-- Itens serão adicionados aqui via JavaScript -->
            </div>
            
            <div class="text-center py-3" id="sem-itens">
                <i class="fas fa-shopping-basket fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">Nenhum item adicionado</p>
                <small class="text-muted">Clique em "Adicionar Item" para começar</small>
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
                <!-- Parcelas serão geradas aqui via JavaScript -->
            </div>
            
            <div class="text-center py-3" id="sem-parcelas">
                <i class="fas fa-credit-card fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">Nenhuma parcela configurada</p>
                <small class="text-muted">Configure o número de parcelas e clique em "Gerar Parcelas"</small>
            </div>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </a>
                
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Salvar Venda
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Campo oculto para valor total -->
    <input type="hidden" name="valor_total" id="valor_total" value="0">
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

// Adicionar primeiro item automaticamente
document.addEventListener('DOMContentLoaded', function() {
    VendasApp.adicionarItem();
    VendasApp.gerarParcelas();
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
