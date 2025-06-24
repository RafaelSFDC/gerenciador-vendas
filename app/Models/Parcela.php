<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parcela extends Model
{
    protected $fillable = [
        'venda_id',
        'numero_parcela',
        'valor',
        'valor_original',
        'customizada',
        'data_vencimento',
        'data_pagamento',
        'status',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_original' => 'decimal:2',
        'customizada' => 'boolean',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    /**
     * Marcar parcela como customizada
     */
    public function customizar(float $novoValor): void
    {
        if (!$this->customizada) {
            $this->valor_original = $this->valor;
        }

        $this->valor = $novoValor;
        $this->customizada = true;
        $this->save();
    }

    /**
     * Remover customização da parcela
     */
    public function removerCustomizacao(): void
    {
        if ($this->customizada && $this->valor_original) {
            $this->valor = $this->valor_original;
            $this->valor_original = null;
            $this->customizada = false;
            $this->save();
        }
    }

    /**
     * Verificar se a parcela pode ser recalculada automaticamente
     */
    public function podeSerRecalculada(): bool
    {
        return !$this->customizada && $this->status === 'pendente';
    }
}
