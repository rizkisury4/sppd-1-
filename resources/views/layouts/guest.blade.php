<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-emerald-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
            <div class="max-w-7xl mx-auto px-6 py-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
                    <div class="hidden lg:flex">
                        <div class="relative w-full rounded-2xl overflow-hidden ring-1 ring-sky-200/60 dark:ring-white/10 bg-gradient-to-br from-sky-600 to-emerald-600 p-8 flex flex-col justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded bg-yellow-300 flex items-center justify-center ring-2 ring-white/40">
                                    <svg class="h-6 w-6 text-sky-900" viewBox="0 0 24 24" fill="currentColor"><path d="M13 3l-1 7h4l-5 11 1-7H8l5-11z"/></svg>
                                </div>
                                <div class="text-white/90">
                                    <div class="text-lg font-semibold">PLN SPPD Portal</div>
                                    <div class="text-xs text-white/70">Manajemen Surat Perjalanan Dinas</div>
                                </div>
                            </div>
                            <div class="mt-10 text-white/90">
                                <h2 class="text-3xl font-bold leading-tight">Selamat datang</h2>
                                <p class="mt-2 text-white/80">Masuk untuk mengelola perjalanan dinas Anda dengan cepat, aman, dan terstruktur.</p>
                            </div>
                            <div class="mt-10 flex items-center gap-6 text-white/80 text-sm">
                                <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-yellow-300"></span> Single sign-on ready</div>
                                <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-yellow-300"></span> Tracking approval</div>
                            </div>
                            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                                <svg class="absolute -top-16 -right-16 h-64 w-64 opacity-20" viewBox="0 0 200 200"><circle cx="100" cy="100" r="80" fill="white"/></svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="mx-auto w-full max-w-md bg-white/90 dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200/60 dark:ring-white/10 p-8">
                            <div class="flex items-center gap-3 mb-6 lg:hidden">
                                <div class="h-10 w-10 rounded bg-yellow-300 flex items-center justify-center ring-2 ring-sky-200/60">
                                    <svg class="h-6 w-6 text-sky-900" viewBox="0 0 24 24" fill="currentColor"><path d="M13 3l-1 7h4l-5 11 1-7H8l5-11z"/></svg>
                                </div>
                                <div>
                                    <div class="text-base font-semibold text-slate-800 dark:text-slate-100">PLN SPPD Portal</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">Manajemen Surat Perjalanan Dinas</div>
                                </div>
                            </div>
                            {{ $slot }}
                        </div>
                        <p class="mt-6 text-center text-xs text-slate-500">© {{ date('Y') }} PLN • All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
