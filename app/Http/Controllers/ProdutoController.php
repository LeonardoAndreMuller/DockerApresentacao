<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use App\Models\Produto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $busca = $request->string('busca')->trim()->toString();

        $produtos = Produto::query()
            ->when($busca !== '', fn ($query) => $query->where('nome', 'like', "%{$busca}%"))
            ->latest('criado_em')
            ->paginate(10)
            ->withQueryString();

        return view('produtos.index', compact('produtos', 'busca'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('produtos.create', ['produto' => new Produto]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProdutoRequest $request): RedirectResponse
    {
        $produto = Produto::create($request->produtoData());

        return to_route('produtos.show', $produto)->with('status', 'Produto criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto): View
    {
        $produto->loadCount('itensPedido');

        return view('produtos.show', compact('produto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto): View
    {
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdutoRequest $request, Produto $produto): RedirectResponse
    {
        $produto->update($request->produtoData());

        return to_route('produtos.show', $produto)->with('status', 'Produto atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto): RedirectResponse
    {
        if ($produto->itensPedido()->exists()) {
            return back()->with('error', 'Este produto está em pedidos e não pode ser excluído. Marque-o como indisponível.');
        }

        $produto->delete();

        return to_route('produtos.index')->with('status', 'Produto excluído.');
    }
}
