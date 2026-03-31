<?php

namespace App\Http\Controllers;

use App\Models\Solicitacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SolicitacaoController extends Controller
{
    // Lista paginada para área administrativa
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $solicitacoes = \App\Models\Solicitacao::when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('nome', 'like', "%{$q}%")
                        ->orWhere('cpf', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->appends(['q' => $q]);

        return view('admin.solicitacoes.index', compact('solicitacoes'));
    }

    // Download de um dos arquivos enviados
    public function download(Request $request, Solicitacao $solicitacao, string $file)
    {
        $map = [
            'contracheque' => 'contracheque_path',
            'identidade' => 'identidade_path',
            'comprovante_endereco' => 'comprovante_endereco_path',
            'promissoria' => 'promissoria_path',
        ];

        if (! isset($map[$file])) {
            abort(404);
        }

        $path = $solicitacao->{$map[$file]};

        if (! $path) {
            \Log::warning('Solicitacao download: campo de arquivo vazio', ['solicitacao_id' => $solicitacao->id, 'file' => $file]);
            abort(404, 'Arquivo não informado no registro.');
        }

        // Normaliza: remove prefixo "public/" se presente
        $rel = str_starts_with($path, 'public/') ? substr($path, strlen('public/')) : $path;

        $preview = $request->boolean('preview');
        $sendFile = function (string $fullPath) use ($preview) {
            return $preview ? response()->file($fullPath) : response()->download($fullPath);
        };

        // 1) tenta no storage disk "public" (storage/app/public/...)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($rel)) {
            return $sendFile(\Illuminate\Support\Facades\Storage::disk('public')->path($rel));
        }

        // 1.1) compat: arquivos gravados no disk "local" (storage/app/private/public/...)
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return $sendFile(\Illuminate\Support\Facades\Storage::disk('local')->path($path));
        }

        $localRel = 'public/' . ltrim($rel, '/');
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($localRel)) {
            return $sendFile(\Illuminate\Support\Facades\Storage::disk('local')->path($localRel));
        }

        // 2) tenta public/storage (quando php artisan storage:link existe)
        $publicStoragePath = public_path('storage/' . $rel);
        if (file_exists($publicStoragePath)) {
            return $sendFile($publicStoragePath);
        }

        // 3) tenta caminho direto em public/ (caso arquivos tenham sido salvos aí por engano)
        $directPublicPath = public_path($rel);
        if (file_exists($directPublicPath)) {
            return $sendFile($directPublicPath);
        }

        // fallback: detalhe no log e 404 claro
        \Log::warning("Solicitacao download: arquivo não encontrado", [
            'solicitacao_id' => $solicitacao->id,
            'path_db' => $path,
            'normalized' => $rel,
        ]);

        abort(404, 'Arquivo não encontrado.');
    }
}
