@extends('layouts.app')
@section('title', 'Buat Log Klinis')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Buat Log Klinis Baru</h2>
        <p class="text-sm text-gray-500 mt-1">Catat aktivitas klinis yang telah dilakukan</p>
    </div>

    <form action="{{ route('logs.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        <input type="hidden" name="rotation_id" value="{{ $rotation->id ?? 1 }}">

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pasien</label>
                <select name="patient_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Pasien...</option>
                    @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                        {{ $patient->initials }}*** - {{ Str::limit($patient->working_diagnosis, 30) }}
                    </option>
                    @endforeach
                </select>
                @error('patient_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Aktivitas</label>
                <input type="date" name="log_date" value="{{ old('log_date', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('log_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ringkasan Kondisi</label>
            <textarea name="condition_summary" required rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="例: Pasien mengeluh demam 3 hari, batuk, pilek">{{ old('condition_summary') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pemeriksaan Fisik / Anamnesis</label>
            <textarea name="key_examination" required rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="例: KU sedang, compos mentis">{{ old('key_examination') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Plan / Tindakan</label>
            <textarea name="treatment_plan" required rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="例: Observe, symptomatic treatment">{{ old('treatment_plan') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Prosedur yang Dilakukan (opsional)</label>
            <textarea name="procedures_performed" rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="例: Injeksi IM">{{ old('procedures_performed') }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Refleksi Klinis</label>
            <textarea name="clinical_reflection" required rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="例: learned about importance of thorough examination">{{ old('clinical_reflection') }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <div class="flex space-x-4">
                <label class="flex items-center">
                    <input type="radio" name="status" value="draft" {{ old('status', 'draft') == 'draft' ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Simpan sebagai Draft</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="submitted" {{ old('status') == 'submitted' ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Submit untuk Review</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('logs.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Simpan Log
            </button>
        </div>
    </form>
</div>
@endsection
