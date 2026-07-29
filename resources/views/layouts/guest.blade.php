<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BisaGym') }} - Login</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <script src="https://unpkg.com/@phosphor-icons/web"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-200 antialiased bg-darker">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            
            <!-- Decorative Background Elements -->
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-neon/10 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-neon/10 rounded-full blur-[100px] pointer-events-none"></div>

            <!-- Logo -->
            <div class="z-10 mb-8 flex flex-col items-center">
                <a href="/" class="flex flex-col items-center group">
                    <div class="p-1 rounded-full bg-dark border-2 border-neon shadow-[0_0_20px_rgba(224,255,0,0.3)] transition-transform group-hover:scale-105 duration-300">
                        <img src="{{ asset('asset/logo_gym.jpg') }}" alt="BisaGym Logo" class="h-20 w-20 rounded-full object-cover">
                    </div>
                    <span class="mt-4 text-3xl font-bold text-white tracking-wider">BISA<span class="text-neon">GYM</span></span>
                    <span class="text-gray-400 text-sm tracking-widest uppercase mt-1">Management System</span>
                </a>
            </div>

            <!-- Content Card -->
            <div class="z-10 w-full sm:max-w-md px-8 py-10 bg-card/80 backdrop-blur-xl border border-gray-800 shadow-2xl sm:rounded-2xl">
                {{ $slot }}
            </div>
            
            <div class="z-10 mt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} BisaGym. All rights reserved.
            </div>
        </div>
    </body>
</html>
