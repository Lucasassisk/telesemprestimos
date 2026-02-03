@extends('adminlte::page')

@section('title', 'Cliente')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $cliente->nome }}</h1>
        <div>
            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-primary btn-sm">Editar</a>
            <a href="{{ route('clientes.index') }}" class="btn btn-default btn-sm">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Documento</dt><dd class="col-sm-9">{{ $cliente->documento ?? '-' }}</dd>
                <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $cliente->email ?? '-' }}</dd>
                <dt class="col-sm-3">Telefone</dt><dd class="col-sm-9">{{ $cliente->telefone ?? '-' }}</dd>
                <dt class="col-sm-3">Renda mensal</dt><dd class="col-sm-9">{{ number_format($cliente->renda_mensal ?? 0, 2, ',', '.') }}</dd>
                <dt class="col-sm-3">Disponível</dt><dd class="col-sm-9">{{ number_format($cliente->disponivel ?? 0, 2, ',', '.') }}</dd>
            </dl>

            <h5 class="mt-4">Empréstimos</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Valor bruto</th>
                            <th>Valor líquido</th>
                            <th>Juros %</th>
                            <th>Parcelas</th>
                            <th>Data disponível</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cliente->emprestimos as $e)
                            <tr>
                                <td>{{ number_format($e->valor_bruto,2,',','.') }}</td>
                                <td>{{ number_format($e->valor_liquido ?? 0,2,',','.') }}</td>
                                <td>{{ $e->juros_percent }}</td>
                                <td>{{ $e->parcelas }}</td>
                                <td>{{ $e->data_disponivel?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $e->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">Nenhum empréstimo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Historico do Cliente</h5>
        </div>
        <div class="card-body">
            @if(($timeline ?? collect())->isEmpty())
                <p class="text-muted">Sem historico registrado.</p>
            @else
                <ul class="list-unstyled">
                    @foreach($timeline as $t)
                        <li class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $t['title'] }}</strong>
                                <span class="text-muted text-sm">{{ $t['at']?->format('d/m/Y H:i') ?? '-' }}</span>
                            </div>
                            @if(!empty($t['details']))
                                <div class="text-muted text-sm">{{ $t['details'] }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@stop
