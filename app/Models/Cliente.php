<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'documento',
        'tipo_documento',
        'email',
        'telefone',
        'endereco',
        'renda_mensal',
        'disponivel',
        'ativo',
    ];

    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class);
    }

    // relação com todas as solicitações (se quiser histórico)
    public function solicitacoes()
    {
        return $this->hasMany(\App\Models\Solicitacao::class, 'cpf', 'documento')
            ->orWhere('email', $this->email);
    }

    // pega a última solicitação relacionada ao cliente (por documento ou email)
    public function latestSolicitacao()
    {
        // usa latestOfMany (Laravel) quando disponível
        return $this->hasOne(\App\Models\Solicitacao::class, 'cpf', 'documento')->latestOfMany();
    }
}
