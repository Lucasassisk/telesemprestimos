<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes', function (Blueprint $table) {
            $table->id();

            // Dados pessoais
            $table->string('nome');
            $table->string('cpf')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('rg')->nullable();
            $table->text('endereco')->nullable();
            $table->string('tipo_residencia')->nullable();

            // Contato
            $table->string('telefone_celular')->nullable();
            $table->string('instagram')->nullable();
            $table->string('email')->nullable();
            $table->string('telefone_parente_1')->nullable();
            $table->string('telefone_parente_2')->nullable();

            // Profissionais / adicionais
            $table->string('nome_empresa')->nullable();
            $table->string('pessoa_indicou')->nullable();
            $table->boolean('devendo_agiota')->default(false);
            $table->text('observacoes')->nullable();

            // Documentos (paths)
            $table->string('contracheque_path')->nullable();
            $table->string('identidade_path')->nullable();
            $table->string('comprovante_endereco_path')->nullable();

            $table->string('status')->default('recebido');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes');
    }
};