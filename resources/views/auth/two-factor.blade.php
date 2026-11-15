<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm flex items-start">
            <i class="ph ph-warning-circle text-lg mr-2 shrink-0 mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-white">Verifikasi Keamanan</h2>
        <p class="text-gray-400 text-sm mt-1">Masukkan kode 6-digit verifikasi akun Anda</p>
        @if(!empty($autoCode))
            <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-neon/10 text-neon border border-neon/30 animate-pulse">
                <i class="ph ph-check-circle mr-1.5 text-sm"></i> Kode OTP terisi otomatis
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('2fa.store') }}" class="space-y-5" id="form-2fa">
        @csrf

        <!-- Kode OTP -->
        <div>
            <label for="code" class="block text-sm font-medium text-gray-300">Kode OTP</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph ph-shield-check text-gray-400 text-lg"></i>
                </div>
                <input id="code" type="text" name="code" value="{{ old('code', $autoCode ?? '') }}" required autofocus autocomplete="off" maxlength="6"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-700 rounded-lg bg-dark text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon transition-colors sm:text-sm text-center tracking-widest text-lg font-bold">
            </div>
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" id="btn-submit-2fa" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-darker bg-neon hover:bg-[#c4e600] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neon focus:ring-offset-dark transition-all duration-200">
                <i class="ph ph-check-circle mr-2 text-lg"></i>
                <span id="btn-text">{{ __('Verifikasi') }}</span>
            </button>
        </div>
    </form>

    @if(!empty($autoCode) && !$errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('form-2fa');
                const btnText = document.getElementById('btn-text');
                if (form) {
                    if (btnText) {
                        btnText.innerText = 'Memverifikasi Otomatis...';
                    }
                    setTimeout(() => {
                        form.submit();
                    }, 600);
                }
            });
        </script>
    @endif

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
