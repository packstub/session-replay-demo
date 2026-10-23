<x-layouts.app title="Shop · Acme Supply">
    <h1 class="text-2xl font-semibold tracking-tight">Shop</h1>
    <p class="mt-1 text-stone-600">Order by 6 pm, it ships tomorrow.</p>

    <div class="mt-8 grid grid-cols-3 gap-6">
        @foreach ($products as $product)
            <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
                <a href="{{ route('products.show', $product) }}" class="block">
                    <x-product-tile :product="$product" class="h-40" />
                </a>
                <div class="flex items-end justify-between gap-4 p-4">
                    <div>
                        <a href="{{ route('products.show', $product) }}" class="font-medium hover:underline">{{ $product->name }}</a>
                        <p class="text-sm text-stone-500">{{ $product->priceForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <button class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-medium hover:border-stone-900">Add to cart</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
