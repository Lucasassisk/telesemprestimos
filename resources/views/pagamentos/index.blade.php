@extends('adminlte::page')

@section('title', 'Histórico de Pagamentos')

@section('content_header')
    <h1>Histórico de Pagamentos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Empréstimo</th>
                        <th>Cliente</th>
                        <th>Parcela</th>
                        <th>Valor</th>
                        <th>Pago em</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parcelasPagas as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>#{{ $p->emprestimo->id ?? '-' }}</td>
                            <td>{{ $p->emprestimo->cliente->nome ?? '-' }}</td>
                            <td>#{{ $p->numero }}</td>
                            <td>R$ {{ number_format($p->valor,2,',','.') }}</td>
                            <td>{{ $p->pago_em?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">Nenhum pagamento registrado.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $parcelasPagas->links() }}
            </div>
        </div>
    </div>
@stop
