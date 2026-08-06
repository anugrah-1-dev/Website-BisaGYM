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

                    @hasanyrole('admin|developer')
                    <!-- Developer Only Geofencing Settings -->
                    <div class="pt-4 mt-4 border-t border-purple-900/50 bg-purple-950/20 p-4 rounded-lg border">
                        <input type="hidden" name="is_location_restricted_present" value="1">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h4 class="text-purple-300 font-semibold text-sm flex items-center">
                                    <i class="ph ph-map-pin-line text-lg mr-2"></i> Pembatasan Lokasi GPS (Geofencing Opsional Admin)
                                </h4>
                                <p class="text-xs text-gray-400">Diatur oleh Developer System. Jika diaktifkan, akun Admin wajib login di lokasi gym.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_location_restricted" value="1" 
                                    {{ old('is_location_restricted', $user->is_location_restricted) ? 'checked' : '' }} 
                                    class="sr-only peer" id="toggle-geo">
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>

                        <div id="geo-details" class="space-y-4 mt-3 pt-3 border-t border-purple-900/40 {{ old('is_location_restricted', $user->is_location_restricted) ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="allowed_latitude" :value="__('Latitude Lokasi Target')" class="text-gray-300 text-xs" />
                                    <x-text-input id="allowed_latitude" name="allowed_latitude" type="text" 
                                        :value="old('allowed_latitude', $user->allowed_latitude ?? '-7.33405')" 
                                        placeholder="-7.33405" 
                                        class="block mt-1 w-full bg-darker border-gray-700 text-white text-xs" />
                                </div>
                                <div>
                                    <x-input-label for="allowed_longitude" :value="__('Longitude Lokasi Target')" class="text-gray-300 text-xs" />
                                    <x-text-input id="allowed_longitude" name="allowed_longitude" type="text" 
                                        :value="old('allowed_longitude', $user->allowed_longitude ?? '112.78255')" 
                                        placeholder="112.78255" 
                                        class="block mt-1 w-full bg-darker border-gray-700 text-white text-xs" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="allowed_radius_meters" :value="__('Radius Maksimal (Meter)')" class="text-gray-300 text-xs" />
                                <x-text-input id="allowed_radius_meters" name="allowed_radius_meters" type="number" 
                                    :value="old('allowed_radius_meters', $user->allowed_radius_meters ?? 500)" 
                                    placeholder="500" 
                                    class="block mt-1 w-full bg-darker border-gray-700 text-white text-xs" />
                                <p class="text-[11px] text-purple-400 mt-1">Default: 500 meter dari BisaGym Surabaya (Jl. Dr. Ir. H. Soekarno No.678).</p>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const toggle = document.getElementById('toggle-geo');
                            const details = document.getElementById('geo-details');
                            if (toggle && details) {
                                toggle.addEventListener('change', function() {
                                    if (this.checked) {
                                        details.classList.remove('hidden');
                                    } else {
                                        details.classList.add('hidden');
                                    }
                                });
                            }
                        });
                    </script>
                    @endhasanyrole

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
