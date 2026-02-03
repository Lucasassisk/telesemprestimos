<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumo_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable()->index();
            $table->string('nome');
            $table->decimal('valor', 18, 2)->default(0);
            $table->string('tipo')->default('currency'); // currency | number | text
            $table->string('cor')->nullable();
            $table->integer('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumo_items');
    }
};