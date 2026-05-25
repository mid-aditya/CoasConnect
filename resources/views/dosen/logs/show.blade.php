<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Review Logbook: ') }} {{ $patientLog->koas->name }}
            </h2>
            <a href="{{ route('logs.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali ke Daftar</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Log Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Klinis</h3>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-sm text-gray-500">Tanggal Log</p>
                                    <p class="font-medium">
                                        {{ \Carbon\Carbon::parse($patientLog->log_date)->format('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Pasien</p>
                                    <p class="font-medium">{{ $patientLog->patient->initials }}
                                        ({{ $patientLog->patient->gender }}, {{ $patientLog->patient->age_category }})
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Rotasi</p>
                                    <p class="font-medium">{{ $patientLog->rotation->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Saat Ini</p>
                                    <p class="font-medium capitalize">{{ $patientLog->status }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 bg-gray-50 px-3 py-1 rounded">S-O
                                        (Subjektif-Objektif)</h4>
                                    <p class="mt-2 text-gray-600 px-3 whitespace-pre-line">
                                        {{ $patientLog->condition_summary }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 bg-gray-50 px-3 py-1 rounded">A
                                        (Assessment / Diagnosis)</h4>
                                    <p class="mt-2 text-gray-600 px-3 whitespace-pre-line">
                                        {{ $patientLog->key_examination }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 bg-gray-50 px-3 py-1 rounded">P (Plan
                                        / Rencana Terapi)</h4>
                                    <p class="mt-2 text-gray-600 px-3 whitespace-pre-line">
                                        {{ $patientLog->treatment_plan }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 bg-gray-50 px-3 py-1 rounded">
                                        Refleksi Klinis</h4>
                                    <p class="mt-2 text-gray-600 px-3 whitespace-pre-line">
                                        {{ $patientLog->clinical_reflection }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Form Review Dosen</h3>

                            @if(session('success'))
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-sm"
                                    role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('logs.update', $patientLog) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Keputusan
                                        Review</label>
                                    <select id="status" name="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="" disabled selected>Pilih Aksi...</option>
                                        <option value="approved" {{ $patientLog->status == 'approved' ? 'selected' : '' }}>Setujui (Approve)</option>
                                        <option value="revised" {{ $patientLog->status == 'revised' ? 'selected' : '' }}>
                                            Minta Revisi (Revise)</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="supervisor_comment"
                                        class="block text-sm font-medium text-gray-700">Catatan/Komentar
                                        (Opsional)</label>
                                    <textarea id="supervisor_comment" name="supervisor_comment" rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Berikan feedback untuk koas terkait laporan kasus ini...">{{ old('supervisor_comment', $patientLog->supervisor_comment) }}</textarea>
                                    @error('supervisor_comment') <span
                                    class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Kirim Review
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>