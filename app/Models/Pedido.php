<?php

namespace App\Models;

use App\Enums\StatusPedido;
use Database\Factories\PedidoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('pedidos')]
#[Fillable(['status', 'taxa_entrega', 'observacao'])]
class Pedido extends Model
{
    /** @use HasFactory<PedidoFactory> */
    use HasFactory;

    const CREATED_AT = 'criado_em';

    const UPDATED_AT = 'atualizado_em';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pendente',
        'taxa_entrega' => 0,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusPedido::class,
            'taxa_entrega' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<ItemPedido, $this>
     */
    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    /**
     * Sum of the order lines, without the delivery fee.
     *
     * @return Attribute<float, never>
     */
    protected function subtotal(): Attribute
    {
        return Attribute::get(fn (): float => (float) $this->itens->sum(
            fn (ItemPedido $item): float => $item->quantidade * (float) $item->preco_unitario
        ));
    }

    /**
     * Order total including the delivery fee.
     *
     * @return Attribute<float, never>
     */
    protected function total(): Attribute
    {
        return Attribute::get(fn (): float => $this->subtotal + (float) $this->taxa_entrega);
    }
}
