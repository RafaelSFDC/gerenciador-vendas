<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Parcelas em Aberto - DC Tecnologia</title>
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
            border-bottom: 2px solid #dc3545;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .date-info {
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
            color: #dc3545;
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
        }
        
        .value-danger {
            color: #dc3545;
        }
        
        .value-warning {
            color: #ffc107;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 6px;
            border-left: 4px solid #dc3545;
            margin-bottom: 10px;
        }
        
        .section-title.warning {
            border-left-color: #ffc107;
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
        
        .table .danger {
            background-color: #f8d7da;
        }
        
        .table .warning {
            background-color: #fff3cd;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-vencida {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
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
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="company-name">DC TECNOLOGIA</div>
        <div class="document-title">RELATÓRIO DE PARCELAS EM ABERTO</div>
        <div class="date-info">
            Gerado em: {{ $data_geracao->format('d/m/Y H:i:s') }}
        </div>
        <div class="date-info">
            Vendedor: {{ $usuario->name }}
        </div>
    </div>

    <!-- Resumo Executivo -->
    <div class="summary-section">
        <div class="summary-title">RESUMO EXECUTIVO</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <span class="summary-label">Parcelas Vencidas</span>
                    <span class="summary-value value-danger">{{ $parcelas_vencidas->count() }}</span>
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Valor Vencido</span>
                    <span class="summary-value value-danger">R$ {{ number_format($total_vencidas, 2, ',', '.') }}</span>
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Parcelas a Vencer</span>
                    <span class="summary-value value-warning">{{ $parcelas_a_vencer->count() }}</span>
                </div>
                <div class="summary-cell">
                    <span class="summary-label">Valor a Vencer</span>
                    <span class="summary-value value-warning">R$ {{ number_format($total_a_vencer, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 15px; text-align: center;">
            <strong>Total Geral em Aberto: R$ {{ number_format($total_vencidas + $total_a_vencer, 2, ',', '.') }}</strong>
        </div>
    </div>

    <!-- Alertas -->
    @if($parcelas_vencidas->count() > 0)
        <div class="alert alert-danger">
            <strong>⚠️ ATENÇÃO:</strong> Existem {{ $parcelas_vencidas->count() }} parcelas vencidas no valor total de R$ {{ number_format($total_vencidas, 2, ',', '.') }}.
        </div>
    @endif

    @if($parcelas_a_vencer->count() > 0)
        <div class="alert alert-warning">
            <strong>📅 LEMBRETE:</strong> Existem {{ $parcelas_a_vencer->count() }} parcelas a vencer no valor total de R$ {{ number_format($total_a_vencer, 2, ',', '.') }}.
        </div>
    @endif

    <!-- Parcelas Vencidas -->
    @if($parcelas_vencidas->count() > 0)
        <div class="section-title">PARCELAS VENCIDAS ({{ $parcelas_vencidas->count() }})</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Venda</th>
                    <th>Cliente</th>
                    <th>Parcela</th>
                    <th class="text-right">Valor</th>
                    <th class="text-center">Vencimento</th>
                    <th class="text-center">Dias Atraso</th>
                    <th>Forma Pagto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parcelas_vencidas as $parcela)
                    <tr class="danger">
                        <td>#{{ $parcela->venda_id }}</td>
                        <td>
                            @if($parcela->venda->cliente)
                                {{ $parcela->venda->cliente->nome }}
                            @else
                                <em>Não informado</em>
                            @endif
                        </td>
                        <td class="text-center">{{ $parcela->numero_parcela }}ª</td>
                        <td class="text-right">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <strong>{{ $parcela->data_vencimento->diffInDays(now()) }} dias</strong>
                        </td>
                        <td>{{ $parcela->venda->formaPagamento->nome }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAL VENCIDO</strong></td>
                    <td class="text-right"><strong>R$ {{ number_format($total_vencidas, 2, ',', '.') }}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="section-title">PARCELAS VENCIDAS</div>
        <div class="no-data">
            <p>✅ Não há parcelas vencidas no momento.</p>
        </div>
    @endif

    <!-- Parcelas a Vencer -->
    @if($parcelas_a_vencer->count() > 0)
        <div class="section-title warning">PARCELAS A VENCER ({{ $parcelas_a_vencer->count() }})</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Venda</th>
                    <th>Cliente</th>
                    <th>Parcela</th>
                    <th class="text-right">Valor</th>
                    <th class="text-center">Vencimento</th>
                    <th class="text-center">Dias Restantes</th>
                    <th>Forma Pagto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parcelas_a_vencer as $parcela)
                    <tr class="{{ $parcela->data_vencimento->diffInDays(now()) <= 7 ? 'warning' : '' }}">
                        <td>#{{ $parcela->venda_id }}</td>
                        <td>
                            @if($parcela->venda->cliente)
                                {{ $parcela->venda->cliente->nome }}
                            @else
                                <em>Não informado</em>
                            @endif
                        </td>
                        <td class="text-center">{{ $parcela->numero_parcela }}ª</td>
                        <td class="text-right">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @php
                                $diasRestantes = now()->diffInDays($parcela->data_vencimento);
                            @endphp
                            @if($diasRestantes <= 7)
                                <strong>{{ $diasRestantes }} dias</strong>
                            @else
                                {{ $diasRestantes }} dias
                            @endif
                        </td>
                        <td>{{ $parcela->venda->formaPagamento->nome }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAL A VENCER</strong></td>
                    <td class="text-right"><strong>R$ {{ number_format($total_a_vencer, 2, ',', '.') }}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="section-title warning">PARCELAS A VENCER</div>
        <div class="no-data">
            <p>ℹ️ Não há parcelas a vencer no momento.</p>
        </div>
    @endif

    <!-- Resumo Final -->
    @if($parcelas_vencidas->count() > 0 || $parcelas_a_vencer->count() > 0)
        <div class="summary-section">
            <div class="summary-title">RESUMO FINAL</div>
            <table class="table">
                <tr>
                    <td><strong>Total de Parcelas em Aberto:</strong></td>
                    <td class="text-right"><strong>{{ $parcelas_vencidas->count() + $parcelas_a_vencer->count() }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Valor Total em Aberto:</strong></td>
                    <td class="text-right"><strong>R$ {{ number_format($total_vencidas + $total_a_vencer, 2, ',', '.') }}</strong></td>
                </tr>
                <tr class="danger">
                    <td><strong>Valor Vencido (Prioridade Alta):</strong></td>
                    <td class="text-right"><strong>R$ {{ number_format($total_vencidas, 2, ',', '.') }}</strong></td>
                </tr>
                <tr class="warning">
                    <td><strong>Valor a Vencer (Acompanhar):</strong></td>
                    <td class="text-right"><strong>R$ {{ number_format($total_a_vencer, 2, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Rodapé -->
    <div class="footer">
        <p>Relatório gerado em {{ $data_geracao->format('d/m/Y H:i:s') }}</p>
        <p>DC Tecnologia - Sistema de Vendas</p>
        <p>Este é um documento gerado automaticamente pelo sistema.</p>
        <p><strong>Recomendação:</strong> Acompanhe regularmente as parcelas vencidas para manter o fluxo de caixa saudável.</p>
    </div>
</body>
</html>
