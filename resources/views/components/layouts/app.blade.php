<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Acme Supply' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-900 antialiased">
    <header class="border-b border-stone-200 bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-8 px-6">
            <a href="{{ route('shop') }}" class="flex items-center gap-2 font-semibold tracking-tight">
                <span class="grid size-7 place-items-center rounded-md bg-stone-900 text-sm text-white">A</span>
                Acme Supply
            </a>
            @auth
                <nav class="flex gap-6 text-sm text-stone-600">
                    <a href="{{ route('shop') }}" @class(['hover:text-stone-900', 'text-stone-900 font-medium' => request()->routeIs('shop', 'products.*')])>Shop</a>
                    <a href="{{ route('orders') }}" @class(['hover:text-stone-900', 'text-stone-900 font-medium' => request()->routeIs('orders*')])>Orders</a>
                    @if (auth()->user()->is_support)
                        <a href="/session-replay" class="hover:text-stone-900">Session replays</a>
                        <a href="/log-viewer" class="hover:text-stone-900">Logs</a>
                    @endif
                </nav>
                <div class="ml-auto flex items-center gap-5 text-sm">
                    <a href="{{ route('checkout') }}" class="flex items-center gap-2 rounded-full border border-stone-200 px-3 py-1.5 hover:border-stone-300">
                        Cart <span class="rounded-full bg-stone-900 px-2 text-xs leading-5 text-white">{{ app(App\Cart::class)->count() }}</span>
                    </a>
                    <span class="text-stone-500">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-stone-500 hover:text-stone-900">Log out</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    @if (session('status'))
        <div class="mx-auto mt-6 max-w-6xl px-6">
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        </div>
    @endif

    <main class="mx-auto max-w-6xl px-6 py-10">
        {{ $slot }}
    </main>

    @auth
        <footer class="mx-auto max-w-6xl px-6 pb-10 text-sm text-stone-500">
            <div class="flex items-center justify-between border-t border-stone-200 pt-6">
                <span>Acme Supply · office things, shipped tomorrow</span>
                <button type="button" data-report-open class="hover:text-stone-900">Report a problem</button>
            </div>
        </footer>

        <dialog data-report class="m-auto w-full max-w-md rounded-xl p-0 shadow-xl backdrop:bg-stone-900/40">
            <form data-report-form class="space-y-4 p-6">
                <h2 class="text-lg font-semibold">Report a problem</h2>
                <p class="text-sm text-stone-600">Tell us what went wrong. A replay of what you just did goes with it, so you don't have to explain every step.</p>
                <textarea name="message" rows="3" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-stone-900 focus:outline-none" placeholder="What happened?"></textarea>
                <div class="flex justify-end gap-3">
                    <button type="button" data-report-close class="rounded-lg px-4 py-2 text-sm text-stone-600 hover:text-stone-900">Cancel</button>
                    <button class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-medium text-white hover:bg-stone-800">Send</button>
                </div>
            </form>
            <div data-report-thanks hidden class="space-y-4 p-6">
                <h2 class="text-lg font-semibold">Thanks, we're on it</h2>
                <p class="text-sm text-stone-600">Support has your message and a replay of what happened.</p>
                <div class="flex justify-end">
                    <button type="button" data-report-close class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-medium text-white">Close</button>
                </div>
            </div>
        </dialog>
    @endauth

    @sessionReplay
</body>
</html>
