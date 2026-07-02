<?php

namespace App\Http\Controllers;

use App\Models\Ocorrencia;
use App\Services\CloudinaryService;
use App\Support\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OcorrenciaController extends Controller
{
    public function __construct(private CloudinaryService $cloudinary) {}

    public function store(Request $request)
    {
        $request->validate([
            'denunciante_nome' => 'required|string|max:255',
            'denunciante_contato_tipo' => 'required|string',
            'denunciante_contato_valor' => 'required|string',
            'distincao_biologica' => 'required|string',
            'tipo_animal' => 'required|string',
            'situacao_animal' => 'required|in:Preso,Morto,Ferido,Avistado,Área de Risco,Outro',
            'descricao' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'ponto_referencia' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $this->cloudinary->upload($request->file('foto'), 'ocorrencias');
        }

        $ocorrencia = Ocorrencia::create([
            'denunciante_nome' => $request->denunciante_nome,
            'denunciante_contato_tipo' => $request->denunciante_contato_tipo,
            'denunciante_contato_valor' => $request->denunciante_contato_valor,
            'distincao_biologica' => $request->distincao_biologica,
            'tipo_animal' => $request->tipo_animal,
            'situacao_animal' => $request->situacao_animal,
            'descricao_ocorrencia' => $request->descricao,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'ponto_referencia' => $request->ponto_referencia,
            'foto_path' => $fotoPath,
            'status' => 'Pendente',
        ]);

        return response()->json([
            'message' => 'Ocorrência registrada com sucesso!',
            'data' => $this->mapearOcorrencia($ocorrencia),
        ], 201);
    }

    public function indexPendentes()
    {
        try {
            $ocorrencias = Ocorrencia::where('status', 'Pendente')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($item) => $this->mapearOcorrencia($item));

            return response()->json($ocorrencias, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar ocorrências: '.$e->getMessage(),
            ], 500);
        }
    }

    public function indexArquivadas()
    {
        try {
            $ocorrencias = Ocorrencia::whereIn('status', ['Resolvido', 'Falso Alarme', 'Em Atendimento', 'Publicado'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(fn ($item) => $this->mapearOcorrencia($item));

            return response()->json($ocorrencias, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar arquivadas: '.$e->getMessage(),
            ], 500);
        }
    }

    public function avaliar(Request $request, $id)
    {
        try {
            $ocorrencia = Ocorrencia::where('id', $id)->first();

            if (! $ocorrencia) {
                return response()->json([
                    'error' => 'Ocorrência não encontrada',
                ], 404);
            }

            $request->validate([
                'status' => 'required|in:Pendente,Em Atendimento,Resolvido,Falso Alarme,Publicado',
                'parecer_tecnico' => 'nullable|string',
            ]);

            $ocorrencia->update([
                'status' => $request->status,
                'parecer_tecnico' => $request->parecer_tecnico,
            ]);

            return response()->json([
                'message' => 'Ocorrência atualizada com sucesso!',
                'data' => $this->mapearOcorrencia($ocorrencia),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar: '.$e->getMessage(),
            ], 500);
        }
    }

    private function mapearOcorrencia($ocorrencia)
    {
        return [
            'id' => $ocorrencia->id,
            'tipo_animal' => $ocorrencia->tipo_animal,
            'denunciante_nome' => $ocorrencia->denunciante_nome,
            'distincao_biologica' => $ocorrencia->distincao_biologica,
            'descricao' => $ocorrencia->descricao_ocorrencia,
            'descricao_ocorrencia' => $ocorrencia->descricao_ocorrencia,
            'foto_path' => $ocorrencia->foto_path,
            'foto_url' => StorageUrl::publicUrl($ocorrencia->foto_path),
            'ponto_referencia' => $ocorrencia->ponto_referencia,
            'created_at' => $ocorrencia->created_at,
            'updated_at' => $ocorrencia->updated_at,
            'status' => $ocorrencia->status,
            'publicado_no_mapa' => (bool) $ocorrencia->publicado_no_mapa,
            'situacao_animal' => $ocorrencia->situacao_animal,
            'parecer_tecnico' => $ocorrencia->parecer_tecnico,
            'latitude' => $ocorrencia->latitude,
            'longitude' => $ocorrencia->longitude,
        ];
    }

    public function indexPublicados()
    {
        try {
            $publicados = Ocorrencia::where('status', 'Publicado')
                ->where('publicado_no_mapa', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($item) => $this->mapearOcorrencia($item));

            return response()->json($publicados, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar publicados: '.$e->getMessage(),
            ], 500);
        }
    }

    public function indexRecentes()
    {
        try {
            $recentes = Ocorrencia::whereIn('status', ['Pendente', 'Em Atendimento', 'Publicado'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'tipo_animal' => $item->tipo_animal,
                    'distincao_biologica' => $item->distincao_biologica,
                    'situacao_animal' => $item->situacao_animal,
                    'status' => $item->status,
                    'ponto_referencia' => $item->ponto_referencia,
                    'created_at' => $item->created_at,
                ]);

            return response()->json($recentes, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar ocorrências recentes: '.$e->getMessage(),
            ], 500);
        }
    }

    public function showPublicada($id)
    {
        try {
            $ocorrencia = Ocorrencia::where('id', $id)
                ->where('status', 'Publicado')
                ->first();

            if (! $ocorrencia) {
                return response()->json([
                    'error' => 'Ocorrência não encontrada',
                ], 404);
            }

            return response()->json([
                'data' => $this->mapearOcorrencia($ocorrencia),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar ocorrência: '.$e->getMessage(),
            ], 500);
        }
    }

    public function publicar($id)
    {
        try {
            $ocorrencia = Ocorrencia::where('id', $id)->first();

            if (! $ocorrencia) {
                return response()->json([
                    'error' => 'Ocorrência não encontrada',
                ], 404);
            }

            $ocorrencia->update([
                'status' => 'Publicado',
                'publicado_no_mapa' => true,
            ]);

            return response()->json([
                'message' => 'Ocorrência publicada com sucesso!',
                'data' => $this->mapearOcorrencia($ocorrencia),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao publicar: '.$e->getMessage(),
            ], 500);
        }
    }

    public function despublicar($id)
    {
        try {
            $ocorrencia = Ocorrencia::where('id', $id)->first();

            if (! $ocorrencia) {
                return response()->json([
                    'error' => 'Ocorrência não encontrada',
                ], 404);
            }

            $ocorrencia->update([
                'publicado_no_mapa' => false,
            ]);

            return response()->json([
                'message' => 'Ocorrência removida do mapa com sucesso!',
                'data' => $this->mapearOcorrencia($ocorrencia),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover do mapa: '.$e->getMessage(),
            ], 500);
        }
    }

    public function pendentes()
    {
        return $this->indexPendentes();
    }

    public function dadosParaAnalise()
    {
        try {
            $dados = Ocorrencia::where('status', 'Publicado')
                ->select('tipo_animal', 'situacao_animal', 'latitude', 'longitude', 'created_at')
                ->get()
                ->map(fn ($item) => [
                    'especie' => $item->tipo_animal,
                    'situacao' => $item->situacao_animal,
                    'situacao_animal' => $item->situacao_animal,
                    'lat' => $item->latitude !== null ? (float) $item->latitude : null,
                    'lng' => $item->longitude !== null ? (float) $item->longitude : null,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'ano' => $item->created_at->format('Y'),
                    'mes' => $item->created_at->format('m'),
                    'hora' => $item->created_at->format('H'),
                ]);

            return response()->json($dados, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar dados: ' . $e->getMessage()], 500);
        }
    }
}