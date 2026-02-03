<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitacao extends Model
{
    use HasFactory;

    protected $table = 'solicitacoes';

    protected $fillable = [
        'nome',
        'cpf',
        'data_nascimento',
        'rg',
        'endereco',
        'tipo_residencia',
        'telefone_celular',
        'instagram',
        'email',
        'telefone_parente_1',
        'telefone_parente_2',
        'nome_empresa',
        'pessoa_indicou',
        'devendo_agiota',
        'observacoes',
        'contracheque_path',
        'identidade_path',
        'comprovante_endereco_path',
        'status',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'devendo_agiota' => 'boolean',
    ];
}