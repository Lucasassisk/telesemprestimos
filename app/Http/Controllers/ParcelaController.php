<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Models\Cliente;
use App\Models\Emprestimo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParcelaController extends Controller
{
    // Substitui implementação antiga: lista clientes com parcelas em aberto
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Lista clientes com total de parcelas em aberto e valor total em aberto
        $clientsWithDebt = DB::table('parcelas')
            ->join('emprestimos', 'parcelas.emprestimo_id', '=', 'emprestimos.id')
            ->join('clientes', 'emprestimos.cliente_id', '=', 'clientes.id')
            ->where('parcelas.pago', false)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('clientes.nome', 'like', "%{$q}%")
                        ->orWhere('clientes.documento', 'like', "%{$q}%");
                });
            })
            ->selectRaw('clientes.id, clientes.nome, COUNT(parcelas.id) as aberto_count, SUM(parcelas.valor) as aberto_valor')
            ->groupBy('clientes.id', 'clientes.nome')
            ->orderByDesc('aberto_valor')
            ->paginate(25)
            ->appends(['q' => $q]);

        return view('admin.parcelas.index', compact('clientsWithDebt'));
    }

    // Mostra todas as parcelas do cliente (todas as parcelas de todos os empréstimos do cliente)
    public function porCliente(Cliente $cliente)
    {
        $parcelas = Parcela::with('emprestimo')
            ->whereHas('emprestimo', function ($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })
            ->orderBy('vencimento')
            ->get();

        return view('admin.parcelas.cliente', compact('cliente', 'parcelas'));
    }

    // Marca todas parcelas não pagas do cliente como pagas (quitar dívida)
    public function quitar(Request $request, Cliente $cliente)
    {
        DB::transaction(function () use ($cliente) {
            $parcelas = Parcela::whereHas('emprestimo', function ($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })->where('pago', false)->get();

            $now = now();
            foreach ($parcelas as $p) {
                $p->pago = true;
                $p->pago_em = $now;
                $p->status = 'paga';
                $p->save();
            }

            // Opcional: se todos os empréstimos do cliente estiverem quitados, atualiza status do empréstimo
            $emprestimos = Emprestimo::where('cliente_id', $cliente->id)->get();
            foreach ($emprestimos as $e) {
                $faltam = $e->parcelas()->where('pago', false)->count();
                if ($faltam === 0) {
                    $e->status = 'quitado';
                    $e->save();
                }
            }
        });

        return redirect()->route('parcelas.por_cliente', $cliente)->with('success', 'Dívida quitada (parcelas marcadas como pagas).');
    }

    public function vencidas()
    {
        $parcelas = Parcela::with('emprestimo.cliente')
            ->where('pago', false)
            ->whereDate('vencimento', '<', now()->toDateString())
            ->orderBy('vencimento')
            ->paginate(25);

        return view('admin.parcelas.vencidas', compact('parcelas'));
    }
    
    public function pagar(Request $request, Parcela $parcela)
    {
        DB::transaction(function () use ($parcela) {
            $parcela->pago = true;
            $parcela->pago_em = now();
            $parcela->status = 'paga';
            $parcela->save();
        });
        
        return redirect()->back()->with('success', 'Parcela marcada como paga.');
    }

    public function whatsapp(\App\Models\Parcela $parcela)
    {
        $parcela->load('emprestimo.cliente');
        $emprestimo = $parcela->emprestimo;
        $cliente = $emprestimo->cliente ?? null;

        if (! $cliente) {
            return redirect()->back()->with('error', 'Cliente não encontrado para esta parcela.');
        }

        $rawPhone = $cliente->whatsapp ?? $cliente->telefone ?? $cliente->telefone_celular ?? $cliente->celular ?? null;
        $phone = $rawPhone ? preg_replace('/\D+/', '', $rawPhone) : '';

        if (empty($phone)) {
            return redirect()->back()->with('error', 'Telefone do cliente não informado.');
        }

        if (! str_starts_with($phone, '55')) {
            $phone = '55' . ltrim($phone, '0');
        }

        $firstName = trim(explode(' ', $cliente->nome ?? '')[0] ?? 'Cliente');

        $dataVenc = $parcela->vencimento?->format('d/m/Y') ?? '-';
        $valorParcela = number_format($parcela->valor ?? 0, 2, ',', '.');

        $template = \App\Models\Configuracao::getValue(
            'whatsapp_parcela_template',
            "Olá {nome}, sua parcela vence em {vencimento}, no valor de R$ {valor}. Pix telefone: {pix_telefone}\nNome: {pix_nome}\nInstituição {pix_instituicao}. Obrigado!"
        );
        $pixTelefone = \App\Models\Configuracao::getValue('whatsapp_pix_telefone', '62991275510');
        $pixNome = \App\Models\Configuracao::getValue('whatsapp_pix_nome', 'André Luiz Teles Santos');
        $pixInstituicao = \App\Models\Configuracao::getValue('whatsapp_pix_instituicao', 'Bradesco');

        $mensagem = str_replace(
            ['{nome}', '{vencimento}', '{valor}', '{pix_telefone}', '{pix_nome}', '{pix_instituicao}'],
            [$firstName, $dataVenc, $valorParcela, $pixTelefone, $pixNome, $pixInstituicao],
            $template
        );

        $url = "https://wa.me/{$phone}?text=" . rawurlencode($mensagem);

        return redirect()->away($url);
    }

}
