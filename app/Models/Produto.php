<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'estoque',
        'ativo',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function itensVenda(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
