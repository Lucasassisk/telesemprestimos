@extends('adminlte::page')

@section('title', 'Contratos Quitados')

@section('content_header')
    <h1>Contratos Quitados</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="text-right"># Empréstimos</th>
                        <th class="text-right">Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $c)
                        <tr>
                            <td>{{ $c->nome }}</td>
                            <td class="text-right">{{ $c->emprestimos_count }}</td>
                            <td class="text-right">
                                @if($c->nao_quitados_count == 0 && $c->emprestimos_count > 0)
                                    <span class="badge bg-success">Quitado</span>
                                @elseif($c->emprestimos_count == 0)
                                    <span class="text-muted">Sem empréstimos</span>
                                @else
                                    <span class="badge bg-warning">Em aberto</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('clientes.show', $c) }}" class="btn btn-sm btn-outline-secondary">Ver cliente</a>
                                <a href="{{ route('parcelas.por_cliente', $c->id) }}" class="btn btn-sm btn-outline-primary">Ver parcelas</a>

                                @if($c->nao_quitados_count > 0)
                                    <form action="{{ route('parcelas.quitar', $c) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Confirmar quitação de todas as parcelas em aberto deste cliente?')">Quitar dívida</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Nenhum cliente encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $clients->links() }}
        </div>
    </div>
@stop