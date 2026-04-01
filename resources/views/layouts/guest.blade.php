@php
    $primaryColor = \App\Models\Configuracao::where('key', 'primary_color')->value('value') ?? '#0b2a22';
    $isLoginRoute = request()->routeIs('login');
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            overflow-x: hidden;
        }

        body {
            min-height: 100vh;
        }
    </style>
</head>

<body class="{{ $isLoginRoute ? 'min-h-screen' : 'min-h-screen flex items-center justify-center' }}"
      style="background-color: {{ $isLoginRoute ? '#020617' : $primaryColor }};">
    @if ($isLoginRoute)
        {{ $slot }}
    @else
        <div class="w-full sm:max-w-md px-8 py-8 bg-[#111827] rounded-xl shadow-2xl border border-gray-700">
            {{ $slot }}
        </div>
    @endif
</body>
</html>
