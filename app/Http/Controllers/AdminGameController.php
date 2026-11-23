<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminGameController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado');

        $games = Game::with(['playerX', 'playerO', 'winner'])
            ->when(in_array($estado, Game::ESTADOS, true), fn ($q) => $q->where('status', $estado))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.partidas', compact('games', 'estado'));
    }

    public function cancel(Game $game)
    {
        $cancelada = DB::transaction(function () use ($game) {
            $game = Game::whereKey($game->id)->lockForUpdate()->firstOrFail();

            if (! in_array($game->status, [Game::ESPERANDO, Game::EN_CURSO], true)) {
                return false;
            }

            $game->update(['status' => Game::CANCELADA, 'resultado' => 'cancelada', 'finished_at' => now()]);

            return true;
        });

        return back()->with(
            $cancelada ? 'status' : 'error',
            $cancelada ? 'Partida cancelada correctamente.' : 'Esa partida ya no está activa.'
        );
    }
}
