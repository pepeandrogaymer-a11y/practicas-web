<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComputerRequest;
use App\Models\Computer;
use Illuminate\Http\Request;

class ComputerController extends Controller
{
    public function index(Request $request)
    {
        $query = Computer::with('activeReservation.user')->orderBy('nombre');

        // El estudiante solo ve los equipos disponibles; el admin ve todos
        if (! $request->user()->isAdmin()) {
            $query->available();
        }

        $computers = $query->paginate(15);

        return view('equipos.index', compact('computers'));
    }

    public function create()
    {
        return view('equipos.create');
    }

    public function store(ComputerRequest $request)
    {
        Computer::create($request->validated());

        return redirect()->route('equipos.index')
            ->with('status', 'Equipo registrado correctamente.');
    }

    public function edit(Computer $computer)
    {
        return view('equipos.edit', compact('computer'));
    }

    public function update(ComputerRequest $request, Computer $computer)
    {
        $computer->update($request->validated());

        return redirect()->route('equipos.index')
            ->with('status', 'Equipo actualizado correctamente.');
    }

    public function destroy(Computer $computer)
    {
        if ($computer->reservations()->where('estado', 'activa')->exists()) {
            return back()->with('error', 'No se puede eliminar: el equipo tiene una reservación activa.');
        }

        $computer->delete(); // su historial de reservaciones se borra en cascada

        return redirect()->route('equipos.index')
            ->with('status', 'Equipo eliminado correctamente.');
    }
}
