<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    /**
     * Serve arquivos de storage com headers CORS corretos.
     *
     * O php artisan serve entrega arquivos estáticos de public/ sem passar pelo
     * middleware do Laravel (sem CORS). Por isso não usamos storage:link — em vez
     * disso, todas as requisições /storage/* passam por aqui e recebem os headers
     * Access-Control-Allow-Origin explicitamente.
     */
    public function serve(string $path)
    {
        // Se o caminho vier com 'public/' no início, removemos para evitar duplicidade no disco 'public'
        $cleanPath = preg_replace('/^public\//', '', $path);

        if (!Storage::disk('public')->exists($cleanPath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $file     = Storage::disk('public')->get($cleanPath);
        $mimeType = Storage::disk('public')->mimeType($cleanPath) ?: 'application/octet-stream';

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', '*')
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}
