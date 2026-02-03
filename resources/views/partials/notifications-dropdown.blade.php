@if(($items ?? collect())->count() > 0)
    <span class="dropdown-item dropdown-header">{{ $total }} Notificacoes</span>
    <div class="dropdown-divider"></div>

    @foreach($items as $n)
        <a href="{{ $n->link ?? '#' }}" class="dropdown-item">
            <i class="fas fa-bell mr-2 text-{{ $n->level }}"></i> {{ $n->title }}
            <span class="float-right text-muted text-sm">{{ $n->created_at?->diffForHumans() }}</span>
            @if(!empty($n->body))
                <div class="text-muted text-sm">{{ $n->body }}</div>
            @endif
        </a>
        <div class="dropdown-divider"></div>
    @endforeach
@elseif($total > 0)
    <span class="dropdown-item dropdown-header">{{ $total }} Notificacoes</span>
    <div class="dropdown-divider"></div>

    @if($solicitacoesPendentes > 0)
        <a href="{{ route('admin.solicitacoes.index') }}" class="dropdown-item">
            <i class="fas fa-file-signature mr-2"></i> {{ $solicitacoesPendentes }} solicitacao(oes) pendente(s)
            <span class="float-right text-muted text-sm">agora</span>
        </a>
        <div class="dropdown-divider"></div>
    @endif

    @if($parcelasAtrasadas > 0)
        <a href="{{ route('parcelas.vencidas') }}" class="dropdown-item">
            <i class="fas fa-clock mr-2"></i> {{ $parcelasAtrasadas }} parcela(s) atrasada(s)
            <span class="float-right text-muted text-sm">atraso</span>
        </a>
        <div class="dropdown-divider"></div>
    @endif

    @if($pagamentosHoje > 0)
        <a href="{{ route('pagamentos.index') }}" class="dropdown-item">
            <i class="fas fa-receipt mr-2"></i> {{ $pagamentosHoje }} pagamento(s) hoje
            <span class="float-right text-muted text-sm">hoje</span>
        </a>
        <div class="dropdown-divider"></div>
    @endif

    @if($despesasVencidas > 0)
        <a href="{{ route('relatorios.emprestimos') }}" class="dropdown-item">
            <i class="fas fa-file-invoice-dollar mr-2"></i> {{ $despesasVencidas }} despesa(s) vencida(s)
            <span class="float-right text-muted text-sm">atraso</span>
        </a>
        <div class="dropdown-divider"></div>
    @endif
@else
    <span class="dropdown-item dropdown-header">Sem notificacoes</span>
@endif
