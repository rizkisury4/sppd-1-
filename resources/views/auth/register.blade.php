<x-guest-layout>
    <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-1">Daftar</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Buat akun PLN SPPD Anda.</p>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nama</label>
            <input id="name" class="mt-1 w-full rounded-md border-gray-300 bg-white dark:bg-slate-800 dark:text-slate-100" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Email</label>
            <input id="email" class="mt-1 w-full rounded-md border-gray-300 bg-white dark:bg-slate-800 dark:text-slate-100" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Password</label>
            <input id="password" class="mt-1 w-full rounded-md border-gray-300 bg-white dark:bg-slate-800 dark:text-slate-100" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Konfirmasi Password</label>
            <input id="password_confirmation" class="mt-1 w-full rounded-md border-gray-300 bg-white dark:bg-slate-800 dark:text-slate-100" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <button class="inline-flex items-center justify-center w-full px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-sky-600 text-white hover:bg-sky-700 ring-sky-700/20 dark:bg-sky-500 dark:hover:bg-sky-400 dark:ring-white/10">
            Daftar
        </button>
        <p class="text-xs text-slate-500 dark:text-slate-400 text-center">Sudah punya akun? <a href="{{ route('login') }}" class="text-sky-700 dark:text-sky-400 hover:underline">Masuk</a></p>
    </form>
</x-guest-layout>
