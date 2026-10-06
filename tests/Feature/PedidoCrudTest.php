<?php

use App\Enums\StatusPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('lista os pedidos e filtra por status', function () {
    $entregue = Pedido::factory()->create(['status' => StatusPedido::Entregue]);
    $pendente = Pedido::factory()->create(['status' => StatusPedido::Pendente]);

    $this->get(route('pedidos.index', ['status' => 'entregue']))
        ->assertOk()
        ->assertSee(route('pedidos.show', $entregue))
        ->assertDontSee(route('pedidos.show', $pendente));
});

test('exibe criação, edição e detalhe do pedido', function () {
    $item = ItemPedido::factory()->create();

    $this->get(route('pedidos.create'))->assertOk();
    $this->get(route('pedidos.edit', $item->pedido))->assertOk()->assertSee($item->produto->nome);
    $this->get(route('pedidos.show', $item->pedido))->assertOk()->assertSee($item->produto->nome);
});

test('cria um pedido gravando o preço unitário do momento', function () {
    $pizza = Produto::factory()->create(['preco' => 40]);
    $suco = Produto::factory()->create(['preco' => 8.5]);

    $this->post(route('pedidos.store'), [
        'status' => 'pendente',
        'taxa_entrega' => '5.00',
        'observacao' => 'Sem cebola',
        'itens' => [
            ['produto_id' => $pizza->id, 'quantidade' => 2],
            ['produto_id' => $suco->id, 'quantidade' => 1],
        ],
    ])->assertRedirect();

    $pedido = Pedido::with('itens')->sole();

    expect($pedido->itens)->toHaveCount(2)
        ->and($pedido->itens->firstWhere('produto_id', $pizza->id)->preco_unitario)->toBe('40.00')
        ->and($pedido->total)->toBe(93.5);
});

test('valida itens do pedido', function () {
    $produto = Produto::factory()->create();

    $this->post(route('pedidos.store'), [
        'status' => 'inexistente',
        'taxa_entrega' => '-1',
        'itens' => [
            ['produto_id' => $produto->id, 'quantidade' => 0],
            ['produto_id' => $produto->id, 'quantidade' => 1],
        ],
    ])->assertSessionHasErrors(['status', 'taxa_entrega', 'itens.0.quantidade', 'itens.0.produto_id']);

    $this->post(route('pedidos.store'), [
        'status' => 'pendente',
        'taxa_entrega' => '0',
        'itens' => [],
    ])->assertSessionHasErrors('itens');

    expect(Pedido::count())->toBe(0);
});

test('atualiza o pedido mantendo o preço original dos itens existentes', function () {
    $item = ItemPedido::factory()->create(['quantidade' => 1, 'preco_unitario' => 20]);
    $item->produto->update(['preco' => 99]);
    $novoProduto = Produto::factory()->create(['preco' => 15]);

    $this->put(route('pedidos.update', $item->pedido), [
        'status' => 'confirmado',
        'taxa_entrega' => '0',
        'itens' => [
            ['produto_id' => $item->produto_id, 'quantidade' => 3],
            ['produto_id' => $novoProduto->id, 'quantidade' => 1],
        ],
    ])->assertRedirect(route('pedidos.show', $item->pedido));

    $pedido = $item->pedido->fresh('itens');

    expect($pedido->status)->toBe(StatusPedido::Confirmado)
        ->and($pedido->itens->firstWhere('produto_id', $item->produto_id)->preco_unitario)->toBe('20.00')
        ->and($pedido->itens->firstWhere('produto_id', $novoProduto->id)->preco_unitario)->toBe('15.00');
});

test('exclui o pedido junto com os itens', function () {
    $item = ItemPedido::factory()->create();

    $this->delete(route('pedidos.destroy', $item->pedido))->assertRedirect(route('pedidos.index'));

    $this->assertModelMissing($item->pedido);
    $this->assertModelMissing($item);
});
