@props(['type' => 'success'])

<div data-dismissible {{ $attributes->class([
    'mb-6 flex items-start justify-between gap-4 rounded-xl px-4 py-3 text-sm shadow-sm ring-1',
    'bg-emerald-50 text-emerald-800 ring-emerald-200' => $type === 'success',
    'bg-zinc-900 text-zinc-100 ring-zinc-800' => $type === 'error',
]) }}>
    <span>{{ $slot }}</span>
    <button type="button" data-dismiss class="opacity-60 transition hover:opacity-100" aria-label="Fechar">&times;</button>
</div>
