<x-layouts.app title="Produtos">
    <x-page-header title="Produtos" subtitle="Itens disponíveis para pedido">
        <x-slot:actions>
            <x-button :href="route('produtos.create')">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Novo produto
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <form method="GET" action="{{ route('produtos.index') }}" class="flex gap-2 border-b border-zinc-200 p-4">
            <input type="search" name="busca" value="{{ $busca }}" placeholder="Buscar por nome..." class="form-input max-w-sm">
            <x-button variant="secondary">Buscar</x-button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">Produto</th>
                        <th class="px-6 py-3 font-medium">Preço</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Criado em</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($produtos as $produto)
                        <tr class="transition hover:bg-zinc-50/80">
                            <td class="px-6 py-4">
                                <a href="{{ route('produtos.show', $produto) }}" class="font-medium text-zinc-900 hover:text-emerald-700">{{ $produto->nome }}</a>
                                <p class="max-w-md truncate text-xs text-zinc-500">{{ $produto->descricao }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium"><x-money :value="$produto->preco" /></td>
                            <td class="px-6 py-4">
                                @if ($produto->disponivel)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span> Disponível
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-600 ring-1 ring-zinc-300">
                                        <span class="size-1.5 rounded-full bg-zinc-400"></span> Indisponível
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-500">{{ $produto->criado_em?->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <x-button variant="ghost" :href="route('produtos.edit', $produto)">Editar</x-button>
                                <x-delete-button :action="route('produtos.destroy', $produto)" :confirm="'Excluir o produto '.$produto->nome.'?'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-zinc-500">
                                Nenhum produto encontrado.
                                <a href="{{ route('produtos.create') }}" class="font-medium text-emerald-700 hover:underline">Cadastre o primeiro.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($produtos->hasPages())
            <div class="border-t border-zinc-200 px-6 py-4">{{ $produtos->links() }}</div>
        @endif
    </x-card>
</x-layouts.app>
