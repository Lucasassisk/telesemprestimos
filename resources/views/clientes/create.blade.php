@extends('adminlte::page')

@section('title', 'Solicitação de Empréstimo')

@section('content')
<div class="max-w-3xl w-full mx-auto py-8 px-4">
    <div class="mb-4">
        <h1 class="text-2xl font-semibold">Formulário de Solicitação</h1>
        <p class="text-sm text-gray-600">Preencha seus dados para análise do pedido de empréstimo.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('solicitacoes.store') }}" enctype="multipart/form-data" class="space-y-4 bg-white p-4 shadow-sm rounded">
        @csrf

        <h3 class="font-semibold">Dados Pessoais</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="form-label">Nome completo</label>
                <input type="text" name="nome" value="{{ old('nome') }}" class="form-control" required>
            </div>
            <div>
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" value="{{ old('cpf') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Data de nascimento</label>
                <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">RG</label>
                <input type="text" name="rg" value="{{ old('rg') }}" class="form-control">
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Endereço completo</label>
                <input type="text" name="endereco" value="{{ old('endereco') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Tipo de residência</label>
                <select name="tipo_residencia" class="form-control">
                    <option value="">— selecione —</option>
                    <option value="aluguel" @selected(old('tipo_residencia')=='aluguel')>Aluguel</option>
                    <option value="casa_propria" @selected(old('tipo_residencia')=='casa_propria')>Casa própria</option>
                </select>
            </div>
        </div>

        <h3 class="font-semibold mt-4">Dados de Contato</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="form-label">Telefone celular</label>
                <input type="text" name="telefone_celular" value="{{ old('telefone_celular') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Instagram</label>
                <input type="text" name="instagram" value="{{ old('instagram') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Telefone parente 1</label>
                <input type="text" name="telefone_parente_1" value="{{ old('telefone_parente_1') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Telefone parente 2</label>
                <input type="text" name="telefone_parente_2" value="{{ old('telefone_parente_2') }}" class="form-control">
            </div>
        </div>

        <h3 class="font-semibold mt-4">Dados Profissionais</h3>
        <div>
            <label class="form-label">Nome da empresa</label>
            <input type="text" name="nome_empresa" value="{{ old('nome_empresa') }}" class="form-control">
        </div>

        <h3 class="font-semibold mt-4">Informações Adicionais</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="form-label">Pessoa que indicou</label>
                <input type="text" name="pessoa_indicou" value="{{ old('pessoa_indicou') }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Está devendo algum agiota?</label>
                <select name="devendo_agiota" class="form-control">
                    <option value="0" @selected(old('devendo_agiota')=='0')>Não</option>
                    <option value="1" @selected(old('devendo_agiota')=='1')>Sim</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control">{{ old('observacoes') }}</textarea>
            </div>
        </div>

        <h3 class="font-semibold mt-4">Documentos (uploads)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="form-label">Contra-cheque</label>
                <input type="file" name="contracheque" class="form-control">
            </div>
            <div>
                <label class="form-label">Foto da identidade</label>
                <input type="file" name="identidade" class="form-control">
            </div>
            <div>
                <label class="form-label">Comprovante de endereço</label>
                <input type="file" name="comprovante_endereco" class="form-control">
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">Enviar solicitação</button>
        </div>
    </form>
</div>
@stop