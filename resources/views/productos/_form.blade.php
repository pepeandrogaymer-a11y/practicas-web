<x-form-field name="nombre" label="Nombre" :value="$product->nombre ?? ''" />

<div class="mb-4">
    <label for="descripcion" class="block text-sm font-medium mb-1">Descripción</label>
    <textarea id="descripcion" name="descripcion" rows="3"
              class="w-full border rounded px-3 py-2">{{ old('descripcion', $product->descripcion ?? '') }}</textarea>
    @error('descripcion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label for="category_id" class="block text-sm font-medium mb-1">Categoría</label>
    <select id="category_id" name="category_id" class="w-full border rounded px-3 py-2">
        <option value="">Selecciona una categoría</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id ?? 0) === $category->id)>
                {{ $category->nombre }}
            </option>
        @endforeach
    </select>
    @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<x-form-field name="precio" label="Precio" type="number" step="0.01" :value="$product->precio ?? ''" />
<x-form-field name="existencias" label="Existencias" type="number" :value="$product->existencias ?? 0" />

<div class="mb-6">
    <label for="estado" class="block text-sm font-medium mb-1">Estado</label>
    <select id="estado" name="estado" class="w-full border rounded px-3 py-2">
        @foreach (\App\Models\Product::ESTADOS as $estado)
            <option value="{{ $estado }}" @selected(old('estado', $product->estado ?? 'Disponible') === $estado)>{{ $estado }}</option>
        @endforeach
    </select>
    @error('estado') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>