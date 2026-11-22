@props(['estado'])

<span class="px-2 py-1 rounded text-sm font-semibold
    {{ $estado === 'Disponible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
    {{ $estado }}
</span>