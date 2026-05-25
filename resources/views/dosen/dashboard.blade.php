<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Dosen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Koas Bimbingan</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ auth()->user()->supervisedAssignments()->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-400">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Menunggu Review</h3>
                    <p class="mt-2 text-3xl font-semibold text-yellow-600">
                        @php
                            $koasIds = auth()->user()->supervisedAssignments()->pluck('user_id');
                            $pendingReview = \App\Models\PatientLog::whereIn('user_id', $koasIds)->where('status', 'submitted')->count();
                        @endphp
                        {{ $pendingReview }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-400">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Koas Inaktif (>24j)</h3>
                    <p class="mt-2 text-3xl font-semibold text-red-600">0</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Daftar Koas Bimbingan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Koas</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rotasi</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Log Menunggu</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(auth()->user()->supervisedAssignments as $assignment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span
                                                        class="text-gray-600 font-medium">{{ substr($assignment->koas->name, 0, 1) }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $assignment->koas->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $assignment->rotation->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $pendingCount = \App\Models\PatientLog::where('user_id', $assignment->user_id)->where('status', 'submitted')->count();
                                            @endphp
                                            @if($pendingCount > 0)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ $pendingCount }} Menunggu
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('logs.index') }}" class="text-indigo-600 hover:text-indigo-900 relative">
                                                Lihat Log
                                                @if($pendingCount > 0)
                                                    <span
                                                        class="absolute top-0 right-[-10px] inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada koas
                                            bimbingan</td>
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