<?php

use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/produtos');

Route::resource('produtos', ProdutoController::class);
Route::resource('pedidos', PedidoController::class);
