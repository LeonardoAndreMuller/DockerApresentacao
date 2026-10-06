<x-layouts.app :title="$produto->nome">
    <x-page-header :title="$produto->nome" :subtitle="'Produto #'.$produto->id">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('produtos.index')">Voltar</x-button>
            <x-delete-button :action="route('produtos.destroy', $produto)" :confirm="'Excluir o produto '.$produto->nome.'?'" />
            <x-button :href="route('produtos.edit', $produto)">Editar</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="p-6 lg:col-span-2">
            <h2 class="text-sm font-semibold text-zinc-900">Descrição</h2>
            <p class="mt-2 text-sm leading-relaxed text-zinc-600">{{ $produto->descricao ?: 'Sem descrição.' }}</p>

            <h2 class="mt-8 text-sm font-semibold text-zinc-900">Atributos</h2>
            @if (empty($produto->atributos))
                <p class="mt-2 text-sm text-zinc-500">Nenhum atributo informado.</p>
            @else
                <dl class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach ($produto->atributos as $chave => $valor)
                        <div class="rounded-xl bg-zinc-200/60 px-4 py-3 ring-1 ring-zinc-300">
                            <dt class="text-xs uppercase tracking-wide text-zinc-500">{{ str_replace('_', ' ', $chave) }}</dt>
                            <dd class="mt-1 text-sm font-medium text-zinc-800">
                                @if (is_bool($valor))
                                    {{ $valor ? 'Sim' : 'Não' }}
                                @elseif (is_array($valor))
                                    {{ implode(', ', array_map(fn ($item) => is_scalar($item) ? $item : json_encode($item), $valor)) ?: '—' }}
                                @else
                                    {{ $valor }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </x-card>

        <x-card class="divide-y divide-zinc-100">
            <div class="p-6">
                <p class="text-xs uppercase tracking-wide text-zinc-500">Preço</p>
                <p class="mt-1 text-3xl font-semibold text-emerald-600"><x-money :value="$produto->preco" /></p>
            </div>
            <dl class="space-y-3 p-6 text-sm">
                <div class="flex justify-between"><dt class="text-zinc-500">Status</dt><dd class="font-medium">{{ $produto->disponivel ? 'Disponível' : 'Indisponível' }}</dd></div>
                <div class="flex justify-between"><dt class="text-zinc-500">Em pedidos</dt><dd class="font-medium">{{ $produto->itens_pedido_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-zinc-500">Criado em</dt><dd class="font-medium">{{ $produto->criado_em?->format('d/m/Y H:i') }}</dd></div>
            </dl>
        </x-card>
    </div>
</x-layouts.app>
