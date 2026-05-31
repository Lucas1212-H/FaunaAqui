<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Ocorrencia;
class Especie extends Model
{
    protected $table = 'especies';

    protected $primaryKey = 'id_especie';

    protected $fillable = [
        'descricao',
        'nome_cientifico',
        'nome_popular',
        'foto',
        'id_categoria',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function ocorrencias(): HasMany
    {
        // estrutura que o Laravel exige:
        // hasMany(ModelRelacionado, 'chave_estrangeira_na_tabela_relacionada', 'chave_primaria_na_tabela_atual')
        return $this->hasMany(Ocorrencia::class, 'especie_id', 'id_especie');
    }
}
