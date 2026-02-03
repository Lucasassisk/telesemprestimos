@extends('adminlte::page')

@section('title', 'Parcelas Vencidas')

@section('content_header')
    <div class="text-muted text-sm mb-1">Dashboard &gt; Parcelas &gt; Vencidas</div>
    <h1>Parcelas Vencidas</h1>
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
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parcelas as $parcela)
                        <tr>
                            <td>{{ $parcela->numero }}</td>
                            <td>#{{ $parcela->emprestimo->id ?? '-' }}</td>
                            <td>{{ $parcela->emprestimo->cliente->nome ?? '-' }}</td>
                            <td>{{ $parcela->vencimento?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ number_format($parcela->valor,2,',','.') }}</td>
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
                        <tr><td colspan="7" class="text-muted">Nenhuma parcela vencida encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $parcelas->links() }}
            </div>
        </div>
    </div>
@stop
