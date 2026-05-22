<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ocorrencia extends Model
{
    use HasFactory;

    protected $table = 'ocorrencias';
    protected $fillable = [
        'denunciante_nome',
        'denunciante_contato_tipo',
        'denunciante_contato_valor',
        'codigo_acesso',
        'categoria_ocorrencia',
        'tipo_animal',
        'situacao_animal',
        'descricao',
        'latitude',
        'longitude',
        'ponto_referencia',
        'foto_path',
        'status',
        'parecer_tecnico',
    ];
}
