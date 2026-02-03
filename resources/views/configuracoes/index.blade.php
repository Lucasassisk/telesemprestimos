@extends('adminlte::page')

@section('title', 'Configuracoes')

@section('content_header')
    <h1>Configuracoes</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('configuracoes.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome do sistema</label>
                        <input type="text" name="app_name" class="form-control" value="{{ old('app_name', $values['app_name'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cor primaria</label>
                        <input type="text" name="primary_color" class="form-control" value="{{ old('primary_color', $values['primary_color'] ?? '') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Juros padrao (%)</label>
                        <input type="number" step="0.01" name="default_interest" class="form-control" value="{{ old('default_interest', $values['default_interest'] ?? '0') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Valor minimo emprestimo</label>
                        <input type="number" step="0.01" name="min_loan_amount" class="form-control" value="{{ old('min_loan_amount', $values['min_loan_amount'] ?? '0') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Valor maximo emprestimo</label>
                        <input type="number" step="0.01" name="max_loan_amount" class="form-control" value="{{ old('max_loan_amount', $values['max_loan_amount'] ?? '0') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mensagem padrao WhatsApp</label>
                    <textarea name="whatsapp_template" class="form-control" rows="3" required>{{ old('whatsapp_template', $values['whatsapp_template'] ?? '') }}</textarea>
                    <small class="text-muted">Use {nome} para personalizar.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mensagem padrao WhatsApp (parcelas)</label>
                    <textarea name="whatsapp_parcela_template" class="form-control" rows="4" required>{{ old('whatsapp_parcela_template', $values['whatsapp_parcela_template'] ?? '') }}</textarea>
                    <small class="text-muted">Use {nome}, {vencimento}, {valor}, {pix_telefone}, {pix_nome}, {pix_instituicao}.</small>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pix telefone</label>
                        <input type="text" name="whatsapp_pix_telefone" class="form-control" value="{{ old('whatsapp_pix_telefone', $values['whatsapp_pix_telefone'] ?? '') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nome do Pix</label>
                        <input type="text" name="whatsapp_pix_nome" class="form-control" value="{{ old('whatsapp_pix_nome', $values['whatsapp_pix_nome'] ?? '') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Instituicao do Pix</label>
                        <input type="text" name="whatsapp_pix_instituicao" class="form-control" value="{{ old('whatsapp_pix_instituicao', $values['whatsapp_pix_instituicao'] ?? '') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Logo do sistema (opcional)</label>
                    <input type="file" name="logo" class="form-control">
                    @if(!empty($values['logo_path']))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $values['logo_path']) }}" alt="Logo" style="max-height:60px;">
                        </div>
                    @endif
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="require_documents" name="require_documents" value="1"
                        @checked(old('require_documents', $values['require_documents'] ?? '0') == '1')>
                    <label for="require_documents" class="form-check-label">Exigir documentos na solicitacao publica</label>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="developer_mode" name="developer_mode" value="1"
                        @checked(old('developer_mode', $values['developer_mode'] ?? '0') == '1')>
                    <label for="developer_mode" class="form-check-label">Modo desenvolvedor (fechar sistema para visitantes)</label>
                </div>

                <button class="btn btn-primary">Salvar configuracoes</button>
            </form>
        </div>
    </div>
@stop
