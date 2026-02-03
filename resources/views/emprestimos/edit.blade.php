@extends('adminlte::page')

@section('title', 'Editar Empréstimo')

@section('content_header')
    <h1>Editar Empréstimo #{{ $emprestimo->id }}</h1>
@stop

@section('content')
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><strong>Dados do Cliente</strong></div>
                <div class="card-body">
                    <p><strong>Nome:</strong> {{ $emprestimo->cliente->nome ?? '-' }}</p>
                    <p><strong>Documento (CPF/CNPJ):</strong> {{ $emprestimo->cliente->documento ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $emprestimo->cliente->email ?? '-' }}</p>
                    <p><strong>Telefone:</strong> {{ $emprestimo->cliente->telefone ?? '-' }}</p>
                    <p><strong>Endereço:</strong> {{ $emprestimo->cliente->endereco ?? '-' }}</p>
                    <p><strong>Renda mensal:</strong> R$ {{ number_format($emprestimo->cliente->renda_mensal ?? 0,2,',','.') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><strong>Dados da Solicitação (última)</strong></div>
                <div class="card-body">
                    @if(isset($solicitacao))
                        <p><strong>Nome:</strong> {{ $solicitacao->nome }}</p>
                        <p><strong>CPF:</strong> {{ $solicitacao->cpf ?? '-' }}</p>
                        <p><strong>Email:</strong> {{ $solicitacao->email ?? '-' }}</p>
                        <p><strong>Telefone:</strong> {{ $solicitacao->telefone_celular ?? '-' }}</p>
                        <p><strong>Tipo residência:</strong> {{ $solicitacao->tipo_residencia ?? '-' }}</p>
                        <p><strong>Observações:</strong> {{ $solicitacao->observacoes ?? '-' }}</p>
                        <p><strong>Arquivos:</strong>
                            @if($solicitacao->contracheque_path)
                                <a href="{{ Storage::disk('public')->url(Str::after($solicitacao->contracheque_path, 'public/')) }}" target="_blank">Contra‑cheque</a>
                            @endif
                            @if($solicitacao->identidade_path)
                                — <a href="{{ Storage::disk('public')->url(Str::after($solicitacao->identidade_path, 'public/')) }}" target="_blank">Identidade</a>
                            @endif
                            @if($solicitacao->comprovante_endereco_path)
                                — <a href="{{ Storage::disk('public')->url(Str::after($solicitacao->comprovante_endereco_path, 'public/')) }}" target="_blank">Comprovante</a>
                            @endif
                        </p>
                    @else
                        <p class="text-muted">Nenhuma solicitação vinculada encontrada.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('emprestimos.update', $emprestimo) }}">
                @csrf @method('PUT')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Cliente</label>
                        <select name="cliente_id" class="form-control" required>
                            @foreach($clientes as $id => $nome)
                                <option value="{{ $id }}" @selected(old('cliente_id', $emprestimo->cliente_id) == $id)>{{ $nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Valor bruto</label>
                        <input type="number" name="valor_bruto" step="0.01" class="form-control" value="{{ old('valor_bruto', $emprestimo->valor_bruto) }}" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Juros (%)</label>
                        <input type="number" name="juros_percent" step="0.01" class="form-control" value="{{ old('juros_percent', $emprestimo->juros_percent) }}">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="form-group col-md-3">
                        <label>Parcelas</label>
                        <input type="number" name="parcelas" class="form-control" value="{{ old('parcelas', $emprestimo->parcelas) }}" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Data disponível</label>
                        <input type="date" name="data_disponivel" class="form-control" value="{{ old('data_disponivel', $emprestimo->data_disponivel?->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Data contratação</label>
                        <input type="date" name="data_contratacao" class="form-control" value="{{ old('data_contratacao', $emprestimo->data_contratacao?->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="pendente" @selected(old('status', $emprestimo->status)=='pendente')>Pendente</option>
                            <option value="aprovado" @selected(old('status', $emprestimo->status)=='aprovado')>Aprovado</option>
                            <option value="ativo" @selected(old('status', $emprestimo->status)=='ativo')>Ativo</option>
                            <option value="quitado" @selected(old('status', $emprestimo->status)=='quitado')>Quitado</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button class="btn btn-primary">Salvar</button>
                        <a href="{{ route('emprestimos.show', $emprestimo) }}" class="btn btn-default">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
<div>
    <!-- Be present above all else. - Naval Ravikant -->
</div>
