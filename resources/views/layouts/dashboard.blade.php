<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="font-sans antialiased">

<div
    x-data="{
        openSidebar: window.innerWidth >= 768,
        collapsed: false,

        init() {
            const v = localStorage.getItem('sidebarCollapsed');
            this.collapsed = v === '1';
        },

        toggleCollapse() {
            this.openSidebar = true;
            this.collapsed = !this.collapsed;

            localStorage.setItem(
                'sidebarCollapsed',
                this.collapsed ? '1' : '0'
            );
        }
    }"
    @toggle-sidebar.window="openSidebar = !openSidebar"
    class="min-h-screen bg-gray-100 dark:bg-gray-900"
>

{{-- TOP NAVIGATION --}}
@include('layouts.navigation')


{{-- MAIN WRAPPER --}}
<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative"
    :class="openSidebar
        ? (collapsed ? 'md:pl-20' : 'md:pl-64')
        : ''"
>

<div class="flex gap-6">

{{-- MOBILE OVERLAY --}}
<div
    x-show="openSidebar"
    x-transition.opacity
    class="fixed inset-0 bg-black/40 z-40 md:hidden"
    @click="openSidebar=false">
</div>


{{-- SIDEBAR --}}
<aside
    class="fixed inset-y-0 left-0 pt-16
           bg-gradient-to-b from-indigo-700 to-indigo-800
           text-indigo-100 z-50
           transform transition-all duration-200"
    :class="[
        openSidebar ? 'translate-x-0' : '-translate-x-full',
        collapsed ? 'md:w-20' : 'md:w-64'
    ]"
>

<div class="h-full flex flex-col">

{{-- SIDEBAR HEADER --}}
<div class="px-4 py-3 border-b border-indigo-600 flex items-center justify-between">

    <span
        class="text-sm font-semibold"
        x-show="!collapsed">
        Menu
    </span>

    <button
        class="p-2 rounded hidden md:inline-flex
               hover:bg-indigo-600/70
               focus:outline-none focus:ring-2 focus:ring-indigo-400"
        @click="toggleCollapse"
        aria-label="Collapse sidebar">

        <svg class="h-5 w-5"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             :class="collapsed ? 'rotate-180' : ''">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15 19l-7-7 7-7"/>
        </svg>

    </button>
</div>


{{-- MENU --}}
<nav class="p-2 space-y-1 flex-1 overflow-y-auto">

<a href="{{ route('dashboard') }}"
   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4
   {{ request()->routeIs('dashboard')
        ? 'bg-indigo-800 border-indigo-400'
        : 'border-transparent hover:bg-indigo-600/70' }}">

    <x-heroicon-o-home class="w-5 h-5"/>
    <span x-show="!collapsed">Home</span>
</a>

<a href="{{ route('sppd.create') }}"
   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4
   {{ request()->routeIs('sppd.create')
        ? 'bg-indigo-800 border-indigo-400'
        : 'border-transparent hover:bg-indigo-600/70' }}">

    <x-heroicon-o-document-plus class="w-5 h-5"/>
    <span x-show="!collapsed">Buat Surat Tugas</span>
</a>

<a href="{{ route('sppd.index') }}"
   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4
   {{ request()->routeIs('sppd.index')
        ? 'bg-indigo-800 border-indigo-400'
        : 'border-transparent hover:bg-indigo-600/70' }}">

    <x-heroicon-o-clock class="w-5 h-5"/>
    <span x-show="!collapsed">Riwayat Surat</span>
</a>

<a href="{{ route('profile.edit') }}"
   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4
   {{ request()->routeIs('profile.edit')
        ? 'bg-indigo-800 border-indigo-400'
        : 'border-transparent hover:bg-indigo-600/70' }}">

    <x-heroicon-o-user class="w-5 h-5"/>
    <span x-show="!collapsed">Settings</span>
</a>

</nav>


{{-- LOGOUT --}}
<div class="p-2 border-t border-indigo-600">
<form method="POST" action="{{ route('logout') }}">
@csrf

<button type="submit"
    class="w-full flex items-center gap-3 px-3 py-2 rounded
           hover:bg-indigo-600 text-red-200">

    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5"/>
    <span x-show="!collapsed">Logout</span>

</button>

</form>
</div>

</div>
</aside>


{{-- CONTENT --}}
<main class="flex-1">

<header class="bg-white dark:bg-gray-800 shadow">
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center gap-3">

<button
    @click="$dispatch('toggle-sidebar')"
    class="md:hidden p-2 rounded-md
           text-gray-700 dark:text-gray-200
           hover:bg-gray-100 dark:hover:bg-gray-700">

<x-heroicon-o-bars-3 class="w-6 h-6"/>

</button>

{{ $header ?? '' }}

</div>
</header>

<div class="py-6">
{{ $slot }}
</div>

</main>

</div>
</div>
</div>

@stack('scripts')

</body>
</html>