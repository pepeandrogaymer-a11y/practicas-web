<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    public const ACTIVA = 'activa';
    public const CANCELADA = 'cancelada';

    protected $fillable = ['user_id', 'computer_id', 'estado', 'cancelled_at'];

    protected function casts(): array
    {
        return ['cancelled_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class);
    }

    public function isActive(): bool
    {
        return $this->estado === self::ACTIVA;
    }
}