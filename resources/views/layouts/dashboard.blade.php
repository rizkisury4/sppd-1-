<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <style>
            [x-cloak] { display: none !important; }
        </style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{
                openSidebar: window.innerWidth >= 768,
                collapsed: false,
                init() {
                    this.collapsed = localStorage.getItem('sidebarCollapsed') === '1';
                    if (window.innerWidth < 768) {
                        this.openSidebar = false;
                    }
                },
                isDesktop() {
                    return window.innerWidth >= 768;
                },
                syncCollapse() {
                    localStorage.setItem('sidebarCollapsed', this.collapsed ? '1' : '0');
                },
                toggleSidebar() {
                    if (this.isDesktop()) {
                        this.collapsed = !this.collapsed;
                        this.openSidebar = true;
                        this.syncCollapse();
                        return;
                    }

                    this.openSidebar = !this.openSidebar;
                },
                closeSidebar() {
                    this.openSidebar = false;
                }
            }"
             x-on:toggle-sidebar.window="toggleSidebar()"
             class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')
            <div class="relative">
                <div x-show="openSidebar" x-transition.opacity class="fixed inset-0 bg-black/40 z-40 md:hidden"
                     x-on:click="openSidebar=false"></div>
                <aside class="fixed inset-y-0 left-0 pt-16 bg-gradient-to-b from-cyan-800 via-sky-800 to-cyan-900 text-cyan-50 z-50 w-[17rem] overflow-x-hidden shadow-2xl transform transition-[width,transform] duration-200 md:w-[17rem]"
                       :class="[openSidebar ? 'translate-x-0' : '-translate-x-full', collapsed ? 'md:w-[6.75rem]' : 'md:w-[17rem]']"
                       aria-label="Left Sidebar">
                    <div class="h-full flex flex-col">
                        <div class="border-b border-white/10 px-4 pb-6 pt-5" :class="collapsed ? 'px-3' : 'px-5'">
                            <div class="flex items-center" :class="collapsed ? 'justify-center' : 'gap-3'">
                                <div class="flex h-12 w-20 shrink-0 items-center justify-center rounded-xl bg-white p-2 shadow-lg shadow-cyan-950/20 ring-1 ring-white/20">
                                    <img src="{{ asset('images/pln.png') }}" alt="Logo PLN" class="h-full w-full object-contain object-left" />
                                </div>
                                <div x-show="!collapsed" x-cloak class="min-w-0 leading-tight">
                                    <div class="text-sm font-semibold tracking-wide text-white">SPPD PLN EMI</div>
                                    <div class="mt-1 text-[11px] text-cyan-100/70">Portal perjalanan dinas</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center border-b border-white/10 py-3"
                             :class="collapsed ? 'justify-center px-3' : 'justify-end px-5'">
                            <button class="hidden rounded-xl p-2 text-cyan-100/80 transition-colors hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-cyan-300 md:inline-flex"
                                    x-on:click="toggleSidebar()" aria-label="Toggle sidebar collapse">
                                <svg class="h-5 w-5 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     :class="collapsed ? 'rotate-180' : ''">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        </div>
                        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5" :class="collapsed ? 'px-3' : 'px-4'">
                            <a href="{{ route('dashboard') }}"
                               class="group flex items-center rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/14 text-white shadow-lg shadow-cyan-950/15' : 'text-cyan-50/90 hover:bg-white/10 hover:text-white' }}"
                               :class="collapsed ? 'mx-auto h-12 w-12 justify-center px-0' : 'gap-4 px-4 py-3.5'"
                               :title="collapsed ? 'Dashboard' : null">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5l9-7 9 7V20a2 2 0 01-2 2h-4a2 2 0 01-2-2v-5H9v5a2 2 0 01-2 2H3z"/></svg>
                                <span class="text-[15px] font-medium tracking-wide transition-colors group-hover:text-white" x-show="!collapsed" x-cloak>Dashboard</span>
                            </a>
                            @if(auth()->user()?->role==='admin')
                            <a href="{{ route('admin.metrics') }}"
                               class="group flex items-center rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.metrics') ? 'bg-white/14 text-white shadow-lg shadow-cyan-950/15' : 'text-cyan-50/90 hover:bg-white/10 hover:text-white' }}"
                               :class="collapsed ? 'mx-auto h-12 w-12 justify-center px-0' : 'gap-4 px-4 py-3.5'"
                               :title="collapsed ? 'Master Data' : null">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                <span class="text-[15px] font-medium tracking-wide transition-colors group-hover:text-white" x-show="!collapsed" x-cloak>Master Data</span>
                            </a>
                            <a href="{{ route('admin.employees.index') }}"
                               class="group flex items-center rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.employees.*') ? 'bg-white/14 text-white shadow-lg shadow-cyan-950/15' : 'text-cyan-50/90 hover:bg-white/10 hover:text-white' }}"
                               :class="collapsed ? 'mx-auto h-12 w-12 justify-center px-0' : 'gap-4 px-4 py-3.5'"
                               :title="collapsed ? 'Data Kepegawaian' : null">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 11c1.657 0 3-1.79 3-4S9.657 3 8 3 5 4.79 5 7s1.343 4 3 4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 13c-2.761 0-5 2.239-5 5v1h10v-1c0-2.761-2.239-5-5-5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 13c-.932 0-1.803.254-2.55.696A6.98 6.98 0 0115 18v1h6v-1c0-2.761-2.239-5-5-5z"/></svg>
                                <span class="text-[15px] font-medium tracking-wide transition-colors group-hover:text-white" x-show="!collapsed" x-cloak>Data Kepegawaian</span>
                            </a>
                            @endif
                            @if(auth()->user()?->role==='admin')
                            <a href="{{ route('sppd.create') }}"
                               class="group flex items-center rounded-2xl transition-all duration-200 {{ request()->routeIs('sppd.create') ? 'bg-white/14 text-white shadow-lg shadow-cyan-950/15' : 'text-cyan-50/90 hover:bg-white/10 hover:text-white' }}"
                               :class="collapsed ? 'mx-auto h-12 w-12 justify-center px-0' : 'gap-4 px-4 py-3.5'"
                               :title="collapsed ? 'Buat Surat Tugas' : null">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l4 4v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 11v6M9 14h6"/></svg>
                                <span class="text-[15px] font-medium tracking-wide transition-colors group-hover:text-white" x-show="!collapsed" x-cloak>Buat Surat Tugas</span>
                            </a>
                            @endif
                            <a href="{{ route('sppd.index') }}"
                               class="group flex items-center rounded-2xl transition-all duration-200 {{ request()->routeIs('sppd.index') ? 'bg-white/14 text-white shadow-lg shadow-cyan-950/15' : 'text-cyan-50/90 hover:bg-white/10 hover:text-white' }}"
                               :class="collapsed ? 'mx-auto h-12 w-12 justify-center px-0' : 'gap-4 px-4 py-3.5'"
                               :title="collapsed ? 'Riwayat Surat' : null">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                                <span class="text-[15px] font-medium tracking-wide transition-colors group-hover:text-white" x-show="!collapsed" x-cloak>Riwayat Surat</span>
                            </a>
                            <a href="{{ route('profile.edit') }}"
                               class="group flex items-center rounded-2xl transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'bg-white/14 text-white shadow-lg shadow-cyan-950/15' : 'text-cyan-50/90 hover:bg-white/10 hover:text-white' }}"
                               :class="collapsed ? 'mx-auto h-12 w-12 justify-center px-0' : 'gap-4 px-4 py-3.5'"
                               :title="collapsed ? 'Settings' : null">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a4 4 0 110 8 4 4 0 010-8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 20a6 6 0 1112 0"/></svg>
                                <span class="text-[15px] font-medium tracking-wide transition-colors group-hover:text-white" x-show="!collapsed" x-cloak>Settings</span>
                            </a>
                        </nav>
                        <div class="border-t border-white/10 p-4">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="group flex w-full items-center rounded-2xl text-amber-100 transition-all duration-200 hover:bg-white/10 hover:text-white"
                                        :class="collapsed ? 'mx-auto h-12 w-12 justify-center px-0' : 'gap-4 px-4 py-3.5 text-left'"
                                        :title="collapsed ? 'Logout' : null">
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12H9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 19a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h5a2 2 0 012 2"/></svg>
                                    <span class="text-[15px] font-medium tracking-wide transition-colors group-hover:text-white" x-show="!collapsed" x-cloak>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>
                <main class="min-h-screen transition-all duration-200"
                      :class="openSidebar ? (collapsed ? 'md:ml-[6.75rem]' : 'md:ml-[17rem]') : 'md:ml-0'">
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="flex items-center gap-3 px-4 py-6 sm:px-6 lg:px-8">
                                <button x-on:click="$dispatch('toggle-sidebar')" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-label="Toggle sidebar">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                {{ $header ?? '' }}
                            </div>
                        </header>
                        <div class="px-4 py-6 sm:px-6 lg:px-8">
                            {{ $slot }}
                        </div>
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
    </html>
