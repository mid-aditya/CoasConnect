@extends('layouts.app')
@section('title', 'Tambah Pasien')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Tambah Pasien Baru</h2>
        <p class="text-sm text-gray-500 mt-1">Lengkapi data pasien di bawah ini</p>
    </div>

    <form action="{{ route('patients.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inisial Pasien</label>
                <input type="text" name="initials" value="{{ old('initials') }}" required maxlength="10"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('initials') border-red-500 @enderror"
                    placeholder="AB">
                @error('initials')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Usia</label>
                <select name="age_category" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('age_category') border-red-500 @enderror">
                    <option value="">Pilih...</option>
                    <option value="Anak" {{ old('age_category') == 'Anak' ? 'selected' : '' }}>Anak</option>
                    <option value="Dewasa" {{ old('age_category') == 'Dewasa' ? 'selected' : '' }}>Dewasa</option>
                    <option value="Lansia" {{ old('age_category') == 'Lansia' ? 'selected' : '' }}>Lansia</option>
                </select>
                @error('age_category')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="gender" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('gender') border-red-500 @enderror">
                    <option value="">Pilih...</option>
                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konteks Perawatan</label>
                <select name="care_context" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('care_context') border-red-500 @enderror">
                    <option value="">Pilih...</option>
                    <option value="IGD" {{ old('care_context') == 'IGD' ? 'selected' : '' }}>IGD</option>
                    <option value="Rawat Inap" {{ old('care_context') == 'Rawat Inap' ? 'selected' : '' }}>Rawat Inap</option>
                    <option value="Poliklinik" {{ old('care_context') == 'Poliklinik' ? 'selected' : '' }}>Poliklinik</option>
                </select>
                @error('care_context')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis Kerja</label>
            <textarea name="working_diagnosis" required rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('working_diagnosis') border-red-500 @enderror"
                placeholder="例: Diabetes Melitus Tipe 2, Hipertensi Grade 2">{{ old('working_diagnosis') }}</textarea>
            @error('working_diagnosis')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('patients.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
