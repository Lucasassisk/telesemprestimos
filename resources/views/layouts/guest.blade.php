@php
    $primaryColor = \App\Models\Configuracao::where('key', 'primary_color')->value('value') ?? '#0b2a22';
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center"
      style="background-color: {{ $primaryColor }};">

    <div class="w-full sm:max-w-md px-8 py-8 bg-[#111827] rounded-xl shadow-2xl border border-gray-700">
        {{ $slot }}
    </div>

</body>
</html>
