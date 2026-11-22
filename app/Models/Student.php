<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = ['user_id', 'faltas', 'calificacion'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Equivalencia calculada automáticamente: $student->equivalencia
    public function getEquivalenciaAttribute(): string
    {
        $calificacion = (float) $this->calificacion;

        foreach (config('academico.minimos') as $letra => $minimo) {
            if ($calificacion >= $minimo) {
                return $letra;
            }
        }

        return 'NA';
    }
}
