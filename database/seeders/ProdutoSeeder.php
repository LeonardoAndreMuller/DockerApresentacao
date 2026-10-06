<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    /**
     * Seed a realistic delivery menu.
     */
    public function run(): void
    {
        foreach ($this->cardapio() as $indice => $produto) {
            Produto::factory()->create([
                ...$produto,
                'disponivel' => $produto['disponivel'] ?? true,
                'criado_em' => now()->subDays(45 - $indice),
            ]);
        }
    }

    /**
     * Menu items with flexible JSONB attributes.
     *
     * @return list<array{nome: string, descricao: string, preco: float, atributos: array<string, mixed>, disponivel?: bool}>
     */
    private function cardapio(): array
    {
        return [
            ['nome' => 'Pizza Margherita', 'descricao' => 'Molho de tomate, mussarela de búfala e manjericão fresco.', 'preco' => 54.90, 'atributos' => ['tipo' => 'pizza', 'vegetariano' => true, 'tamanho' => 'G', 'fatias' => 8, 'alergenos' => ['gluten', 'lactose']]],
            ['nome' => 'Pizza Calabresa', 'descricao' => 'Calabresa artesanal, cebola roxa e azeitonas pretas.', 'preco' => 56.90, 'atributos' => ['tipo' => 'pizza', 'vegetariano' => false, 'tamanho' => 'G', 'fatias' => 8, 'alergenos' => ['gluten', 'lactose']]],
            ['nome' => 'Pizza Quatro Queijos', 'descricao' => 'Mussarela, gorgonzola, parmesão e catupiry.', 'preco' => 62.90, 'atributos' => ['tipo' => 'pizza', 'vegetariano' => true, 'tamanho' => 'G', 'fatias' => 8, 'alergenos' => ['gluten', 'lactose']]],
            ['nome' => 'Pizza Portuguesa (Broto)', 'descricao' => 'Presunto, ovos, cebola, ervilha e mussarela.', 'preco' => 34.90, 'atributos' => ['tipo' => 'pizza', 'vegetariano' => false, 'tamanho' => 'P', 'fatias' => 4, 'alergenos' => ['gluten', 'lactose', 'ovo']]],
            ['nome' => 'Hambúrguer Clássico', 'descricao' => 'Blend bovino 180g, cheddar, alface, tomate e molho da casa.', 'preco' => 36.90, 'atributos' => ['tipo' => 'lanche', 'vegetariano' => false, 'ponto' => 'ao ponto', 'acompanha' => ['batata frita'], 'alergenos' => ['gluten', 'lactose']]],
            ['nome' => 'Hambúrguer Veggie', 'descricao' => 'Burger de grão-de-bico, queijo prato e cebola caramelizada.', 'preco' => 34.90, 'atributos' => ['tipo' => 'lanche', 'vegetariano' => true, 'vegano' => false, 'acompanha' => ['batata rústica'], 'alergenos' => ['gluten', 'lactose']]],
            ['nome' => 'Smash Duplo Bacon', 'descricao' => 'Dois smash burgers, bacon crocante e maionese defumada.', 'preco' => 42.90, 'atributos' => ['tipo' => 'lanche', 'vegetariano' => false, 'picante' => true, 'alergenos' => ['gluten', 'ovo']]],
            ['nome' => 'Wrap de Frango', 'descricao' => 'Frango grelhado, mix de folhas e molho de iogurte.', 'preco' => 28.90, 'atributos' => ['tipo' => 'lanche', 'vegetariano' => false, 'alergenos' => ['gluten', 'lactose']], 'disponivel' => false],
            ['nome' => 'Batata Frita Grande', 'descricao' => 'Porção de 400g com sal de ervas.', 'preco' => 22.00, 'atributos' => ['tipo' => 'porcao', 'vegetariano' => true, 'vegano' => true, 'peso_g' => 400]],
            ['nome' => 'Refrigerante Lata', 'descricao' => 'Coca-Cola, Guaraná ou Sprite.', 'preco' => 6.50, 'atributos' => ['tipo' => 'bebida', 'volume_ml' => 350, 'gelado' => true, 'sabores' => ['cola', 'guaraná', 'limão']]],
            ['nome' => 'Suco Natural de Laranja', 'descricao' => 'Laranja espremida na hora, sem açúcar.', 'preco' => 11.90, 'atributos' => ['tipo' => 'bebida', 'volume_ml' => 500, 'vegano' => true, 'acucar' => false]],
            ['nome' => 'Água com Gás', 'descricao' => 'Garrafa 500ml.', 'preco' => 5.00, 'atributos' => ['tipo' => 'bebida', 'volume_ml' => 500, 'gelado' => true]],
            ['nome' => 'Cerveja Artesanal IPA', 'descricao' => 'India Pale Ale local, amargor marcante.', 'preco' => 18.90, 'atributos' => ['tipo' => 'bebida', 'volume_ml' => 473, 'alcoolica' => true, 'teor_alcoolico' => 6.2, 'alergenos' => ['gluten']]],
            ['nome' => 'Brownie com Sorvete', 'descricao' => 'Brownie de chocolate meio amargo e sorvete de creme.', 'preco' => 19.90, 'atributos' => ['tipo' => 'sobremesa', 'vegetariano' => true, 'alergenos' => ['gluten', 'lactose', 'ovo']]],
            ['nome' => 'Pudim de Leite', 'descricao' => 'Receita da casa com calda de caramelo.', 'preco' => 14.90, 'atributos' => ['tipo' => 'sobremesa', 'vegetariano' => true, 'alergenos' => ['lactose', 'ovo']]],
            ['nome' => 'Açaí 500ml', 'descricao' => 'Açaí com banana, granola e leite condensado.', 'preco' => 24.90, 'atributos' => ['tipo' => 'sobremesa', 'vegetariano' => true, 'adicionais' => ['banana', 'granola', 'leite condensado']], 'disponivel' => false],
        ];
    }
}
