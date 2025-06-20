<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas - DC Tecnologia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .period-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .summary-section {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #007bff;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            width: 25%;
            padding: 5px;
            text-align: center;
        }
        
        .summary-label {
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }
        
        .summary-value {
            font-size: 14px;
            color: #007bff;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 6px;
            border-left: 4px solid #007bff;
            margin-bottom: 10px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 9px;
        }
        
        .table .text-right {
            text-align: right;
        }
        
        .table .text-center {
            text-align: center;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
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
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        
        .filters-info {
            background-color: #e9ecef;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="company-name">DC TECNOLOGIA</div>
        <div class="document-title">RELATÓRIO DE VENDAS</div>
        <div class="period-info">
            Período: {{ $periodo['inicio']->format('d/m/Y') }} a {{ $periodo['fim']->format('d/m/Y') }}
        </div>
        <div class="period-info">
            Vendedor: {{ $usuario->name }}
        </div>
    </div>

    <!-- Filtros Aplicados -->
    @if(array_filter($filtros))
        <div class="filters-info">
            <strong>Filtros aplicados:</strong>
            @if($filtros['cliente_id'])
                Cliente específico |
            @endif
            @if($filtros['forma_pagamento_id'])
                Forma de pagamento específica |
            @endif
            @if($filtros['status'])
                Status: {{ ucfirst($filtros['status']) }}
            @endif
        </div>
    @endif

    <!-- Resumo Executivo -->
    <div class="summary-section">
        <div class="summary-title">RESUMO EXECUTIVO</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <span class="summary-label">Total de Vendas</span>
                    <span class="summary-value">{{ $totais['quantidade'] }}</span>
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Valor Total</span>
                    <span class="summary-value">R$ {{ number_format($totais['valor'], 2, ',', '.') }}</span>
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Ticket Médio</span>
                    <span class="summary-value">
                        R$ {{ $totais['quantidade'] > 0 ? number_format($totais['valor'] / $totais['quantidade'], 2, ',', '.') : '0,00' }}
                    </span>
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Período</span>
                    <span class="summary-value">{{ $periodo['inicio']->diffInDays($periodo['fim']) + 1 }} dias</span>
                </div>
            </div>
        </div>
        
        @if($totais['por_status']->count() > 0)
            <div style="margin-top: 15px;">
                <strong>Vendas por Status:</strong>
                @foreach($totais['por_status'] as $status => $quantidade)
                    <span class="status-badge status-{{ $status }}">
                        {{ ucfirst($status) }}: {{ $quantidade }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Lista de Vendas -->
    @if($vendas->count() > 0)
        <div class="section-title">DETALHAMENTO DAS VENDAS</div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Forma Pagto</th>
                    <th class="text-right">Valor</th>
                    <th class="text-center">Parcelas</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendas as $venda)
                    <tr>
                        <td>#{{ $venda->id }}</td>
                        <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
                        <td>
                            @if($venda->cliente)
                                {{ $venda->cliente->nome }}
                            @else
                                <em>Não informado</em>
                            @endif
                        </td>
                        <td>{{ $venda->formaPagamento->nome }}</td>
                        <td class="text-right">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $venda->numero_parcelas }}x</td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $venda->status }}">
                                @if($venda->status === 'pendente')
                                    Pendente
                                @elseif($venda->status === 'paga')
                                    Paga
                                @else
                                    Cancelada
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Detalhamento dos Itens (se houver poucas vendas) -->
        @if($vendas->count() <= 10)
            <div class="page-break"></div>
            <div class="section-title">DETALHAMENTO DOS ITENS VENDIDOS</div>
            @foreach($vendas as $venda)
                <div style="margin-bottom: 20px;">
                    <h4 style="font-size: 12px; margin-bottom: 8px;">
                        Venda #{{ $venda->id }} - {{ $venda->data_venda->format('d/m/Y') }}
                        @if($venda->cliente)
                            - {{ $venda->cliente->nome }}
                        @endif
                    </h4>
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
                                    <td>{{ $item->produto->nome }}</td>
                                    <td class="text-center">{{ $item->quantidade }}</td>
                                    <td class="text-right">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                    <td class="text-right">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif

    @else
        <div class="no-data">
            <h3>Nenhuma venda encontrada</h3>
            <p>Não foram encontradas vendas no período selecionado com os filtros aplicados.</p>
        </div>
    @endif

    <!-- Rodapé -->
    <div class="footer">
        <p>Relatório gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>DC Tecnologia - Sistema de Vendas</p>
        <p>Este é um documento gerado automaticamente pelo sistema.</p>
    </div>
</body>
</html>
