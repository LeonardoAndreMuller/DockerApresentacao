<?php

namespace App\Http\Controllers;

use App\Enums\StatusPedido;
use App\Http\Requests\StorePedidoRequest;
use App\Http\Requests\UpdatePedidoRequest;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = StatusPedido::tryFrom($request->string('status')->toString());

        $pedidos = Pedido::query()
            ->with('itens')
            ->withCount('itens')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('criado_em')
            ->paginate(10)
            ->withQueryString();

        return view('pedidos.index', compact('pedidos', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pedidos.create', [
            'pedido' => new Pedido,
            'produtos' => Produto::disponiveis()->orderBy('nome')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePedidoRequest $request): RedirectResponse
    {
        $pedido = DB::transaction(function () use ($request): Pedido {
            $pedido = Pedido::create($request->safe()->only(['status', 'taxa_entrega', 'observacao']));

            $this->syncItens($pedido, $request->validated('itens'));

            return $pedido;
        });

        return to_route('pedidos.show', $pedido)->with('status', 'Pedido criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido): View
    {
        $pedido->load('itens.produto');

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido): View
    {
        $pedido->load('itens');

        $produtosDoPedido = $pedido->itens->pluck('produto_id');

        return view('pedidos.edit', [
            'pedido' => $pedido,
            'produtos' => Produto::query()
                ->where(fn ($query) => $query->where('disponivel', true)->orWhereIn('id', $produtosDoPedido))
                ->orderBy('nome')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePedidoRequest $request, Pedido $pedido): RedirectResponse
    {
        DB::transaction(function () use ($request, $pedido): void {
            $pedido->update($request->safe()->only(['status', 'taxa_entrega', 'observacao']));

            $this->syncItens($pedido, $request->validated('itens'));
        });

        return to_route('pedidos.show', $pedido)->with('status', 'Pedido atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido): RedirectResponse
    {
        $pedido->delete();

        return to_route('pedidos.index')->with('status', 'Pedido excluído.');
    }

    /**
     * Replace the order lines, keeping the original unit price of products already in the order.
     *
     * @param  array<int, array{produto_id: int|string, quantidade: int|string}>  $itens
     */
    private function syncItens(Pedido $pedido, array $itens): void
    {
        $precosAtuais = Produto::whereIn('id', array_column($itens, 'produto_id'))->pluck('preco', 'id');
        $precosOriginais = $pedido->itens()->pluck('preco_unitario', 'produto_id');

        $pedido->itens()->delete();

        $pedido->itens()->createMany(array_map(fn (array $item): array => [
            'produto_id' => $item['produto_id'],
            'quantidade' => $item['quantidade'],
            'preco_unitario' => $precosOriginais[$item['produto_id']] ?? $precosAtuais[$item['produto_id']],
        ], $itens));
    }
}
