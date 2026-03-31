@extends('adminlte::page')

@section('title', 'Solicitações')

@section('content_header')
    <div class="text-muted text-sm mb-1">Dashboard &gt; Solicitações</div>
    <h1>Solicitações</h1>
@stop

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.solicitacoes.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <label for="solicitacoes-search" class="text-sm text-muted mb-1">Buscar</label>
                        <div class="input-group">
                            <input id="solicitacoes-search" type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Pesquisar por nome, CPF ou email">
                            <div class="input-group-append">
                                <button class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto mt-2 mt-md-0">
                        <a href="{{ route('admin.solicitacoes.index') }}" class="btn btn-default">Limpar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitacoes as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->nome }}</td>
                            <td>{{ $s->email ?? '-' }}</td>
                            <td>{{ $s->telefone_celular ?? '-' }}</td>
                            <td>{{ ucfirst($s->status) }}</td>
                            <td>{{ $s->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-right">
                                @if($s->contracheque_path)
                                    <a href="{{ route('admin.solicitacoes.download', [$s, 'contracheque']) }}" class="btn btn-sm btn-outline-primary">Contra‑cheque</a>
                                @endif
                                @if($s->identidade_path)
                                    <a href="{{ route('admin.solicitacoes.download', [$s, 'identidade']) }}" class="btn btn-sm btn-outline-secondary">Identidade</a>
                                @endif
                                @if($s->comprovante_endereco_path)
                                    <a href="{{ route('admin.solicitacoes.download', [$s, 'comprovante_endereco']) }}" class="btn btn-sm btn-outline-info">Comprovante</a>
                                @endif
                                @if($s->promissoria_path)
                                    <a href="{{ route('admin.solicitacoes.download', [$s, 'promissoria']) }}" class="btn btn-sm btn-outline-dark">Promissoria</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">Nenhuma solicitação encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $solicitacoes->links() }}
        </div>
    </div>
@stop
