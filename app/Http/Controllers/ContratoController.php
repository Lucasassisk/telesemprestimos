<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function ativos() {
    return view('admin.contratos.ativos');
}

public function quitados()
    {
        // lista clientes com total de empréstimos e quantos NÃO estão quitados
        $clients = \App\Models\Cliente::withCount('emprestimos')
            ->withCount(['emprestimos as nao_quitados_count' => function ($q) {
                $q->where('status', '!=', 'quitado');
            }])
            ->orderBy('nome')
            ->paginate(25);

        return view('admin.contratos.quitados', compact('clients'));
    }

public function inadimplentes() {
    return view('admin.contratos.inadimplentes');
}

}
