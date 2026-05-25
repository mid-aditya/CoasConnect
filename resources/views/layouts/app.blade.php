<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CoasConnect') }} - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <div class="flex">
            {{-- Sidebar --}}
            <aside class="fixed left-0 top-0 h-screen w-64 bg-slate-800 text-white z-40 transform transition-transform duration-200 ease-in-out" x-data="{ open: true }">
                {{-- Logo --}}
                <div class="h-16 flex items-center px-6 border-b border-slate-700">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <span class="font-bold text-lg">CoasConnect</span>
                    </a>
                </div>

                {{-- Role Badge --}}
                <div class="px-4 py-3 border-b border-slate-700">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if(auth()->user()->isAdmin()) bg-purple-500
                            @elseif(auth()->user()->isCoordinator()) bg-green-500
                            @elseif(auth()->user()->isDoctor()) bg-blue-500
                            @else bg-orange-500 @endif">
                            {{ auth()->user()->isAdmin() ? 'Admin' :
                               (auth()->user()->isCoordinator() ? 'Coordinator' :
                               (auth()->user()->isDoctor() ? 'Doctor' : 'COAS')) }}
                        </span>
                        <span class="text-sm text-slate-400">{{ auth()->user()->name }}</span>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    {{-- Common Links --}}
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    {{-- COAS Links --}}
                    @if(auth()->user()->isCoas())
                        <a href="{{ route('patients.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('patients.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Pasien Saya
                        </a>
                        <a href="{{ route('logs.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('logs.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Log Klinis
                        </a>
                    @endif

                    {{-- Doctor Links --}}
                    @if(auth()->user()->isDoctor())
                        <a href="{{ route('logs.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('logs.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Review Log
                        </a>
                    @endif

                    {{-- Admin/Coordinator Links --}}
                    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                        <div class="pt-4 pb-2">
                            <span class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Manajemen</span>
                        </div>
                        <a href="{{ route('admin.assignments.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-slate-300 hover:bg-slate-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Penugasan
                        </a>
                        <a href="{{ route('patients.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-slate-300 hover:bg-slate-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pasien
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-slate-300 hover:bg-slate-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Kurikulum
                        </a>
                    @endif

                    {{-- Admin Only Links --}}
                    @if(auth()->user()->isAdmin())
                        <div class="pt-4 pb-2">
                            <span class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sistem</span>
                        </div>
                        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-slate-300 hover:bg-slate-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Pengguna
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-slate-300 hover:bg-slate-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pengaturan
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 rounded-lg transition-colors text-slate-300 hover:bg-slate-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Audit Log
                        </a>
                    @endif
                </nav>

                {{-- Footer --}}
                <div class="p-4 border-t border-slate-700">
                    <div class="text-xs text-slate-500 text-center">
                        v1.0.0 &copy; 2024 CoasConnect
                    </div>
                </div>
            </aside>

            {{-- Main Content --}}
            <main class="flex-1 ml-64 min-h-screen">
                {{-- Top Header --}}
                <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        {{-- Notifications --}}
                        <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        {{-- Profile Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 hover:bg-gray-100 px-3 py-2 rounded-lg">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                                </div>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Page Content --}}
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
