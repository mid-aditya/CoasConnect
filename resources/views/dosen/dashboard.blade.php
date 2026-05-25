@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@section('content')
<div class="space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="text-indigo-100 mt-1">Berikut ringkasan supervise COAS Anda</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total COAS --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total COAS</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_coas'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Review --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Menunggu Review</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ $stats['pending_reviews'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Reviewed This Week --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sudah Direview</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['reviewed_this_week'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Assignments --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penugasan Aktif</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['active_assignments'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Pending Reviews List --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Log Menunggu Review</h3>
                    <span class="px-2 py-1 text-xs font-semibold bg-orange-100 text-orange-700 rounded-full">
                        {{ $pendingLogs->count() }} tertunda
                    </span>
                </div>
                <div class="p-6">
                    @if($pendingLogs->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500">Semua log sudah direview!</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th class="pb-3">Tanggal</th>
                                        <th class="pb-3">COAS</th>
                                        <th class="pb-3">Jenis Aktivitas</th>
                                        <th class="pb-3">Status</th>
                                        <th class="pb-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($pendingLogs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 text-sm text-gray-600">
                                            {{ $log->created_at->format('d M Y') }}
                                        </td>
                                        <td class="py-3 text-sm font-medium text-gray-800">
                                            {{ $log->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 text-sm text-gray-600">
                                            {{ ucfirst(str_replace('_', ' ', $log->activity_type)) }}
                                        </td>
                                        <td class="py-3">
                                            <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-full">
                                                Pending
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <a href="{{ route('logs.show', $log) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                                Review →
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- COAS List --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">COAS Saya</h3>
                </div>
                <div class="p-6">
                    @if($coasList->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <p class="text-gray-500">Belum ada COAS</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($coasList as $coas)
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold">
                                        {{ substr($coas->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-800">{{ $coas->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $coas->patientLogs()->count() }} log submitted
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-medium text-green-600">
                                            {{ $coas->patientLogs()->where('status', 'reviewed')->count() }} approved
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('logs.index') }}" class="flex items-center p-3 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="font-medium">Review Semua Log</span>
                    </a>
                    <a href="#" class="flex items-center p-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="font-medium">Export Laporan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
