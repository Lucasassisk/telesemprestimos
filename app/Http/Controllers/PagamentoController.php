<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Services\SystemNotificationService;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function index()
    {
        // Lista pagamentos (aqui mostramos parcelas pagas como histórico)
        $parcelasPagas = Parcela::with('emprestimo.cliente')
            ->where('pago', true)
            ->orderByDesc('pago_em')
            ->paginate(25);

        return view('pagamentos.index', compact('parcelasPagas'));
    }

    public function create()
    {
        // Seleciona parcelas em aberto para pagamento
        $parcelas = Parcela::with('emprestimo.cliente')
            ->where('pago', false)
            ->orderBy('vencimento')
            ->get();

        return view('pagamentos.create', compact('parcelas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parcela_id' => 'required|exists:parcelas,id',
            'valor' => 'required|numeric|min:0',
            'observacao' => 'nullable|string|max:255',
        ]);

        $parcela = Parcela::find($data['parcela_id']);
        if (! $parcela) {
            return redirect()->back()->withErrors(['parcela_id' => 'Parcela não encontrada.']);
        }

        // Marca parcela como paga
        $parcela->pago = true;
        $parcela->pago_em = now();
        $parcela->status = 'paga';
        $parcela->save();

        $parcela->load('emprestimo.cliente');

        SystemNotificationService::createOnce(
            'pagamento_' . $parcela->id,
            'Pagamento recebido',
            'Parcela #' . $parcela->numero . ' - ' . ($parcela->emprestimo?->cliente?->nome ?? 'Cliente'),
            'success',
            route('pagamentos.index')
        );

        return redirect()->route('parcelas.index')->with('success', 'Parcela marcada como paga.');
    }
}
