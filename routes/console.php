<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Parcela;
use App\Services\SystemNotificationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Notificacoes automaticas de parcelas (atrasadas e proximas do vencimento)
Artisan::command('notifications:run', function () {
    $today = now()->startOfDay();
    $soon = now()->addDays(3)->endOfDay();

    $overdue = Parcela::with('emprestimo.cliente')
        ->where('pago', false)
        ->whereDate('vencimento', '<', $today->toDateString())
        ->get();

    foreach ($overdue as $p) {
        SystemNotificationService::createOnce(
            'parcela_overdue_' . $p->id . '_' . $today->format('Ymd'),
            'Parcela atrasada',
            ($p->emprestimo?->cliente?->nome ?? 'Cliente') . ' - Parcela #' . $p->numero,
            'warning',
            route('emprestimos.show', $p->emprestimo_id)
        );
    }

    $upcoming = Parcela::with('emprestimo.cliente')
        ->where('pago', false)
        ->whereBetween('vencimento', [$today->toDateString(), $soon->toDateString()])
        ->get();

    foreach ($upcoming as $p) {
        SystemNotificationService::createOnce(
            'parcela_due_' . $p->id . '_' . $today->format('Ymd'),
            'Parcela vencendo',
            ($p->emprestimo?->cliente?->nome ?? 'Cliente') . ' - Parcela #' . $p->numero,
            'info',
            route('emprestimos.show', $p->emprestimo_id)
        );
    }
})->purpose('Gera notificacoes automaticas de parcelas');

Schedule::command('notifications:run')->dailyAt('08:00');
