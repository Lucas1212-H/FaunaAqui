<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Rotas web
Route::get('/', function () {
    return view('welcome');
});

// Rota para servir arquivos de storage com headers CORS corretos
// Necessário porque o `php artisan serve` não adiciona CORS em arquivos estáticos
Route::get('/storage/{path}', function (string $path) {
    // Monta o caminho relativo dentro de storage/app/public
    $filePath = 'public/' . $path;

    if (!Storage::exists($filePath)) {
        abort(404);
    }

    $file     = Storage::get($filePath);
    $mimeType = Storage::mimeType($filePath);

    return response($file, 200)
        ->header('Content-Type', $mimeType)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', '*')
        ->header('Cache-Control', 'public, max-age=31536000');
})->where('path', '.*');
