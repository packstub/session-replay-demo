<x-layouts.app title="Sign in · Acme Supply">
    <div class="mx-auto max-w-sm">
        <h1 class="text-2xl font-semibold tracking-tight">Sign in</h1>
        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <label class="block">
                <span class="text-sm font-medium">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1 w-full rounded-lg border border-stone-300 bg-white px-3 py-2 focus:border-stone-900 focus:outline-none">
            </label>
            <label class="block">
                <span class="text-sm font-medium">Password</span>
                <input type="password" name="password" required class="mt-1 w-full rounded-lg border border-stone-300 bg-white px-3 py-2 focus:border-stone-900 focus:outline-none">
            </label>
            @error('email')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <button class="w-full rounded-lg bg-stone-900 px-4 py-2.5 font-medium text-white hover:bg-stone-800">Sign in</button>
        </form>

        {{-- One-click sign-in for the seeded accounts (Account Switcher's developer logins, local only). --}}
        <x-account-switcher::developer-logins />
    </div>
</x-layouts.app>
