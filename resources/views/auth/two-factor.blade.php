<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-white">Verifikasi Keamanan</h2>
        <p class="text-gray-400 text-sm mt-1">Masukkan kode 6-digit yang dikirim ke email Anda</p>
    </div>

    <form method="POST" action="{{ route('2fa.store') }}" class="space-y-5">
        @csrf

        <!-- Kode OTP -->
        <div>
            <label for="code" class="block text-sm font-medium text-gray-300">Kode OTP</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph ph-shield-check text-gray-400 text-lg"></i>
                </div>
                <input id="code" type="text" name="code" required autofocus autocomplete="off" maxlength="6"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-700 rounded-lg bg-dark text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon transition-colors sm:text-sm text-center tracking-widest text-lg font-bold">
            </div>
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-darker bg-neon hover:bg-[#c4e600] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neon focus:ring-offset-dark transition-all duration-200">
                <i class="ph ph-check-circle mr-2 text-lg"></i>
                {{ __('Verifikasi') }}
            </button>
        </div>
    </form>

    <div class="mt-6 pt-4 border-t border-gray-800 text-center">
        <p class="text-xs text-gray-500 mb-2">Belum menerima kode?</p>
        <form method="POST" action="{{ route('2fa.resend') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-neon hover:text-[#c4e600] transition-colors">
                Kirim Ulang Kode
            </button>
        </form>
    </div>
</x-guest-layout>
