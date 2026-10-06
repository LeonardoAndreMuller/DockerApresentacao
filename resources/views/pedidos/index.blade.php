<x-layouts.app title="Pedidos">
    <x-page-header title="Pedidos" subtitle="Acompanhe e gerencie os pedidos">
        <x-slot:actions>
            <x-button :href="route('pedidos.create')">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Novo pedido
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('pedidos.index') }}" @class([
            'rounded-full px-3 py-1 text-xs font-medium ring-1 transition',
            'bg-zinc-900 text-white ring-zinc-900' => ! $status,
            'bg-white text-zinc-600 ring-zinc-200 hover:ring-zinc-400' => $status,
        ])>Todos</a>
        @foreach (\App\Enums\StatusPedido::cases() as $opcao)
            <a href="{{ route('pedidos.index', ['status' => $opcao->value]) }}" @class([
                'rounded-full px-3 py-1 text-xs font-medium ring-1 transition',
                'bg-emerald-600 text-white ring-emerald-600' => $status === $opcao,
                'bg-white text-zinc-600 ring-zinc-200 hover:ring-zinc-400' => $status !== $opcao,
            ])>{{ $opcao->label() }}</a>
        @endforeach
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-200 text-xs uppercase tracking-wide text-zinc-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">Pedido</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Itens</th>
                        <th class="px-6 py-3 font-medium">Total</th>
                        <th class="px-6 py-3 font-medium">Criado em</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($pedidos as $pedido)
                        <tr class="transition hover:bg-zinc-200/60">
                            <td class="px-6 py-4">
                                <a href="{{ route('pedidos.show', $pedido) }}" class="font-semibold text-zinc-900 hover:text-emerald-700">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</a>
                            </td>
                            <td class="px-6 py-4"><x-status-badge :status="$pedido->status" /></td>
                            <td class="px-6 py-4 text-zinc-600">{{ $pedido->itens_count }}</td>
                            <td class="px-6 py-4 font-medium"><x-money :value="$pedido->total" /></td>
                            <td class="px-6 py-4 text-zinc-500">{{ $pedido->criado_em?->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <x-button variant="ghost" :href="route('pedidos.edit', $pedido)">Editar</x-button>
                                <x-delete-button :action="route('pedidos.destroy', $pedido)" :confirm="'Excluir o pedido #'.$pedido->id.'?'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-zinc-500">
                                Nenhum pedido encontrado.
                                <a href="{{ route('pedidos.create') }}" class="font-medium text-emerald-700 hover:underline">Crie um pedido.</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pedidos->hasPages())
            <div class="border-t border-zinc-200 px-6 py-4">{{ $pedidos->links() }}</div>
        @endif
    </x-card>
</x-layouts.app>
