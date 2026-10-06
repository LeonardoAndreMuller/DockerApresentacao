<?php

use App\Models\ItemPedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('lista os produtos e filtra pela busca', function () {
    Produto::factory()->create(['nome' => 'Pizza Margherita']);
    Produto::factory()->create(['nome' => 'Suco de Laranja']);

    $this->get(route('produtos.index', ['busca' => 'Pizza']))
        ->assertOk()
        ->assertSee('Pizza Margherita')
        ->assertDontSee('Suco de Laranja');
});

test('exibe os formulários de criação e edição', function () {
    $produto = Produto::factory()->create();

    $this->get(route('produtos.create'))->assertOk();
    $this->get(route('produtos.edit', $produto))->assertOk()->assertSee($produto->nome);
    $this->get(route('produtos.show', $produto))->assertOk()->assertSee($produto->nome);
});

test('cria um produto com atributos em JSON', function () {
    $this->post(route('produtos.store'), [
        'nome' => 'Hambúrguer Veggie',
        'descricao' => 'Pão brioche e grão-de-bico',
        'preco' => '32.90',
        'disponivel' => '1',
        'atributos' => '{"vegetariano": true, "alergenos": ["gluten"]}',
    ])->assertRedirect();

    $produto = Produto::sole();

    expect($produto->nome)->toBe('Hambúrguer Veggie')
        ->and($produto->preco)->toBe('32.90')
        ->and($produto->disponivel)->toBeTrue()
        ->and($produto->atributos)->toBe(['vegetariano' => true, 'alergenos' => ['gluten']]);
});

test('valida os campos do produto', function () {
    $this->post(route('produtos.store'), [
        'nome' => '',
        'preco' => '-1',
        'atributos' => '{invalido',
    ])->assertSessionHasErrors(['nome', 'preco', 'atributos']);

    expect(Produto::count())->toBe(0);
});

test('atualiza um produto', function () {
    $produto = Produto::factory()->create();

    $this->put(route('produtos.update', $produto), [
        'nome' => 'Novo nome',
        'preco' => '10.00',
        'atributos' => '',
    ])->assertRedirect(route('produtos.show', $produto));

    $produto->refresh();

    expect($produto->nome)->toBe('Novo nome')
        ->and($produto->disponivel)->toBeFalse()
        ->and($produto->atributos)->toBe([]);
});

test('exclui um produto sem pedidos', function () {
    $produto = Produto::factory()->create();

    $this->delete(route('produtos.destroy', $produto))->assertRedirect(route('produtos.index'));

    $this->assertModelMissing($produto);
});

test('não exclui um produto presente em pedidos', function () {
    $item = ItemPedido::factory()->create();

    $this->delete(route('produtos.destroy', $item->produto))->assertSessionHas('error');

    $this->assertModelExists($item->produto);
});
