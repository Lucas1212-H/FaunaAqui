<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
