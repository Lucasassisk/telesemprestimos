@extends('adminlte::page')

@section('title', 'Novo Empréstimo')

@section('content_header')
    <h1>Novo Empréstimo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('emprestimos.store') }}">
                @csrf

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Cliente</label>
                        <select name="cliente_id" class="form-control" required>
                            <option value="">— selecione —</option>
                            @foreach($clientes as $id => $nome)
                                <option value="{{ $id }}" @selected(old('cliente_id') == $id)>{{ $nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Valor bruto</label>
                        <input type="number" name="valor_bruto" step="0.01" class="form-control"
                               value="{{ old('valor_bruto') }}"
                               @if(!empty($minLoan)) min="{{ $minLoan }}" @endif
                               @if(!empty($maxLoan) && $maxLoan > 0) max="{{ $maxLoan }}" @endif
                               required>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Juros (%)</label>
                        <input type="number" name="juros_percent" step="0.01" class="form-control" value="{{ old('juros_percent', $defaultInterest ?? 0) }}">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="form-group col-md-3">
                        <label>Parcelas</label>
                        <input type="number" name="parcelas" class="form-control" value="{{ old('parcelas', 1) }}" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Data disponível</label>
                        <input type="date" name="data_disponivel" class="form-control" value="{{ old('data_disponivel') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Data contratação</label>
                        <input type="date" name="data_contratacao" class="form-control" value="{{ old('data_contratacao') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="pendente">Pendente</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="ativo">Ativo</option>
                            <option value="quitado">Quitado</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button class="btn btn-primary">Salvar</button>
                        <a href="{{ route('emprestimos.index') }}" class="btn btn-default">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
<div>
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
</div>
