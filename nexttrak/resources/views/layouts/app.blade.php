<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
            
            <!-- Back to Top Button -->
            <div 
                x-data="{ showBackToTop: false }"
                x-init="
                    window.addEventListener('scroll', () => {
                        showBackToTop = window.scrollY > 300;
                    });
                "
                x-show="showBackToTop"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90"
                class="fixed bottom-6 right-6 z-40"
            >
                <button 
                    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                    class="w-12 h-12 bg-sage-green hover:bg-sage-green/90 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 ease-out transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                    aria-label="Back to top"
                >
                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Floating Quick Add Button (Applications Page Only) -->
            @if(request()->routeIs('applications.index'))
            <div class="fixed bottom-6 left-6 z-40 sm:hidden">
                <a 
                    href="{{ route('applications.create') }}"
                    class="w-14 h-14 bg-sage-green hover:bg-sage-green/90 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 ease-out transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:ring-offset-2 dark:focus:ring-offset-gray-900 flex items-center justify-center"
                    aria-label="Add new application"
                >
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>
            </div>
            @endif
        </div>
    </body>
</html>
