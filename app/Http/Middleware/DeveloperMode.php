<?php

namespace App\Http\Middleware;

use App\Models\Configuracao;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DeveloperMode
{
    public function handle(Request $request, Closure $next)
    {
        if (! Schema::hasTable('configuracoes')) {
            return $next($request);
        }

        $enabled = Configuracao::where('key', 'developer_mode')->value('value') === '1';
        if (! $enabled) {
            return $next($request);
        }

        if ($request->routeIs('login') || $request->routeIs('password.*')) {
            return $next($request);
        }

        if (auth()->check()) {
            return $next($request);
        }

        abort(404);
    }
}
