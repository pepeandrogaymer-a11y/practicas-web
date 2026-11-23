@extends('layouts.app')

@section('title', 'Partida #' . $game->id)

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Partida #{{ $game->id }}</h1>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.partidas.index') : route('juego.index') }}"
           class="px-3 py-1 border rounded text-sm hover:bg-gray-50">Salir</a>
    </div>

    <p class="mb-1"><strong>X:</strong> <span id="playerX"></span></p>
    <p class="mb-1"><strong>O:</strong> <span id="playerO"></span></p>
    <p id="mySymbol" class="text-sm text-gray-600 mb-4"></p>

    <p id="message" class="mb-4 font-semibold"></p>
    <div id="error" class="hidden bg-red-100 text-red-800 px-3 py-2 rounded mb-4"></div>

    <div id="board" class="grid grid-cols-3 gap-2 mb-6"></div>

    @if ($game->hasPlayer(auth()->user()) && in_array($game->status, ['esperando', 'en_curso'], true))
        <form id="leaveForm" method="POST" action="{{ route('juego.leave', $game) }}"
              onsubmit="return confirm('¿Seguro que quieres abandonar la partida?')">
            @csrf
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Abandonar partida
            </button>
        </form>
    @endif
</div>

<script>
    const stateUrl = @json(route('juego.state', $game));
    const moveUrl = @json(route('juego.move', $game));
    const csrf = document.querySelector('meta[name=csrf-token]').content;
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
    };
    const activos = ['esperando', 'en_curso'];
    let timer = null;

    // Dibuja la pantalla a partir del estado que manda el servidor
    function render(s) {
        document.getElementById('playerX').textContent = s.playerX;
        document.getElementById('playerO').textContent = s.playerO ?? 'Esperando oponente...';
        document.getElementById('mySymbol').textContent = s.mySymbol ? 'Tú juegas con: ' + s.mySymbol : '';
        document.getElementById('message').textContent = s.message;

        const board = document.getElementById('board');
        board.innerHTML = '';

        s.board.forEach((value, i) => {
            const libre = s.canMove && !value;
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.textContent = value ?? '';
            cell.disabled = !libre;
            cell.className = 'h-24 border-2 rounded text-5xl font-bold '
                + (value === 'X' ? 'text-blue-600' : 'text-red-600')
                + (libre ? ' hover:bg-gray-100' : '');
            cell.addEventListener('click', () => move(i));
            board.appendChild(cell);
        });

        const leave = document.getElementById('leaveForm');
        if (leave) leave.classList.toggle('hidden', !activos.includes(s.status));

        // Cuando la partida termina, se deja de preguntar al servidor
        if (!activos.includes(s.status)) clearInterval(timer);
    }

    function sesionCaducada(r) {
        if (r.status === 401 || r.status === 419) {
            window.location.href = '/login';
            return true;
        }
        return false;
    }

    async function fetchState() {
        try {
            const r = await fetch(stateUrl, { headers });
            if (sesionCaducada(r) || !r.ok) return;
            render(await r.json());
        } catch (e) { /* un fallo de red aislado no detiene el polling */ }
    }

    async function move(posicion) {
        const errorBox = document.getElementById('error');
        errorBox.classList.add('hidden');

        const r = await fetch(moveUrl, { method: 'POST', headers, body: JSON.stringify({ posicion }) });
        if (sesionCaducada(r)) return;

        const data = await r.json();

        if (!r.ok) {
            errorBox.textContent = data.message ?? 'No se pudo realizar el movimiento.';
            errorBox.classList.remove('hidden');
            fetchState();
            return;
        }

        render(data);
    }

    // Primer dibujo con lo que mandó el servidor, y luego polling cada 2 segundos
    render(@json($state));
    if (activos.includes(@json($state['status']))) {
        timer = setInterval(fetchState, 2000);
    }
</script>
@endsection