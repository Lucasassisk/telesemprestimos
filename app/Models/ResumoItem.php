<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumoItem extends Model
{
    use HasFactory;

    protected $table = 'resumo_items';

    protected $fillable = [
        'key',
        'nome',
        'valor',
        'tipo',
        'cor',
        'ordem',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'ordem' => 'integer',
    ];
}