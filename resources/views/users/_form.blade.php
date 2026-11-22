<div class="mb-4">
    <label for="name" class="block text-sm font-medium mb-1">Nombre</label>
    <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}"
           class="w-full border rounded px-3 py-2" required>
    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label for="email" class="block text-sm font-medium mb-1">Correo electrónico</label>
    <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}"
           class="w-full border rounded px-3 py-2" required>
    @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label for="password" class="block text-sm font-medium mb-1">
        Contraseña @isset($user) <span class="text-gray-500">(déjala vacía para no cambiarla)</span> @endisset
    </label>
    <input type="password" id="password" name="password"
           class="w-full border rounded px-3 py-2" @empty($user) required @endempty>
    @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirmar contraseña</label>
    <input type="password" id="password_confirmation" name="password_confirmation"
           class="w-full border rounded px-3 py-2">
</div>

{{-- Solo el admin ve y puede cambiar el rol --}}
@if (auth()->user()->isAdmin())
    <div class="mb-6">
        <label for="role" class="block text-sm font-medium mb-1">Rol</label>
        <select id="role" name="role" class="w-full border rounded px-3 py-2">
            @foreach (['empleado' => 'Empleado', 'admin' => 'Administrador'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role ?? 'empleado') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
@endif