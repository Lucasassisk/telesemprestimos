<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- IFrame Preloader Removal Workaround --}}
    <!-- IFrame Preloader Removal Workaround -->
    <style type="text/css">
        body.iframe-mode .preloader {
            display: none !important;
        }
    </style>

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets (depends on Laravel asset bundling tool) --}}
    @if(config('adminlte.enabled_laravel_mix', false))
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @else
        @switch(config('adminlte.laravel_asset_bundling', false))
            @case('mix')
                <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_css_path', 'css/app.css')) }}">
            @break

            @case('vite')
                @vite([config('adminlte.laravel_css_path', 'resources/css/app.css'), config('adminlte.laravel_js_path', 'resources/js/app.js')])
            @break

            @case('vite_js_only')
                @vite(config('adminlte.laravel_js_path', 'resources/js/app.js'))
            @break

            @default
                <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
                <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
                <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

                @if(config('adminlte.google_fonts.allowed', true))
                    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
                @endif
        @endswitch
    @endif

    {{-- Extra Configured Plugins Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])

    {{-- Livewire Styles --}}
    @if(config('adminlte.livewire'))
        @if(intval(app()->version()) >= 7)
            @livewireStyles
        @else
            <livewire:styles />
        @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    <style>
        :root { --primary-color: {{ config('app.primary_color', '#0d6efd') }}; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { opacity: 0.9; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .btn { border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.08); }
        .btn-sm { padding: 0.35rem 0.65rem; font-weight: 600; }
        .btn-primary, .btn-success, .btn-warning, .btn-danger, .btn-secondary, .btn-default {
            border-width: 1px;
        }
        .btn-default { background: #f1f3f5; border-color: #dfe3e8; color: #2f3437; }
        .btn-default:hover { background: #e9ecef; }
        .card { border-radius: 8px; box-shadow: 0 1px 8px rgba(0,0,0,0.06); }
        .card-header { border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .form-control { border-radius: 6px; }
        .main-sidebar .brand-link.teles-brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 56px;
            padding: 0.65rem 0.9rem;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }
        .main-sidebar .brand-link.teles-brand-link .brand-image {
            float: none;
            margin: 0;
            width: 30px;
            height: 30px;
            max-height: 30px;
            object-fit: contain;
            border-radius: 8px;
        }
        .main-sidebar .brand-link.teles-brand-link .brand-text.teles-brand-text {
            margin: 0;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 1.06rem;
            font-weight: 600 !important;
            letter-spacing: 0.01em;
            color: #f8fafc;
            line-height: 1.2;
        }
        .sidebar-mini.sidebar-collapse .main-sidebar .brand-link.teles-brand-link {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }
        .sidebar-mini.sidebar-collapse .main-sidebar .brand-link.teles-brand-link .brand-text.teles-brand-text {
            display: none;
        }
    </style>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/telesbank-logo.svg') }}">
    <link rel="shortcut icon" href="{{ asset('images/telesbank-logo.svg') }}" />
</head>

<body class="@yield('classes_body')" @yield('body_data')>

    {{-- Body Content --}}
    @yield('body')

    {{-- Base Scripts (depends on Laravel asset bundling tool) --}}
    @if(config('adminlte.enabled_laravel_mix', false))
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @else
        @switch(config('adminlte.laravel_asset_bundling', false))
            @case('mix')
                <script src="{{ mix(config('adminlte.laravel_js_path', 'js/app.js')) }}"></script>
            @break

            @case('vite')
            @case('vite_js_only')
            @break

            @default
                <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
                <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
                <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
                <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
        @endswitch
    @endif

    {{-- Extra Configured Plugins Scripts --}}
    @include('adminlte::plugins', ['type' => 'js'])

    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
        @if(intval(app()->version()) >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

</body>

</html>

