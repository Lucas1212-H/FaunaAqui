<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function __construct(private CloudinaryService $cloudinary) {}

    public function index()
    {
        try {
            $posts = Post::orderBy('created_at', 'desc')->get()->map(function ($post) {
                $post->autor = $post->autor ? ['name' => $post->autor->name] : ['name' => 'Administração'];

                return $post;
            });

            return response()->json($posts, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar publicações',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function isAdmin(): bool
    {
        $user = auth()->user();

        return $user && $user->tipo_conta === 'Administrador';
    }

    public function update(Request $request, $id)
    {
        if (! $this->isAdmin()) {
            return response()->json(['message' => 'Apenas administradores podem editar anúncios.'], 403);
        }

        try {
            $post = Post::findOrFail($id);

            $dados = $request->validate([
                'titulo' => 'required|string|max:255',
                'conteudo' => 'required|string',
                'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
                'tipo' => 'required|in:Noticia,Conferencia,Anuncio',
                'link_externo' => 'nullable|string',
            ]);

            if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
                $this->cloudinary->deleteByUrl($post->getRawOriginal('imagem_url'));
                $post->imagem_url = $this->cloudinary->upload($request->file('imagem'), 'posts');
            }

            $post->titulo = $dados['titulo'];
            $post->conteudo = $dados['conteudo'];
            $post->tipo = $dados['tipo'];
            $post->link_externo = $dados['link_externo'] ?? null;

            $post->save();

            return response()->json([
                'message' => 'Publicação atualizada com sucesso!',
                'post' => $post,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar publicação.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        if (! $this->isAdmin()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        try {
            $post = Post::findOrFail($id);
            $this->cloudinary->deleteByUrl($post->getRawOriginal('imagem_url'));
            $post->delete();

            return response()->json(['message' => 'Publicação excluída com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir publicação.'], 500);
        }
    }

    public function store(Request $request)
    {
        if (! $this->isAdmin()) {
            return response()->json(['message' => 'Apenas administradores podem publicar anúncios.'], 403);
        }

        try {
            $dados = $request->validate([
                'titulo' => 'required|string|max:255',
                'conteudo' => 'required|string',
                'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
                'tipo' => 'required|in:Noticia,Conferencia,Anuncio',
                'link_externo' => 'nullable|string',
            ]);

            $imagemUrl = null;

            if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
                $imagemUrl = $this->cloudinary->upload($request->file('imagem'), 'posts');
            }

            $post = Post::create([
                'titulo' => $dados['titulo'],
                'conteudo' => $dados['conteudo'],
                'imagem_url' => $imagemUrl,
                'tipo' => $dados['tipo'],
                'link_externo' => $dados['link_externo'] ?? null,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Publicação criada com sucesso!',
                'post' => $post,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar publicação.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
