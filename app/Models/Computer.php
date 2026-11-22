<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Computer extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // La reservación activa (si existe): un equipo tiene como máximo una
    public function activeReservation(): HasOne
    {
        return $this->hasOne(Reservation::class)->where('estado', Reservation::ACTIVA);
    }

    // Equipos sin reservación activa
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereDoesntHave(
            'reservations',
            fn ($q) => $q->where('estado', Reservation::ACTIVA)
        );
    }
}
