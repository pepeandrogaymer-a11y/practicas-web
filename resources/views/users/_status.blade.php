@if (session('status'))
    <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">
        {{ session('status') }}
    </div>
@endif