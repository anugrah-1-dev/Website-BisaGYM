<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" style="color-scheme: dark;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BisaGym') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <script src="https://unpkg.com/@phosphor-icons/web"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-darker text-gray-200">
        <div class="flex h-screen overflow-hidden">
            
            <!-- Sidebar -->
            <aside class="w-64 bg-dark border-r border-gray-800 hidden md:flex flex-col">
                <div class="h-16 flex items-center px-6 border-b border-gray-800">
                    <img src="{{ asset('asset/logo_gym.jpg') }}" alt="BisaGym Logo" class="h-10 w-10 rounded-full object-cover mr-3 border-2 border-neon">
                    <span class="text-xl font-bold text-white tracking-wider">BISA<span class="text-neon">GYM</span></span>
                </div>

                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="ph ph-squares-four text-xl mr-3"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Management</p>
                    </div>
                    
                    <x-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
                        <i class="ph ph-users text-xl mr-3"></i>
                        {{ __('Members') }}
                    </x-nav-link>

                    <x-nav-link :href="route('cashier.member')" :active="request()->routeIs('cashier.*')">
                        <i class="ph ph-cash-register text-xl mr-3"></i>
                        {{ __('Kasir Member') }}
                    </x-nav-link>

                    <x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')">
                        <i class="ph ph-barcode text-xl mr-3"></i>
                        {{ __('Scan Attendance') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                        <i class="ph ph-storefront text-xl mr-3"></i>
                        {{ __('POS Snack') }}
                    </x-nav-link>

                    @hasanyrole('admin|developer|kasir|penjaga')
                    <x-nav-link :href="route('snacks.index')" :active="request()->routeIs('snacks.*')">
                        <i class="ph ph-package text-xl mr-3"></i>
                        {{ __('Inventaris Snack') }}
                    </x-nav-link>
                    @endhasanyrole

                    @php
                        $isEmployee = \App\Models\Employee::where('user_id', Auth::id())->exists();
                    @endphp
                    
                    @if($isEmployee)
                    <x-nav-link :href="route('employee.my-info')" :active="request()->routeIs('employee.my-info')">
                        <i class="ph ph-briefcase text-xl mr-3"></i>
                        {{ __('Informasi Pekerjaan') }}
                    </x-nav-link>
                    @endif

                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</p>
                    </div>

                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        <i class="ph ph-chart-line-up text-xl mr-3"></i>
                        {{ __('Transactions') }}
                    </x-nav-link>

                    @hasanyrole('admin|developer|kasir|penjaga')
                    <x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                        <i class="ph ph-shopping-cart-open text-xl mr-3"></i>
                        {{ __('Pengeluaran Operasional') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('financial-report.index')" :active="request()->routeIs('financial-report.*')">
                        <i class="ph ph-wallet text-xl mr-3"></i>
                        {{ __('Laporan Keuangan') }}
                    </x-nav-link>
                    @endhasanyrole

                    @role('developer')
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Developer System</p>
                    </div>

                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        <i class="ph ph-users-three text-xl mr-3"></i>
                        {{ __('Manajemen User Login') }}
                    </x-nav-link>

                    <x-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.*')">
                        <i class="ph ph-clock-counter-clockwise text-xl mr-3"></i>
                        {{ __('Riwayat Aktivitas') }}
                    </x-nav-link>
                    @endrole

                    @hasanyrole('admin|developer')
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin Settings</p>
                    </div>

                    <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                        <i class="ph ph-identification-badge text-xl mr-3"></i>
                        {{ __('Manajemen Karyawan') }}
                    </x-nav-link>

                    <x-nav-link :href="route('employee-attendances.index')" :active="request()->routeIs('employee-attendances.*')">
                        <i class="ph ph-calendar-check text-xl mr-3"></i>
                        {{ __('Absensi Karyawan') }}
                    </x-nav-link>

                    <x-nav-link :href="route('gym-packages.index')" :active="request()->routeIs('gym-packages.*')">
                        <i class="ph ph-tags text-xl mr-3"></i>
                        {{ __('Paket & Harga') }}
                    </x-nav-link>
                    @endhasanyrole

                </nav>
            </aside>

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="h-16 bg-dark border-b border-gray-800 flex items-center justify-between px-6">
                    <div>
                        <!-- Mobile menu button can go here -->
                        @isset($header)
                            <h2 class="font-semibold text-xl text-white leading-tight">
                                {{ $header }}
                            </h2>
                        @endisset
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-400">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->roles->first()?->name ?? 'User') }})</span>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-neon transition-colors p-2 rounded-full hover:bg-gray-800">
                                <i class="ph ph-sign-out text-xl"></i>
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-darker p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html>
