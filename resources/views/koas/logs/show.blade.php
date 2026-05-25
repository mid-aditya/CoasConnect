@extends('layouts.app')
@section('title', 'Detail Log Klinis')

@section('content')
<div class="mb-6">
    <a href="{{ route('logs.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Log Klinis</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $log->log_date->format('d M Y') }} | {{ $log->patient->initials ?? 'N/A' }}***</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                @if($log->status === 'reviewed') bg-green-100 text-green-700
                @elseif($log->status === 'submitted') bg-yellow-100 text-yellow-700
                @else bg-gray-100 text-gray-700 @endif">
                {{ ucfirst($log->status) }}
            </span>
        </div>

        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Ringkasan Kondisi</h3>
                <p class="text-gray-800">{{ $log->condition_summary }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Pemeriksaan / Anamnesis</h3>
                <p class="text-gray-800">{{ $log->key_examination }}</p>
            </div>

            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Plan / Tindakan</h3>
                <p class="text-gray-800">{{ $log->treatment_plan }}</p>
            </div>

            @if($log->procedures_performed)
            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Prosedur</h3>
                <p class="text-gray-800">{{ $log->procedures_performed }}</p>
            </div>
            @endif

            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Refleksi Klinis</h3>
                <p class="text-gray-800">{{ $log->clinical_reflection }}</p>
            </div>

            @if($log->supervisor_comment)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-blue-700 uppercase tracking-wider mb-2">Komentar Supervisor</h3>
                <p class="text-blue-800">{{ $log->supervisor_comment }}</p>
            </div>
            @endif
        </div>

        @if($log->status === 'draft')
        <div class="mt-6 flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('logs.edit', $log) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Edit Log
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
