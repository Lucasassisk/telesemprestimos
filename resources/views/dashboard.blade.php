@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Financeiro</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <div class="text-uppercase text-xs opacity-80">Valor emprestado</div>
                <div class="display-6 font-weight-bold">R$ {{ number_format($valor_emprestado ?? 0, 2, ',', '.') }}</div>
                <div class="text-xs opacity-75">Atualizado: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign" style="font-size:1.6rem;opacity:0.35;"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <div class="text-uppercase text-xs opacity-80">Lucro (juros)</div>
                <div class="display-6 font-weight-bold">R$ {{ number_format($lucro_realizado ?? 0, 2, ',', '.') }}</div>
                <div class="text-xs opacity-75">Atualizado: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line" style="font-size:1.6rem;opacity:0.35;"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <div class="text-uppercase text-xs opacity-80">Em atraso</div>
                <div class="display-6 font-weight-bold">{{ number_format($parcelas_em_atraso_count ?? 0) }} parcelas</div>
                <div class="text-xs opacity-75">Atualizado: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-times" style="font-size:1.6rem;opacity:0.35;"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <div class="text-uppercase text-xs opacity-80">Valor em atraso</div>
                <div class="display-6 font-weight-bold">R$ {{ number_format($parcelas_em_atraso_valor ?? 0, 2, ',', '.') }}</div>
                <div class="text-xs opacity-75">Atualizado: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle" style="font-size:1.6rem;opacity:0.35;"></i>
            </div>
        </div>
    </div>
</div>
    <div class="row mt-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Empréstimos por mês (últimos 12 meses)</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartDisbursed" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Distribuição por status</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartStatus" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Totais</h5>
                    <p>Total clientes: <strong>{{ number_format($total_clientes ?? 0) }}</strong></p>
                    <p>Total empréstimos: <strong>{{ number_format($total_emprestimos ?? 0) }}</strong></p>
                    <p>Valor bruto total: <strong>R$ {{ number_format($valor_bruto_total ?? 0, 2, ',', '.') }}</strong></p>
                    <p>Valor líquido total: <strong>R$ {{ number_format($valor_liquido_total ?? 0, 2, ',', '.') }}</strong></p>
                    <p>Total recebido: <strong>R$ {{ number_format($total_recebido ?? 0, 2, ',', '.') }}</strong></p>
                    <p>Juros esperado (total): <strong>R$ {{ number_format($juros_total_esperado ?? 0, 2, ',', '.') }}</strong></p>
                    <p>Juros recebido: <strong>R$ {{ number_format($juros_recebido ?? 0, 2, ',', '.') }}</strong></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top 5 devedores (valor em atraso)</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th class="text-right">Devido (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topDevedores as $d)
                                <tr>
                                    <td>{{ $d->nome }}</td>
                                    <td class="text-right">{{ number_format($d->devido,2,',','.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">Nenhum débito em atraso.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Empréstimos por mês (valor desembolsado)
            (function() {
                const labels = {!! json_encode($monthlyLabels ?? []) !!};
                const data = {!! json_encode($monthlyData ?? []) !!};
                const ctx = document.getElementById('chartDisbursed').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'R$ emprestado',
                            data: data,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0,123,255,0.1)',
                            fill: true,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            })();

            // Distribuição por status
            (function() {
                const dataMap = {!! json_encode($statusCounts ?? []) !!};
                const labels = Object.keys(dataMap);
                const data = Object.values(dataMap);
                const colors = ['#28a745','#ffc107','#17a2b8','#dc3545','#6c757d'];
                const ctx = document.getElementById('chartStatus').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: { labels: labels, datasets: [{ data: data, backgroundColor: colors.slice(0, labels.length) }] },
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                });
            })();
        </script>
    @endpush

@stop
