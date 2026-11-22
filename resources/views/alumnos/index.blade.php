@extends('layouts.app')

@section('title', 'Alumnos')

@section('content')
<div class="max-w-5xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Alumnos</h1>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Inicio</a>
            <a href="{{ route('alumnos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nuevo alumno</a>
        </div>
    </div>

    @include('users._status')

    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2"><x-sort-link campo="nombre" titulo="Nombre" /></th>
                <th class="py-2"><x-sort-link campo="faltas" titulo="Faltas" /></th>
                <th class="py-2"><x-sort-link campo="calificacion" titulo="Calificación" /></th>
                <th class="py-2">Equivalencia</th>
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr class="border-b">
                    <td class="py-2">{{ $student->nombre }}</td>
                    <td class="py-2">{{ $student->faltas }}</td>
                    <td class="py-2">{{ number_format($student->calificacion, 2) }}</td>
                    <td class="py-2"><x-grade-badge :letra="$student->equivalencia" /></td>
                    <td class="py-2">
                        <div class="flex gap-2 items-center">
                            <a href="{{ route('alumnos.edit', $student) }}" class="text-yellow-600 hover:underline">Editar</a>
                            <form method="POST" action="{{ route('alumnos.destroy', $student) }}"
                                  onsubmit="return confirm('¿Eliminar a este alumno? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 text-gray-500">Todavía no hay alumnos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $students->links() }}</div>
</div>
@endsection