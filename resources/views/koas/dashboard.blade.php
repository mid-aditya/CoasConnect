<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Koas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-400">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Rotasi Saat Ini</h3>
                    <p class="mt-2 text-xl font-semibold text-gray-900 border-b pb-2">
                        @php $activeAssignment = auth()->user()->assignments()->where('end_date', '>=', now())->first();@endphp
                        {{ $activeAssignment->rotation->name ?? 'Tidak Ada Rotasi Aktif' }}
                    </p>
                    <p class="text-sm mt-2 text-gray-500">
                        {{ $activeAssignment ? \Carbon\Carbon::parse($activeAssignment->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($activeAssignment->end_date)->format('d M Y') : '-' }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Pasien</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ auth()->user()->patients()->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-400">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Log Disetujui</h3>
                    <p class="mt-2 text-3xl font-semibold text-green-600">
                        {{ auth()->user()->patientLogs()->where('status', 'approved')->count() }}
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg">Logbook Pasien Terbaru</h3>
                    <div class="space-x-2">
                        <a href="{{ route('patients.create') }}"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm transition">
                            + Pasien Baru
                        </a>
                        <a href="{{ route('logs.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition">
                            + Tulis Log
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto px-6 pb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pasien (Inisial)</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Diagnosis</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse(auth()->user()->patientLogs()->latest('log_date')->take(5)->get() as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($log->log_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $log->patient->initials }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 truncate max-w-xs">
                                        {{ $log->patient->working_diagnosis }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->status == 'approved')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                                        @elseif($log->status == 'submitted')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                                        @elseif($log->status == 'revised')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Revisi</span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Lihat / Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada log pasien
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>