<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcorrenciaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EspecieController;

Route::post('/ocorrencias', [OcorrenciaController::class, 'store']);
Route::get('/ocorrencias/pendentes', [OcorrenciaController::class, 'indexPendentes']);
Route::get('/ocorrencias/arquivadas', [OcorrenciaController::class, 'indexArquivadas']);
Route::get('/ocorrencias/publicados', [OcorrenciaController::class, 'indexPublicados']);
Route::get('/ocorrencias/{id}', [OcorrenciaController::class, 'showPublicada']);
Route::put('/ocorrencias/{id}/avaliar', [OcorrenciaController::class, 'avaliar']);
Route::put('/ocorrencias/{id}/publicar', [OcorrenciaController::class, 'publicar']);

Route::get('/categorias', [CategoriaController::class, 'index']);
Route::post('/categorias', [CategoriaController::class, 'store']);
Route::get('/categorias/{id}', [CategoriaController::class, 'show']);
Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

Route::get('/especies', [EspecieController::class, 'index']);
Route::post('/especies', [EspecieController::class, 'store']);
Route::get('/especies/{id}', [EspecieController::class, 'show']);
Route::put('/especies/{id}', [EspecieController::class, 'update']);
Route::delete('/especies/{id}', [EspecieController::class, 'destroy']);

Route::post('/especies/{id}/vincular-ocorrencias', [EspecieController::class, 'vincularOcorrencias']);

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

Route::get('/imagens/{filename}', function ($filename) {
    $path = storage_path('app/public/imagens/' . $filename);

    if (!file_exists($path)) {
        return response()->json(['error' => 'Imagem não encontrada.'], 404);
    }

    $file = Storage::get($path);
    $type = Storage::mimeType($path);

    return response::make($file, 200, [
        'Content-Type' => $type,
        'Access-control-allow-origin' => '*',
        'Access-control-allow-methods' => 'GET',
    ]);
});