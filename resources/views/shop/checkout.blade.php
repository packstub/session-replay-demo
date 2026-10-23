<x-layouts.app title="Checkout · Acme Supply">
    <h1 class="text-2xl font-semibold tracking-tight">Checkout</h1>

    <div class="mt-8 grid grid-cols-5 gap-10">
        <form data-checkout action="{{ route('checkout.pay') }}" class="col-span-3 space-y-8">
            <section class="space-y-4">
                <h2 class="font-medium">Shipping</h2>
                <label class="block">
                    <span class="text-sm text-stone-600">Full name</span>
                    <input name="name" value="{{ auth()->user()->name }}" autocomplete="name" class="mt-1 w-full rounded-lg border border-stone-300 bg-white px-3 py-2 focus:border-stone-900 focus:outline-none">
                </label>
                <label class="block">
                    <span class="text-sm text-stone-600">Address</span>
                    <textarea name="address" rows="2" autocomplete="street-address" class="mt-1 w-full rounded-lg border border-stone-300 bg-white px-3 py-2 focus:border-stone-900 focus:outline-none"></textarea>
                </label>
            </section>

            <section class="space-y-4">
                <h2 class="font-medium">Payment</h2>
                <label class="block">
                    <span class="text-sm text-stone-600">Card number</span>
                    <input name="card" inputmode="numeric" autocomplete="cc-number" placeholder="1234 1234 1234 1234" class="mt-1 w-full rounded-lg border border-stone-300 bg-white px-3 py-2 font-mono focus:border-stone-900 focus:outline-none">
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="block">
                        <span class="text-sm text-stone-600">Expiry</span>
                        <input name="expiry" placeholder="MM / YY" autocomplete="cc-exp" class="mt-1 w-full rounded-lg border border-stone-300 bg-white px-3 py-2 font-mono focus:border-stone-900 focus:outline-none">
                    </label>
                    <label class="block">
                        <span class="text-sm text-stone-600">CVC</span>
                        <input name="cvc" inputmode="numeric" autocomplete="cc-csc" class="mt-1 w-full rounded-lg border border-stone-300 bg-white px-3 py-2 font-mono focus:border-stone-900 focus:outline-none">
                    </label>
                </div>
                <p class="text-xs text-stone-500">Test cards: <code>4242 4242 4242 4242</code> goes through, <code>4000 0000 0000 0119</code> fails at the provider.</p>
            </section>

            <ul data-errors hidden class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></ul>

            <button type="button" data-pay class="flex w-full items-center justify-center gap-2 rounded-lg bg-stone-900 px-5 py-3 font-medium text-white hover:bg-stone-800">
                <svg data-spinner hidden class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".3" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                Pay {{ money($total) }}
            </button>
        </form>

        <aside class="col-span-2 h-fit rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-medium">Your order</h2>
            <ul class="mt-4 divide-y divide-stone-100 text-sm">
                @foreach ($lines as $line)
                    <li class="flex justify-between gap-4 py-3">
                        <span>{{ $line['product']->name }} <span class="text-stone-500">× {{ $line['quantity'] }}</span></span>
                        <span>{{ money($line['total']) }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="mt-2 flex justify-between border-t border-stone-200 pt-4 font-medium">
                <span>Total</span>
                <span>{{ money($total) }}</span>
            </div>
        </aside>
    </div>
</x-layouts.app>
