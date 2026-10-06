<?php

use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('popula produtos, pedidos e itens', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Produto::count())->toBe(16)
        ->and(Pedido::count())->toBe(30)
        ->and(ItemPedido::count())->toBeGreaterThanOrEqual(30)
        ->and(Produto::where('disponivel', false)->count())->toBe(2);
});

test('não duplica os dados ao rodar novamente', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Produto::count())->toBe(16)
        ->and(Pedido::count())->toBe(30);
});
