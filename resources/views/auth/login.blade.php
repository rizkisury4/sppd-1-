<x-guest-layout>
    <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-1">Masuk</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Gunakan akun PLN Anda untuk melanjutkan.</p>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Email</label>
            <input id="email" class="mt-1 w-full rounded-md border-gray-300 bg-white dark:bg-slate-800 dark:text-slate-100" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Password</label>
            <input id="password" class="mt-1 w-full rounded-md border-gray-300 bg-white dark:bg-slate-800 dark:text-slate-100" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500 dark:bg-gray-900 dark:border-gray-700" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-sky-700 dark:text-sky-400 hover:underline" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>
        <button class="inline-flex items-center justify-center w-full px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-sky-600 text-white hover:bg-sky-700 ring-sky-700/20 dark:bg-sky-500 dark:hover:bg-sky-400 dark:ring-white/10">
            Masuk
        </button>
        <p class="text-xs text-slate-500 dark:text-slate-400 text-center">Belum punya akun? <a href="{{ route('register') }}" class="text-sky-700 dark:text-sky-400 hover:underline">Daftar</a></p>
    </form>
</x-guest-layout>
