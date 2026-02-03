@extends('layouts.guest')

@section('title', 'Obrigado')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4 text-center">
    <h1 class="text-2xl font-semibold mb-4">Obrigado — Solicitação recebida</h1>
    <p class="mb-4">Recebemos seus dados. Nossa equipe fará a análise e entrará em contato em breve.</p>

    <div class="mt-6">
        <a href="{{ url('/') }}" class="btn btn-primary">Voltar ao site</a>
        <a href="{{ route('solicitacoes.create') }}" class="btn btn-default ms-2">Enviar outra solicitação</a>
    </div>
</div>
@stop