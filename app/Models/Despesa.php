<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    use HasFactory;

    protected $table = 'despesas';

    protected $fillable = [
        'nome',
        'categoria',
        'valor',
        'vencimento',
        'pago',
        'cor',
    ];

    protected $casts = [
        'vencimento' => 'date',
        'pago' => 'boolean',
        'valor' => 'decimal:2',
    ];
}