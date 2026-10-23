<?php

namespace App;

use App\Models\Product;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/** The cart lives in the session: product id => quantity. */
class Cart
{
    public function __construct(private Session $session) {}

    public function add(Product $product, int $quantity = 1): void
    {
        $items = $this->session->get('cart', []);
        $items[$product->id] = ($items[$product->id] ?? 0) + $quantity;
        $this->session->put('cart', $items);
    }

    public function remove(Product $product): void
    {
        $this->session->forget("cart.{$product->id}");
    }

    public function clear(): void
    {
        $this->session->forget('cart');
    }

    /** @return Collection<int, array{product: Product, quantity: int, total: int}> */
    public function lines(): Collection
    {
        $items = $this->session->get('cart', []);

        return Product::whereKey(array_keys($items))->get()->map(fn (Product $product) => [
            'product' => $product,
            'quantity' => $items[$product->id],
            'total' => $product->price * $items[$product->id],
        ]);
    }

    public function count(): int
    {
        return array_sum($this->session->get('cart', []));
    }

    public function total(): int
    {
        return $this->lines()->sum('total');
    }
}
