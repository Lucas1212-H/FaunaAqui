<?php

namespace App\Http\Controllers;

use App\Models\Ocorrencia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OcorrenciaController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'denunciante_nome'    => 'required|string|max:255',
            'denunciante_contato_tipo'  => 'required|string',
            'denunciante_contato_valor' => 'required|string',
            'categoria_ocorrencia'=> 'required|string',
            'tipo_animal'         => 'required|string',
            'situacao_animal'     => 'required|string',
            'descricao'           => 'required|string',
            'latitude'            => 'required|numeric',
            'longitude'           => 'required|numeric',
            'ponto_referencia'    => 'required|string',
            'foto'                => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('ocorrencias', 'public');
        }

        $ocorrencia = Ocorrencia::create([
            'denunciante_nome'          => $request->denunciante_nome,
            'denunciante_contato_tipo'  => $request->denunciante_contato_tipo,
            'denunciante_contato_valor' => $request->denunciante_contato_valor,
            'codigo_ocorrencia'         => strtoupper(Str::random(6)),
            'categoria_ocorrencia'      => $request->categoria_ocorrencia,
            'tipo_animal'               => $request->tipo_animal,
            'local_ocorrencia'          => $request->situacao_animal,
            'descricao_ocorrencia'      => $request->descricao,
            'latitude'                  => $request->latitude,
            'longitude'                 => $request->longitude,
            'ponto_referencia'          => $request->ponto_referencia,
            'foto_path'                 => $fotoPath, 
            'status'                    => 'Pendente', 
        ]);

        return response()->json([
            'message' => 'Ocorrência registrada com sucesso!',
            'codigo' => $ocorrencia->codigo_ocorrencia,
            'data' => $ocorrencia
        ], 201);
    }
    /**
     * Lista ocorrências pendentes para triagem
     */
    public function indexPendentes()
    {
        try {
            $ocorrencias = Ocorrencia::where('status', 'Pendente')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($item) => $this->mapearOcorrencia($item));
            
            return response()->json($ocorrencias, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar ocorrências: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista ocorrências arquivadas (resolvidas/descartadas)
     */
    public function indexArquivadas()
    {
        try {
            $ocorrencias = Ocorrencia::whereIn('status', ['Resolvido', 'Falso Alarme', 'Em Atendimento'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(fn($item) => $this->mapearOcorrencia($item));
            
            return response()->json($ocorrencias, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar arquivadas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza parecer técnico e status da ocorrência
     */
    public function avaliar(Request $request, $id)
    {
        try {
            $ocorrencia = Ocorrencia::where('codigo_ocorrencia', $id)->first();
            
            if (!$ocorrencia) {
                return response()->json([
                    'error' => 'Ocorrência não encontrada'
                ], 404);
            }

            $request->validate([
                'status' => 'required|in:Pendente,Em Atendimento,Resolvido,Falso Alarme',
                'parecer_tecnico' => 'nullable|string'
            ]);

            $ocorrencia->update([
                'status' => $request->status,
                'parecer_tecnico' => $request->parecer_tecnico
            ]);

            return response()->json([
                'message' => 'Ocorrência atualizada com sucesso!',
                'data' => $this->mapearOcorrencia($ocorrencia)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mapeia campos do DB para formato esperado pelo frontend
     */
    private function mapearOcorrencia($ocorrencia)
    {
        return [
            'id' => $ocorrencia->codigo_ocorrencia,
            'codigo_ocorrencia' => $ocorrencia->codigo_ocorrencia,
            'tipo_animal' => $ocorrencia->tipo_animal,
            'denunciante_nome' => $ocorrencia->denunciante_nome,
            'categoria_ocorrencia' => $ocorrencia->categoria_ocorrencia,
            'descricao' => $ocorrencia->descricao_ocorrencia,
            'descricao_ocorrencia' => $ocorrencia->descricao_ocorrencia,
            'foto_path' => $ocorrencia->foto_path,
            'ponto_referencia' => $ocorrencia->ponto_referencia,
            'created_at' => $ocorrencia->created_at,
            'updated_at' => $ocorrencia->updated_at,
            'status' => $ocorrencia->status,
            'situacao_animal' => $ocorrencia->local_ocorrencia,
            'local_ocorrencia' => $ocorrencia->local_ocorrencia,
            'parecer_tecnico' => $ocorrencia->parecer_tecnico,
            'latitude' => $ocorrencia->latitude,
            'longitude' => $ocorrencia->longitude,
        ];
    }
}