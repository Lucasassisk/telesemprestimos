@extends('adminlte::page')

@section('title', 'Registrar Pagamento')

@section('content_header')
    <h1>Registrar Pagamento</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('pagamentos.store') }}">
                @csrf

                <div class="form-group">
                    <label>Parcela</label>
                    <select name="parcela_id" class="form-control" required>
                        <option value="">— selecione —</option>
                        @foreach($parcelas as $p)
                            <option value="{{ $p->id }}">
                                #{{ $p->numero }} — Empréstimo #{{ $p->emprestimo->id ?? '-' }} — {{ $p->emprestimo->cliente->nome ?? '-' }} — {{ $p->vencimento?->format('d/m/Y') ?? '-' }} — R$ {{ number_format($p->valor,2,',','.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mt-2">
                    <label>Valor recebido</label>
                    <input type="number" name="valor" step="0.01" class="form-control" required>
                </div>

                <div class="form-group mt-2">
                    <label>Observação</label>
                    <input type="text" name="observacao" class="form-control">
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Registrar pagamento</button>
                    <a href="{{ route('parcelas.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop
