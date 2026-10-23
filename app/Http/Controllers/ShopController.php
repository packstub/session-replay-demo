<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        return view('shop.index', ['products' => Product::orderBy('id')->get()]);
    }

    public function show(Product $product): View
    {
        return view('shop.show', ['product' => $product]);
    }
}
