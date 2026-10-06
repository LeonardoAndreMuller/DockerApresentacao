@props(['label', 'name', 'hint' => null])

<div {{ $attributes->class('flex flex-col gap-1.5') }}>
    <label for="{{ $name }}" class="text-sm font-medium text-zinc-700">{{ $label }}</label>
    {{ $slot }}
    @if ($hint)
        <p class="text-xs text-zinc-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
