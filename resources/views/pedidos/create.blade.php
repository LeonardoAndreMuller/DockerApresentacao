<x-layouts.app title="Novo pedido">
    <x-page-header title="Novo pedido" subtitle="Monte o pedido escolhendo os produtos" />

    <form method="POST" action="{{ route('pedidos.store') }}">
        @include('pedidos._form')
    </form>
</x-layouts.app>
