<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Emprestimo;
use Illuminate\Support\Facades\DB;
use App\Models\Parcela;
use App\Models\Despesa;
use App\Services\SystemNotificationService;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $total_clientes = Cliente::count();
        $total_emprestimos = Emprestimo::count();
        $valor_bruto_total = (float) Emprestimo::sum('valor_bruto');
        $valor_liquido_total = (float) Emprestimo::sum('valor_liquido');

        // Valor emprestado (desembolso) - aqui usamos o bruto como referência
        $valor_emprestado = $valor_bruto_total;

        // Juros: total esperado (somatório de juros de todas as parcelas) e recebido (parcelas pagas)
        $juros_total_esperado = (float) DB::table('parcelas')->sum('juros');
        $juros_recebido = (float) DB::table('parcelas')->where('pago', true)->sum('juros');

        // Lucro simples: juros já recebidos (pode ser adaptado para descontar custos)
        $lucro_realizado = $juros_recebido;

        // Parcelas em atraso (não pagas e vencimento < hoje)
        $parcelas_em_atraso_count = Parcela::where('pago', false)->whereDate('vencimento', '<', now()->toDateString())->count();
        $parcelas_em_atraso_valor = (float) Parcela::where('pago', false)->whereDate('vencimento', '<', now()->toDateString())->sum('valor');

        $todayKey = now()->format('Y-m-d');
        if ($parcelas_em_atraso_count > 0) {
            SystemNotificationService::createOnce(
                'parcelas_atrasadas_' . $todayKey,
                'Parcelas em atraso',
                $parcelas_em_atraso_count . ' parcela(s) vencida(s)',
                'warning',
                route('parcelas.vencidas')
            );
        }

        if (Schema::hasTable('despesas')) {
            $despesas_vencidas_count = Despesa::where('pago', false)->whereDate('vencimento', '<', now()->toDateString())->count();
            if ($despesas_vencidas_count > 0) {
                SystemNotificationService::createOnce(
                    'despesas_atrasadas_' . $todayKey,
                    'Despesas vencidas',
                    $despesas_vencidas_count . ' despesa(s) em atraso',
                    'danger',
                    route('relatorios.emprestimos')
                );
            }
        }

        // Total recebido (principal + juros) — soma das parcelas pagas
        $total_recebido = (float) DB::table('parcelas')->where('pago', true)->sum('valor');

        // Próximas 30 dias - parcelas a vencer
        $proximas_30_dias_count = Parcela::where('pago', false)
            ->whereBetween('vencimento', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();

        // Top 5 devedores (clientes com maior valor em atraso)
        $topDevedores = DB::table('parcelas')
            ->join('emprestimos', 'parcelas.emprestimo_id', '=', 'emprestimos.id')
            ->join('clientes', 'emprestimos.cliente_id', '=', 'clientes.id')
            ->where('parcelas.pago', false)
            ->whereDate('parcelas.vencimento', '<', now()->toDateString())
            ->selectRaw('clientes.id, clientes.nome, SUM(parcelas.valor) as devido')
            ->groupBy('clientes.id', 'clientes.nome')
            ->orderByDesc('devido')
            ->limit(5)
            ->get();

        $solicitacoes = Emprestimo::where('status', 'pendente')->count();
        $contratos_ativos = Emprestimo::whereIn('status', ['aprovado', 'ativo', 'contratado'])->count();

        // Monthly disbursed (last 12 months)
        $months = [];
        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $dt = \Carbon\Carbon::now()->subMonths($i);
            $months[] = $dt->format('Y-m');
            $monthlyLabels[] = $dt->format('M/Y');
        }

        $disbursed = Emprestimo::selectRaw("DATE_FORMAT(data_contratacao, '%Y-%m') as ym, SUM(valor_bruto) as total")
            ->whereNotNull('data_contratacao')
            ->whereBetween('data_contratacao', [\Carbon\Carbon::now()->subMonths(11)->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])
            ->groupBy('ym')
            ->pluck('total', 'ym')
            ->toArray();

        foreach ($months as $m) {
            $monthlyData[] = isset($disbursed[$m]) ? (float) $disbursed[$m] : 0.0;
        }

        // Status counts for doughnut
        $statusCounts = Emprestimo::selectRaw('status, COUNT(*) as cnt')->groupBy('status')->pluck('cnt', 'status')->toArray();

        // Upcoming 6 months payable from parcelas
        $upcomingLabels = [];
        $upcomingData = [];
        for ($i = 0; $i < 6; $i++) {
            $dt = \Carbon\Carbon::now()->addMonths($i);
            $ym = $dt->format('Y-m');
            $upcomingLabels[] = $dt->format('M/Y');
            $upcomingData[$ym] = 0.0;
        }

        $parcelas = \DB::table('parcelas')
            ->selectRaw("DATE_FORMAT(vencimento, '%Y-%m') as ym, SUM(valor) as total")
            ->where('pago', false)
            ->whereBetween('vencimento', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->addMonths(5)->endOfMonth()])
            ->groupBy('ym')
            ->pluck('total', 'ym')
            ->toArray();

        foreach (array_keys($upcomingData) as $ym) {
            $upcomingData[$ym] = isset($parcelas[$ym]) ? (float) $parcelas[$ym] : 0.0;
        }

        return view('dashboard', compact(
            'total_clientes',
            'total_emprestimos',
            'valor_bruto_total',
            'valor_liquido_total',
            'valor_emprestado',
            'juros_total_esperado',
            'juros_recebido',
            'lucro_realizado',
            'parcelas_em_atraso_count',
            'parcelas_em_atraso_valor',
            'total_recebido',
            'proximas_30_dias_count',
            'topDevedores',
            'solicitacoes',
            'contratos_ativos',
            'monthlyLabels',
            'monthlyData',
            'statusCounts',
            'upcomingLabels',
            'upcomingData'
        ));
    }
}
