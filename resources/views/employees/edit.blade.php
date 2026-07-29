<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('employees.index') }}" class="text-gray-400 hover:text-neon transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <span>{{ __('Edit Data Karyawan') }} - {{ $employee->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('employees.update', $employee->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-card rounded-xl border border-gray-800 p-6 shadow-lg">
                <h3 class="text-white font-medium mb-4 border-b border-gray-800 pb-2 flex items-center gap-2">
                    <i class="ph ph-user text-neon"></i> Edit Profil Karyawan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">No. WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Posisi / Jabatan</label>
                        <input type="text" name="position" value="{{ old('position', $employee->position) }}" required
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Gaji Pokok (Rp)</label>
                        <input type="number" name="base_salary" value="{{ old('base_salary', intval($employee->base_salary)) }}" required min="0" step="1000"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tgl Masuk</label>
                        <input type="date" name="join_date" value="{{ old('join_date', $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '') }}"
                            class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Status Karyawan</label>
                        <select name="status" class="w-full border-gray-700 rounded-lg bg-dark text-white focus:ring-neon focus:border-neon text-sm">
                            <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Nonaktif / Resign</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Jika dinonaktifkan, akun login (jika ada) juga akan otomatis dinonaktifkan.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors text-sm font-medium">Batal</a>
                <button type="submit" class="px-6 py-2 bg-neon hover:bg-[#c4e600] text-darker rounded-lg font-bold transition-colors text-sm shadow-lg shadow-neon/20 flex items-center">
                    <i class="ph ph-floppy-disk mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
