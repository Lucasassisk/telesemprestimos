@extends('adminlte::page')

@section('title', 'Empréstimo')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Empréstimo #{{ $emprestimo->id }}</h1>
        <div>
            <a href="{{ route('emprestimos.edit', $emprestimo) }}" class="btn btn-primary btn-sm">Editar</a>
            <a href="{{ route('emprestimos.index') }}" class="btn btn-default btn-sm">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Cliente</dt><dd class="col-sm-9">{{ $emprestimo->cliente->nome ?? '-' }}</dd>
                <dt class="col-sm-3">Valor bruto</dt><dd class="col-sm-9">{{ number_format($emprestimo->valor_bruto,2,',','.') }}</dd>
                <dt class="col-sm-3">Valor líquido</dt><dd class="col-sm-9">{{ number_format($emprestimo->valor_liquido ?? 0,2,',','.') }}</dd>
                <dt class="col-sm-3">Juros %</dt><dd class="col-sm-9">{{ $emprestimo->juros_percent }}</dd>
                <dt class="col-sm-3">Parcelas</dt><dd class="col-sm-9">{{ $emprestimo->parcelas }}</dd>
                <dt class="col-sm-3">Data disponível</dt><dd class="col-sm-9">{{ $emprestimo->data_disponivel?->format('d/m/Y') ?? '-' }}</dd>
                <dt class="col-sm-3">Data contratação</dt><dd class="col-sm-9">{{ $emprestimo->data_contratacao?->format('d/m/Y') ?? '-' }}</dd>
                <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ ucfirst($emprestimo->status) }}</dd>
            </dl>
        </div>
    </div>
@stop

@section('content')
    @parent

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Parcelas</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Principal</th>
                        <th>Juros</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emprestimo->parcelas()->orderBy('numero')->get() as $parcela)
                        <tr>
                            <td>{{ $parcela->numero }}</td>
                            <td>{{ $parcela->vencimento?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ number_format($parcela->valor,2,',','.') }}</td>
                            <td>{{ number_format($parcela->principal,2,',','.') }}</td>
                            <td>{{ number_format($parcela->juros,2,',','.') }}</td>
                            <td>{{ $parcela->pago ? 'Paga' : ucfirst($parcela->status) }}</td>
                            <td class="text-right">
                                @if(! $parcela->pago)
                                    <form method="POST" action="{{ route('parcelas.pagar', $parcela) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Confirmar pagamento da parcela #{{ $parcela->numero }}?')">Pagar</button>
                                    </form>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">Nenhuma parcela gerada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
