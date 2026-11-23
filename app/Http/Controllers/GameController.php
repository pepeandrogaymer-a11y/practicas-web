<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveRequest;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
    // Sala de espera (lobby)
    public function index(Request $request)
    {
        $user = $request->user();

        // Mi partida activa, si tengo una
        $myGame = Game::active()->involving($user)->with(['playerX', 'playerO'])->first();

        // Partidas de otros jugadores que esperan oponente
        $waiting = Game::where('status', Game::ESPERANDO)
            ->where('player_x_id', '!=', $user->id)
            ->with('playerX')
            ->latest()
            ->get();

        // Jugadores disponibles: los que no tienen ninguna partida activa
        $busy = Game::active()->get(['player_x_id', 'player_o_id'])
            ->flatMap(fn ($game) => [$game->player_x_id, $game->player_o_id])
            ->filter()
            ->unique();

        $available = User::where('role', 'jugador')
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $busy)
            ->orderBy('name')
            ->get();

        return view('juego.index', compact('myGame', 'waiting', 'available'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $game = DB::transaction(function () use ($user) {
            // Bloquea la fila del usuario: si hace doble clic, la segunda petición espera y ve la partida ya creada
            User::whereKey($user->id)->lockForUpdate()->first();

            if (Game::active()->involving($user)->exists()) {
                return null;
            }

            return Game::create(['player_x_id' => $user->id, 'status' => Game::ESPERANDO]);
        });

        if (! $game) {
            return redirect()->route('juego.index')
                ->with('error', 'Ya tienes una partida activa. Termínala o abandónala antes de crear otra.');
        }

        return redirect()->route('juego.show', $game);
    }

    public function join(Request $request, Game $game)
    {
        $user = $request->user();

        $error = DB::transaction(function () use ($user, $game) {
            User::whereKey($user->id)->lockForUpdate()->first();
            $game = Game::whereKey($game->id)->lockForUpdate()->firstOrFail();

            if ($game->status !== Game::ESPERANDO) {
                return 'Esa partida ya no está disponible.';
            }

            if ($game->player_x_id === $user->id) {
                return 'No puedes unirte a tu propia partida.';
            }

            if (Game::active()->involving($user)->exists()) {
                return 'Ya tienes una partida activa. Termínala o abandónala antes de unirte a otra.';
            }

            // Asignación automática de símbolos: el creador ya es X, quien se une es O
            $game->update(['player_o_id' => $user->id, 'status' => Game::EN_CURSO]);

            return null;
        });

        if ($error) {
            return redirect()->route('juego.index')->with('error', $error);
        }

        return redirect()->route('juego.show', $game);
    }

    public function show(Request $request, Game $game)
    {
        Gate::authorize('view', $game); // 403 si no eres jugador de esta partida ni admin

        $game->load(['playerX', 'playerO', 'winner', 'moves']);

        return view('juego.show', [
            'game' => $game,
            'state' => $game->stateFor($request->user()),
        ]);
    }

    // Lo consulta el navegador cada 2 segundos (polling)
    public function state(Request $request, Game $game)
    {
        Gate::authorize('view', $game);

        $game->load(['playerX', 'playerO', 'winner', 'moves']);

        return response()->json($game->stateFor($request->user()));
    }

    public function move(MoveRequest $request, Game $game)
    {
        $user = $request->user();

        $state = DB::transaction(function () use ($request, $game, $user) {
            // Bloquea la partida: si ambos jugadores mueven a la vez, el segundo espera su turno
            $game = Game::whereKey($game->id)->lockForUpdate()->firstOrFail();
            $game->load('moves');

            abort_unless($game->hasPlayer($user), 403, 'No eres jugador de esta partida.');
            abort_unless($game->status === Game::EN_CURSO, 422, 'La partida no está en curso.');

            $symbol = $game->symbolFor($user); // se deduce de la sesión, no de lo que mande el navegador
            abort_unless($game->currentSymbol() === $symbol, 422, 'No es tu turno.');

            $posicion = $request->integer('posicion');
            abort_if($game->board()[$posicion] !== null, 422, 'Esa casilla ya está ocupada.');

            $game->moves()->create([
                'user_id' => $user->id,
                'posicion' => $posicion,
                'simbolo' => $symbol,
            ]);

            // Recarga los movimientos para incluir el recién guardado
            $game->load('moves');

            if (Game::winnerSymbol($game->board()) !== null) {
                // Solo quien acaba de mover puede haber completado la línea
                $game->update([
                    'status' => Game::TERMINADA,
                    'resultado' => 'victoria',
                    'winner_id' => $user->id,
                    'finished_at' => now(),
                ]);
            } elseif ($game->moves->count() === 9) {
                $game->update([
                    'status' => Game::TERMINADA,
                    'resultado' => 'empate',
                    'finished_at' => now(),
                ]);
            }

            $game->loadMissing(['playerX', 'playerO', 'winner']);

            return $game->stateFor($user);
        });

        return response()->json($state);
    }

    public function leave(Request $request, Game $game)
    {
        $user = $request->user();

        $mensaje = DB::transaction(function () use ($user, $game) {
            $game = Game::whereKey($game->id)->lockForUpdate()->firstOrFail();

            abort_unless($game->hasPlayer($user), 403, 'No eres jugador de esta partida.');

            if (! in_array($game->status, [Game::ESPERANDO, Game::EN_CURSO], true)) {
                return ['error', 'La partida ya había terminado.'];
            }

            if ($game->status === Game::ESPERANDO) {
                // Nadie se había unido: simplemente se cancela
                $game->update(['status' => Game::CANCELADA, 'resultado' => 'cancelada', 'finished_at' => now()]);

                return ['status', 'Cancelaste tu partida.'];
            }

            // En curso: gana el oponente por abandono
            $oponenteId = $game->player_x_id === $user->id ? $game->player_o_id : $game->player_x_id;

            $game->update([
                'status' => Game::TERMINADA,
                'resultado' => 'abandono',
                'winner_id' => $oponenteId,
                'finished_at' => now(),
            ]);

            return ['status', 'Abandonaste la partida.'];
        });

        return redirect()->route('juego.index')->with($mensaje[0], $mensaje[1]);
    }

    public function history(Request $request)
    {
        $games = Game::involving($request->user())
            ->whereIn('status', [Game::TERMINADA, Game::CANCELADA])
            ->with(['playerX', 'playerO', 'winner'])
            ->latest('finished_at')
            ->paginate(15);

        return view('juego.historial', compact('games'));
    }
}
