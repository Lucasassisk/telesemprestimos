<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprestimos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->decimal('valor_bruto', 15, 2);
            $table->decimal('valor_liquido', 15, 2)->nullable();
            $table->decimal('juros_percent', 8, 3)->default(0);
            $table->integer('parcelas')->default(1);
            $table->date('data_disponivel')->nullable();
            $table->date('data_contratacao')->nullable();
            $table->string('status')->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprestimos');
    }
};