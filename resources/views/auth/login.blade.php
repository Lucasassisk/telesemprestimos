<x-guest-layout>

@php
    $config = \App\Models\Configuracao::first();
@endphp

<div class="text-center mb-8">
    <h1 class="text-2xl font-semibold text-white">Bem-vindo ao Sistema Telesemprestimos</h1>
    <p class="text-sm text-gray-400">Acesse sua conta</p>
</div>

<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div>
        <x-input-label for="email" value="E-mail" class="text-gray-300" />
        <x-text-input id="email"
            class="block mt-1 w-full bg-gray-800 border-gray-600 text-white"
            type="email" name="email" required autofocus />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="password" value="Senha" class="text-gray-300" />
        <x-text-input id="password"
            class="block mt-1 w-full bg-gray-800 border-gray-600 text-white"
            type="password" name="password" required />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="flex items-center justify-between mt-4 text-gray-400">
        <label class="flex items-center">
            <input type="checkbox" name="remember"
                class="rounded border-gray-600 bg-gray-800 text-indigo-600 shadow-sm">
            <span class="ms-2 text-sm">Lembrar-me</span>
        </label>

        <a href="{{ route('password.request') }}" class="text-sm underline">
            Esqueci minha senha
        </a>
    </div>

    <div class="mt-6">
        <x-primary-button class="w-full justify-center">
            Entrar
        </x-primary-button>
    </div>
</form>

</x-guest-layout>
