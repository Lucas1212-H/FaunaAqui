<?php
namespace App\Http\Controllers; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcorrenciaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EspecieController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Api\ContatoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::put('/perfil', [AuthController::class, 'atualizarPerfil']);
    Route::delete('/perfil', [AuthController::class, 'deletarPerfil']);

    // Gerenciamento de Usuários (Admin)
    Route::get('/usuarios', [UserController::class, 'index']);
    Route::post('/usuarios/{id}/aprovar', [UserController::class, 'aprovarUsuario']);
    Route::put('/usuarios/{id}', [UserController::class, 'update']);
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);
});

Route::post('/registrar', [AuthController::class, 'registrar']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/esqueci-senha', [AuthController::class, 'forgotPassword']);
Route::post('/redefinir-senha', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/ocorrencias', [OcorrenciaController::class, 'store']);
Route::get('/ocorrencias/pendentes', [OcorrenciaController::class, 'indexPendentes']);
Route::get('/ocorrencias/arquivadas', [OcorrenciaController::class, 'indexArquivadas']);
Route::get('/ocorrencias/publicados', [OcorrenciaController::class, 'indexPublicados']);
Route::get('/ocorrencias/recentes', [OcorrenciaController::class, 'indexRecentes']);
Route::get('/ocorrencias/{id}', [OcorrenciaController::class, 'showPublicada']);
Route::put('/ocorrencias/{id}/avaliar', [OcorrenciaController::class, 'avaliar']);
Route::put('/ocorrencias/{id}/publicar', [OcorrenciaController::class, 'publicar']);
Route::put('/ocorrencias/{id}/despublicar', [OcorrenciaController::class, 'despublicar']);

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

Route::get('/posts', [PostController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
});

Route::post('/contato', [ContatoController::class, 'enviarContato']);

use Illuminate\Support\Facades\Artisan;

Route::get('/gerar-link-storage', function () {
    try {
        Artisan::call('storage:link');
        return response()->json(['mensagem' => 'Link criado com sucesso!']);
    } catch (\Exception $e) {
        return response()->json(['erro' => $e->getMessage()], 500);
    }
});
 
use Illuminate\Support\Facades\File;

Route::get('/configurar-producao', function () {
    try {
        // 1. Se o link antigo e quebrado já existir, nós removemo-lo primeiro
        $linkPublico = public_path('storage');
        if (File::exists($linkPublico) || is_link($linkPublico)) {
            File::delete($linkPublico);
        }

        // 2. Executa o comando para criar o link simbólico correto
        Artisan::call('storage:link');
        
        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Link do Storage criado com sucesso no ambiente de produção!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'sucesso' => false,
            'erro' => $e->getMessage()
        ], 500);
    }
});

Route::get('/analise/ocorrencias', [OcorrenciaController::class, 'dadosParaAnalise']);
