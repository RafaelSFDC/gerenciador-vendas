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
}
