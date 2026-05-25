@extends('layouts.app')
@section('title', 'Dashboard COAS')

@section('content')
<div class="space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="text-blue-100 mt-1">Berikut ringkasan aktivitas klinis Anda</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Active Patients --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pasien Aktif</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['active_patients'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Log Submitted --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Log Dikirim</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['submitted_logs'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Log Approved --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Log Disetujui</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['approved_logs'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Progress --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Progress</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['progress'] ?? 0 }}%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $stats['progress'] ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- My Patients --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Pasien Saya</h3>
                    <a href="{{ route('patients.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Lihat Semua →</a>
                </div>
                <div class="p-6">
                    @if($patients->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-gray-500">Belum ada pasien yang ditugaskan</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($patients as $patient)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold">
                                        {{ strtoupper(substr($patient->initials, 0, 1)) }}
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $patient->initials }}***</p>
                                                <p class="text-sm text-gray-500">{{ $patient->working_diagnosis }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($patient->condition === 'stable') bg-green-100 text-green-700
                                                @elseif($patient->condition === 'improving') bg-blue-100 text-blue-700
                                                @elseif($patient->condition === 'worsening') bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700 @endif">
                                                {{ ucfirst($patient->condition) }}
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('patients.show', $patient) }}" class="ml-4 px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Detail
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                </div>
                <div class="p-6">
                    @if($recentLogs->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500">Belum ada aktivitas</p>
                            <a href="{{ route('logs.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-700">
                                Buat Log Klinis →
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentLogs as $log)
                                <div class="flex items-start">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center
                                        @if($log->status === 'reviewed') bg-green-100 text-green-600
                                        @elseif($log->status === 'submitted') bg-yellow-100 text-yellow-600
                                        @else bg-gray-100 text-gray-600 @endif">
                                        @if($log->status === 'reviewed')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @elseif($log->status === 'submitted')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-800">{{ $log->activity_type }}</p>
                                        <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
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
                    <a href="{{ route('logs.create') }}" class="flex items-center p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="font-medium">Buat Log Klinis Baru</span>
                    </a>
                    <a href="{{ route('patients.index') }}" class="flex items-center p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-medium">Lihat Pasien Saya</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
