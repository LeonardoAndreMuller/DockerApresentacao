<x-layouts.app title="Editar pedido">
    <x-page-header :title="'Editar pedido #'.$pedido->id" subtitle="Altere itens, status ou taxa de entrega" />

    <form method="POST" action="{{ route('pedidos.update', $pedido) }}">
        @method('PUT')
        @include('pedidos._form')
    </form>
</x-layouts.app>
