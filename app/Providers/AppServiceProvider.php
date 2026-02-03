<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\Despesa;
use App\Models\Emprestimo;
use App\Models\Parcela;
use App\Models\Solicitacao;
use App\Models\Configuracao;
use App\Observers\AuditableObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cliente::observe(AuditableObserver::class);
        Emprestimo::observe(AuditableObserver::class);
        Parcela::observe(AuditableObserver::class);
        Solicitacao::observe(AuditableObserver::class);
        Despesa::observe(AuditableObserver::class);

        if (Schema::hasTable('configuracoes')) {
            $values = Configuracao::all()->pluck('value', 'key');

            if (!empty($values['app_name'])) {
                config(['app.name' => $values['app_name']]);
            }

            if (!empty($values['logo_path'])) {
                config(['adminlte.logo_img' => 'storage/' . $values['logo_path']]);
            }

            if (!empty($values['app_name'])) {
                config(['adminlte.logo' => $values['app_name']]);
            }

            if (!empty($values['primary_color'])) {
                config(['app.primary_color' => $values['primary_color']]);
            }
        }
    }
}
