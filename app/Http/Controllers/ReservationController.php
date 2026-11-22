<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReservationController extends Controller
{
    // Administrador: todas las reservaciones
    public function index()
    {
        $reservations = Reservation::with(['user', 'computer'])->latest()->paginate(15);

        return view('reservaciones.index', ['reservations' => $reservations, 'todas' => true]);
    }

    // Estudiante: solo las suyas. No recibe ningún id: usa al usuario de la sesión
    public function mine(Request $request)
    {
        $reservations = $request->user()->reservations()->with('computer')->latest()->paginate(15);

        return view('reservaciones.index', ['reservations' => $reservations, 'todas' => false]);
    }

    public function show(Reservation $reservation)
    {
        Gate::authorize('view', $reservation); // 403 si no es tuya ni eres admin

        $reservation->load(['user', 'computer']);

        return view('reservaciones.show', compact('reservation'));
    }

    public function store(Request $request, Computer $computer)
    {
        $reservado = DB::transaction(function () use ($request, $computer) {
            // Bloquea la fila del equipo: una petición simultánea espera aquí hasta que termine esta
            $equipo = Computer::whereKey($computer->id)->lockForUpdate()->first();

            if (! $equipo || $equipo->reservations()->where('estado', Reservation::ACTIVA)->exists()) {
                return false; // ya lo reservó alguien más
            }

            $equipo->reservations()->create([
                'user_id' => $request->user()->id, // siempre el de la sesión, nunca el de un formulario
                'estado' => Reservation::ACTIVA,
            ]);

            return true;
        });

        if (! $reservado) {
            return redirect()->route('equipos.index')
                ->with('error', 'Lo sentimos, ese equipo ya no está disponible.');
        }

        return redirect()->route('reservaciones.mias')
            ->with('status', 'Equipo reservado correctamente.');
    }

    public function cancel(Reservation $reservation)
    {
        Gate::authorize('cancel', $reservation);

        $reservation->update([
            'estado' => Reservation::CANCELADA,
            'cancelled_at' => now(),
        ]);

        return back()->with('status', 'Reservación cancelada correctamente.');
    }
}