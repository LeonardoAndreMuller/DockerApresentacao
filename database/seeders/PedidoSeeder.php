<?php

namespace Database\Seeders;

use App\Enums\StatusPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    /**
     * Seed orders spread over the last 30 days using the existing products.
     */
    public function run(): void
    {
        $produtos = Produto::disponiveis()->get();

        if ($produtos->isEmpty()) {
            return;
        }

        $observacoes = [null, null, 'Sem cebola, por favor.', 'Troco para R$ 100.', 'Interfone com defeito, ligar ao chegar.', 'Caprichar no molho!', 'Entregar na portaria.'];

        foreach (range(1, 30) as $numero) {
            $criadoEm = now()->subDays(30 - $numero)->setTime(rand(11, 22), rand(0, 59));
            $status = $this->statusPara($criadoEm->diffInDays(now()));

            $pedido = Pedido::factory()->create([
                'status' => $status,
                'taxa_entrega' => fake()->randomElement([0, 4.99, 6.90, 8.50, 12.00]),
                'observacao' => fake()->randomElement($observacoes),
                'criado_em' => $criadoEm,
                'atualizado_em' => $criadoEm->copy()->addMinutes(rand(5, 90)),
            ]);

            $produtos->random(rand(1, 4))->each(fn (Produto $produto) => ItemPedido::factory()->create([
                'pedido_id' => $pedido->id,
                'produto_id' => $produto->id,
                'quantidade' => rand(1, 3),
                'preco_unitario' => $produto->preco,
            ]));
        }
    }

    /**
     * Older orders are mostly finished; recent ones are still in progress.
     */
    private function statusPara(float $diasAtras): StatusPedido
    {
        if ($diasAtras >= 2) {
            return fake()->randomElement([StatusPedido::Entregue, StatusPedido::Entregue, StatusPedido::Entregue, StatusPedido::Cancelado]);
        }

        return fake()->randomElement([StatusPedido::Pendente, StatusPedido::Confirmado, StatusPedido::EmPreparo, StatusPedido::SaiuParaEntrega]);
    }
}
