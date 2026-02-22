<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Koas</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ \App\Models\User::whereHas('role', function ($q) {
    $q->where('name', 'Koas'); })->count() }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Rotasi Aktif</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ \App\Models\Rotation::count() }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Akses Cepat</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="#"
                            class="block p-4 bg-blue-50 text-blue-700 rounded-lg text-center hover:bg-blue-100 transition">
                            Manajemen User
                        </a>
                        <a href="#"
                            class="block p-4 bg-green-50 text-green-700 rounded-lg text-center hover:bg-green-100 transition">
                            Periode Akademik
                        </a>
                        <a href="{{ route('admin.assignments.index') }}"
                            class="block p-4 bg-purple-50 text-purple-700 rounded-lg text-center hover:bg-purple-100 transition">
                            Penempatan Rotasi
                        </a>
                        <a href="#"
                            class="block p-4 bg-yellow-50 text-yellow-700 rounded-lg text-center hover:bg-yellow-100 transition">
                            Data Master
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>