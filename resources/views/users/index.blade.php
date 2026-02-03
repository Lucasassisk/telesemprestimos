@extends('adminlte::page')

@section('title', 'Usuario')

@section('content_header')
    <h1>Configuracoes do Usuario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Conta</h5>
            <p><strong>Nome:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Perfil:</strong> {{ $user->role ?? 'admin' }}</p>

            <div class="mt-3">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Editar perfil</a>
                <a href="{{ route('password.confirm') }}" class="btn btn-outline-secondary">Alterar senha</a>
            </div>
        </div>
    </div>
@stop
