<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Nota Promiss?ria</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { text-align: center; font-size: 18px; margin: 0 0 18px; }
        p { line-height: 1.5; margin: 0 0 10px; }
        .line { margin: 16px 0; }
        .signature { margin-top: 28px; }
        .muted { color: #333; }
    </style>
</head>
<body>
@php
    $blankLong = '______________________________________________';
    $blankLonger = '________________________________________________________________________';
    $blankMedium = '____________________________';
@endphp

<h1>NOTA PROMISS?RIA</h1>

<p>
    Eu, {{ filled($nome ?? null) ? $nome : $blankLong }}, CPF
    {{ filled($cpf ?? null) ? $cpf : $blankMedium }}, residente no endere?o
    {{ filled($endereco ?? null) ? $endereco : $blankLonger }},
    reconhe?o que devo a {{ filled($credor_nome ?? null) ? $credor_nome : $blankLong }}, CPF/CNPJ
    {{ filled($credor_documento ?? null) ? $credor_documento : $blankMedium }}, a quantia de R$
    {{ filled($valor ?? null) ? $valor : $blankMedium }}
    ({{ filled($valor_extenso ?? null) ? $valor_extenso : $blankLonger }}).
</p>

<p>
    Declaro que o valor acima ser? pago em {{ filled($parcelas ?? null) ? $parcelas : $blankMedium }} parcelas mensais de R$
    {{ filled($valor_parcela ?? null) ? $valor_parcela : $blankMedium }},
    vencendo a primeira em {{ filled($primeiro_vencimento ?? null) ? $primeiro_vencimento : '____/____/________' }} e as demais no mesmo dia dos meses
    subsequentes.
</p>

<p>
    Em caso de atraso no pagamento, incidir? multa de {{ filled($multa_percent ?? null) ? $multa_percent : $blankMedium }}% sobre o valor da parcela, al?m de
    juros de {{ filled($juros_percent ?? null) ? $juros_percent : $blankMedium }}% ao m?s.
</p>

<p class="line">Local e data: {{ filled($local_data ?? null) ? $local_data : $blankLonger }}</p>

<p class="signature">Assinatura do Devedor: {{ $blankLonger }}</p>
<p class="signature">Assinatura do Credor: {{ $blankLonger }}</p>
<p class="signature">Assinatura Digital (Gov.br, Clicksign, ZapSign ou similar): {{ $blankLonger }}</p>

</body>
</html>
