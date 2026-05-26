<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ocorrencia extends Model
{
    use HasFactory;

    protected $table = 'ocorrencias';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'denunciante_nome',
        'denunciante_contato_tipo',
        'denunciante_contato_valor',
        'categoria_ocorrencia',
        'tipo_animal',
        'local_ocorrencia',
        'descricao_ocorrencia',
        'latitude',
        'longitude',
        'ponto_referencia',
        'foto_path',
        'status',
        'parecer_tecnico',
    ];
}
