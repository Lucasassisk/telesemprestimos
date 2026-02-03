<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;

    protected $table = 'parcelas';

    protected $fillable = [
        'emprestimo_id',
        'numero',
        'valor',
        'principal',
        'juros',
        'vencimento',
        'pago',
        'pago_em',
        'status',
    ];

    protected $casts = [
        'vencimento' => 'date',
        'pago' => 'boolean',
        'pago_em' => 'datetime',
        'valor' => 'decimal:2',
        'principal' => 'decimal:2',
        'juros' => 'decimal:2',
    ];

    public function emprestimo()
    {
        return $this->belongsTo(Emprestimo::class);
    }
}
