<x-layouts.app title="Orders · Acme Supply">
    <h1 class="text-2xl font-semibold tracking-tight">Orders</h1>
    @forelse ($orders as $order)
        <a href="{{ route('orders.show', $order) }}" class="mt-4 flex justify-between rounded-xl border border-stone-200 bg-white px-5 py-4 hover:border-stone-300">
            <span>#{{ $order->id }} · {{ $order->created_at->toFormattedDayDateString() }}</span>
            <span class="font-medium">{{ money($order->total) }}</span>
        </a>
    @empty
        <p class="mt-4 text-stone-600">No orders yet.</p>
    @endforelse
</x-layouts.app>
