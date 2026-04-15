<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
     <div 
x-data="{ 
    openSidebar: window.innerWidth >= 768, 
    collapsed: false,

    init(){ 
        const v = localStorage.getItem('sidebarCollapsed'); 
        this.collapsed = v === '1' 
    },

    toggleCollapse(){ 
        this.collapsed = !this.collapsed; 
        localStorage.setItem('sidebarCollapsed', this.collapsed ? '1' : '0') 
    }
}" 
@toggle-sidebar.window="openSidebar = !openSidebar"
class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative"
               :class="collapsed ? 'md:pl-20' : 'md:pl-64'">
                 <div x-data="{ open: false }">
    
</div>
                <div class="flex gap-6">
                    <div x-show="openSidebar" x-transition.opacity class="fixed inset-0 bg-black/40 z-40 md:hidden"
                         x-on:click="openSidebar=false"></div>
         <aside 
class="fixed inset-y-0 left-0 pt-16 bg-gradient-to-b from-indigo-700 to-indigo-800 text-indigo-100 z-50 transform transition-all duration-200"
:class="[
    openSidebar ? 'translate-x-0' : '-translate-x-full',
    collapsed ? 'w-20' : 'w-64'
]"
>
                        
                        <div class="h-full flex flex-col">
                            <div class="px-4 py-3 border-b border-indigo-600 flex items-center justify-between">
                                <div class="text-sm font-semibold" x-show="!collapsed">Menu</div>
                                <button class="p-2 rounded hidden md:inline-flex transition-colors hover:bg-indigo-600/70 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                         x-on:click="toggleCollapse()" aria-label="Collapse sidebar">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :class="collapsed ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                            </div>
                            <nav class="p-2 space-y-1 flex-1 overflow-y-auto">
                                <a href="{{ route('dashboard') }}"
                                   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-800 border-indigo-400' : 'border-transparent hover:bg-indigo-600/70' }}">
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5l9-7 9 7V20a2 2 0 01-2 2h-4a2 2 0 01-2-2v-5H9v5a2 2 0 01-2 2H3z"/></svg>
                                    <span class="text-base transition-colors group-hover:text-white" x-show="!collapsed">Home</span>
                                </a>
                                @if(auth()->user()?->role==='admin')
                                <a href="{{ route('admin.metrics') }}"
                                   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4 transition-colors {{ request()->routeIs('admin.metrics') ? 'bg-indigo-800 border-indigo-400' : 'border-transparent hover:bg-indigo-600/70' }}">
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                    <span class="text-base transition-colors group-hover:text-white" x-show="!collapsed">Master Data</span>
                                </a>
                                @endif
                                <a href="{{ route('sppd.create') }}"
                                   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4 transition-colors {{ request()->routeIs('sppd.create') ? 'bg-indigo-800 border-indigo-400' : 'border-transparent hover:bg-indigo-600/70' }}">
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 11v6M9 14h6"/></svg>
                                    <span class="text-base transition-colors group-hover:text-white" x-show="!collapsed">Buat Surat Tugas</span>
                                </a>
                                <a href="{{ route('sppd.index') }}"
                                   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4 transition-colors {{ request()->routeIs('sppd.index') ? 'bg-indigo-800 border-indigo-400' : 'border-transparent hover:bg-indigo-600/70' }}">
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                                    <span class="text-base transition-colors group-hover:text-white" x-show="!collapsed">Riwayat Surat</span>
                                </a>
                                <a href="{{ route('profile.edit') }}"
                                   class="group flex items-center gap-3 px-3 py-2 rounded border-l-4 transition-colors {{ request()->routeIs('profile.edit') ? 'bg-indigo-800 border-indigo-400' : 'border-transparent hover:bg-indigo-600/70' }}">
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a4 4 0 110 8 4 4 0 010-8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 20a6 6 0 1112 0"/></svg>
                                    <span class="text-base transition-colors group-hover:text-white" x-show="!collapsed">Settings</span>
                                </a>
                            </nav>
                            <div class="p-2 border-t border-indigo-600">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="group w-full text-left flex items-center gap-3 px-3 py-2 rounded transition-colors hover:bg-indigo-600 text-red-200">
                                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12H9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 19a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h5a2 2 0 012 2"/></svg>
                                        <span class="text-base transition-colors group-hover:text-white" x-show="!collapsed">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </aside>
                    <main class="flex-1">
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center gap-3">
                                <button x-on:click="$dispatch('toggle-sidebar')" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="Toggle sidebar">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
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
