<x-layouts.app :title="'Pedido #'.$pedido->id">
    <x-page-header :title="'Pedido #'.str_pad($pedido->id, 5, '0', STR_PAD_LEFT)" :subtitle="'Criado em '.$pedido->criado_em?->format('d/m/Y H:i').' · atualizado em '.$pedido->atualizado_em?->format('d/m/Y H:i')">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('pedidos.index')">Voltar</x-button>
            <x-delete-button :action="route('pedidos.destroy', $pedido)" :confirm="'Excluir o pedido #'.$pedido->id.'?'" />
            <x-button :href="route('pedidos.edit', $pedido)">Editar</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="overflow-hidden lg:col-span-2">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-200 text-xs uppercase tracking-wide text-zinc-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">Produto</th>
                        <th class="px-6 py-3 text-right font-medium">Qtd.</th>
                        <th class="px-6 py-3 text-right font-medium">Preço unit.</th>
                        <th class="px-6 py-3 text-right font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach ($pedido->itens as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('produtos.show', $item->produto) }}" class="font-medium text-zinc-900 hover:text-emerald-700">{{ $item->produto->nome }}</a>
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums">{{ $item->quantidade }}</td>
                            <td class="px-6 py-4 text-right"><x-money :value="$item->preco_unitario" /></td>
                            <td class="px-6 py-4 text-right font-medium"><x-money :value="$item->quantidade * $item->preco_unitario" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>

        <div class="space-y-6">
            <x-card class="space-y-4 p-6 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500">Status</span>
                    <x-status-badge :status="$pedido->status" />
                </div>
                <div>
                    <p class="text-zinc-500">Observação</p>
                    <p class="mt-1 text-zinc-800">{{ $pedido->observacao ?: '—' }}</p>
                </div>
            </x-card>

            <x-card class="overflow-hidden">
                <dl class="space-y-2 p-6 text-sm">
                    <div class="flex justify-between text-zinc-500"><dt>Subtotal</dt><dd><x-money :value="$pedido->subtotal" /></dd></div>
                    <div class="flex justify-between text-zinc-500"><dt>Entrega</dt><dd><x-money :value="$pedido->taxa_entrega" /></dd></div>
                </dl>
                <div class="flex items-center justify-between bg-zinc-950 px-6 py-4 text-white">
                    <span class="text-sm text-zinc-400">Total</span>
                    <x-money :value="$pedido->total" class="text-xl font-semibold text-emerald-400" />
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
