@props(['variant' => 'primary', 'href' => null])

@php
    $classes = [
        'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 cursor-pointer',
        'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20 hover:bg-emerald-500' => $variant === 'primary',
        'bg-white text-zinc-700 ring-1 ring-inset ring-zinc-300 hover:bg-zinc-50' => $variant === 'secondary',
        'text-zinc-500 hover:bg-zinc-200/60 hover:text-zinc-900' => $variant === 'ghost',
        'bg-zinc-900 text-white hover:bg-zinc-700' => $variant === 'danger',
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit'])->class($classes) }}>{{ $slot }}</button>
@endif
