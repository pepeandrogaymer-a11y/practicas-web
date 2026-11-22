<div class="mb-4">
    <label for="name" class="block text-sm font-medium mb-1">Nombre</label>
    <input type="text" id="name" name="name"
           value="{{ old('name', isset($student) ? $student->user->name : '') }}"
           class="w-full border rounded px-3 py-2">
    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label for="email" class="block text-sm font-medium mb-1">Correo electrónico</label>
    <input type="email" id="email" name="email"
           value="{{ old('email', isset($student) ? $student->user->email : '') }}"
           class="w-full border rounded px-3 py-2">
    @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label for="password" class="block text-sm font-medium mb-1">
        Contraseña @isset($student) <span class="text-gray-500">(déjala vacía para no cambiarla)</span> @endisset
    </label>
    <input type="password" id="password" name="password" class="w-full border rounded px-3 py-2">
    @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirmar contraseña</label>
    <input type="password" id="password_confirmation" name="password_confirmation"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-4">
    <label for="faltas" class="block text-sm font-medium mb-1">
        Número de faltas (0 a {{ config('academico.max_faltas') }})
    </label>
    <input type="number" id="faltas" name="faltas"
           value="{{ old('faltas', isset($student) ? $student->faltas : 0) }}"
           class="w-full border rounded px-3 py-2">
    @error('faltas') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
    <label for="calificacion" class="block text-sm font-medium mb-1">
        Calificación ({{ config('academico.min_calificacion') }} a {{ config('academico.max_calificacion') }})
    </label>
    <input type="number" step="0.01" id="calificacion" name="calificacion"
           value="{{ old('calificacion', isset($student) ? $student->calificacion : 0) }}"
           class="w-full border rounded px-3 py-2">
    @error('calificacion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    <p class="text-gray-500 text-sm mt-1">La equivalencia (NA, SA, DE o AU) se calcula automáticamente.</p>
</div>