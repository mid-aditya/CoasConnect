<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tulis Logbook Pasien') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('logs.store') }}">
                        @csrf

                        <input type="hidden" name="rotation_id" value="{{ $rotation->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="patient_id" :value="__('Pasien')" />
                                <select id="patient_id" name="patient_id"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>Pilih Pasien</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->initials }} -
                                            {{ $patient->working_diagnosis }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('patient_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="log_date" :value="__('Tanggal')" />
                                <x-text-input id="log_date" class="block mt-1 w-full" type="date" name="log_date"
                                    :value="old('log_date', date('Y-m-d'))" required autofocus />
                                <x-input-error :messages="$errors->get('log_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label for="condition_summary" :value="__('S-O (Ringkasan Kondisi & Pemeriksaan)')" />
                            <textarea id="condition_summary" name="condition_summary" rows="4"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>{{ old('condition_summary') }}</textarea>
                            <x-input-error :messages="$errors->get('condition_summary')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="key_examination" :value="__('A (Diagnosis/Assessment/Pemeriksaan Penting)')" />
                            <textarea id="key_examination" name="key_examination" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>{{ old('key_examination') }}</textarea>
                            <x-input-error :messages="$errors->get('key_examination')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="treatment_plan" :value="__('P (Rencana Terapi/Tindakan)')" />
                            <textarea id="treatment_plan" name="treatment_plan" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>{{ old('treatment_plan') }}</textarea>
                            <x-input-error :messages="$errors->get('treatment_plan')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label for="clinical_reflection" :value="__('Refleksi Klinis / Hal yang Dipelajari')" />
                            <textarea id="clinical_reflection" name="clinical_reflection" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>{{ old('clinical_reflection') }}</textarea>
                            <x-input-error :messages="$errors->get('clinical_reflection')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <button type="submit" name="status" value="draft"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                                Simpan Draft
                            </button>
                            <button type="submit" name="status" value="submitted"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                Submit untuk Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>