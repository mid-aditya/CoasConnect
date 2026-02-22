<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Pasien Baru') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('patients.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="initials" :value="__('Inisial Pasien (Cth: Tn. A / Ny. B)')" />
                                <x-text-input id="initials" class="block mt-1 w-full" type="text" name="initials"
                                    :value="old('initials')" required autofocus />
                                <x-input-error :messages="$errors->get('initials')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                                <select id="gender" name="gender"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="age_category" :value="__('Kategori Usia')" />
                                <select id="age_category" name="age_category"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    <option value="Neonatus">Neonatus (0-28 hari)</option>
                                    <option value="Bayi">Bayi (1 bulan - 1 tahun)</option>
                                    <option value="Balita">Balita (1 - 5 tahun)</option>
                                    <option value="Anak">Anak (>5 - 12 tahun)</option>
                                    <option value="Remaja">Remaja (>12 - 18 tahun)</option>
                                    <option value="Dewasa">Dewasa (>18 - 60 tahun)</option>
                                    <option value="Lansia">Lansia (> 60 tahun)</option>
                                </select>
                                <x-input-error :messages="$errors->get('age_category')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="care_context" :value="__('Konteks Perawatan')" />
                                <select id="care_context" name="care_context"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>Pilih Konteks</option>
                                    <option value="IGD">IGD (Gawat Darurat)</option>
                                    <option value="Rawat Inap">Rawat Inap (Bangsal)</option>
                                    <option value="Rawat Jalan">Rawat Jalan (Poliklinik)</option>
                                    <option value="ICU">ICU/HCU</option>
                                    <option value="Kamar Operasi">Kamar Operasi (OK)</option>
                                </select>
                                <x-input-error :messages="$errors->get('care_context')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label for="working_diagnosis" :value="__('Diagnosis Kerja (Working Diagnosis)')" />
                            <x-text-input id="working_diagnosis" class="block mt-1 w-full" type="text"
                                name="working_diagnosis" :value="old('working_diagnosis')" required />
                            <x-input-error :messages="$errors->get('working_diagnosis')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Simpan Data Pasien
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>