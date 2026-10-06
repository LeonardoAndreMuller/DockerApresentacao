@php
    $itens = old('itens', $pedido->itens->map(fn ($item) => [
        'produto_id' => $item->produto_id,
        'quantidade' => $item->quantidade,
    ])->all());

    if (empty($itens)) {
        $itens = [['produto_id' => '', 'quantidade' => 1]];
    }

    $statusAtual = old('status', $pedido->status?->value ?? \App\Enums\StatusPedido::Pendente->value);
@endphp

@csrf

<div class="grid gap-6 lg:grid-cols-3" data-pedido-form>
    <x-card class="lg:col-span-2">
        <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4">
            <h2 class="text-sm font-semibold text-zinc-900">Itens do pedido</h2>
            <x-button type="button" variant="secondary" data-add-item>+ Adicionar item</x-button>
        </div>

        <div class="space-y-3 p-6" data-itens>
            @foreach ($itens as $index => $item)
                <div class="grid grid-cols-12 items-start gap-3" data-item>
                    <div class="col-span-12 sm:col-span-7">
                        <select name="itens[{{ $index }}][produto_id]" required class="form-input" data-produto>
                            <option value="">Selecione um produto</option>
                            @foreach ($produtos as $produto)
                                <option value="{{ $produto->id }}" data-preco="{{ $produto->preco }}" @selected((string) $item['produto_id'] === (string) $produto->id)>
                                    {{ $produto->nome }} — R$ {{ number_format((float) $produto->preco, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error("itens.$index.produto_id")
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-6 sm:col-span-2">
                        <input type="number" name="itens[{{ $index }}][quantidade]" min="1" required value="{{ $item['quantidade'] }}" class="form-input" data-quantidade aria-label="Quantidade">
                    </div>
                    <div class="col-span-4 py-2 text-right text-sm font-medium tabular-nums sm:col-span-2" data-linha-total>R$ 0,00</div>
                    <div class="col-span-2 text-right sm:col-span-1">
                        <button type="button" class="rounded-lg p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-900" data-remove-item aria-label="Remover item">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @error('itens')
            <p class="px-6 pb-4 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
        <p class="px-6 pb-6 text-xs text-zinc-500">O preço unitário é registrado no momento do pedido; alterações futuras no produto não mudam pedidos já feitos.</p>
    </x-card>

    <div class="space-y-6">
        <x-card class="space-y-5 p-6">
            <x-field label="Status" name="status">
                <select id="status" name="status" class="form-input">
                    @foreach (\App\Enums\StatusPedido::cases() as $opcao)
                        <option value="{{ $opcao->value }}" @selected($statusAtual === $opcao->value)>{{ $opcao->label() }}</option>
                    @endforeach
                </select>
            </x-field>

            <x-field label="Taxa de entrega (R$)" name="taxa_entrega">
                <input id="taxa_entrega" name="taxa_entrega" type="number" step="0.01" min="0" required value="{{ old('taxa_entrega', $pedido->taxa_entrega ?? 0) }}" class="form-input" data-taxa>
            </x-field>

            <x-field label="Observação" name="observacao">
                <textarea id="observacao" name="observacao" rows="3" class="form-input">{{ old('observacao', $pedido->observacao) }}</textarea>
            </x-field>
        </x-card>

        <x-card class="overflow-hidden">
            <dl class="space-y-2 p-6 text-sm">
                <div class="flex justify-between text-zinc-500"><dt>Subtotal</dt><dd class="tabular-nums" data-subtotal>R$ 0,00</dd></div>
                <div class="flex justify-between text-zinc-500"><dt>Entrega</dt><dd class="tabular-nums" data-entrega>R$ 0,00</dd></div>
            </dl>
            <div class="flex items-center justify-between bg-zinc-950 px-6 py-4 text-white">
                <span class="text-sm text-zinc-400">Total</span>
                <span class="text-xl font-semibold text-emerald-400 tabular-nums" data-total>R$ 0,00</span>
            </div>
            <div class="flex gap-2 p-4">
                <x-button variant="secondary" class="flex-1" :href="$pedido->exists ? route('pedidos.show', $pedido) : route('pedidos.index')">Cancelar</x-button>
                <x-button class="flex-1">Salvar pedido</x-button>
            </div>
        </x-card>
    </div>
</div>
