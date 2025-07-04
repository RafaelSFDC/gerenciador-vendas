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
            // Verificar se a coluna valor_original já existe antes de tentar adicioná-la
            if (!Schema::hasColumn('parcelas', 'valor_original')) {
                $table->decimal('valor_original', 10, 2)->nullable()->after('valor');
            }

            // Verificar se a coluna customizada já existe antes de tentar adicioná-la
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
            // Só remover as colunas se elas existirem e foram adicionadas por esta migration
            // Como valor_original já existia na migration original, não devemos removê-la
            if (Schema::hasColumn('parcelas', 'customizada')) {
                $table->dropColumn('customizada');
            }
        });
    }
};
