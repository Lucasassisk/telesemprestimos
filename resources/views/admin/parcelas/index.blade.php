@extends('adminlte::page')

@section('title', 'Parcelas')

@section('content_header')
    <div class="text-muted text-sm mb-1">Dashboard &gt; Parcelas</div>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Parcelas (por cliente)</h1>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('parcelas.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <label for="parcelas-search" class="text-sm text-muted mb-1">Buscar</label>
                        <div class="input-group">
                            <input id="parcelas-search" type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Pesquisar por nome ou CPF">
                            <div class="input-group-append">
                                <button class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto mt-2 mt-md-0">
                        <a href="{{ route('parcelas.index') }}" class="btn btn-default">Limpar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="text-right">Parcelas em aberto</th>
                        <th class="text-right">Valor em aberto</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientsWithDebt as $c)
                        <tr>
                            <td>{{ $c->nome }}</td>
                            <td class="text-right">{{ $c->aberto_count }}</td>
                            <td class="text-right">R$ {{ number_format($c->aberto_valor ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">
                                <a href="{{ route('parcelas.por_cliente', $c->id) }}" class="btn btn-sm btn-outline-primary">Ver parcelas</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Nenhuma parcela em aberto encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $clientsWithDebt->links() }}
        </div>
    </div>
@stop
