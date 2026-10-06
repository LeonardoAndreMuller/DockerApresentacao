<?php

namespace Database\Factories;

use App\Enums\StatusPedido;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pedido>
 */
class PedidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(StatusPedido::cases()),
            'taxa_entrega' => fake()->randomFloat(2, 0, 15),
            'observacao' => fake()->optional()->sentence(),
        ];
    }
}
