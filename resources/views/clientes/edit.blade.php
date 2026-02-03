@extends('adminlte::page')

@section('title', 'Editar cliente')

@section('content_header')
    <h1>Editar cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('clientes.update', $cliente) }}">
                @csrf @method('PUT')

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control" value="{{ old('nome', $cliente->nome) }}" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Documento</label>
                        <input type="text" name="documento" class="form-control" value="{{ old('documento', $cliente->documento) }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tipo</label>
                        <select name="tipo_documento" class="form-control">
                            <option value="cpf" @selected(old('tipo_documento', $cliente->tipo_documento)==='cpf')>CPF</option>
                            <option value="cnpj" @selected(old('tipo_documento', $cliente->tipo_documento)==='cnpj')>CNPJ</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $cliente->email) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Telefone</label>
                        <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $cliente->telefone) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Renda mensal</label>
                        <input type="number" step="0.01" name="renda_mensal" class="form-control" value="{{ old('renda_mensal', $cliente->renda_mensal) }}">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="form-group col-md-2">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control">
                            <option value="1" @selected(old('ativo', $cliente->ativo)==1)>Sim</option>
                            <option value="0" @selected(old('ativo', $cliente->ativo)==0)>Não</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                        <a href="{{ route('clientes.index') }}" class="btn btn-default">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop