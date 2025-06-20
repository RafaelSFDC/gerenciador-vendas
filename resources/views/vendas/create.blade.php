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

// Definir VendasApp inline caso não esteja carregado
if (typeof window.VendasApp === 'undefined') {
    console.log('Definindo VendasApp inline...');

    window.VendasApp = {
        // Adicionar item à venda
        adicionarItem: function() {
            const container = document.getElementById('itens-container');
            const index = container.children.length;

            const itemHtml = `
                <div class="row mb-3 item-venda" data-index="${index}">
                    <div class="col-md-4">
                        <select name="itens[${index}][produto_id]" class="form-select produto-select" required>
                            <option value="">Selecione um produto</option>
                            ${window.produtos.map(produto =>
                                `<option value="${produto.id}" data-preco="${produto.preco}">${produto.nome} - R$ ${produto.preco}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="itens[${index}][quantidade]" class="form-control quantidade-input"
                               placeholder="Qtd" min="1" step="1" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="itens[${index}][preco_unitario]" class="form-control preco-input"
                               placeholder="Preço" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="itens[${index}][subtotal]" class="form-control subtotal-input"
                               placeholder="Subtotal" readonly>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm" onclick="VendasApp.removerItem(this)">
                            <i class="fas fa-trash"></i> Remover
                        </button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', itemHtml);
            this.configurarEventosItem(container.lastElementChild);
            this.atualizarVisibilidadeItens();
            this.atualizarContadorItens();
        },

        // Remover item da venda
        removerItem: function(button) {
            button.closest('.item-venda').remove();
            this.calcularTotal();
            this.reindexarItens();
            this.atualizarVisibilidadeItens();
            this.atualizarContadorItens();
        },

        // Configurar eventos para um item
        configurarEventosItem: function(itemElement) {
            const produtoSelect = itemElement.querySelector('.produto-select');
            const quantidadeInput = itemElement.querySelector('.quantidade-input');
            const precoInput = itemElement.querySelector('.preco-input');

            // Quando selecionar produto, preencher preço
            produtoSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const preco = selectedOption.dataset.preco || 0;
                precoInput.value = preco;
                VendasApp.calcularSubtotal(itemElement);
            });

            // Quando alterar quantidade, recalcular subtotal
            quantidadeInput.addEventListener('input', function() {
                VendasApp.calcularSubtotal(itemElement);
            });

            // Quando alterar preço, recalcular subtotal
            precoInput.addEventListener('input', function() {
                VendasApp.calcularSubtotal(itemElement);
            });
        },

        // Calcular subtotal de um item
        calcularSubtotal: function(itemElement) {
            const quantidade = parseFloat(itemElement.querySelector('.quantidade-input').value) || 0;
            const preco = parseFloat(itemElement.querySelector('.preco-input').value) || 0;
            const subtotal = quantidade * preco;

            itemElement.querySelector('.subtotal-input').value = subtotal.toFixed(2);
            this.calcularTotal();
        },

        // Calcular total da venda
        calcularTotal: function() {
            let total = 0;
            document.querySelectorAll('.subtotal-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            const totalElement = document.getElementById('total-venda');
            if (totalElement) {
                totalElement.textContent = 'R$ ' + total.toFixed(2);
            }

            const totalInput = document.getElementById('valor_total');
            if (totalInput) {
                totalInput.value = total.toFixed(2);
            }

            this.atualizarParcelas(total);
        },

        // Reindexar itens após remoção
        reindexarItens: function() {
            document.querySelectorAll('.item-venda').forEach((item, index) => {
                item.dataset.index = index;
                item.querySelectorAll('input, select').forEach(input => {
                    const name = input.name;
                    if (name && name.includes('itens[')) {
                        input.name = name.replace(/itens\[\d+\]/, `itens[${index}]`);
                    }
                });
            });
        },

        // Gerar parcelas
        gerarParcelas: function() {
            const numeroParcelas = parseInt(document.getElementById('numero_parcelas').value) || 1;
            const total = parseFloat(document.getElementById('valor_total').value) || 0;
            const valorParcela = total / numeroParcelas;

            const container = document.getElementById('parcelas-container');
            container.innerHTML = '';

            for (let i = 0; i < numeroParcelas; i++) {
                const dataVencimento = new Date();
                dataVencimento.setMonth(dataVencimento.getMonth() + i + 1);

                const parcelaHtml = `
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label class="form-label">Parcela ${i + 1}</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="parcelas[${i}][data_vencimento]"
                                   class="form-control" value="${dataVencimento.toISOString().split('T')[0]}" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="parcelas[${i}][valor]"
                                   class="form-control" value="${valorParcela.toFixed(2)}"
                                   min="0" step="0.01" required>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', parcelaHtml);
            }
            this.atualizarVisibilidadeParcelas();
        },

        // Atualizar parcelas quando total mudar
        atualizarParcelas: function(total) {
            const numeroParcelas = parseInt(document.getElementById('numero_parcelas')?.value) || 1;
            const valorParcela = total / numeroParcelas;

            document.querySelectorAll('input[name*="[valor]"]').forEach(input => {
                input.value = valorParcela.toFixed(2);
            });
        },

        // Atualizar visibilidade dos elementos de itens
        atualizarVisibilidadeItens: function() {
            const itens = document.querySelectorAll('.item-venda');
            const semItens = document.getElementById('sem-itens');

            if (itens.length > 0) {
                semItens.style.display = 'none';
            } else {
                semItens.style.display = 'block';
            }
        },

        // Atualizar contador de itens
        atualizarContadorItens: function() {
            const itens = document.querySelectorAll('.item-venda');
            const totalItensElement = document.getElementById('total-itens');

            if (totalItensElement) {
                totalItensElement.textContent = itens.length;
            }
        },

        // Atualizar visibilidade dos elementos de parcelas
        atualizarVisibilidadeParcelas: function() {
            const parcelas = document.querySelectorAll('input[name*="[valor]"]');
            const semParcelas = document.getElementById('sem-parcelas');

            if (parcelas.length > 0) {
                semParcelas.style.display = 'none';
            } else {
                semParcelas.style.display = 'block';
            }
        }
    };
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    console.log('VendasApp carregado com sucesso');
    console.log('Produtos disponíveis:', window.produtos);

    // Garantir que os elementos de "sem itens" e "sem parcelas" estejam visíveis inicialmente
    VendasApp.atualizarVisibilidadeItens();
    VendasApp.atualizarVisibilidadeParcelas();
    VendasApp.atualizarContadorItens();

    // Evento para gerar parcelas
    const numeroParcelasInput = document.getElementById('numero_parcelas');
    if (numeroParcelasInput) {
        numeroParcelasInput.addEventListener('change', VendasApp.gerarParcelas);
    }
});

// Validação do formulário
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endpush
