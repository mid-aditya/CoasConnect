<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Koordinator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Koas Aktif</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ \App\Models\RotationAssignment::where('end_date', '>=', now())->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Log Disetujui</h3>
                    <p class="mt-2 text-3xl font-semibold text-green-600">
                        {{ \App\Models\PatientLog::where('status', 'approved')->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Menunggu Review</h3>
                    <p class="mt-2 text-3xl font-semibold text-yellow-600">
                        {{ \App\Models\PatientLog::where('status', 'submitted')->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Angkatan Aktif</h3>
                    <p class="mt-2 text-3xl font-semibold text-indigo-600">{{ \App\Models\Cohort::count() }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Agregat Rotasi</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rotasi</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Koas Terdaftar</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Logbook Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(\App\Models\Rotation::all() as $rotation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $rotation->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $rotation->assignments()->count() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                @php
                                                    $total = $rotation->assignments()->count();
                                                    $approved = \App\Models\PatientLog::where('rotation_id', $rotation->id)->where('status', 'approved')->count();
                                                    $percentage = $total > 0 ? min(100, ceil(($approved / ($total * 20)) * 100)) : 0;
                                                @endphp
                                                <div class="bg-blue-600 h-2.5 rounded-full"
                                                    style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada rotasi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>