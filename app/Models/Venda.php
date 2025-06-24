<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venda extends Model
{
    protected $fillable = [
        'user_id',
        'cliente_id',
        'forma_pagamento_id',
        'valor_total',
        'numero_parcelas',
        'data_venda',
        'observacoes',
        'status',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'data_venda' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
    }

    public function parcelas(): HasMany
    {
        return $this->hasMany(Parcela::class);
    }

    /**
     * Recalcular parcelas não customizadas
     */
    public function recalcularParcelas(): void
    {
        $parcelasCustomizadas = $this->parcelas()->where('customizada', true)->get();
        $parcelasNaoCustomizadas = $this->parcelas()->where('customizada', false)->get();

        if ($parcelasNaoCustomizadas->isEmpty()) {
            return;
        }

        // Calcular valor total das parcelas customizadas
        $valorCustomizado = $parcelasCustomizadas->sum('valor');

        // Valor restante para distribuir entre parcelas não customizadas
        $valorRestante = $this->valor_total - $valorCustomizado;

        // Distribuir valor restante igualmente entre parcelas não customizadas
        $valorPorParcela = $valorRestante / $parcelasNaoCustomizadas->count();

        // Atualizar parcelas não customizadas
        foreach ($parcelasNaoCustomizadas as $parcela) {
            if ($parcela->podeSerRecalculada()) {
                $parcela->update(['valor' => $valorPorParcela]);
            }
        }
    }

    /**
     * Obter valor total das parcelas customizadas
     */
    public function getValorParcelasCustomizadas(): float
    {
        return $this->parcelas()->where('customizada', true)->sum('valor');
    }

    /**
     * Obter valor disponível para parcelas não customizadas
     */
    public function getValorDisponivelParaParcelas(): float
    {
        return $this->valor_total - $this->getValorParcelasCustomizadas();
    }
}
