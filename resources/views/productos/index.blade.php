@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="max-w-6xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Productos</h1>
        <div class="flex gap-2">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Inicio</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('productos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nuevo producto</a>
            @endif
        </div>
    </div>

    @include('users._status')

    {{-- Filtros, orden y cantidad por página: todo viaja por la URL (GET) --}}
    <form method="GET" action="{{ route('productos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded">
        <div>
            <label for="buscar" class="block text-sm font-medium mb-1">Buscar por nombre</label>
            <input type="text" id="buscar" name="buscar" value="{{ request('buscar') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label for="categoria" class="block text-sm font-medium mb-1">Categoría</label>
            <select id="categoria" name="categoria" class="w-full border rounded px-3 py-2">
                <option value="">Todas</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('categoria') == $category->id)>{{ $category->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="precio_min" class="block text-sm font-medium mb-1">Precio mínimo</label>
            <input type="number" step="0.01" id="precio_min" name="precio_min" value="{{ request('precio_min') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label for="precio_max" class="block text-sm font-medium mb-1">Precio máximo</label>
            <input type="number" step="0.01" id="precio_max" name="precio_max" value="{{ request('precio_max') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label for="estado" class="block text-sm font-medium mb-1">Estado</label>
            <select id="estado" name="estado" class="w-full border rounded px-3 py-2">
                <option value="">Todos</option>
                @foreach (\App\Models\Product::ESTADOS as $estado)
                    <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ $estado }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="orden" class="block text-sm font-medium mb-1">Ordenar por</label>
            <select id="orden" name="orden" class="w-full border rounded px-3 py-2">
                @foreach ([
                    'nombre_asc' => 'Nombre (A-Z)',
                    'nombre_desc' => 'Nombre (Z-A)',
                    'precio_desc' => 'Precio (mayor a menor)',
                    'precio_asc' => 'Precio (menor a mayor)',
                    'existencias_desc' => 'Existencias (mayor a menor)',
                    'existencias_asc' => 'Existencias (menor a mayor)',
                    'recientes' => 'Fecha (más recientes)',
                    'antiguos' => 'Fecha (más antiguos)',
                ] as $valor => $texto)
                    <option value="{{ $valor }}" @selected(request('orden', 'nombre_asc') === $valor)>{{ $texto }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="por_pagina" class="block text-sm font-medium mb-1">Registros por página</label>
            <select id="por_pagina" name="por_pagina" class="w-full border rounded px-3 py-2">
                @foreach ([10, 15, 25, 50] as $cantidad)
                    <option value="{{ $cantidad }}" @selected((int) request('por_pagina', 10) === $cantidad)>{{ $cantidad }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Aplicar</button>
            <a href="{{ route('productos.index') }}" class="px-4 py-2 border rounded hover:bg-gray-100">Limpiar filtros</a>
        </div>
    </form>

    <p class="text-sm text-gray-600 mb-3">
        {{ $products->total() }} {{ $products->total() === 1 ? 'producto encontrado' : 'productos encontrados' }}
        · Página {{ $products->currentPage() }} de {{ $products->lastPage() }}
    </p>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2">ID</th>
                    <th class="py-2">Nombre</th>
                    <th class="py-2">Descripción</th>
                    <th class="py-2">Categoría</th>
                    <th class="py-2">Precio</th>
                    <th class="py-2">Existencias</th>
                    <th class="py-2">Estado</th>
                    <th class="py-2">Fecha de registro</th>
                    @if (auth()->user()->isAdmin())
                        <th class="py-2">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b">
                        <td class="py-2">{{ $product->id }}</td>
                        <td class="py-2">{{ $product->nombre }}</td>
                        <td class="py-2">{{ \Illuminate\Support\Str::limit($product->descripcion, 40) }}</td>
                        <td class="py-2">{{ $product->category->nombre }}</td>
                        <td class="py-2">${{ number_format($product->precio, 2) }}</td>
                        <td class="py-2">{{ $product->existencias }}</td>
                        <td class="py-2"><x-status-badge :estado="$product->estado" /></td>
                        <td class="py-2">{{ $product->created_at->format('d/m/Y') }}</td>
                        @if (auth()->user()->isAdmin())
                            <td class="py-2">
                                <div class="flex gap-2 items-center">
                                    <a href="{{ route('productos.edit', $product) }}" class="text-yellow-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('productos.destroy', $product) }}"
                                          onsubmit="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="9" class="py-4 text-gray-500">No se encontraron productos con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection