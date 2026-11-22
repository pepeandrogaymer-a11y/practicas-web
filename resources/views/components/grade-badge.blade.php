@props(['letra'])

@php
    $colores = [
        'NA' => 'bg-red-100 text-red-800',
        'SA' => 'bg-yellow-100 text-yellow-800',
        'DE' => 'bg-blue-100 text-blue-800',
        'AU' => 'bg-green-100 text-green-800',
    ];
@endphp

<span title="{{ config('academico.nombres.' . $letra) }}"
      class="px-2 py-1 rounded text-sm font-semibold {{ $colores[$letra] ?? '' }}">
    {{ $letra }}
</span>