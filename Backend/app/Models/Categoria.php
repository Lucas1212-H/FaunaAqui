<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nome_cientifico',
        'nome_popular',
        'foto',
    ];

    public function especies(): HasMany
    {
        return $this->hasMany(Especie::class, 'id_categoria', 'id_categoria');
    }
}
