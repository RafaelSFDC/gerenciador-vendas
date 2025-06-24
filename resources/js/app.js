import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/app.css';

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

        // Regenerar parcelas automaticamente quando o total mudar
        const parcelasExistentes = document.querySelectorAll('input[name*="[valor]"]');
        if (parcelasExistentes.length > 0 && total > 0) {
            this.atualizarParcelas(total);
        }
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

        const container = document.getElementById('parcelas-container');

        // Salvar parcelas customizadas existentes
        const parcelasCustomizadas = [];
        document.querySelectorAll('.parcela-row').forEach((row, index) => {
            const checkbox = row.querySelector('.parcela-customizada');
            const valorInput = row.querySelector('.parcela-valor');
            const dataInput = row.querySelector('input[type="date"]');

            if (checkbox && checkbox.checked) {
                parcelasCustomizadas[index] = {
                    valor: parseFloat(valorInput.value) || 0,
                    data: dataInput.value,
                    customizada: true
                };
            }
        });

        // Calcular valor das parcelas customizadas
        let valorCustomizado = 0;
        let parcelasNaoCustomizadas = 0;

        for (let i = 0; i < numeroParcelas; i++) {
            if (parcelasCustomizadas[i]) {
                valorCustomizado += parcelasCustomizadas[i].valor;
            } else {
                parcelasNaoCustomizadas++;
            }
        }

        // Calcular valor para parcelas não customizadas
        const valorRestante = total - valorCustomizado;
        const valorPorParcela = parcelasNaoCustomizadas > 0 ? valorRestante / parcelasNaoCustomizadas : 0;

        container.innerHTML = '';

        for (let i = 0; i < numeroParcelas; i++) {
            const dataVencimento = new Date();
            dataVencimento.setMonth(dataVencimento.getMonth() + i + 1);

            // Usar dados salvos se parcela era customizada, senão usar valores padrão
            const parcelaCustomizada = parcelasCustomizadas[i];
            const valor = parcelaCustomizada ? parcelaCustomizada.valor : valorPorParcela;
            const data = parcelaCustomizada ? parcelaCustomizada.data : dataVencimento.toISOString().split('T')[0];
            const customizada = parcelaCustomizada ? true : false;

            const parcelaHtml = `
                <div class="row mb-2 parcela-row" data-index="${i}">
                    <div class="col-md-2">
                        <label class="form-label">Parcela ${i + 1}</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="parcelas[${i}][data_vencimento]"
                               class="form-control" value="${data}" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="parcelas[${i}][valor]"
                               class="form-control parcela-valor" value="${valor.toFixed(2)}"
                               min="0" step="0.01" required
                               style="${customizada ? 'background-color: #fff3cd; border-color: #ffc107;' : ''}">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input type="checkbox" name="parcelas[${i}][customizada]"
                                   class="form-check-input parcela-customizada"
                                   id="customizada_${i}" value="1"
                                   ${customizada ? 'checked' : ''}
                                   onchange="VendasApp.toggleCustomizacao(${i})">
                            <label class="form-check-label" for="customizada_${i}">
                                <small>Customizar</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <span class="badge bg-secondary parcela-status" style="${customizada ? 'display: inline-block;' : 'display: none;'}">
                            <i class="fas fa-lock me-1"></i>Fixo
                        </span>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', parcelaHtml);
        }

        // Configurar eventos para parcelas customizadas
        document.querySelectorAll('.parcela-customizada').forEach(checkbox => {
            if (checkbox.checked) {
                const row = checkbox.closest('.parcela-row');
                const valorInput = row.querySelector('.parcela-valor');

                valorInput.addEventListener('input', () => {
                    this.atualizarParcelas(parseFloat(document.getElementById('valor_total').value) || 0);
                });
            }
        });

        this.atualizarVisibilidadeParcelas();
    },

    // Atualizar parcelas quando total mudar
    atualizarParcelas: function(total) {
        // Não fazer nada se não há parcelas
        const parcelas = document.querySelectorAll('.parcela-row');
        if (parcelas.length === 0) {
            return;
        }

        // Calcular valor das parcelas customizadas
        let valorCustomizado = 0;
        let parcelasNaoCustomizadas = 0;

        parcelas.forEach(row => {
            const checkbox = row.querySelector('.parcela-customizada');
            const valorInput = row.querySelector('.parcela-valor');

            if (checkbox && checkbox.checked) {
                valorCustomizado += parseFloat(valorInput.value) || 0;
            } else {
                parcelasNaoCustomizadas++;
            }
        });

        // Calcular valor para parcelas não customizadas
        const valorRestante = Math.max(0, total - valorCustomizado);
        const valorPorParcela = parcelasNaoCustomizadas > 0 ? valorRestante / parcelasNaoCustomizadas : 0;

        // Atualizar apenas parcelas não customizadas
        parcelas.forEach(row => {
            const checkbox = row.querySelector('.parcela-customizada');
            const valorInput = row.querySelector('.parcela-valor');

            if (checkbox && !checkbox.checked) {
                valorInput.value = valorPorParcela.toFixed(2);
            }
        });

        // Mostrar aviso se valor customizado excede o total
        if (valorCustomizado > total) {
            console.warn('Valor das parcelas customizadas excede o valor total da venda');
        }
    },

    // Toggle customização de parcela
    toggleCustomizacao: function(index) {
        const row = document.querySelector(`.parcela-row[data-index="${index}"]`);
        if (!row) return;

        const checkbox = row.querySelector('.parcela-customizada');
        const valorInput = row.querySelector('.parcela-valor');
        const status = row.querySelector('.parcela-status');

        if (checkbox.checked) {
            // Marcar como customizada
            valorInput.style.backgroundColor = '#fff3cd';
            valorInput.style.borderColor = '#ffc107';
            status.style.display = 'inline-block';

            // Remover listeners antigos para evitar duplicação
            const newInput = valorInput.cloneNode(true);
            valorInput.parentNode.replaceChild(newInput, valorInput);

            // Adicionar evento para recalcular quando valor customizado mudar
            newInput.addEventListener('input', () => {
                this.atualizarParcelas(parseFloat(document.getElementById('valor_total').value) || 0);
            });
        } else {
            // Remover customização
            valorInput.style.backgroundColor = '';
            valorInput.style.borderColor = '';
            status.style.display = 'none';

            // Recalcular valor desta parcela
            this.atualizarParcelas(parseFloat(document.getElementById('valor_total').value) || 0);
        }
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

    // Evento para gerar parcelas automaticamente
    const numeroParcelasInput = document.getElementById('numero_parcelas');
    if (numeroParcelasInput) {
        numeroParcelasInput.addEventListener('change', function() {
            VendasApp.gerarParcelas();
        });
    }

    // Calcular total inicial
    VendasApp.calcularTotal();

    // Gerar parcelas iniciais se não existirem
    const parcelasExistentes = document.querySelectorAll('input[name*="[valor]"]');
    if (parcelasExistentes.length === 0) {
        VendasApp.gerarParcelas();
    }
});
