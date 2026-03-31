<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PromissoriaService
{
    public static function gerar(array $data, string $folder, string $filenameBase): string
    {
        $pdf = Pdf::loadView('promissorias.nota', $data)->setPaper('a4');

        $filename = $filenameBase . '-' . now()->format('YmdHis') . '.pdf';
        $path = trim($folder, '/') . '/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
