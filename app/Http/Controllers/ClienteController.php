<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\PromissoriaService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // ...existing code...

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $clientes = \App\Models\Cliente::withCount('emprestimos')
            ->with('latestSolicitacao')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('nome', 'like', "%{$q}%")
                       ->orWhere('documento', 'like', "%{$q}%");
                });
            })
            ->orderBy('nome')
            ->paginate(15)
            ->appends(['q' => $q]);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'documento' => ['nullable', 'string', 'max:30', new \App\Rules\CpfCnpj],
            'tipo_documento' => 'in:cpf,cnpj',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:50',
            'endereco' => 'nullable|string|max:255',
            'renda_mensal' => 'nullable|numeric',
            'disponivel' => 'nullable|numeric',
        ]);

        if (!empty($data['documento'])) {
            $doc = preg_replace('/\D+/', '', $data['documento']);
            $data['tipo_documento'] = strlen($doc) === 14 ? 'cnpj' : 'cpf';
        }

        $cliente = Cliente::create($data);

        try {
            $promissoriaPath = PromissoriaService::gerar(
                [
                    'nome' => $cliente->nome,
                    'cpf' => $cliente->documento,
                    'endereco' => $cliente->endereco,
                    'credor_nome' => config('app.name'),
                    'credor_documento' => null,
                    'valor' => null,
                    'valor_extenso' => null,
                    'parcelas' => null,
                    'valor_parcela' => null,
                    'primeiro_vencimento' => null,
                    'multa_percent' => null,
                    'juros_percent' => null,
                    'local_data' => null,
                ],
                'clientes/promissorias',
                'promissoria-cliente-' . $cliente->id
            );

            $cliente->update(['promissoria_path' => $promissoriaPath]);
        } catch (\Throwable $e) {
            \Log::error('Falha ao gerar promissoria (cliente)', [
                'cliente_id' => $cliente->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente criado.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('emprestimos');

        $timeline = collect();

        $documento = $cliente->documento;
        $email = $cliente->email;

        $solicitacoes = \App\Models\Solicitacao::query()
            ->when($documento, fn($q) => $q->orWhere('cpf', $documento))
            ->when($email, fn($q) => $q->orWhere('email', $email))
            ->get();

        foreach ($solicitacoes as $s) {
            $timeline->push([
                'type' => 'solicitacao',
                'title' => 'Solicitacao criada',
                'details' => $s->nome ?? null,
                'at' => $s->created_at,
            ]);
        }

        foreach ($cliente->emprestimos as $e) {
            $timeline->push([
                'type' => 'emprestimo',
                'title' => 'Emprestimo #' . $e->id,
                'details' => 'Valor R$ ' . number_format($e->valor_bruto ?? 0, 2, ',', '.'),
                'at' => $e->created_at,
            ]);
        }

        $emprestimoIds = $cliente->emprestimos->pluck('id')->all();
        if (! empty($emprestimoIds)) {
            $parcelas = \App\Models\Parcela::whereIn('emprestimo_id', $emprestimoIds)->get();
            foreach ($parcelas as $p) {
                if ($p->pago && $p->pago_em) {
                    $timeline->push([
                        'type' => 'parcela_paga',
                        'title' => 'Parcela paga #' . $p->numero,
                        'details' => 'R$ ' . number_format($p->valor ?? 0, 2, ',', '.'),
                        'at' => $p->pago_em,
                    ]);
                } elseif (! $p->pago && $p->vencimento && $p->vencimento->isPast()) {
                    $timeline->push([
                        'type' => 'parcela_atraso',
                        'title' => 'Parcela em atraso #' . $p->numero,
                        'details' => 'Vencimento ' . $p->vencimento->format('d/m/Y'),
                        'at' => $p->vencimento,
                    ]);
                }
            }
        }

        $timeline = $timeline->sortByDesc('at')->values();

        return view('clientes.show', compact('cliente', 'timeline'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'documento' => ['nullable', 'string', 'max:30', new \App\Rules\CpfCnpj],
            'tipo_documento' => 'in:cpf,cnpj',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:50',
            'endereco' => 'nullable|string|max:255',
            'renda_mensal' => 'nullable|numeric',
            'disponivel' => 'nullable|numeric',
            'ativo' => 'boolean',
        ]);

        if (!empty($data['documento'])) {
            $doc = preg_replace('/\D+/', '', $data['documento']);
            $data['tipo_documento'] = strlen($doc) === 14 ? 'cnpj' : 'cpf';
        }

        $cliente->update($data);

        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente atualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente removido.');
    }
}
