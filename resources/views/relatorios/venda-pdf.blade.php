<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venda #{{ $venda->id }} - DC Tecnologia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 10px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .table .text-right {
            text-align: right;
        }
        
        .table .text-center {
            text-align: center;
        }
        
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-paga {
            background-color: #d1edff;
            color: #0c5460;
        }
        
        .status-cancelada {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: inline-block;
            width: 48%;
            margin-bottom: 10px;
        }
        
        .summary-label {
            font-weight: bold;
            display: block;
        }
        
        .summary-value {
            font-size: 14px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="company-name">DC TECNOLOGIA</div>
        <div class="company-subtitle">Sistema de Vendas</div>
        <div class="document-title">COMPROVANTE DE VENDA #{{ $venda->id }}</div>
    </div>

    <!-- Informações da Venda -->
    <div class="info-section">
        <div class="section-title">INFORMAÇÕES DA VENDA</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Número da Venda:</div>
                <div class="info-value">#{{ $venda->id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Data da Venda:</div>
                <div class="info-value">{{ $venda->data_venda->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Vendedor:</div>
                <div class="info-value">{{ $venda->user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Cliente:</div>
                <div class="info-value">
                    @if($venda->cliente)
                        {{ $venda->cliente->nome }}
                        @if($venda->cliente->email)
                            <br>{{ $venda->cliente->email }}
                        @endif
                        @if($venda->cliente->telefone)
                            <br>{{ $venda->cliente->telefone }}
                        @endif
                    @else
                        Cliente não informado
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Forma de Pagamento:</div>
                <div class="info-value">{{ $venda->formaPagamento->nome }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $venda->status }}">
                        @if($venda->status === 'pendente')
                            Pendente
                        @elseif($venda->status === 'paga')
                            Paga
                        @else
                            Cancelada
                        @endif
                    </span>
                </div>
            </div>
            @if($venda->observacoes)
                <div class="info-row">
                    <div class="info-label">Observações:</div>
                    <div class="info-value">{{ $venda->observacoes }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Resumo Financeiro -->
    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Valor Total:</span>
            <span class="summary-value">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Número de Parcelas:</span>
            <span class="summary-value">{{ $venda->numero_parcelas }}x</span>
        </div>
    </div>

    <!-- Itens da Venda -->
    <div class="info-section">
        <div class="section-title">ITENS DA VENDA</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th class="text-center">Qtd</th>
                    <th class="text-right">Preço Unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venda->itens as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->produto->nome }}</strong>
                            @if($item->produto->descricao)
                                <br><small>{{ $item->produto->descricao }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantidade }}</td>
                        <td class="text-right">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAL GERAL</strong></td>
                    <td class="text-right"><strong>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Parcelas -->
    @if($venda->parcelas->count() > 0)
        <div class="info-section">
            <div class="section-title">PARCELAS</div>
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">Parcela</th>
                        <th class="text-right">Valor</th>
                        <th class="text-center">Vencimento</th>
                        <th class="text-center">Pagamento</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venda->parcelas as $parcela)
                        <tr>
                            <td class="text-center">{{ $parcela->numero_parcela }}ª</td>
                            <td class="text-right">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                            <td class="text-center">{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if($parcela->data_pagamento)
                                    {{ $parcela->data_pagamento->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-{{ $parcela->status }}">
                                    @if($parcela->status === 'pendente')
                                        Pendente
                                    @elseif($parcela->status === 'paga')
                                        Paga
                                    @else
                                        Vencida
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Rodapé -->
    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>DC Tecnologia - Sistema de Vendas</p>
        <p>Este é um documento gerado automaticamente pelo sistema.</p>
    </div>
</body>
</html>
