<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'cpf_cnpj',
        'endereco',
    ];

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }
}
