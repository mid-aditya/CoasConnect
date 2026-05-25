@extends('layouts.app')
@section('title', 'Edit Pasien')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('patients.show', $patient) }}" class="text-blue-600 hover:text-blue-700 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Edit Data Pasien</h2>
        <p class="text-sm text-gray-500 mt-1">Perbarui informasi pasien</p>
    </div>

    <form action="{{ route('patients.update', $patient) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inisial Pasien</label>
                <input type="text" name="initials" value="{{ old('initials', $patient->initials) }}" required maxlength="10"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Usia</label>
                <select name="age_category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="Anak" {{ $patient->age_category == 'Anak' ? 'selected' : '' }}>Anak</option>
                    <option value="Dewasa" {{ $patient->age_category == 'Dewasa' ? 'selected' : '' }}>Dewasa</option>
                    <option value="Lansia" {{ $patient->age_category == 'Lansia' ? 'selected' : '' }}>Lansia</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="gender" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="L" {{ $patient->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $patient->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konteks Perawatan</label>
                <select name="care_context" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="IGD" {{ $patient->care_context == 'IGD' ? 'selected' : '' }}>IGD</option>
                    <option value="Rawat Inap" {{ $patient->care_context == 'Rawat Inap' ? 'selected' : '' }}>Rawat Inap</option>
                    <option value="Poliklinik" {{ $patient->care_context == 'Poliklinik' ? 'selected' : '' }}>Poliklinik</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Kerja</label>
            <textarea name="working_diagnosis" required rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $patient->working_diagnosis }}</textarea>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
