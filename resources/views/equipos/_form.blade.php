<x-form-field name="nombre" label="Nombre del equipo" :value="$computer->nombre ?? ''" />

<div class="mb-6">
    <label for="descripcion" class="block text-sm font-medium mb-1">Descripción (opcional)</label>
    <textarea id="descripcion" name="descripcion" rows="3"
              class="w-full border rounded px-3 py-2">{{ old('descripcion', $computer->descripcion ?? '') }}</textarea>
    @error('descripcion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>