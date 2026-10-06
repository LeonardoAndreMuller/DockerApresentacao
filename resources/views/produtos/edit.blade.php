<x-layouts.app title="Editar produto">
    <x-page-header :title="'Editar '.$produto->nome" subtitle="Atualize as informações do produto" />

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ route('produtos.update', $produto) }}">
            @method('PUT')
            @include('produtos._form')
        </form>
    </x-card>
</x-layouts.app>
