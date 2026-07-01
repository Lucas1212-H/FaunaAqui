<?php

namespace App\Models;

use App\Support\StorageUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function foto(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => StorageUrl::publicUrl($value),
            set: fn (?string $value) => $value,
        );
    }
}
