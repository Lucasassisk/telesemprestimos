<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emprestimo_id')->constrained('emprestimos')->cascadeOnDelete();
            $table->integer('numero');
            $table->decimal('valor', 15, 2);
            $table->decimal('principal', 15, 2)->default(0);
            $table->decimal('juros', 15, 2)->default(0);
            $table->date('vencimento')->nullable();
            $table->boolean('pago')->default(false);
            $table->dateTime('pago_em')->nullable();
            $table->string('status')->default('aberta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelas');
    }
};
