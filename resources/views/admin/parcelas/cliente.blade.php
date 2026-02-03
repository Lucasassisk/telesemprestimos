@extends('adminlte::page')

@section('title', 'Parcelas de ' . ($cliente->nome ?? 'Cliente'))

@section('content_header')
    <div class="text-muted text-sm mb-1">Dashboard &gt; Parcelas &gt; {{ $cliente->nome }}</div>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Parcelas de {{ $cliente->nome }}</h1>
        <div>
            <a href="{{ route('parcelas.index') }}" class="btn btn-default btn-sm">Voltar</a>
            <form action="{{ route('parcelas.quitar', $cliente) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-danger btn-sm" onclick="return confirm('Confirmar quitação de todas as parcelas em aberto deste cliente?')">Quitar dívida</button>
            </form>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Empréstimo</th>
                        <th>Vencimento</th>
                        <th class="text-right">Valor</th>
                        <th class="text-right">Juros</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parcelas as $parcela)
                        <tr>
                            <td>{{ $parcela->id }}</td>
                            <td>#{{ $parcela->emprestimo->id ?? '-' }}</td>
                            <td>{{ $parcela->vencimento?->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-right">R$ {{ number_format($parcela->valor,2,',','.') }}</td>
                            <td class="text-right">R$ {{ number_format($parcela->juros,2,',','.') }}</td>
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

                                <!-- botão WhatsApp para a parcela -->
                                <a href="{{ route('parcelas.whatsapp', $parcela) }}"
                                   class="btn btn-success btn-sm"
                                   title="Enviar lembrete da parcela via WhatsApp" target="_blank" rel="noopener">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
