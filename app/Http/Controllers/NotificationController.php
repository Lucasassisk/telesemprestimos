<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use App\Models\Parcela;
use App\Models\Solicitacao;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function navbar(Request $request)
    {
        $today = now()->toDateString();

        $items = collect();
        $unread = 0;
        if (Schema::hasTable('system_notifications')) {
            $items = SystemNotification::orderByDesc('created_at')->limit(10)->get();
            $unread = SystemNotification::whereNull('read_at')->count();
        }

        $solicitacoesPendentes = 0;
        if (Schema::hasTable('solicitacoes')) {
            $solicitacoesPendentes = Solicitacao::where(function ($q) {
                    $q->whereNull('status')->orWhere('status', 'pendente');
                })
                ->count();
        }

        $parcelasAtrasadas = Schema::hasTable('parcelas')
            ? Parcela::where('pago', false)->whereDate('vencimento', '<', $today)->count()
            : 0;

        $pagamentosHoje = Schema::hasTable('parcelas')
            ? Parcela::where('pago', true)->whereDate('pago_em', $today)->count()
            : 0;

        $despesasVencidas = Schema::hasTable('despesas')
            ? Despesa::where('pago', false)->whereDate('vencimento', '<', $today)->count()
            : 0;

        $total = $unread > 0 ? $unread : ($solicitacoesPendentes + $parcelasAtrasadas + $pagamentosHoje + $despesasVencidas);

        $dropdown = view('partials.notifications-dropdown', [
            'total' => $total,
            'items' => $items,
            'solicitacoesPendentes' => $solicitacoesPendentes,
            'parcelasAtrasadas' => $parcelasAtrasadas,
            'pagamentosHoje' => $pagamentosHoje,
            'despesasVencidas' => $despesasVencidas,
        ])->render();

        return response()->json([
            'label' => $total > 0 ? $total : '',
            'label_color' => $total > 0 ? 'danger' : 'secondary',
            'icon_color' => $total > 0 ? 'warning' : 'muted',
            'dropdown' => $dropdown,
        ]);
    }
}
