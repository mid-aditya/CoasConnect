@extends('layouts.app')
@section('title', 'Detail Pasien')

@section('content')
<div class="mb-6">
    <a href="{{ route('patients.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Patient Info --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-2xl mx-auto">
                    {{ strtoupper(substr($patient->initials, 0, 1)) }}
                </div>
                <h2 class="mt-4 text-xl font-semibold text-gray-800">{{ $patient->initials }}***</h2>
                <p class="text-gray-500 text-sm">{{ $patient->care_context }}</p>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Kategori</span>
                    <span class="font-medium text-gray-800">{{ $patient->age_category }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Jenis Kelamin</span>
                    <span class="font-medium text-gray-800">{{ $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Jumlah Log</span>
                    <span class="font-medium text-gray-800">{{ $logs->count() }}</span>
                </div>
            </div>

            <div class="mt-6 flex space-x-2">
                <a href="{{ route('patients.edit', $patient) }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-center text-gray-700 hover:bg-gray-50">
                    Edit
                </a>
                <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin hapus pasien ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 border border-red-300 rounded-lg text-red-600 hover:bg-red-50">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
            <h3 class="font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
            <a href="{{ route('logs.create') }}?patient_id={{ $patient->id }}" class="block w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 text-center mb-2">
                + Buat Log Klinis
            </a>
        </div>
    </div>

    {{-- Patient Details & Logs --}}
    <div class="lg:col-span-2">
        {{-- Diagnosis --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4">Diagnosis Kerja</h3>
            <p class="text-gray-600">{{ $patient->working_diagnosis }}</p>
        </div>

        {{-- Recent Logs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Log Klinis</h3>
                <a href="{{ route('logs.create') }}?patient_id={{ $patient->id }}" class="text-sm text-blue-600 hover:text-blue-700">
                    + Tambah Log
                </a>
            </div>
            <div class="p-6">
                @if($logs->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500">Belum ada log klinis</p>
                    <a href="{{ route('logs.create') }}?patient_id={{ $patient->id }}" class="text-blue-600 hover:text-blue-700 mt-2 inline-block">
                        Buat log pertama →
                    </a>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($logs as $log)
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="w-12 text-center">
                            <span class="text-2xl font-bold text-gray-400">{{ $log->log_date->format('d') }}</span>
                            <span class="text-xs text-gray-500 block">{{ $log->log_date->format('M') }}</span>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $log->condition_summary }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ Str::limit($log->key_examination, 100) }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($log->status === 'reviewed') bg-green-100 text-green-700
                                    @elseif($log->status === 'submitted') bg-yellow-100 text-yellow-700
                                    @else bg-gray-200 text-gray-600 @endif">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </div>
                            <a href="{{ route('logs.show', $log) }}" class="text-sm text-blue-600 hover:text-blue-700 mt-2 inline-block">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
