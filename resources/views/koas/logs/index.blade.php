@extends('layouts.app')
@section('title', 'Log Klinis')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-xl font-semibold text-gray-800">Log Klinis Saya</h2>
    <a href="{{ route('logs.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        + Buat Log Baru
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ringkasan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $log->log_date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                        {{ $log->patient->initials ?? 'N/A' }}***
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ Str::limit($log->condition_summary, 50) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($log->status === 'reviewed') bg-green-100 text-green-700
                            @elseif($log->status === 'submitted') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($log->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('logs.show', $log) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Belum ada log klinis.</p>
                        <a href="{{ route('logs.create') }}" class="text-blue-600 hover:text-blue-700 mt-2 inline-block">Buat log pertama →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
