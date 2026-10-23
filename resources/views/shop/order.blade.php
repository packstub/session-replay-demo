<x-layouts.app title="Order confirmed · Acme Supply">
    <div class="mx-auto max-w-lg rounded-xl border border-stone-200 bg-white p-8">
        <p class="text-sm font-medium text-emerald-700">Order #{{ $order->id }} confirmed</p>
        <h1 class="mt-1 text-2xl font-semibold tracking-tight">Thanks, it ships tomorrow</h1>
        <ul class="mt-6 divide-y divide-stone-100 text-sm">
            @foreach ($order->items as $item)
                <li class="flex justify-between py-3"><span>{{ $item['name'] }} × {{ $item['quantity'] }}</span><span>{{ money($item['total']) }}</span></li>
            @endforeach
        </ul>
        <div class="flex justify-between border-t border-stone-200 pt-4 font-medium"><span>Paid with •••• {{ $order->card_last4 }}</span><span>{{ money($order->total) }}</span></div>
        <a href="{{ route('shop') }}" class="mt-8 inline-block text-sm text-stone-600 hover:text-stone-900">← Keep shopping</a>
    </div>
</x-layouts.app>
