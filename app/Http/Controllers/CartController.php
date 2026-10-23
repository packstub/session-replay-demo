<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, Product $product, Cart $cart): RedirectResponse
    {
        $cart->add($product, max(1, (int) $request->input('quantity', 1)));

        return back()->with('status', "{$product->name} is in your cart.");
    }

    public function remove(Product $product, Cart $cart): RedirectResponse
    {
        $cart->remove($product);

        return back();
    }
}
