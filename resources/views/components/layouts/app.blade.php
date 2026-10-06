<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? 'Painel' }} · Pedidos</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-300 font-sans text-zinc-800 antialiased">
        <div class="flex min-h-screen">
            <aside class="hidden w-64 shrink-0 flex-col bg-zinc-950 text-zinc-300 lg:flex">
                <div class="flex items-center gap-3 px-6 py-6">
                    <span class="grid size-9 place-items-center rounded-xl bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/30">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2.3 2.3c-.6.6-.2 1.7.7 1.7H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-white">Delivery</p>
                        <p class="text-xs text-zinc-500">Gestão de pedidos</p>
                    </div>
                </div>

                <nav class="mt-4 flex flex-col gap-1 px-3">
                    <x-nav-link :href="route('produtos.index')" :active="request()->routeIs('produtos.*')">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Produtos
                    </x-nav-link>
                    <x-nav-link :href="route('pedidos.index')" :active="request()->routeIs('pedidos.*')">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/></svg>
                        Pedidos
                    </x-nav-link>
                </nav>

                <p class="mt-auto px-6 py-6 text-xs text-zinc-600">Laravel {{ app()->version() }}</p>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex items-center justify-between gap-4 bg-zinc-950 px-4 py-3 text-white lg:hidden">
                    <span class="font-semibold">Delivery</span>
                    <nav class="flex gap-4 text-sm">
                        <a href="{{ route('produtos.index') }}" @class(['text-emerald-400' => request()->routeIs('produtos.*')])>Produtos</a>
                        <a href="{{ route('pedidos.index') }}" @class(['text-emerald-400' => request()->routeIs('pedidos.*')])>Pedidos</a>
                    </nav>
                </header>

                <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-8">
                    @if (session('status'))
                        <x-alert type="success">{{ session('status') }}</x-alert>
                    @endif
                    @if (session('error'))
                        <x-alert type="error">{{ session('error') }}</x-alert>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
