@extends('layouts.app')
@section('title', 'Pasien Saya')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-xl font-semibold text-gray-800">Daftar Pasien</h2>
    <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        + Tambah Pasien
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inisial</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori Usia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konteks</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($patients as $patient)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold">
                                {{ strtoupper(substr($patient->initials, 0, 1)) }}
                            </div>
                            <span class="ml-3 font-medium text-gray-900">{{ $patient->initials }}***</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $patient->age_category }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $patient->gender }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($patient->working_diagnosis, 40) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $patient->care_context }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('patients.show', $patient) }}" class="text-blue-600 hover:text-blue-800 mr-3">Detail</a>
                        <a href="{{ route('patients.edit', $patient) }}" class="text-green-600 hover:text-green-800 mr-3">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p>Belum ada pasien.</p>
                        <a href="{{ route('patients.create') }}" class="text-blue-600 hover:text-blue-700 mt-2 inline-block">Tambah pasien pertama →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $patients->links() }}
    </div>
    @endif
</div>
@endsection
