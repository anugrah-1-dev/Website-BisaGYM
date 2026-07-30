<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-white">Welcome Back</h2>
        <p class="text-gray-400 text-sm mt-1">Please sign in to your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph ph-envelope text-gray-400 text-lg"></i>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-700 rounded-lg bg-dark text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon transition-colors sm:text-sm">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph ph-lock-key text-gray-400 text-lg"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="block w-full pl-10 pr-10 py-2 border border-gray-700 rounded-lg bg-dark text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-neon focus:border-neon transition-colors sm:text-sm">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button type="button" id="toggle-password" onclick="togglePassword()" 
                        class="text-gray-400 hover:text-neon focus:outline-none transition-colors duration-200"
                        title="Lihat/Sembunyikan Password">
                        <i id="eye-icon" class="ph ph-eye text-lg"></i>
                    </button>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <script>
            function togglePassword() {
                const input = document.getElementById('password');
                const icon = document.getElementById('eye-icon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ph-eye');
                    icon.classList.add('ph-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ph-eye-slash');
                    icon.classList.add('ph-eye');
                }
            }
        </script>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-neon bg-dark border-gray-700 rounded focus:ring-neon focus:ring-offset-dark">
                <label for="remember_me" class="ml-2 block text-sm text-gray-400">
                    {{ __('Remember me') }}
                </label>
            </div>

            @if (Route::has('password.request'))
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-gray-400 hover:text-neon transition-colors">
                        {{ __('Forgot password?') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-darker bg-neon hover:bg-[#c4e600] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neon focus:ring-offset-dark transition-all duration-200">
                <i class="ph ph-sign-in mr-2 text-lg"></i>
                {{ __('Log in') }}
            </button>
        </div>
        

    </form>
</x-guest-layout>
