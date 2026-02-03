<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Despesa;
use App\Models\Parcela;
use App\Models\Emprestimo;
use App\Models\Solicitacao;
use App\Models\ResumoItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelatorioController extends Controller
{
    // Página "Despesas Gerais" (mantém o nome de rota relatorios.emprestimos para compatibilidade)
    public function despesas(Request $request)
    {
        // evita exceção se tabela ainda não criada
        if (! Schema::hasTable('despesas')) {
            $despesas = collect([]);
            $total = 0;
            return view('relatorios.despesas', compact('despesas', 'total'));
        }

        $q = trim((string) $request->query('q', ''));
        $period = $request->query('period', null); // formato YYYY-MM opcional

        $despesasQuery = Despesa::query();

        if ($period) {
            [$ano, $mes] = explode('-', $period) + [null, null];
            if ($ano && $mes) {
                $despesasQuery->whereYear('vencimento', $ano)->whereMonth('vencimento', $mes);
            }
        }

        $despesasQuery->when($q !== '', function ($qBuilder) use ($q) {
                $qBuilder->where('nome', 'like', "%{$q}%")
                         ->orWhere('categoria', 'like', "%{$q}%");
            });

        $despesas = $despesasQuery->orderByDesc('vencimento')
            ->paginate(50)
            ->appends(['q' => $q, 'period' => $period]);

        $total = Despesa::when($period, function ($qb) use ($period) {
                    [$ano, $mes] = explode('-', $period) + [null, null];
                    if ($ano && $mes) {
                        $qb->whereYear('vencimento', $ano)->whereMonth('vencimento', $mes);
                    }
                })->sum('valor');

        return view('relatorios.despesas', compact('despesas', 'total'));
    }

    // Criar nova despesa
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:100',
            'valor' => 'required|numeric|min:0',
            'vencimento' => 'nullable|date',
            'pago' => 'nullable|boolean',
            'cor' => 'nullable|string|max:30',
        ]);

        $data['pago'] = (bool) ($data['pago'] ?? false);

        Despesa::create($data);

        return redirect()->route('relatorios.emprestimos')->with('success', 'Despesa adicionada.');
    }

    // Remover despesa
    public function destroy(Despesa $despesa)
    {
        $despesa->delete();
        return redirect()->route('relatorios.emprestimos')->with('success', 'Despesa removida.');
    }

    // Mostrar formulário de edição
    public function edit(Despesa $despesa)
    {
        return view('relatorios.edit', compact('despesa'));
    }

    // Atualizar despesa
    public function update(Request $request, Despesa $despesa)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:100',
            'valor' => 'required|numeric|min:0',
            'vencimento' => 'nullable|date',
            'pago' => 'nullable|boolean',
            'cor' => 'nullable|string|max:30',
        ]);

        $data['pago'] = (bool) ($data['pago'] ?? false);

        $despesa->update($data);

        return redirect()->route('relatorios.emprestimos')->with('success', 'Despesa atualizada.');
    }

    // Alterna o flag "pago" (marca/desmarca)
    public function togglePaid(Despesa $despesa)
    {
        $despesa->pago = ! (bool) $despesa->pago;
        $despesa->save();

        return redirect()->route('relatorios.emprestimos')->with('success', 'Status de pagamento atualizado.');
    }

    // Página de inadimplência (parcelas vencidas e não pagas)
    public function inadimplencia(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));
        [$ano, $mes] = explode('-', $period) + [null, null];
        $start = $ano && $mes ? Carbon::createFromDate((int) $ano, (int) $mes, 1)->startOfMonth() : now()->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $periodLabel = $start->format('m/Y');

        $hasSolicitacoes = Schema::hasTable('solicitacoes');
        $hasEmprestimos = Schema::hasTable('emprestimos');
        $hasParcelas = Schema::hasTable('parcelas');
        $hasDespesas = Schema::hasTable('despesas');

        $solicitacoesCount = $hasSolicitacoes
            ? Solicitacao::whereBetween('created_at', [$start, $end])->count()
            : 0;

        $emprestimosQuery = $hasEmprestimos
            ? Emprestimo::with('cliente')->where(function ($q) use ($start, $end) {
                $q->whereBetween('data_contratacao', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('data_contratacao')->whereBetween('created_at', [$start, $end]);
                  });
            })
            : null;

        $emprestimosCount = $emprestimosQuery ? (clone $emprestimosQuery)->count() : 0;
        $emprestimosValorBruto = $emprestimosQuery ? (clone $emprestimosQuery)->sum('valor_bruto') : 0;
        $emprestimosValorLiquido = $emprestimosQuery ? (clone $emprestimosQuery)->sum('valor_liquido') : 0;

        $pagamentosQuery = $hasParcelas
            ? Parcela::with('emprestimo.cliente')->where('pago', true)->whereBetween('pago_em', [$start, $end])
            : null;
        $pagamentosCount = $pagamentosQuery ? (clone $pagamentosQuery)->count() : 0;
        $pagamentosValor = $pagamentosQuery ? (clone $pagamentosQuery)->sum('valor') : 0;

        $parcelasAtrasadasQuery = $hasParcelas
            ? Parcela::with('emprestimo.cliente')
                ->where('pago', false)
                ->whereBetween('vencimento', [$start, $end])
            : null;
        $parcelasAtrasadasCount = $parcelasAtrasadasQuery ? (clone $parcelasAtrasadasQuery)->count() : 0;
        $parcelasAtrasadasValor = $parcelasAtrasadasQuery ? (clone $parcelasAtrasadasQuery)->sum('valor') : 0;

        $despesasQuery = $hasDespesas
            ? Despesa::whereBetween('vencimento', [$start, $end])
            : null;
        $despesasCount = $despesasQuery ? (clone $despesasQuery)->count() : 0;
        $despesasValor = $despesasQuery ? (clone $despesasQuery)->sum('valor') : 0;

        $solicitacoesRecentes = $hasSolicitacoes
            ? Solicitacao::whereBetween('created_at', [$start, $end])->orderByDesc('created_at')->limit(10)->get()
            : collect();
        $emprestimosRecentes = $emprestimosQuery ? (clone $emprestimosQuery)->orderByDesc('created_at')->limit(10)->get() : collect();
        $pagamentosRecentes = $pagamentosQuery ? (clone $pagamentosQuery)->orderByDesc('pago_em')->limit(10)->get() : collect();
        $parcelasAtrasadasRecentes = $parcelasAtrasadasQuery ? (clone $parcelasAtrasadasQuery)->orderBy('vencimento')->limit(10)->get() : collect();
        $despesasRecentes = $despesasQuery ? (clone $despesasQuery)->orderByDesc('vencimento')->limit(10)->get() : collect();

        return view('relatorios.inadimplencia', compact(
            'period',
            'periodLabel',
            'start',
            'end',
            'solicitacoesCount',
            'emprestimosCount',
            'emprestimosValorBruto',
            'emprestimosValorLiquido',
            'pagamentosCount',
            'pagamentosValor',
            'parcelasAtrasadasCount',
            'parcelasAtrasadasValor',
            'despesasCount',
            'despesasValor',
            'solicitacoesRecentes',
            'emprestimosRecentes',
            'pagamentosRecentes',
            'parcelasAtrasadasRecentes',
            'despesasRecentes'
        ));
    }

    public function exportInadimplencia(Request $request): StreamedResponse
    {
        $period = $request->query('period', now()->format('Y-m'));
        [$ano, $mes] = explode('-', $period) + [null, null];
        $start = $ano && $mes ? Carbon::createFromDate((int) $ano, (int) $mes, 1)->startOfMonth() : now()->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $periodLabel = $start->format('m/Y');

        $hasSolicitacoes = Schema::hasTable('solicitacoes');
        $hasEmprestimos = Schema::hasTable('emprestimos');
        $hasParcelas = Schema::hasTable('parcelas');
        $hasDespesas = Schema::hasTable('despesas');

        $solicitacoesCount = $hasSolicitacoes
            ? Solicitacao::whereBetween('created_at', [$start, $end])->count()
            : 0;

        $emprestimosQuery = $hasEmprestimos
            ? Emprestimo::with('cliente')->where(function ($q) use ($start, $end) {
                $q->whereBetween('data_contratacao', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('data_contratacao')->whereBetween('created_at', [$start, $end]);
                  });
            })
            : null;

        $emprestimosCount = $emprestimosQuery ? (clone $emprestimosQuery)->count() : 0;
        $emprestimosValorBruto = $emprestimosQuery ? (clone $emprestimosQuery)->sum('valor_bruto') : 0;
        $emprestimosValorLiquido = $emprestimosQuery ? (clone $emprestimosQuery)->sum('valor_liquido') : 0;

        $pagamentosQuery = $hasParcelas
            ? Parcela::with('emprestimo.cliente')->where('pago', true)->whereBetween('pago_em', [$start, $end])
            : null;
        $pagamentosCount = $pagamentosQuery ? (clone $pagamentosQuery)->count() : 0;
        $pagamentosValor = $pagamentosQuery ? (clone $pagamentosQuery)->sum('valor') : 0;

        $parcelasAtrasadasQuery = $hasParcelas
            ? Parcela::with('emprestimo.cliente')
                ->where('pago', false)
                ->whereBetween('vencimento', [$start, $end])
            : null;
        $parcelasAtrasadasCount = $parcelasAtrasadasQuery ? (clone $parcelasAtrasadasQuery)->count() : 0;
        $parcelasAtrasadasValor = $parcelasAtrasadasQuery ? (clone $parcelasAtrasadasQuery)->sum('valor') : 0;

        $despesasQuery = $hasDespesas
            ? Despesa::whereBetween('vencimento', [$start, $end])
            : null;
        $despesasCount = $despesasQuery ? (clone $despesasQuery)->count() : 0;
        $despesasValor = $despesasQuery ? (clone $despesasQuery)->sum('valor') : 0;

        $filename = 'relatorio_mensal_' . $period . '.xls';

        return response()->streamDownload(function () use (
            $periodLabel,
            $solicitacoesCount,
            $emprestimosCount,
            $emprestimosValorBruto,
            $emprestimosValorLiquido,
            $pagamentosCount,
            $pagamentosValor,
            $parcelasAtrasadasCount,
            $parcelasAtrasadasValor,
            $despesasCount,
            $despesasValor,
            $hasSolicitacoes,
            $hasEmprestimos,
            $hasParcelas,
            $hasDespesas,
            $emprestimosQuery,
            $pagamentosQuery,
            $parcelasAtrasadasQuery,
            $despesasQuery,
            $start,
            $end
        ) {
            $out = fopen('php://output', 'w');

            $write = function ($line) use ($out) {
                fwrite($out, $line . "\n");
            };

            $write('<html><head><meta charset="utf-8"></head><body>');
            $write('<table border="1">');
            $write('<tr><th colspan="2">Relatorio Mensal</th></tr>');
            $write('<tr><td>Periodo</td><td>' . $periodLabel . '</td></tr>');
            $write('</table><br>');

            $write('<table border="1">');
            $write('<tr><th colspan="2">Resumo</th></tr>');
            $write('<tr><td>Solicitacoes no mes</td><td>' . $solicitacoesCount . '</td></tr>');
            $write('<tr><td>Emprestimos contratados</td><td>' . $emprestimosCount . '</td></tr>');
            $write('<tr><td>Emprestimos - valor bruto</td><td>' . number_format($emprestimosValorBruto, 2, ',', '.') . '</td></tr>');
            $write('<tr><td>Emprestimos - valor liquido</td><td>' . number_format($emprestimosValorLiquido, 2, ',', '.') . '</td></tr>');
            $write('<tr><td>Pagamentos recebidos</td><td>' . $pagamentosCount . '</td></tr>');
            $write('<tr><td>Pagamentos - valor total</td><td>' . number_format($pagamentosValor, 2, ',', '.') . '</td></tr>');
            $write('<tr><td>Parcelas atrasadas</td><td>' . $parcelasAtrasadasCount . '</td></tr>');
            $write('<tr><td>Parcelas atrasadas - valor total</td><td>' . number_format($parcelasAtrasadasValor, 2, ',', '.') . '</td></tr>');
            $write('<tr><td>Despesas do mes</td><td>' . $despesasCount . '</td></tr>');
            $write('<tr><td>Despesas - valor total</td><td>' . number_format($despesasValor, 2, ',', '.') . '</td></tr>');
            $write('</table><br>');

            if ($hasSolicitacoes) {
                $write('<table border="1">');
                $write('<tr><th colspan="4">Solicitacoes do mes</th></tr>');
                $write('<tr><th>Nome</th><th>CPF</th><th>Email</th><th>Criado em</th></tr>');
                Solicitacao::whereBetween('created_at', [$start, $end])
                    ->orderBy('id')
                    ->chunk(500, function ($rows) use ($write) {
                        foreach ($rows as $s) {
                            $write('<tr><td>' . htmlspecialchars($s->nome) . '</td><td>' . ($s->cpf ?? '') . '</td><td>' . ($s->email ?? '') . '</td><td>' . ($s->created_at?->format('Y-m-d H:i') ?? '') . '</td></tr>');
                        }
                    });
                $write('</table><br>');
            }

            if ($hasEmprestimos && $emprestimosQuery) {
                $write('<table border="1">');
                $write('<tr><th colspan="5">Emprestimos do mes</th></tr>');
                $write('<tr><th>ID</th><th>Cliente</th><th>Valor bruto</th><th>Valor liquido</th><th>Data</th></tr>');
                (clone $emprestimosQuery)->orderBy('id')->chunk(500, function ($rows) use ($write) {
                    foreach ($rows as $e) {
                        $write('<tr><td>' . $e->id . '</td><td>' . htmlspecialchars($e->cliente?->nome ?? '') . '</td><td>' . number_format($e->valor_bruto ?? 0, 2, ',', '.') . '</td><td>' . number_format($e->valor_liquido ?? 0, 2, ',', '.') . '</td><td>' . (($e->data_contratacao?->format('Y-m-d') ?? $e->created_at?->format('Y-m-d')) ?? '') . '</td></tr>');
                    }
                });
                $write('</table><br>');
            }

            if ($hasParcelas && $pagamentosQuery) {
                $write('<table border="1">');
                $write('<tr><th colspan="4">Pagamentos do mes</th></tr>');
                $write('<tr><th>Cliente</th><th>Parcela</th><th>Valor</th><th>Pago em</th></tr>');
                (clone $pagamentosQuery)->orderBy('id')->chunk(500, function ($rows) use ($write) {
                    foreach ($rows as $p) {
                        $write('<tr><td>' . htmlspecialchars($p->emprestimo?->cliente?->nome ?? '') . '</td><td>#' . $p->numero . '</td><td>' . number_format($p->valor ?? 0, 2, ',', '.') . '</td><td>' . ($p->pago_em?->format('Y-m-d H:i') ?? '') . '</td></tr>');
                    }
                });
                $write('</table><br>');
            }

            if ($hasParcelas && $parcelasAtrasadasQuery) {
                $write('<table border="1">');
                $write('<tr><th colspan="4">Parcelas atrasadas do mes</th></tr>');
                $write('<tr><th>Cliente</th><th>Parcela</th><th>Valor</th><th>Vencimento</th></tr>');
                (clone $parcelasAtrasadasQuery)->orderBy('id')->chunk(500, function ($rows) use ($write) {
                    foreach ($rows as $p) {
                        $write('<tr><td>' . htmlspecialchars($p->emprestimo?->cliente?->nome ?? '') . '</td><td>#' . $p->numero . '</td><td>' . number_format($p->valor ?? 0, 2, ',', '.') . '</td><td>' . ($p->vencimento?->format('Y-m-d') ?? '') . '</td></tr>');
                    }
                });
                $write('</table><br>');
            }

            if ($hasDespesas && $despesasQuery) {
                $write('<table border="1">');
                $write('<tr><th colspan="5">Despesas do mes</th></tr>');
                $write('<tr><th>Nome</th><th>Categoria</th><th>Valor</th><th>Vencimento</th><th>Pago</th></tr>');
                (clone $despesasQuery)->orderBy('id')->chunk(500, function ($rows) use ($write) {
                    foreach ($rows as $d) {
                        $write('<tr><td>' . htmlspecialchars($d->nome) . '</td><td>' . ($d->categoria ?? '') . '</td><td>' . number_format($d->valor ?? 0, 2, ',', '.') . '</td><td>' . ($d->vencimento?->format('Y-m-d') ?? '') . '</td><td>' . ($d->pago ? 'Sim' : 'Nao') . '</td></tr>');
                    }
                });
                $write('</table><br>');
            }

            $write('</body></html>');
            fclose($out);
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
// Store novo item do resumo
    public function storeResumo(Request $request)
    {
        $data = $request->validate([
            'key' => 'nullable|string|max:100',
            'nome' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'tipo' => 'nullable|in:currency,number,text',
            'cor' => 'nullable|string|max:30',
        ]);

        // gera key simples se não informado
        if (empty($data['key'])) {
            $data['key'] = \Str::slug($data['nome']);
        }

        // define ordem máxima +1
        $data['ordem'] = ResumoItem::max('ordem') + 1;

        ResumoItem::create($data);

        return redirect()->route('relatorios.inadimplencia')->with('success', 'Item do resumo adicionado.');
    }

    // Atualiza item do resumo
    public function updateResumo(Request $request, ResumoItem $item)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'tipo' => 'nullable|in:currency,number,text',
            'cor' => 'nullable|string|max:30',
        ]);

        $item->update($data);

        return redirect()->route('relatorios.inadimplencia')->with('success', 'Item do resumo atualizado.');
    }

    // Remove item do resumo
    public function destroyResumo(ResumoItem $item)
    {
        $item->delete();
        return redirect()->route('relatorios.inadimplencia')->with('success', 'Item do resumo removido.');
    }

    // Export CSV (respeita filtro period/q)
    public function export(Request $request): StreamedResponse
    {
        if (! Schema::hasTable('despesas')) {
            abort(404, 'Tabela despesas não encontrada.');
        }

        $period = $request->query('period', null);
        $q = trim((string) $request->query('q', ''));

        $query = Despesa::query();

        if ($period) {
            [$ano, $mes] = explode('-', $period) + [null, null];
            if ($ano && $mes) {
                $query->whereYear('vencimento', $ano)->whereMonth('vencimento', $mes);
            }
        }

        if ($q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('nome', 'like', "%{$q}%")
                   ->orWhere('categoria', 'like', "%{$q}%");
            });
        }

        $filename = 'despesas_' . ($period ?? 'todas') . '.csv';

        $response = response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // cabeçalho CSV
            fputcsv($handle, ['Nome', 'Categoria', 'Valor', 'Pago', 'Vencimento']);
            $query->orderByDesc('vencimento')->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->nome,
                        $r->categoria,
                        number_format($r->valor, 2, '.', ''), // valor com ponto para CSV
                        $r->pago ? 'Sim' : 'Não',
                        $r->vencimento?->format('Y-m-d') ?? '',
                    ]);
                }
            });
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);

        return $response;
    }
}



