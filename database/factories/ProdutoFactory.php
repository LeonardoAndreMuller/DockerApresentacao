<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => ucfirst(fake()->unique()->words(2, true)),
            'descricao' => fake()->sentence(),
            'preco' => fake()->randomFloat(2, 5, 120),
            'disponivel' => true,
            'atributos' => [
                'vegetariano' => fake()->boolean(),
                'tamanho' => fake()->randomElement(['P', 'M', 'G']),
            ],
        ];
    }

    /**
     * Indicate that the product is not available.
     */
    public function indisponivel(): static
    {
        return $this->state(fn (array $attributes): array => [
            'disponivel' => false,
        ]);
    }
}
