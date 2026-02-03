<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Emprestimo;
use App\Models\Parcela;
use App\Services\SystemNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmprestimoController extends Controller
{
    private function clienteTemAtraso(int $clienteId): bool
    {
        return Parcela::where('pago', false)
            ->whereDate('vencimento', '<', now()->toDateString())
            ->whereHas('emprestimo', fn($q) => $q->where('cliente_id', $clienteId))
            ->exists();
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $emprestimos = Emprestimo::with('cliente')
            ->withSum('parcelas', 'juros')
            ->withMax('parcelas', 'vencimento')
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('cliente', function ($q2) use ($q) {
                    $q2->where('nome', 'like', "%{$q}%")
                       ->orWhere('documento', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->appends(['q' => $q]);

        return view('emprestimos.index', compact('emprestimos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->pluck('nome', 'id');
        $defaultInterest = (float) \App\Models\Configuracao::getValue('default_interest', 0);
        $minLoan = (float) \App\Models\Configuracao::getValue('min_loan_amount', 0);
        $maxLoan = (float) \App\Models\Configuracao::getValue('max_loan_amount', 0);

        return view('emprestimos.create', compact('clientes', 'defaultInterest', 'minLoan', 'maxLoan'));
    }

    public function store(Request $request)
    {
        $minLoan = (float) \App\Models\Configuracao::getValue('min_loan_amount', 0);
        $maxLoan = (float) \App\Models\Configuracao::getValue('max_loan_amount', 0);
        $maxRule = $maxLoan > 0 ? '|max:' . $maxLoan : '';

        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor_bruto' => 'required|numeric|min:' . max($minLoan, 0.01) . $maxRule,
            'juros_percent' => 'nullable|numeric|min:0',
            'parcelas' => 'required|integer|min:1',
            'data_disponivel' => 'nullable|date',
            'data_contratacao' => 'nullable|date',
            'status' => 'nullable|in:' . implode(',', Emprestimo::allowedStatuses()),
        ]);

        $data['juros_percent'] = $data['juros_percent'] ?? 0;

        if ($this->clienteTemAtraso((int) $data['cliente_id'])) {
            return back()->withErrors(['cliente_id' => 'Cliente possui parcelas em atraso. Regularize antes de novo emprestimo.'])->withInput();
        }

        // Interpretacao: juros_percent aqui e o total de juros cobrados sobre o capital (flat)
        // valor_liquido e o que sera desembolsado ao cliente (neste projeto assumimos sem taxas)
        $data['valor_liquido'] = $data['valor_bruto'];

        DB::transaction(function () use ($data) {
            $emprestimo = Emprestimo::create([
                'cliente_id' => $data['cliente_id'],
                'valor_bruto' => $data['valor_bruto'],
                'valor_liquido' => $data['valor_liquido'],
                'juros_percent' => $data['juros_percent'],
                'parcelas' => $data['parcelas'],
                'data_disponivel' => $data['data_disponivel'] ?? null,
                'data_contratacao' => $data['data_contratacao'] ?? null,
                'status' => $data['status'] ?? Emprestimo::STATUS_PENDENTE,
            ]);

            // atualizar saldo disponivel do cliente (adiciona o valor liquido)
            $cliente = Cliente::find($data['cliente_id']);
            $cliente->disponivel = ($cliente->disponivel ?? 0) + $data['valor_liquido'];
            $cliente->save();

            // Gerar parcelas pelo metodo Price (prestacao fixa com capitalizacao mensal)
            $n = (int) $data['parcelas'];
            $valorFinanciado = (float) $data['valor_bruto'];

            // interpretar juros_percent como taxa anual nominal (ex: 40 = 40% a.a.)
            // converter para taxa efetiva mensal
            $annualRate = ($data['juros_percent'] ?? 0) / 100;
            if ($annualRate <= 0) {
                $j = 0.0;
            } else {
                $j = pow(1 + $annualRate, 1 / 12) - 1; // taxa mensal efetiva
            }

            if ($j == 0) {
                $prestacao = round($valorFinanciado / $n, 2);
            } else {
                $prestacao = $valorFinanciado * ($j / (1 - pow(1 + $j, -$n)));
                $prestacao = round($prestacao, 2);
            }

            if (!empty($data['data_disponivel'])) {
                $start = \Carbon\Carbon::parse($data['data_disponivel']);
            } elseif (!empty($data['data_contratacao'])) {
                $start = \Carbon\Carbon::parse($data['data_contratacao']);
            } else {
                $start = \Carbon\Carbon::now();
            }

            $saldo = $valorFinanciado;
            for ($i = 1; $i <= $n; $i++) {
                $jurosPart = round($saldo * $j, 2);
                $principalPart = round($prestacao - $jurosPart, 2);

                // Ajuste na ultima parcela para corrigir arredondamentos
                if ($i === $n) {
                    $principalPart = round($saldo, 2);
                    $prestacao = round($principalPart + $jurosPart, 2);
                }

                Parcela::create([
                    'emprestimo_id' => $emprestimo->id,
                    'numero' => $i,
                    'valor' => $prestacao,
                    'principal' => $principalPart,
                    'juros' => $jurosPart,
                    'vencimento' => $start->copy()->addMonths($i - 1)->toDateString(),
                    'status' => 'aberta',
                ]);

                $saldo = round($saldo - $principalPart, 2);
            }

            SystemNotificationService::createOnce(
                'emprestimo_' . $emprestimo->id,
                'Novo emprestimo #' . $emprestimo->id,
                $cliente?->nome ?? null,
                'primary',
                route('emprestimos.show', $emprestimo)
            );
        });

        return redirect()->route('emprestimos.index')->with('success', 'Emprestimo criado.');
    }

    public function show(Emprestimo $emprestimo)
    {
        $emprestimo->load('cliente');
        return view('emprestimos.show', compact('emprestimo'));
    }

    public function edit(Emprestimo $emprestimo)
    {
        $clientes = Cliente::orderBy('nome')->pluck('nome', 'id');

        $emprestimo->load('cliente');

        // tentar obter a ultima solicitacao relacionada por documento (CPF) ou email
        $solicitacao = null;
        if ($emprestimo->cliente?->documento) {
            $solicitacao = \App\Models\Solicitacao::where('cpf', $emprestimo->cliente->documento)->latest()->first();
        }
        if (! $solicitacao && $emprestimo->cliente?->email) {
            $solicitacao = \App\Models\Solicitacao::where('email', $emprestimo->cliente->email)->latest()->first();
        }

        return view('emprestimos.edit', compact('emprestimo', 'clientes', 'solicitacao'));
    }

    public function update(Request $request, Emprestimo $emprestimo)
    {
        $minLoan = (float) \App\Models\Configuracao::getValue('min_loan_amount', 0);
        $maxLoan = (float) \App\Models\Configuracao::getValue('max_loan_amount', 0);
        $maxRule = $maxLoan > 0 ? '|max:' . $maxLoan : '';

        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor_bruto' => 'required|numeric|min:' . max($minLoan, 0.01) . $maxRule,
            'juros_percent' => 'nullable|numeric|min:0',
            'parcelas' => 'required|integer|min:1',
            'data_disponivel' => 'nullable|date',
            'data_contratacao' => 'nullable|date',
            'status' => 'nullable|in:' . implode(',', Emprestimo::allowedStatuses()),
        ]);

        $data['juros_percent'] = $data['juros_percent'] ?? 0;
        // manter mesma semantica do store: valor_liquido = valor_bruto (desembolso)
        $data['valor_liquido'] = $data['valor_bruto'];

        $status = $data['status'] ?? $emprestimo->status;
        $requiresClean = in_array($status, [Emprestimo::STATUS_APROVADO, Emprestimo::STATUS_ATIVO, Emprestimo::STATUS_CONTRATADO], true);
        if ($requiresClean && $this->clienteTemAtraso((int) $data['cliente_id'])) {
            return back()->withErrors(['cliente_id' => 'Cliente possui parcelas em atraso. Nao pode ativar/contratar emprestimo.'])->withInput();
        }

        // capturar estados antigos para ajustes
        $oldParcelas = $emprestimo->parcelas()->count();
        $oldJurosPercent = $emprestimo->juros_percent;
        $oldValorBruto = $emprestimo->valor_bruto;
        $oldClienteId = $emprestimo->cliente_id;
        $oldValorLiquido = $emprestimo->valor_liquido ?? 0;

        DB::transaction(function () use ($data, $emprestimo, $oldParcelas, $oldJurosPercent, $oldValorBruto, $oldClienteId, $oldValorLiquido) {
            // atualizar emprestimo
            $emprestimo->update([
                'cliente_id' => $data['cliente_id'],
                'valor_bruto' => $data['valor_bruto'],
                'valor_liquido' => $data['valor_liquido'],
                'juros_percent' => $data['juros_percent'],
                'parcelas' => $data['parcelas'],
                'data_disponivel' => $data['data_disponivel'] ?? null,
                'data_contratacao' => $data['data_contratacao'] ?? null,
                'status' => $data['status'] ?? $emprestimo->status,
            ]);

            // ajustar saldo disponivel dos clientes caso cliente ou valor liquido tenham mudado
            if ($oldClienteId != $data['cliente_id'] || $oldValorLiquido != $data['valor_liquido']) {
                // subtrai do cliente antigo
                if ($oldClienteId) {
                    $oldCliente = Cliente::find($oldClienteId);
                    if ($oldCliente) {
                        $oldCliente->disponivel = max(0, ($oldCliente->disponivel ?? 0) - ($oldValorLiquido));
                        $oldCliente->save();
                    }
                }

                // adiciona ao novo cliente
                $newCliente = Cliente::find($data['cliente_id']);
                if ($newCliente) {
                    $newCliente->disponivel = ($newCliente->disponivel ?? 0) + $data['valor_liquido'];
                    $newCliente->save();
                }
            }

            // se numero de parcelas, juros ou valor_bruto mudou, recriar parcelas pelo metodo Price
            if ($oldParcelas != $data['parcelas'] || $oldJurosPercent != $data['juros_percent'] || $oldValorBruto != $data['valor_bruto']) {
                // remover parcelas antigas
                $emprestimo->parcelas()->delete();

                $n = (int) $data['parcelas'];
                $valorFinanciado = (float) $data['valor_bruto'];

                $annualRate = ($data['juros_percent'] ?? 0) / 100;
                if ($annualRate <= 0) {
                    $j = 0.0;
                } else {
                    $j = pow(1 + $annualRate, 1 / 12) - 1; // taxa mensal efetiva
                }

                if ($j == 0) {
                    $prestacao = round($valorFinanciado / $n, 2);
                } else {
                    $prestacao = $valorFinanciado * ($j / (1 - pow(1 + $j, -$n)));
                    $prestacao = round($prestacao, 2);
                }

                if (!empty($data['data_disponivel'])) {
                    $start = \Carbon\Carbon::parse($data['data_disponivel']);
                } elseif (!empty($data['data_contratacao'])) {
                    $start = \Carbon\Carbon::parse($data['data_contratacao']);
                } else {
                    $start = \Carbon\Carbon::now();
                }

                $saldo = $valorFinanciado;
                for ($i = 1; $i <= $n; $i++) {
                    $jurosPart = round($saldo * $j, 2);
                    $principalPart = round($prestacao - $jurosPart, 2);

                    if ($i === $n) {
                        $principalPart = round($saldo, 2);
                        $prestacao = round($principalPart + $jurosPart, 2);
                    }

                    Parcela::create([
                        'emprestimo_id' => $emprestimo->id,
                        'numero' => $i,
                        'valor' => $prestacao,
                        'principal' => $principalPart,
                        'juros' => $jurosPart,
                        'vencimento' => $start->copy()->addMonths($i - 1)->toDateString(),
                        'status' => 'aberta',
                    ]);

                    $saldo = round($saldo - $principalPart, 2);
                }
            }
        });

        return redirect()->route('emprestimos.show', $emprestimo)->with('success', 'Emprestimo atualizado.');
    }

    public function destroy(Emprestimo $emprestimo)
    {
        DB::transaction(function () use ($emprestimo) {
            // reverter disponivel do cliente
            $cliente = Cliente::find($emprestimo->cliente_id);
            if ($cliente) {
                $cliente->disponivel = max(0, ($cliente->disponivel ?? 0) - ($emprestimo->valor_liquido ?? 0));
                $cliente->save();
            }

            $emprestimo->delete();
        });

        return redirect()->route('emprestimos.index')->with('success', 'Emprestimo removido.');
    }

    public function whatsapp(Emprestimo $emprestimo)
    {
        $emprestimo->load('cliente');
        $cliente = $emprestimo->cliente;

        if (! $cliente) {
            return redirect()->back()->with('error', 'Cliente nao encontrado para este emprestimo.');
        }

        // tenta varios campos possiveis
        $rawPhone = $cliente->whatsapp ?? $cliente->telefone ?? $cliente->telefone_celular ?? $cliente->celular ?? null;
        $phone = $rawPhone ? preg_replace('/\\D+/', '', $rawPhone) : '';

        if (empty($phone)) {
            return redirect()->back()->with('error', 'Telefone do cliente nao informado.');
        }

        if (! str_starts_with($phone, '55')) {
            $phone = '55' . ltrim($phone, '0');
        }

        $firstName = trim(explode(' ', $cliente->nome ?? '')[0] ?? 'Cliente');

        $valorTotal = number_format($emprestimo->valor_bruto ?? 0, 2, ',', '.');

        $template = \App\Models\Configuracao::getValue('whatsapp_template', 'Ola {nome}, segue um lembrete do seu emprestimo #{id}. Total: R$ {valor}.');
        $mensagem = str_replace(
            ['{nome}', '{id}', '{valor}'],
            [$firstName, $emprestimo->id, $valorTotal],
            $template
        );

        $url = "https://wa.me/{$phone}?text=" . rawurlencode($mensagem);

        return redirect()->away($url);
    }
}
