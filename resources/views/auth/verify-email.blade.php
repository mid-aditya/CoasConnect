<x-guest-layout>
    <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-indigo-700">
                Terima kasih sudah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik link yang kami kirim ke email Anda. Jika Anda tidak menerima email, kami akan mengirim ulang dengan senang hati.
            </p>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-xl">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-green-700">
                    Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
                </p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
        @csrf

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Verifikasi Email</h1>
            <p class="text-gray-500 mt-2">Cek inbox email Anda untuk link verifikasi</p>
        </div>

        <!-- Resend Button -->
        <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <!-- Logout -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="text-center">
                <span class="text-sm text-gray-500">Tidak sesuai email ini?</span>
                <button type="submit" class="block w-full mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                    Logout dan gunakan email lain
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
