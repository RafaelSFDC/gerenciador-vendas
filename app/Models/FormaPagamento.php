<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormaPagamento extends Model
{
    protected $table = 'formas_pagamento';

    protected $fillable = [
        'nome',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
