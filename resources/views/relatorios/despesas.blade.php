@extends('adminlte::page')

@section('title', 'Despesas Gerais')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Despesas Gerais</h1>
        <a href="{{ route('relatorios.emprestimos') }}" class="btn btn-default btn-sm">Atualizar</a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.emprestimos') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar despesa ou categoria">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">Período</label>
                    <input type="month" name="period" value="{{ request('period') }}" class="form-control">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">Buscar</button>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('relatorios.despesas.export') }}?{{ http_build_query(request()->query()) }}" class="btn btn-outline-success">Exportar CSV</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="POST" action="{{ route('relatorios.despesas.store') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label>Despesa</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label>Valor</label>
                    <input type="number" step="0.01" name="valor" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label>Data</label>
                    <input type="date" name="vencimento" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Categoria</label>
                    <input type="text" name="categoria" class="form-control">
                </div>
                <div class="col-md-1">
                    <label>Pago</label>
                    <select name="pago" class="form-control">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success w-100">Adicionar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>DESPESAS GERAIS</th>
                        <th class="text-right">R$</th>
                        <th class="text-center">PAGO</th>
                        <th class="text-right">DATA</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($despesas as $d)
                        <tr style="background: {{ $d->cor ?? 'transparent' }};">
                            <td>{{ $d->nome }}</td>
                            <td class="text-right">{{ number_format($d->valor,2,',','.') }}</td>
                            <td class="text-center">{{ $d->pago ? 'OK' : '' }}</td>
                            <td class="text-right">{{ $d->vencimento?->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-right">
                                <a href="{{ route('relatorios.despesas.edit', $d) }}" class="btn btn-sm btn-outline-secondary">Editar</a>

                                <form action="{{ route('relatorios.despesas.toggle', $d) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm {{ $d->pago ? 'btn-outline-warning' : 'btn-success' }}">
                                        {{ $d->pago ? 'Desmarcar' : 'Marcar pago' }}
                                    </button>
                                </form>

                                <form action="{{ route('relatorios.despesas.destroy', $d) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover despesa?')">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">Nenhuma despesa registrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>Total: <strong>R$ {{ number_format($total ?? 0, 2, ',', '.') }}</strong></div>
            <div>{{ $despesas->links() }}</div>
        </div>
    </div>
@stop