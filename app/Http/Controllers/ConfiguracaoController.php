<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ConfiguracaoController extends Controller
{
    // Exibe tela de configurações
    public function index()
    {
        $defaults = [
            'app_name' => 'TelesBank',
            'default_interest' => '0',
            'min_loan_amount' => '0',
            'max_loan_amount' => '0',
            'whatsapp_template' => 'Ola {nome}, sua solicitacao foi recebida.',
            'whatsapp_parcela_template' => "Olá {nome}, sua parcela vence em {vencimento}, no valor de R$ {valor}. Pix telefone: {pix_telefone}\nNome: {pix_nome}\nInstituição {pix_instituicao}. Obrigado!",
            'whatsapp_pix_telefone' => '62991275510',
            'whatsapp_pix_nome' => 'André Luiz Teles Santos',
            'whatsapp_pix_instituicao' => 'Bradesco',
            'require_documents' => '1',
            'developer_mode' => '0',
            'primary_color' => '#0d6efd',
            'logo_path' => '',
        ];

        $values = $defaults;
        if (Schema::hasTable('configuracoes')) {
            $items = Configuracao::all()->keyBy('key');
            foreach ($items as $key => $item) {
                $values[$key] = $item->value;
            }
        }

        return view('configuracoes.index', compact('values'));
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable('configuracoes')) {
            abort(500, 'Tabela de configuracoes nao existe. Rode as migrations.');
        }

        $data = $request->validate([
            'app_name' => 'required|string|max:100',
            'default_interest' => 'required|numeric|min:0',
            'min_loan_amount' => 'required|numeric|min:0',
            'max_loan_amount' => 'required|numeric|min:0',
            'whatsapp_template' => 'required|string|max:1000',
            'whatsapp_parcela_template' => 'required|string|max:1000',
            'whatsapp_pix_telefone' => 'required|string|max:50',
            'whatsapp_pix_nome' => 'required|string|max:120',
            'whatsapp_pix_instituicao' => 'required|string|max:120',
            'require_documents' => 'nullable|boolean',
            'developer_mode' => 'nullable|boolean',
            'primary_color' => 'required|string|max:20',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        $requireDocuments = $request->boolean('require_documents');
        $developerMode = $request->boolean('developer_mode');

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('config', 'public');
        }

        $payload = [
            'app_name' => ['value' => $data['app_name'], 'type' => 'string'],
            'default_interest' => ['value' => (string) $data['default_interest'], 'type' => 'number'],
            'min_loan_amount' => ['value' => (string) $data['min_loan_amount'], 'type' => 'number'],
            'max_loan_amount' => ['value' => (string) $data['max_loan_amount'], 'type' => 'number'],
            'whatsapp_template' => ['value' => $data['whatsapp_template'], 'type' => 'text'],
            'whatsapp_parcela_template' => ['value' => $data['whatsapp_parcela_template'], 'type' => 'text'],
            'whatsapp_pix_telefone' => ['value' => $data['whatsapp_pix_telefone'], 'type' => 'string'],
            'whatsapp_pix_nome' => ['value' => $data['whatsapp_pix_nome'], 'type' => 'string'],
            'whatsapp_pix_instituicao' => ['value' => $data['whatsapp_pix_instituicao'], 'type' => 'string'],
            'require_documents' => ['value' => $requireDocuments ? '1' : '0', 'type' => 'boolean'],
            'developer_mode' => ['value' => $developerMode ? '1' : '0', 'type' => 'boolean'],
            'primary_color' => ['value' => $data['primary_color'], 'type' => 'string'],
        ];

        if ($logoPath) {
            $payload['logo_path'] = ['value' => $logoPath, 'type' => 'string'];
        }

        foreach ($payload as $key => $item) {
            Configuracao::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $item['value'],
                    'type' => $item['type'],
                    'group' => 'geral',
                ]
            );
        }

        return redirect()->route('configuracoes.index')->with('success', 'Configuracoes salvas.');
    }
}
