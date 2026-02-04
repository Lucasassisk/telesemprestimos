<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmprestimoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSolicitacaoController;
use App\Http\Controllers\SolicitacaoController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| ROTA INICIAL
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->get('/', function () {
    return redirect()->route('login');
});

/*
| Rota pública inicial agora aponta para a view `welcome`.
*/


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| ROTAS AUTENTICADAS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =====================
    // EMPRÉSTIMOS
    // =====================
    Route::resource('emprestimos', EmprestimoController::class);

    // =====================
    // CONTRATOS
    // =====================
    Route::get('contratos/ativos', [ContratoController::class, 'ativos'])
        ->name('contratos.ativos');

    Route::get('contratos/quitados', [ContratoController::class, 'quitados'])
        ->name('contratos.quitados');

    Route::get('contratos/inadimplentes', [ContratoController::class, 'inadimplentes'])
        ->name('contratos.inadimplentes');

    // =====================
    // PARCELAS
    // =====================
    Route::get('parcelas', [ParcelaController::class, 'index'])
        ->name('parcelas.index');

    Route::get('parcelas/vencidas', [ParcelaController::class, 'vencidas'])
        ->name('parcelas.vencidas');

    Route::post('parcelas/{parcela}/pagar', [ParcelaController::class, 'pagar'])
        ->name('parcelas.pagar');

    // Parcela: lista por cliente e ações do cliente
    Route::get('parcelas', [ParcelaController::class, 'index'])->name('parcelas.index'); // já existe, garantido
    Route::get('parcelas/cliente/{cliente}', [ParcelaController::class, 'porCliente'])->name('parcelas.por_cliente');
    Route::post('parcelas/cliente/{cliente}/quitar', [ParcelaController::class, 'quitar'])->name('parcelas.quitar');

    // rotas para lembretes via WhatsApp
    Route::get('emprestimos/{emprestimo}/whatsapp', [EmprestimoController::class, 'whatsapp'])
        ->name('emprestimos.whatsapp');
    Route::get('parcelas/{parcela}/whatsapp', [ParcelaController::class, 'whatsapp'])
        ->name('parcelas.whatsapp');

    // =====================
    // CLIENTES
    // =====================
    // Expor todas as ações REST para clientes (index, create, store, show, edit, update, destroy)
    Route::resource('clientes', ClienteController::class);

    // =====================
    // PAGAMENTOS
    // =====================
    Route::resource('pagamentos', PagamentoController::class)
        ->only(['index', 'create', 'store']);

    // =====================
    // RELATÓRIOS
    // =====================
    Route::get('relatorios/emprestimos', [\App\Http\Controllers\RelatorioController::class, 'despesas'])
        ->middleware('role:admin')->name('relatorios.emprestimos');

    // rota faltante: inadimplência
    Route::get('relatorios/inadimplencia', [\App\Http\Controllers\RelatorioController::class, 'inadimplencia'])
        ->middleware('role:admin')->name('relatorios.inadimplencia');
    Route::get('relatorios/inadimplencia/export', [\App\Http\Controllers\RelatorioController::class, 'exportInadimplencia'])
        ->middleware('role:admin')->name('relatorios.inadimplencia.export');

    // rotas de gerenciamento do Resumo Financeiro
    Route::post('relatorios/resumo', [\App\Http\Controllers\RelatorioController::class, 'storeResumo'])
        ->middleware('role:admin')->name('relatorios.resumo.store');
    Route::put('relatorios/resumo/{item}', [\App\Http\Controllers\RelatorioController::class, 'updateResumo'])
        ->middleware('role:admin')->name('relatorios.resumo.update');
    Route::delete('relatorios/resumo/{item}', [\App\Http\Controllers\RelatorioController::class, 'destroyResumo'])
        ->middleware('role:admin')->name('relatorios.resumo.destroy');

    // rotas para manipular despesas (CRUD básico)
    Route::post('relatorios/despesas', [\App\Http\Controllers\RelatorioController::class, 'store'])
        ->middleware('role:admin')->name('relatorios.despesas.store');
    Route::delete('relatorios/despesas/{despesa}', [\App\Http\Controllers\RelatorioController::class, 'destroy'])
        ->middleware('role:admin')->name('relatorios.despesas.destroy');

    // editar/atualizar e marcar pago
    Route::get('relatorios/despesas/{despesa}/edit', [\App\Http\Controllers\RelatorioController::class, 'edit'])
        ->middleware('role:admin')->name('relatorios.despesas.edit');
    Route::put('relatorios/despesas/{despesa}', [\App\Http\Controllers\RelatorioController::class, 'update'])
        ->middleware('role:admin')->name('relatorios.despesas.update');
    Route::post('relatorios/despesas/{despesa}/toggle', [\App\Http\Controllers\RelatorioController::class, 'togglePaid'])
        ->middleware('role:admin')->name('relatorios.despesas.toggle');

    // nova rota: exportar CSV
    Route::get('relatorios/despesas/export', [\App\Http\Controllers\RelatorioController::class, 'export'])
        ->middleware('role:admin')->name('relatorios.despesas.export');

    // =====================
    // ADMINISTRAÇÃO
    // =====================
    Route::resource('users', UserController::class)->middleware('role:admin');

    Route::get('configuracoes', [ConfiguracaoController::class, 'index'])
        ->middleware('role:admin')->name('configuracoes.index');
    Route::post('configuracoes', [ConfiguracaoController::class, 'store'])
        ->middleware('role:admin')->name('configuracoes.store');

    // Admin: listar e baixar solicitações
    Route::get('admin/solicitacoes', [SolicitacaoController::class, 'index'])
        ->middleware('role:admin')->name('admin.solicitacoes.index');

    Route::get('admin/solicitacoes/{solicitacao}/download/{file}', [SolicitacaoController::class, 'download'])
        ->where('file', 'contracheque|identidade|comprovante_endereco')
        ->middleware('role:admin')->name('admin.solicitacoes.download');

    Route::get('notifications/navbar', [NotificationController::class, 'navbar'])
        ->name('notifications.navbar');
});

// rota pública para formulário de solicitação (fora do middleware auth)
Route::get('/solicitacao', [PublicSolicitacaoController::class, 'create'])->name('solicitacoes.create');
Route::post('/solicitacao', [PublicSolicitacaoController::class, 'store'])->name('solicitacoes.store');
Route::get('/solicitacao/obrigado', [PublicSolicitacaoController::class, 'thankyou'])->name('solicitacoes.thankyou');

require __DIR__.'/auth.php';

