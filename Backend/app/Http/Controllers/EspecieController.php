<?php

namespace App\Http\Controllers;

use App\Models\Especie;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EspecieController extends Controller
{
    public function index(Request $request)
    {
        $query = Especie::with(['categoria:id_categoria,nome_popular,nome_cientifico,foto'])
            ->orderBy('nome_popular');

        if ($request->filled('id_categoria')) {
            $query->where('id_categoria', $request->integer('id_categoria'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'descricao' => ['nullable', 'string'],
            'nome_cientifico' => ['required', 'string', 'max:255', 'unique:especies,nome_cientifico'],
            'nome_popular' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
            'id_categoria' => ['required', 'integer', 'exists:categorias,id_categoria'],
        ]);

        // Se houver foto, fazer upload
        if ($request->hasFile('foto')) {
            $caminhoFoto = $request->file('foto')->store('especies', 'public');
            $dados['foto'] = $caminhoFoto;
        }

        $especie = Especie::create($dados);

        return response()->json($especie->load('categoria'), 201);
    }

    public function show(int $id)
    {
        $especie = Especie::with([
            'categoria:id_categoria,nome_popular,nome_cientifico,foto',
            'ocorrencias' => function ($query) {
                $query->select('id', 'especie_id', 'latitude', 'longitude', 'situacao_animal', 'ponto_referencia', 'created_at');
            }
            ])
            ->where('id_especie', $id)
            ->first();

        if (! $especie) {
            return response()->json(['message' => 'Espécie não encontrada'], 404);
        }

        return response()->json($especie);
    }

    public function update(Request $request, int $id)
    {
        $especie = Especie::where('id_especie', $id)->first();

        if (! $especie) {
            return response()->json(['message' => 'Espécie não encontrada'], 404);
        }

        $dados = $request->validate([
            'descricao' => ['nullable', 'string'],
            'nome_cientifico' => [
                'required',
                'string',
                'max:255',
                Rule::unique('especies', 'nome_cientifico')->ignore($especie->id_especie, 'id_especie'),
            ],
            'nome_popular' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
            'id_categoria' => ['required', 'integer', 'exists:categorias,id_categoria'],
        ]);

        // Se houver foto, fazer upload
        if ($request->hasFile('foto')) {
            // Deletar foto antiga se existir
            if ($especie->foto && Storage::disk('public')->exists($especie->foto)) {
                Storage::disk('public')->delete($especie->foto);
            }
            
            $caminhoFoto = $request->file('foto')->store('especies', 'public');
            $dados['foto'] = $caminhoFoto;
        }

        $especie->update($dados);

        return response()->json($especie->fresh()->load('categoria'));
    }

    public function destroy(int $id)
    {
        $especie = Especie::where('id_especie', $id)->first();

        if (! $especie) {
            return response()->json(['message' => 'Espécie não encontrada'], 404);
        }

        $especie->delete();

        return response()->json(['message' => 'Espécie excluída com sucesso']);
    }
    public function vincularOcorrencias(Request $request, int $id)
    {
        $especie = Especie::where('id_especie', $id)->first();

        if (! $especie) {
            return response()->json(['message' => 'Espécie não encontrada'], 404);
        }

        $dados = $request->validate([
            'ocorrencias_ids' => ['required', 'array'],
            'ocorrencias_ids.*' => ['integer'],
        ]);

        DB::table('ocorrencias')
            ->whereIn('id', $dados['ocorrencias_ids'])
            ->update(['especie_id' => $especie->id_especie]);

        return response()->json([
            'message' => 'Ocorrências vinculadas à espécie com sucesso!',
            'quantidade_vinculada' => count($dados['ocorrencias_ids']),
        ], 200);
    }
}
