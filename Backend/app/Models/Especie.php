<?php

namespace App\Models;

use App\Support\StorageUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(Ocorrencia::class, 'especie_id', 'id_especie');
    }

    protected function foto(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => StorageUrl::publicUrl($value),
            set: fn (?string $value) => $value,
        );
    }
}
