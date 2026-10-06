<?php

namespace App\Models;

use Database\Factories\ProdutoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('produtos')]
#[Fillable(['nome', 'descricao', 'preco', 'disponivel', 'atributos'])]
class Produto extends Model
{
    /** @use HasFactory<ProdutoFactory> */
    use HasFactory;

    const CREATED_AT = 'criado_em';

    const UPDATED_AT = null;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'disponivel' => true,
        'atributos' => '{}',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'disponivel' => 'boolean',
            'atributos' => 'array',
            'criado_em' => 'datetime',
        ];
    }

    /**
     * @return HasMany<ItemPedido, $this>
     */
    public function itensPedido(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    /**
     * Only products available for ordering.
     *
     * @param  Builder<Produto>  $query
     */
    #[Scope]
    protected function disponiveis(Builder $query): void
    {
        $query->where('disponivel', true);
    }
}
