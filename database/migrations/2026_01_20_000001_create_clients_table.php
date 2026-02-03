<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            // Dados pessoais
            $table->string('nome');
            $table->string('documento')->nullable()->comment('CPF ou CNPJ');
            $table->string('tipo_documento')->default('cpf')->comment('cpf|cnpj');
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('endereco')->nullable();
            $table->decimal('renda_mensal', 15, 2)->nullable();
            $table->boolean('ativo')->default(true);
            // Metadados financeiros resumidos
            $table->decimal('disponivel', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};