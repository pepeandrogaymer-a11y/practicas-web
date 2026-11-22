@extends('layouts.app')

@section('title', 'Mi información académica')

@section('content')
<div class="max-w-lg mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6">Mi información académica</h1>

    @if ($student)
        <dl class="space-y-3 mb-6">
            <div><dt class="text-sm text-gray-500">Nombre</dt><dd>{{ auth()->user()->name }}</dd></div>
            <div><dt class="text-sm text-gray-500">Número de faltas</dt><dd>{{ $student->faltas }}</dd></div>
            <div><dt class="text-sm text-gray-500">Calificación</dt><dd>{{ number_format($student->calificacion, 2) }}</dd></div>
            <div>
                <dt class="text-sm text-gray-500">Equivalencia</dt>
                <dd><x-grade-badge :letra="$student->equivalencia" /> {{ config('academico.nombres.' . $student->equivalencia) }}</dd>
            </div>
        </dl>
    @else
        <p class="text-gray-600 mb-6">Todavía no tienes información académica registrada.</p>
    @endif

    <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Volver</a>
</div>
@endsection