@extends('layouts.app')
@section('title', 'Edit Log Klinis')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('logs.show', $log) }}" class="text-blue-600 hover:text-blue-700 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Edit Log Klinis</h2>
        <p class="text-sm text-gray-500 mt-1">Perbarui data log klinis</p>
    </div>

    <form action="{{ route('logs.update', $log) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ringkasan Kondisi</label>
            <textarea name="condition_summary" required rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('condition_summary', $log->condition_summary) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pemeriksaan / Anamnesis</label>
            <textarea name="key_examination" required rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('key_examination', $log->key_examination) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Plan / Tindakan</label>
            <textarea name="treatment_plan" required rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('treatment_plan', $log->treatment_plan) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Prosedur yang Dilakukan</label>
            <textarea name="procedures_performed" rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('procedures_performed', $log->procedures_performed) }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Refleksi Klinis</label>
            <textarea name="clinical_reflection" required rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('clinical_reflection', $log->clinical_reflection) }}</textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <div class="flex space-x-4">
                <label class="flex items-center">
                    <input type="radio" name="status" value="draft" {{ $log->status == 'draft' ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Simpan sebagai Draft</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="submitted" {{ $log->status == 'submitted' ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Submit untuk Review</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('logs.show', $log) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Update Log
            </button>
        </div>
    </form>
</div>
@endsection
