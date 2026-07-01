<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function __construct(private CloudinaryService $cloudinary) {}

    public function index()
    {
        return response()->json(
            Categoria::withCount('especies')
                ->orderBy('nome_popular')
                ->get()
        );
    }

    public function show(int $id)
    {
        $categoria = Categoria::with('especies')->where('id_categoria', $id)->first();

        if (! $categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }

        return response()->json($categoria);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome_cientifico' => ['required', 'string', 'max:255', 'unique:categorias,nome_cientifico'],
            'nome_popular' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        if ($request->hasFile('foto')) {
            $dados['foto'] = $this->cloudinary->upload($request->file('foto'), 'categorias');
        }

        $categoria = Categoria::create($dados);

        return response()->json($categoria->loadCount('especies'), 201);
    }

    public function update(Request $request, int $id)
    {
        $categoria = Categoria::where('id_categoria', $id)->first();

        if (! $categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }

        $dados = $request->validate([
            'nome_cientifico' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categorias', 'nome_cientifico')->ignore($id, 'id_categoria'),
            ],
            'nome_popular' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        if ($request->hasFile('foto')) {
            $this->cloudinary->deleteByUrl($categoria->getRawOriginal('foto'));
            $dados['foto'] = $this->cloudinary->upload($request->file('foto'), 'categorias');
        }

        $categoria->update($dados);

        return response()->json($categoria->fresh()->loadCount('especies'));
    }

    public function destroy(int $id)
    {
        $categoria = Categoria::where('id_categoria', $id)->first();

        if (! $categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }

        $this->cloudinary->deleteByUrl($categoria->getRawOriginal('foto'));
        $categoria->delete();

        return response()->json(['message' => 'Categoria excluída com sucesso']);
    }
}
