<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    public const ESPERANDO = 'esperando';
    public const EN_CURSO = 'en_curso';
    public const TERMINADA = 'terminada';
    public const CANCELADA = 'cancelada';

    public const ESTADOS = [self::ESPERANDO, self::EN_CURSO, self::TERMINADA, self::CANCELADA];

    public const ETIQUETAS = [
        self::ESPERANDO => 'Esperando',
        self::EN_CURSO => 'En curso',
        self::TERMINADA => 'Terminada',
        self::CANCELADA => 'Cancelada',
    ];

    // Las 8 combinaciones ganadoras (posiciones 0 a 8 del tablero)
    private const LINEAS = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // horizontales
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // verticales
        [0, 4, 8], [2, 4, 6],            // diagonales
    ];

    protected $fillable = ['player_x_id', 'player_o_id', 'status', 'resultado', 'winner_id', 'finished_at'];

    protected function casts(): array
    {
        return ['finished_at' => 'datetime'];
    }

    // --- Relaciones (withTrashed: el nombre se sigue viendo aunque el usuario esté deshabilitado) ---

    public function playerX(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_x_id')->withTrashed();
    }

    public function playerO(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_o_id')->withTrashed();
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id')->withTrashed();
    }

    public function moves(): HasMany
    {
        return $this->hasMany(Move::class)->orderBy('id');
    }

    // --- Consultas reutilizables ---

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::ESPERANDO, self::EN_CURSO]);
    }

    public function scopeInvolving(Builder $query, User $user): Builder
    {
        return $query->where(fn ($q) => $q->where('player_x_id', $user->id)->orWhere('player_o_id', $user->id));
    }

    // --- Reglas del juego ---

    public function hasPlayer(User $user): bool
    {
        return $this->player_x_id === $user->id || $this->player_o_id === $user->id;
    }

    public function symbolFor(User $user): ?string
    {
        return match ($user->id) {
            $this->player_x_id => 'X',
            $this->player_o_id => 'O',
            default => null,
        };
    }

    public function opponentFor(User $user): ?User
    {
        return $this->player_x_id === $user->id ? $this->playerO : $this->playerX;
    }

    // Tablero de 9 casillas reconstruido a partir de los movimientos guardados
    public function board(): array
    {
        $board = array_fill(0, 9, null);

        foreach ($this->moves as $move) {
            $board[$move->posicion] = $move->simbolo;
        }

        return $board;
    }

    // X juega con los movimientos pares (0, 2, 4...) y O con los impares
    public function currentSymbol(): string
    {
        return $this->moves->count() % 2 === 0 ? 'X' : 'O';
    }

    public static function winnerSymbol(array $board): ?string
    {
        foreach (self::LINEAS as [$a, $b, $c]) {
            if ($board[$a] !== null && $board[$a] === $board[$b] && $board[$a] === $board[$c]) {
                return $board[$a];
            }
        }

        return null;
    }

    // --- Lo que ve cada usuario (lo usa la vista y el polling) ---

    public function stateFor(?User $user): array
    {
        $mine = $user ? $this->symbolFor($user) : null;
        $turn = $this->status === self::EN_CURSO ? $this->currentSymbol() : null;

        return [
            'status' => $this->status,
            'board' => $this->board(),
            'turn' => $turn,
            'mySymbol' => $mine,
            'canMove' => $mine !== null && $turn === $mine,
            'playerX' => $this->playerX?->name ?? 'Jugador eliminado',
            'playerO' => $this->player_o_id ? ($this->playerO?->name ?? 'Jugador eliminado') : null,
            'message' => $this->messageFor($user, $mine),
        ];
    }

    private function messageFor(?User $user, ?string $mine): string
    {
        return match ($this->status) {
            self::ESPERANDO => 'Esperando a que otro jugador se una a la partida...',
            self::EN_CURSO => $mine === null
                ? 'Partida en curso. Turno de ' . $this->currentSymbol() . '.'
                : ($this->currentSymbol() === $mine ? 'Es tu turno.' : 'Turno de tu oponente...'),
            self::CANCELADA => 'La partida fue cancelada.',
            default => $this->finishedMessage($user, $mine),
        };
    }

    private function finishedMessage(?User $user, ?string $mine): string
    {
        if ($this->resultado === 'empate') {
            return 'La partida terminó en empate.';
        }

        $abandono = $this->resultado === 'abandono';

        if ($user && $this->winner_id === $user->id) {
            return $abandono ? '¡Ganaste! Tu oponente abandonó la partida.' : '¡Ganaste!';
        }

        if ($mine !== null) {
            return $abandono ? 'Abandonaste la partida.' : 'Perdiste. ¡Suerte en la próxima!';
        }

        return 'Ganó ' . ($this->winner?->name ?? 'un jugador') . ($abandono ? ' (por abandono).' : '.');
    }

    // Texto para el historial de un usuario
    public function resultLabelFor(User $user): string
    {
        return match (true) {
            $this->status === self::CANCELADA => 'Cancelada',
            $this->resultado === 'empate' => 'Empate',
            $this->winner_id === $user->id => $this->resultado === 'abandono' ? 'Ganaste (tu oponente abandonó)' : 'Ganaste',
            default => $this->resultado === 'abandono' ? 'Perdiste (abandonaste)' : 'Perdiste',
        };
    }
}