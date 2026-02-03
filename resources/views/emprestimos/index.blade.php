@extends('adminlte::page')

@section('title', 'Empréstimos')

@section('content_header')
    <div class="text-muted text-sm mb-1">Dashboard &gt; Empréstimos</div>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Empréstimos</h1>
        <a href="{{ route('emprestimos.create') }}" class="btn btn-primary">Novo empréstimo</a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('emprestimos.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <label for="emprestimos-search" class="text-sm text-muted mb-1">Buscar</label>
                        <div class="input-group">
                            <input id="emprestimos-search" type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Pesquisar por nome ou CPF">
                            <div class="input-group-append">
                                <button class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto mt-2 mt-md-0">
                        <a href="{{ route('emprestimos.index') }}" class="btn btn-default">Limpar</a>
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
                        <th>ID</th>
                        <th>Nome completo</th>
                        <th>CPF</th>
                        <th>Total Emprestado</th>
                        <th>Lucro</th>
                        <th>Juros %</th>
                        <th>Data Início</th>
                        <th>Data Final</th>
                        <th>Parcelas</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($emprestimos as $emprestimo)
                        <tr>
                            <td>{{ $emprestimo->id }}</td>
                            <td>{{ $emprestimo->cliente->nome ?? '—' }}</td>
                            <td>{{ $emprestimo->cliente->documento ?? '—' }}</td>
                            <td>R$ {{ number_format($emprestimo->valor_bruto ?? 0, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($emprestimo->parcelas_juros_sum ?? 0, 2, ',', '.') }}</td>
                            <td>{{ $emprestimo->juros_percent }}</td>
                            <td>{{ $emprestimo->data_disponivel?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                {{ optional(\Carbon\Carbon::parse($emprestimo->parcelas_vencimento_max))->format('d/m/Y') ?? '-' }}
                            </td>
                            <td>{{ $emprestimo->parcelas }}</td>
                            <td>{{ ucfirst($emprestimo->status) }}</td>
                            <td class="text-right">
                                <a href="{{ route('emprestimos.show', $emprestimo) }}" class="btn btn-sm btn-outline-secondary mb-1 btn-block">Ver</a>
                                <a href="{{ route('emprestimos.edit', $emprestimo) }}" class="btn btn-sm btn-outline-primary mb-1 btn-block">Editar</a>
                                <form action="{{ route('emprestimos.destroy', $emprestimo) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger mb-1 btn-block" onclick="return confirm('Confirmar exclusão?')">Excluir</button>
                                </form>

                                <!-- botão WhatsApp (abre em nova aba) -->
                                <a href="{{ route('emprestimos.whatsapp', $emprestimo) }}"
                                   class="btn btn-success btn-sm btn-block"
                                   title="Enviar lembrete via WhatsApp" target="_blank" rel="noopener">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $emprestimos->links() }}
        </div>
    </div>
@stop
