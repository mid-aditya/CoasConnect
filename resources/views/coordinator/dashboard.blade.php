@extends('layouts.app')
@section('title', 'Dashboard Koordinator')

@section('content')
<div class="space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold">Dashboard Koordinator</h2>
        <p class="text-emerald-100 mt-1">Kelola program dan pantau progress COAS</p>
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
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Rotations --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rotasi Aktif</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['active_rotations'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Avg Progress --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rata-rata Progress</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['avg_progress'] ?? 0 }}%</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Reviews --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Review</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ $stats['pending_reviews'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- COAS Progress --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Progress COAS</h3>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-700">Lihat Detail →</a>
                </div>
                <div class="p-6">
                    @if($coasProgress->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <p class="text-gray-500">Belum ada data COAS</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($coasProgress as $coas)
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 font-semibold">
                                        {{ substr($coas['name'], 0, 1) }}
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium text-gray-800">{{ $coas['name'] }}</span>
                                            <span class="text-sm font-medium {{ $coas['progress'] >= 50 ? 'text-green-600' : 'text-orange-600' }}">
                                                {{ $coas['progress'] }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $coas['progress'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.assignments.index') }}" class="flex items-center p-3 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span class="font-medium">Kelola Penugasan</span>
                    </a>
                    <a href="#" class="flex items-center p-3 bg-teal-50 text-teal-700 rounded-lg hover:bg-teal-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="font-medium">Kelola Kurikulum</span>
                    </a>
                    <a href="#" class="flex items-center p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="font-medium">Export Laporan</span>
                    </a>
                </div>
            </div>

            {{-- Competency Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Ringkasan Kompetensi</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Profesionalitas</span>
                            <span class="text-sm font-medium text-gray-800">{{ $competencies['profesional'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $competencies['profesional'] ?? 0 }}%"></div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Keterampilan Klinis</span>
                            <span class="text-sm font-medium text-gray-800">{{ $competencies['keterampilan'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $competencies['keterampilan'] ?? 0 }}%"></div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pengetahuan</span>
                            <span class="text-sm font-medium text-gray-800">{{ $competencies['pengetahuan'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $competencies['pengetahuan'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
