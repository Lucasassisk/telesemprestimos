<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\Solicitacao;
use App\Models\Cliente;
use App\Models\Emprestimo;
use App\Services\SystemNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class PublicSolicitacaoController extends Controller
{
    public function create()
    {
        return view('solicitacoes.create');
    }

    public function store(Request $request)
    {
        $requireDocuments = false;
        if (Schema::hasTable('configuracoes')) {
            $requireDocuments = Configuracao::where('key', 'require_documents')->value('value') === '1';
        }

        $fileRulePrefix = $requireDocuments ? 'required' : 'nullable';

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => ['nullable', 'string', 'max:30', new \App\Rules\CpfCnpj],
            'data_nascimento' => 'nullable|date',
            'rg' => 'nullable|string|max:50',
            'endereco' => 'nullable|string|max:1000',
            'tipo_residencia' => 'nullable|in:aluguel,casa_propria',
            'telefone_celular' => 'nullable|string|max:50',
            'instagram' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone_parente_1' => 'nullable|string|max:50',
            'telefone_parente_2' => 'nullable|string|max:50',
            'nome_empresa' => 'nullable|string|max:255',
            'pessoa_indicou' => 'nullable|string|max:255',
            'devendo_agiota' => 'nullable|boolean',
            'observacoes' => 'nullable|string|max:2000',

            'contracheque' => $fileRulePrefix . '|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identidade' => $fileRulePrefix . '|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'comprovante_endereco' => $fileRulePrefix . '|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $paths = [];
        foreach (['contracheque', 'identidade', 'comprovante_endereco'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                    . '-' . time() . '.' . $file->getClientOriginalExtension();
                $paths[$field . '_path'] = $file->storeAs('solicitacoes', $name, 'public');
            }
        }

        // salva solicitação (mantém histórico/files)
        $solicitacao = Solicitacao::create(array_merge(
            $data,
            [
                'contracheque_path' => $paths['contracheque_path'] ?? null,
                'identidade_path' => $paths['identidade_path'] ?? null,
                'comprovante_endereco_path' => $paths['comprovante_endereco_path'] ?? null,
                'devendo_agiota' => isset($data['devendo_agiota']) ? (bool)$data['devendo_agiota'] : false,
            ]
        ));

        SystemNotificationService::createOnce(
            'solicitacao_' . $solicitacao->id,
            'Nova solicitacao #' . $solicitacao->id,
            $solicitacao->nome ?? null,
            'info',
            route('admin.solicitacoes.index')
        );

        // encontra ou cria cliente a partir do documento/email
        $clienteAttributes = [
            'nome' => $data['nome'],
            'email' => $data['email'] ?? null,
            'telefone' => $data['telefone_celular'] ?? null,
            'endereco' => $data['endereco'] ?? null,
        ];

        if (!empty($data['cpf'])) {
            // tenta por documento (campo 'documento' na tabela clientes)
            $cliente = Cliente::firstOrCreate(
                ['documento' => $data['cpf']],
                $clienteAttributes
            );
        } elseif (!empty($data['email'])) {
            // se não tiver CPF, tenta por email
            $cliente = Cliente::firstOrCreate(
                ['email' => $data['email']],
                $clienteAttributes
            );
        } else {
            // fallback: cria cliente simples (nome obrigatório)
            $cliente = Cliente::create($clienteAttributes);
        }

        // cria um empréstimo "pendente" que aparecerá em /emprestimos (admin pode aprovar/editar)
        Emprestimo::create([
            'cliente_id' => $cliente->id,
            'valor_bruto' => 0.00,
            'valor_liquido' => 0.00,
            'juros_percent' => 0,
            'parcelas' => 1,
            'data_disponivel' => null,
            'data_contratacao' => null,
            'status' => \App\Models\Emprestimo::STATUS_PENDENTE,
            'solicitacao_id' => $solicitacao->id, // ADICIONADO
        ]);

        // redireciona para a página de obrigado
        return redirect()->route('solicitacoes.thankyou');
    }

    public function thankyou()
    {
        return view('solicitacoes.thankyou');
    }
}
