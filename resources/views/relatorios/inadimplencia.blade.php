@extends('adminlte::page')

@section('title', 'Relatorio Mensal')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Relatorio Mensal</h1>
        <div class="d-flex align-items-center">
            <form method="GET" action="{{ route('relatorios.inadimplencia') }}" class="d-flex align-items-center gap-2">
                <input type="month" name="period" value="{{ $period }}" class="form-control form-control-sm" style="min-width: 140px;">
                <button class="btn btn-primary btn-sm">Filtrar</button>
            </form>
            <a class="btn btn-success btn-sm ml-2" href="{{ route('relatorios.inadimplencia.export', ['period' => $period]) }}">Exportar Excel (.xls)</a>
        </div>
    </div>
@stop

@section('content')
    <div class="mb-3 text-muted">
        Periodo: <strong>{{ $periodLabel }}</strong>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $solicitacoesCount }}</h3>
                    <p>Solicitacoes no mes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $emprestimosCount }}</h3>
                    <p>Emprestimos contratados</p>
                </div>
                <div class="small text-center mb-2">
                    Bruto: R$ {{ number_format($emprestimosValorBruto, 2, ',', '.') }}
                    <br>
                    Liquido: R$ {{ number_format($emprestimosValorLiquido, 2, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $pagamentosCount }}</h3>
                    <p>Pagamentos recebidos</p>
                </div>
                <div class="small text-center mb-2">
                    Total: R$ {{ number_format($pagamentosValor, 2, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $parcelasAtrasadasCount }}</h3>
                    <p>Parcelas atrasadas</p>
                </div>
                <div class="small text-center mb-2">
                    Total: R$ {{ number_format($parcelasAtrasadasValor, 2, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $despesasCount }}</h3>
                    <p>Despesas do mes</p>
                </div>
                <div class="small text-center mb-2">
                    Total: R$ {{ number_format($despesasValor, 2, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><h5>Solicitacoes recentes do mes</h5></div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Email</th>
                        <th>Criado em</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitacoesRecentes as $s)
                        <tr>
                            <td>{{ $s->nome }}</td>
                            <td>{{ $s->cpf ?? '-' }}</td>
                            <td>{{ $s->email ?? '-' }}</td>
                            <td>{{ $s->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Nenhuma solicitacao no periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><h5>Emprestimos recentes do mes</h5></div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Valor bruto</th>
                        <th>Valor liquido</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emprestimosRecentes as $e)
                        <tr>
                            <td>{{ $e->id }}</td>
                            <td>{{ $e->cliente?->nome ?? '-' }}</td>
                            <td>R$ {{ number_format($e->valor_bruto ?? 0, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($e->valor_liquido ?? 0, 2, ',', '.') }}</td>
                            <td>{{ $e->data_contratacao?->format('d/m/Y') ?? $e->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">Nenhum emprestimo no periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><h5>Pagamentos recentes do mes</h5></div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Parcela</th>
                        <th>Valor</th>
                        <th>Pago em</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagamentosRecentes as $p)
                        <tr>
                            <td>{{ $p->emprestimo?->cliente?->nome ?? '-' }}</td>
                            <td>#{{ $p->numero }}</td>
                            <td>R$ {{ number_format($p->valor ?? 0, 2, ',', '.') }}</td>
                            <td>{{ $p->pago_em?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Nenhum pagamento no periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><h5>Parcelas atrasadas do mes</h5></div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Parcela</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parcelasAtrasadasRecentes as $p)
                        <tr>
                            <td>{{ $p->emprestimo?->cliente?->nome ?? '-' }}</td>
                            <td>#{{ $p->numero }}</td>
                            <td>R$ {{ number_format($p->valor ?? 0, 2, ',', '.') }}</td>
                            <td>{{ $p->vencimento?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Nenhuma parcela atrasada no periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><h5>Despesas do mes</h5></div>
        <div class="card-body table-responsive p-0">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($despesasRecentes as $d)
                        <tr>
                            <td>{{ $d->nome }}</td>
                            <td>{{ $d->categoria ?? '-' }}</td>
                            <td>R$ {{ number_format($d->valor ?? 0, 2, ',', '.') }}</td>
                            <td>{{ $d->vencimento?->format('d/m/Y') }}</td>
                            <td>{{ $d->pago ? 'Sim' : 'Nao' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">Nenhuma despesa no periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
