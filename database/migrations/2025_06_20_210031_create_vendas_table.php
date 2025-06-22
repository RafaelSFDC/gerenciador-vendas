<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Vendedor
            $table->foreignId('cliente_id')->nullable()->constrained()->onDelete('set null'); // Cliente opcional
            $table->foreignId('forma_pagamento_id')->constrained('formas_pagamento')->onDelete('cascade');
            $table->decimal('valor_total', 10, 2);
            $table->integer('numero_parcelas')->default(1);
            $table->date('data_venda');
            $table->text('observacoes')->nullable();
            $table->enum('status', ['pendente', 'paga', 'cancelada'])->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
