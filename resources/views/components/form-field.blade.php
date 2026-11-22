@props(['name', 'label', 'type' => 'text', 'value' => '', 'step' => null])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium mb-1">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
           @if ($step) step="{{ $step }}" @endif
           class="w-full border rounded px-3 py-2">
    @error($name) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>