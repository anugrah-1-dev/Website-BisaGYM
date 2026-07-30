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
        <div class="flex h-screen overflow-hidden" x-data="{ mobileMenuOpen: false }">
            
            <!-- Mobile Off-Canvas Drawer Navigation -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 md:hidden flex"
                 style="display: none;">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/75 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>

                <!-- Drawer Content -->
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-dark border-r border-gray-800 z-10 shadow-2xl">
                    <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800">
                        <div class="flex items-center">
                            <img src="{{ asset('asset/logo_gym.jpg') }}" alt="BisaGym Logo" class="h-10 w-10 rounded-full object-cover mr-3 border-2 border-neon">
                            <span class="text-xl font-bold text-white tracking-wider">BISA<span class="text-neon">GYM</span></span>
                        </div>
                        <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-white p-1 rounded-lg">
                            <i class="ph ph-x text-2xl"></i>
                        </button>
                    </div>

                    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                        @include('layouts.sidebar-links')
                    </nav>
                </div>
            </div>

            <!-- Desktop Sidebar -->
            <aside class="w-64 bg-dark border-r border-gray-800 hidden md:flex flex-col">
                <div class="h-16 flex items-center px-6 border-b border-gray-800">
                    <img src="{{ asset('asset/logo_gym.jpg') }}" alt="BisaGym Logo" class="h-10 w-10 rounded-full object-cover mr-3 border-2 border-neon">
                    <span class="text-xl font-bold text-white tracking-wider">BISA<span class="text-neon">GYM</span></span>
                </div>

                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    @include('layouts.sidebar-links')
                </nav>
            </aside>

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="h-16 bg-dark border-b border-gray-800 flex items-center justify-between px-4 md:px-6">
                    <div class="flex items-center min-w-0">
                        <!-- Mobile menu button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden text-gray-300 hover:text-neon p-1.5 mr-3 rounded-lg hover:bg-gray-800 transition-colors focus:outline-none flex-shrink-0">
                            <i class="ph ph-list text-2xl"></i>
                        </button>
                        
                        @isset($header)
                            <h2 class="font-semibold text-lg md:text-xl text-white leading-tight truncate">
                                {{ $header }}
                            </h2>
                        @endisset
                    </div>
                    
                    <div class="flex items-center space-x-2 md:space-x-4 flex-shrink-0">
                        <span class="text-xs md:text-sm text-gray-400 max-w-[130px] sm:max-w-none truncate">{{ Auth::user()->name }} <span class="hidden sm:inline">({{ ucfirst(Auth::user()->roles->first()?->name ?? 'User') }})</span></span>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Logout" class="text-gray-400 hover:text-neon transition-colors p-2 rounded-full hover:bg-gray-800">
                                <i class="ph ph-sign-out text-xl"></i>
                            </button>
                        </form>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-darker p-4 md:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html>
