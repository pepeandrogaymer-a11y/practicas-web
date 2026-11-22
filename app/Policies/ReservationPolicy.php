<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    // Ver el detalle: el dueño o el administrador
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->isAdmin() || $reservation->user_id === $user->id;
    }

    // Cancelar: la reservación debe estar activa, y ser del usuario o ser admin
    public function cancel(User $user, Reservation $reservation): bool
    {
        return $reservation->isActive()
            && ($user->isAdmin() || $reservation->user_id === $user->id);
    }
}
