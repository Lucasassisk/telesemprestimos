@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php($dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home'))

@if (config('adminlte.use_route_url', false))
    @php($dashboard_url = $dashboard_url ? route($dashboard_url) : '')
@else
    @php($dashboard_url = $dashboard_url ? url($dashboard_url) : '')
@endif

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link {{ config('adminlte.classes_brand') }}"
    @endif>

    <img src="{{ asset(config('adminlte.logo_img', 'images/telesbank-logo.svg')) }}"
         alt="{{ config('adminlte.logo_img_alt', 'Teles Empréstimos') }}"
         class="{{ config('adminlte.logo_img_class', 'brand-image img-circle elevation-2') }}"
         style="opacity:.95">

    <span class="brand-text font-weight-light {{ config('adminlte.classes_brand_text') }}">
        {!! config('adminlte.logo', 'Teles Empréstimos') !!}
    </span>

</a>
