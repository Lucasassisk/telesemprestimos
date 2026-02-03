<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Solicitacao de Emprestimo</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-gray-900">
        <main class="min-h-screen flex items-start justify-center py-10 px-4">
            <div class="w-full max-w-3xl">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold">Formulario de Solicitacao</h1>
                    <p class="text-sm text-gray-600">
                        Preencha seus dados para analise do pedido de emprestimo.
                    </p>
                </div>

                @if($errors->any())
                    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('solicitacoes.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-lg bg-white p-6 shadow">
                    @csrf

                    <section class="space-y-3">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Dados Pessoais</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Nome completo <span class="text-red-500">*</span></label>
                                <input type="text" name="nome" value="{{ old('nome') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">CPF <span class="text-red-500">*</span></label>
                                <input type="text" name="cpf" value="{{ old('cpf') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Data de nascimento</label>
                                <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">RG</label>
                                <input type="text" name="rg" value="{{ old('rg') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-gray-700">Endereco completo</label>
                                <input type="text" name="endereco" value="{{ old('endereco') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Tipo de residencia</label>
                                <select name="tipo_residencia" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">- selecione -</option>
                                    <option value="aluguel" @selected(old('tipo_residencia')=='aluguel')>Aluguel</option>
                                    <option value="casa_propria" @selected(old('tipo_residencia')=='casa_propria')>Casa propria</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Dados de Contato</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Telefone celular</label>
                                <input type="text" name="telefone_celular" value="{{ old('telefone_celular') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Instagram</label>
                                <input type="text" name="instagram" value="{{ old('instagram') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Telefone parente 1</label>
                                <input type="text" name="telefone_parente_1" value="{{ old('telefone_parente_1') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Telefone parente 2</label>
                                <input type="text" name="telefone_parente_2" value="{{ old('telefone_parente_2') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Dados Profissionais</h3>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Nome da empresa</label>
                            <input type="text" name="nome_empresa" value="{{ old('nome_empresa') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </section>

                    <section class="space-y-3">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Informacoes Adicionais</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Pessoa que indicou</label>
                                <input type="text" name="pessoa_indicou" value="{{ old('pessoa_indicou') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Esta devendo algum agiota?</label>
                                <select name="devendo_agiota" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="0" @selected(old('devendo_agiota')=='0')>Nao</option>
                                    <option value="1" @selected(old('devendo_agiota')=='1')>Sim</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-gray-700">Observacoes</label>
                                <textarea name="observacoes" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3">{{ old('observacoes') }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Documentos (uploads)</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Contra-cheque <span class="text-red-500">*</span></label>
                                <input type="file" name="contracheque" class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Foto da identidade <span class="text-red-500">*</span></label>
                                <input type="file" name="identidade" class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Comprovante de endereco <span class="text-red-500">*</span></label>
                                <input type="file" name="comprovante_endereco" class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                        </div>
                    </section>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Enviar solicitacao
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </body>
</html>
