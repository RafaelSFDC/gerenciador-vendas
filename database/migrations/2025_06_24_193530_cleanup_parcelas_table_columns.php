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
        Schema::table('parcelas', function (Blueprint $table) {
            // Remover coluna is_customizada se existir (pode ter sido criada por engano)
            if (Schema::hasColumn('parcelas', 'is_customizada')) {
                $table->dropColumn('is_customizada');
            }

            // Garantir que temos as colunas corretas
            if (!Schema::hasColumn('parcelas', 'valor_original')) {
                $table->decimal('valor_original', 10, 2)->nullable()->after('valor');
            }

            if (!Schema::hasColumn('parcelas', 'customizada')) {
                $table->boolean('customizada')->default(false)->after('valor_original');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcelas', function (Blueprint $table) {
            // Recriar is_customizada se necessário (não recomendado)
            // Esta migration é principalmente para limpeza, rollback manual se necessário
        });
    }
};
