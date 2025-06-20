import 'bootstrap';
import '../css/app.css';

// Configuração global do jQuery
window.$ = window.jQuery = require('jquery');

// Funções globais para o sistema de vendas
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
        const subtotalInput = itemElement.querySelector('.subtotal-input');

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

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Configurar eventos existentes
    document.querySelectorAll('.item-venda').forEach(item => {
        VendasApp.configurarEventosItem(item);
    });

    // Evento para gerar parcelas
    const numeroParcelasInput = document.getElementById('numero_parcelas');
    if (numeroParcelasInput) {
        numeroParcelasInput.addEventListener('change', VendasApp.gerarParcelas);
    }

    // Calcular total inicial
    VendasApp.calcularTotal();
});
