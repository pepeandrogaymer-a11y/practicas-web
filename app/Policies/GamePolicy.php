<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
    // Ver una partida: sus dos jugadores o el administrador
    public function view(User $user, Game $game): bool
    {
        return $user->isAdmin() || $game->hasPlayer($user);
    }
}