<x-layouts.app title="Novo produto">
    <x-page-header title="Novo produto" subtitle="Cadastre um item para o cardápio" />

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ route('produtos.store') }}">
            @include('produtos._form')
        </form>
    </x-card>
</x-layouts.app>
