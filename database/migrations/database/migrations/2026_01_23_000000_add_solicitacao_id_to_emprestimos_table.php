<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emprestimos', function (Blueprint $table) {
            $table->foreignId('solicitacao_id')
                ->nullable()
                ->constrained('solicitacoes')
                ->nullOnDelete()
                ->after('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::table('emprestimos', function (Blueprint $table) {
            if (method_exists($table, 'dropConstrainedForeignId')) {
                $table->dropConstrainedForeignId('solicitacao_id');
            } else {
                $table->dropForeign(['solicitacao_id']);
                $table->dropColumn('solicitacao_id');
            }
        });
    }
};