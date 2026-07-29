<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
                <i class="ph ph-user-gear mr-2 text-blue-400"></i> {{ __('Edit User Login') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('users.index') }}" class="inline-flex items-center text-sm text-gray-400 hover:text-blue-400 transition-colors">
                <i class="ph ph-arrow-left mr-2"></i> Kembali ke Daftar User
            </a>
        </div>

        <div class="bg-card rounded-xl border border-gray-800 shadow-lg overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-300" />
                        <x-text-input id="name" class="block mt-1 w-full bg-darker border-gray-700 text-white focus:ring-blue-500 focus:border-blue-500" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email Login')" class="text-gray-300" />
                        <x-text-input id="email" class="block mt-1 w-full bg-darker border-gray-700 text-white focus:ring-blue-500 focus:border-blue-500" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <x-input-label for="role" :value="__('Role (Hak Akses)')" class="text-gray-300" />
                        <select id="role" name="role" class="block mt-1 w-full bg-darker border-gray-700 text-white focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ (old('role') ?? $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="pt-4 mt-4 border-t border-gray-800">
                        <h4 class="text-white font-medium mb-4"><i class="ph ph-lock-key mr-2"></i> Ganti Password (Opsional)</h4>
                        <p class="text-xs text-gray-500 mb-4">Kosongkan kolom di bawah ini jika tidak ingin mengubah password.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div x-data="{ show: false }">
                                <x-input-label for="password" :value="__('Password Baru')" class="text-gray-300" />
                                <div class="relative">
                                    <x-text-input id="password" class="block mt-1 w-full bg-darker border-gray-700 text-white focus:ring-blue-500 focus:border-blue-500 pr-10" x-bind:type="show ? 'text' : 'password'" name="password" autocomplete="new-password" />
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white">
                                        <i class="ph text-lg" :class="show ? 'ph-eye-slash' : 'ph-eye'"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div x-data="{ show: false }">
                                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" class="text-gray-300" />
                                <div class="relative">
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full bg-darker border-gray-700 text-white focus:ring-blue-500 focus:border-blue-500 pr-10" x-bind:type="show ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password" />
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white">
                                        <i class="ph text-lg" :class="show ? 'ph-eye-slash' : 'ph-eye'"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-800">
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold transition-colors shadow-lg shadow-blue-500/20 flex items-center">
                            <i class="ph ph-floppy-disk mr-2 text-lg"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
