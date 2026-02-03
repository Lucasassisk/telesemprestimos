@extends('layouts.app')

@section('title', 'Clientes')

@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <div class="text-muted text-sm mb-1">Dashboard &gt; Clientes</div>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Clientes</h1>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">Novo cliente</a>
    </div>
@stop

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('clientes.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <label for="clientes-search" class="text-sm text-muted mb-1">Buscar</label>
                        <div class="input-group">
                            <input id="clientes-search" type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Pesquisar por nome ou CPF">
                            <div class="input-group-append">
                                <button class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto mt-2 mt-md-0">
                        <a href="{{ route('clientes.index') }}" class="btn btn-default">Limpar</a>
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
                        <th>Nome</th>
                        <th>Documento</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
                        <th>Tipo residência</th>
                        <th>Instagram</th>
                        <th>Pessoa que indicou</th>
                        <th>Devendo agiota</th>
                        <th>Observações</th>
                        <th>Arquivos</th>
                        <th class="text-right">Empréstimos</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        @php $s = $cliente->latestSolicitacao ?? null; @endphp
                        <tr>
                            <td>{{ $cliente->nome }}</td>
                            <td>{{ $cliente->documento ?? ($s->cpf ?? '-') }}</td>
                            <td>{{ $cliente->email ?? ($s->email ?? '-') }}</td>
                            <td>{{ $cliente->telefone ?? ($s->telefone_celular ?? '-') }}</td>
                            <td>{{ $cliente->endereco ?? ($s->endereco ?? '-') }}</td>
                            <td>{{ $s->tipo_residencia ?? '-' }}</td>
                            <td>{{ $s->instagram ?? '-' }}</td>
                            <td>{{ $s->pessoa_indicou ?? '-' }}</td>
                            <td>{{ isset($s->devendo_agiota) ? ($s->devendo_agiota ? 'Sim' : 'Não') : '-' }}</td>
                            <td style="max-width:200px; white-space:pre-wrap;">{{ Str::limit($s->observacoes ?? '-', 200) }}</td>
                            <td>
                                @if($s && $s->contracheque_path)
                                    <button type="button" class="btn btn-sm btn-outline-primary mb-1 btn-block js-preview-file" data-url="{{ route('admin.solicitacoes.download', [$s, 'contracheque']) }}" data-name="Contra-cheque">Contra-cheque</button>
                                @endif
                                @if($s && $s->identidade_path)
                                    <button type="button" class="btn btn-sm btn-outline-secondary mb-1 btn-block js-preview-file" data-url="{{ route('admin.solicitacoes.download', [$s, 'identidade']) }}" data-name="Identidade">Identidade</button>
                                @endif
                                @if($s && $s->comprovante_endereco_path)
                                    <button type="button" class="btn btn-sm btn-outline-info btn-block js-preview-file" data-url="{{ route('admin.solicitacoes.download', [$s, 'comprovante_endereco']) }}" data-name="Comprovante">Comprovante</button>
                                @endif
                            </td>
                            <td class="text-right">{{ $cliente->emprestimos_count }}</td>
                            <td class="text-right">
                                <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-outline-secondary mb-1 btn-block">Ver</a>
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary mb-1 btn-block">Editar</a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger btn-block" onclick="return confirm('Confirmar exclusão?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="13" class="text-muted">Nenhum cliente cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $clientes->links() }}
        </div>
    </div>
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('preview-modal');
    if (!modal) return;
    const title = modal.querySelector('[data-preview-title]');
    const frame = modal.querySelector('[data-preview-frame]');
    const img = modal.querySelector('[data-preview-img]');
    const downloadBtn = modal.querySelector('[data-preview-download]');
    const closeBtns = modal.querySelectorAll('[data-preview-close]');

    function openModal(url, name) {
        title.textContent = name || 'Arquivo';
        downloadBtn.href = url;
        const sep = url && url.includes('?') ? '&' : '?';
        const previewUrl = url ? (url + sep + 'preview=1') : url;
        const lower = (url || '').toLowerCase();
        const isImage = lower.endsWith('.jpg') || lower.endsWith('.jpeg') || lower.endsWith('.png');

        if (isImage) {
            frame.classList.add('d-none');
            img.classList.remove('d-none');
            img.src = previewUrl;
        } else {
            img.classList.add('d-none');
            frame.classList.remove('d-none');
            frame.src = previewUrl;
        }

        modal.classList.add('show');
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    function closeModal() {
        frame.src = '';
        img.src = '';
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    document.querySelectorAll('.js-preview-file').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openModal(btn.dataset.url, btn.dataset.name);
        });
    });

    closeBtns.forEach(function (btn) {
        btn.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
});
</script>
@endpush

@push('css')
<style>
    #preview-modal {
        position: fixed;
        inset: 0;
        background: #00000090;
        z-index: 1050;
        display: none;
        opacity: 1;
    }
    #preview-modal .preview-dialog {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    }
    #preview-modal .preview-header,
    #preview-modal .preview-footer {
        padding: 10px 14px;
        border-bottom: 1px solid #e5e5e5;
    }
    #preview-modal .preview-footer {
        border-top: 1px solid #e5e5e5;
        border-bottom: 0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }
    #preview-modal .preview-body {
        padding: 0;
        background: #f8f9fa;
        height: 70vh;
        overflow: auto;
    }
    #preview-modal iframe {
        width: 100%;
        height: 70vh;
        border: 0;
    }
    #preview-modal img {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 0 auto;
        padding: 12px;
    }
    #preview-modal .d-none { display: none; }
</style>
@endpush

<div id="preview-modal" class="modal-backdrop">
    <div class="preview-dialog">
        <div class="preview-header d-flex align-items-center justify-content-between">
            <strong data-preview-title>Arquivo</strong>
            <button type="button" class="btn btn-sm btn-light" data-preview-close>Fechar</button>
        </div>
        <div class="preview-body">
            <iframe data-preview-frame title="Preview"></iframe>
            <img data-preview-img alt="Preview">
        </div>
        <div class="preview-footer">
            <a class="btn btn-primary" data-preview-download href="#" download>Baixar</a>
            <button type="button" class="btn btn-outline-secondary" data-preview-close>Fechar</button>
        </div>
    </div>
</div>
