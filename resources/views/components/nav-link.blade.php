@props(['active' => false])

<a {{ $attributes->class([
    'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
    'bg-emerald-500/10 text-emerald-400 ring-1 ring-inset ring-emerald-500/20' => $active,
    'text-zinc-400 hover:bg-zinc-900 hover:text-white' => ! $active,
]) }}>
    {{ $slot }}
</a>
