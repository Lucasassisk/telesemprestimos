<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emprestimo extends Model
{
    use HasFactory;

    protected $table = 'emprestimos';

    protected $fillable = [
        'cliente_id',
        'valor_bruto',
        'valor_liquido',
        'juros_percent',
        'parcelas',
        'data_disponivel',
        'data_contratacao',
        'status',
        'solicitacao_id', // ADICIONADO
    ];

    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_CONTRATADO = 'contratado';
    public const STATUS_QUITADO = 'quitado';
    public const STATUS_INADIMPLENTE = 'inadimplente';
    public const STATUS_RECUSADO = 'recusado';
    public const STATUS_CANCELADO = 'cancelado';

    public static function allowedStatuses(): array
    {
        return [
            self::STATUS_PENDENTE,
            self::STATUS_APROVADO,
            self::STATUS_ATIVO,
            self::STATUS_CONTRATADO,
            self::STATUS_QUITADO,
            self::STATUS_INADIMPLENTE,
            self::STATUS_RECUSADO,
            self::STATUS_CANCELADO,
        ];
    }

    /**
     * Casts for attributes to ensure dates and decimals are proper types.
     */
    protected $casts = [
        'data_disponivel' => 'date',
        'data_contratacao' => 'date',
        'valor_bruto' => 'decimal:2',
        'valor_liquido' => 'decimal:2',
        'juros_percent' => 'float',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function parcelas()
    {
        return $this->hasMany(\App\Models\Parcela::class);
    }

    public function solicitacao()
    {
        return $this->belongsTo(\App\Models\Solicitacao::class, 'solicitacao_id');
    }

    /**
     * Outstanding balance (sum of unpaid parcelas)
     */
    public function outstanding()
    {
        return $this->parcelas()->where('pago', false)->sum('valor');
    }
}
