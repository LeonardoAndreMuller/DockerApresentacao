@csrf

<div class="grid gap-6 p-6 sm:grid-cols-2">
    <x-field label="Nome" name="nome" class="sm:col-span-2">
        <input id="nome" name="nome" type="text" maxlength="120" required value="{{ old('nome', $produto->nome) }}" class="form-input">
    </x-field>

    <x-field label="Descrição" name="descricao" class="sm:col-span-2">
        <textarea id="descricao" name="descricao" rows="3" class="form-input">{{ old('descricao', $produto->descricao) }}</textarea>
    </x-field>

    <x-field label="Preço (R$)" name="preco">
        <input id="preco" name="preco" type="number" step="0.01" min="0" required value="{{ old('preco', $produto->preco) }}" class="form-input">
    </x-field>

    <div class="flex items-center pt-6">
        <label class="inline-flex cursor-pointer items-center gap-3">
            <input type="hidden" name="disponivel" value="0">
            <input type="checkbox" name="disponivel" value="1" class="peer sr-only" @checked(old('disponivel', $produto->disponivel))>
            <span class="relative h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-emerald-500 after:absolute after:top-0.5 after:left-0.5 after:size-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></span>
            <span class="text-sm font-medium text-zinc-700">Disponível para pedido</span>
        </label>
    </div>

    <x-field label="Atributos (JSON)" name="atributos" class="sm:col-span-2" hint='Ex.: {"vegetariano": true, "tamanho": "G", "alergenos": ["gluten"]}'>
        <textarea id="atributos" name="atributos" rows="4" class="form-input font-mono text-xs">{{ old('atributos', json_encode($produto->atributos ?: new stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
    </x-field>
</div>

<div class="flex justify-end gap-2 border-t border-zinc-300 bg-zinc-200/60 px-6 py-4">
    <x-button variant="secondary" :href="$produto->exists ? route('produtos.show', $produto) : route('produtos.index')">Cancelar</x-button>
    <x-button>Salvar produto</x-button>
</div>
