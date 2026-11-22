<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    public const ESTADOS = ['Disponible', 'Agotado'];

    protected $fillable = ['nombre', 'descripcion', 'category_id', 'precio', 'existencias', 'estado'];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'existencias' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Filtros combinables: cada uno solo se aplica si viene con valor
    public function scopeFilter(Builder $query, array $filtros): Builder
    {
        return $query
            ->when($filtros['buscar'] ?? null, fn ($q, $valor) => $q->where('nombre', 'like', "%{$valor}%"))
            ->when($filtros['categoria'] ?? null, fn ($q, $valor) => $q->where('category_id', $valor))
            ->when(is_numeric($filtros['precio_min'] ?? null), fn ($q) => $q->where('precio', '>=', $filtros['precio_min']))
            ->when(is_numeric($filtros['precio_max'] ?? null), fn ($q) => $q->where('precio', '<=', $filtros['precio_max']))
            ->when(
                in_array($filtros['estado'] ?? null, self::ESTADOS, true),
                fn ($q) => $q->where('estado', $filtros['estado'])
            );
    }
}
