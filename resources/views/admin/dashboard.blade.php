@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-slate-700 to-slate-800 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Dashboard Administrator</h2>
        <p class="text-slate-300 mt-1">Kelola sistem CoasConnect</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        {{-- Total Users --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total User</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Patients --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Pasien</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_patients'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total COAS --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total COAS</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_coas'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Assignments --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penugasan Aktif</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['active_assignments'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Clinical Logs --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Log Klinis</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_logs'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua →</a>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="pb-3">Waktu</th>
                                    <th class="pb-3">User</th>
                                    <th class="pb-3">Aksi</th>
                                    <th class="pb-3">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 text-sm text-gray-500">2 menit lalu</td>
                                    <td class="py-3 text-sm font-medium text-gray-800">Admin</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">Create</span>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">Pasien baru ditambahkan</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 text-sm text-gray-500">15 menit lalu</td>
                                    <td class="py-3 text-sm font-medium text-gray-800">Dr. John</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Approve</span>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">Log klinis disetujui</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 text-sm text-gray-500">1 jam lalu</td>
                                    <td class="py-3 text-sm font-medium text-gray-800">Andi W.</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-full">Submit</span>
                                    </td>
                                    <td class="py-3 text-sm text-gray-600">Log klinis submitted</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Manajemen</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="#" class="flex items-center p-3 bg-slate-50 text-slate-700 rounded-lg hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="font-medium">Kelola User</span>
                    </a>
                    <a href="{{ route('admin.assignments.index') }}" class="flex items-center p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span class="font-medium">Kelola Penugasan</span>
                    </a>
                    <a href="#" class="flex items-center p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-medium">Kelola Pasien</span>
                    </a>
                    <a href="#" class="flex items-center p-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="font-medium">Kelola Kurikulum</span>
                    </a>
                    <a href="#" class="flex items-center p-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">Template WhatsApp</span>
                    </a>
                </div>
            </div>

            {{-- System Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Status Sistem</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Database</span>
                            <span class="flex items-center text-green-600">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Connected
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">WhatsApp API</span>
                            <span class="flex items-center text-green-600">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Active
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Queue Worker</span>
                            <span class="flex items-center text-green-600">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Running
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Last Backup</span>
                            <span class="text-sm text-gray-800">2 jam lalu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
