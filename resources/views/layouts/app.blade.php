<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Prácticas Web')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    @auth
        <nav class="bg-white shadow">
            <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
                <a href="{{ route('dashboard') }}" class="font-semibold">Prácticas Web</a>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-700">
                        {{ auth()->user()->name }}
                        <span class="text-gray-400">({{ auth()->user()->role }})</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    @yield('content')
</body>
</html>