@extends('adminlte::page')

@section('title', 'Editar Despesa')

@section('content_header')
    <h1>Editar Despesa</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('relatorios.despesas.update', $despesa) }}">
        @csrf @method('PUT')

        <div class="row g-2">
            <div class="col-md-6 mb-2">
                <label>Despesa</label>
                <input type="text" name="nome" class="form-control" value="{{ old('nome', $despesa->nome) }}" required>
            </div>
            <div class="col-md-3 mb-2">
                <label>Valor</label>
                <input type="number" step="0.01" name="valor" class="form-control" value="{{ old('valor', $despesa->valor) }}" required>
            </div>
            <div class="col-md-3 mb-2">
                <label>Data</label>
                <input type="date" name="vencimento" class="form-control" value="{{ old('vencimento', $despesa->vencimento?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3 mb-2">
                <label>Categoria</label>
                <input type="text" name="categoria" class="form-control" value="{{ old('categoria', $despesa->categoria) }}">
            </div>
            <div class="col-md-2 mb-2">
                <label>Pago</label>
                <select name="pago" class="form-control">
                    <option value="0" @selected(old('pago', $despesa->pago)==0)>Não</option>
                    <option value="1" @selected(old('pago', $despesa->pago)==1)>Sim</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label>Cor (hex ou nome)</label>
                <input type="text" name="cor" class="form-control" value="{{ old('cor', $despesa->cor) }}">
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary">Salvar</button>
            <a href="{{ route('relatorios.emprestimos') }}" class="btn btn-default ms-2">Cancelar</a>
        </div>
    </form>
@stop