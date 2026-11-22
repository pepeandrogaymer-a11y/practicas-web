@if (session('status'))
    <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 text-red-800 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif