<?php

namespace Database\Factories;

use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemPedido>
 */
class ItemPedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pedido_id' => Pedido::factory(),
            'produto_id' => Produto::factory(),
            'quantidade' => fake()->numberBetween(1, 5),
            'preco_unitario' => fake()->randomFloat(2, 5, 120),
        ];
    }
}
