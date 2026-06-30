<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PostController extends Controller
{
    public function index()
    {
        try {
            $posts = Post::orderBy('created_at', 'desc')->get()->map(function ($post) {
                $post->autor = $post->autor ? ['name' => $post->autor->name] :  ['name' => 'Administração'];
                return $post;
            });
            return response()->json($posts, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar publicações',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        if (auth()->user()->tipo_conta !== 'Administrador') {
            return response()->json(['message' => 'Apenas administradores podem editar anúncios.'], 403);
        }

        try {
            $post = Post::findOrFail($id);

            // Deixamos a imagem como 'nullable' caso o usuário queira apenas mudar o texto e manter a foto antiga
            $dados = $request->validate([
                'titulo' => 'required|string|max:255',
                'conteudo' => 'required|string',
                'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
                'tipo' => 'required|in:Noticia,Conferencia,Anuncio',
                'link_externo' => 'nullable|string'
            ]);

            // Se uma NOVA imagem foi enviada
            if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
                $resultadoUpload = Cloudinary::upload($request->file('imagem')->getRealPath(), [
                    'folder' => 'posts'
                ]);
                $post->imagem_url = $resultadoUpload->getSecurePath();
            }

            // Atualiza os outros campos
            $post->titulo = $dados['titulo'];
            $post->conteudo = $dados['conteudo'];
            $post->tipo = $dados['tipo'];
            $post->link_externo = $dados['link_externo'] ?? null;
            
            $post->save();

            return response()->json([
                'message' => 'Publicação atualizada com sucesso!',
                'post' => $post
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar publicação.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🗑️ Excluir uma publicação (Apenas Administradores)
     */
    public function destroy($id)
    {
        if (auth()->user()->tipo_conta !== 'Administrador') {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        try {
            $post = Post::findOrFail($id);

            // 🔥 Apaga a foto associada do disco antes de deletar o post
            if ($post->imagem_url) {
                $nomeArquivo = StorageUrl::pathFromUrl($post->getRawOriginal('imagem_url'));
                if ($nomeArquivo) {
                    Storage::disk('public')->delete($nomeArquivo);
                }
            }

            $post->delete();

            return response()->json(['message' => 'Publicação excluída com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir publicação.'], 500);
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->tipo_conta !== 'Administrador') {
            return response()->json(['message' => 'Apenas administradores podem publicar anúncios.'], 403);
        }

        try {
            // Validação preventiva
            $dados = $request->validate([
                'titulo' => 'required|string|max:255',
                'conteudo' => 'required|string',
                'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
                'tipo' => 'required|in:Noticia,Conferencia,Anuncio',
                'link_externo' => 'nullable|string'
            ]);

            $dados['user_id'] = auth()->id();
            $dados['imagem_url'] = null; // Valor padrão inicial

            // Processamento do arquivo físico de imagem
            if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
                $resultadoUpload = Cloudinary::upload($request->file('imagem')->getRealPath(), [
                    'folder' => 'posts'
                ]);
                $dados['imagem_url'] = $resultadoUpload->getSecurePath();
            }

            // Cria o registro no banco
            $post = Post::create([
                'titulo' => $dados['titulo'],
                'conteudo' => $dados['conteudo'],
                'imagem_url' => $dados['imagem_url'],
                'tipo' => $dados['tipo'],
                'link_externo' => $dados['link_externo'] ?? null,
                'user_id' => $dados['user_id']
            ]);

            return response()->json([
                'message' => 'Publicação criada com sucesso!',
                'post' => $post
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Se for erro de validação (ex: imagem muito grande), avisa o front
            return response()->json(['message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // 🔴 RETORNA O ERRO REAL DO PHP DIRETAMENTE NA TELA PARA NÓS
            return response()->json([
                'message' => 'Erro interno no servidor do Laravel.',
                'error_real' => $e->getMessage(),
                'linha' => $e->getLine()
            ], 500);
        }
    }
}
