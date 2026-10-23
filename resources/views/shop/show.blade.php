<x-layouts.app :title="$product->name.' · Acme Supply'">
    <a href="{{ route('shop') }}" class="text-sm text-stone-500 hover:text-stone-900">← Shop</a>
    <div class="mt-4 grid grid-cols-2 gap-10">
        <x-product-tile :product="$product" class="h-80 rounded-xl border border-stone-200" />
        <div>
            <h1 class="text-3xl font-semibold tracking-tight">{{ $product->name }}</h1>
            <p class="mt-2 text-xl text-stone-700">{{ $product->priceForHumans() }}</p>
            <p class="mt-6 leading-relaxed text-stone-600">{{ $product->description }}</p>
            <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-8 flex items-center gap-3">
                @csrf
                <select name="quantity" class="rounded-lg border border-stone-300 bg-white px-3 py-2.5">
                    @foreach (range(1, 5) as $n)
                        <option value="{{ $n }}">{{ $n }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-stone-900 px-5 py-2.5 font-medium text-white hover:bg-stone-800">Add to cart</button>
            </form>
        </div>
    </div>
</x-layouts.app>
