<nav x-data="{ 
        open: false, 
        darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }" 
    x-init="
        console.log('Initial darkMode:', darkMode);
        $watch('darkMode', val => {
            console.log('Dark mode changed to:', val);
            localStorage.setItem('darkMode', val);
            if (val) {
                document.documentElement.classList.add('dark');
                console.log('Added dark class');
            } else {
                document.documentElement.classList.remove('dark');
                console.log('Removed dark class');
            }
        });
        // Initialize on load
        if (darkMode) {
            document.documentElement.classList.add('dark');
            console.log('Initialized with dark mode');
        } else {
            document.documentElement.classList.remove('dark');
            console.log('Initialized with light mode');
        }
    "
    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>
                
                <!-- Page Title (Mobile Only) -->
                <div class="sm:hidden flex items-center ml-4">
                    <h1 class="text-lg font-semibold text-deep-slate dark:text-gray-200">
                        @if(request()->routeIs('dashboard'))
                            {{ __('Dashboard') }}
                        @elseif(request()->routeIs('applications.index'))
                            {{ __('Applications') }}
                        @else
                            {{ __('NextTrak') }}
                        @endif
                    </h1>
                </div>
                
                <!-- Navigation Links (Desktop Only) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="relative group">
                        {{ __('Dashboard') }}
                        <!-- Smooth underline indicator -->
                        <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-sage-green underline-smooth group-hover:w-full"></div>
                    </x-nav-link>
                    <x-nav-link :href="route('applications.index')" :active="request()->routeIs('applications.index')" class="relative group">
                        {{ __('Applications') }}
                        <!-- Smooth underline indicator -->
                        <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-sage-green underline-smooth group-hover:w-full"></div>
                    </x-nav-link>
                </div>
            </div>
            
            <!-- Right side items -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                <!-- Dark/Light Mode Toggler -->
                <button 
                    @click="darkMode = !darkMode; console.log('Toggle clicked, new value:', darkMode)"
                    class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 ease-in-out group focus:outline-none focus:ring-2 focus:ring-sage-green/50"
                    :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <!-- Icon Container -->
                    <div class="relative w-5 h-5">
                        <!-- Sun Icon (Light Mode) -->
                        <svg 
                            x-show="!darkMode"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-75 rotate-45"
                            x-transition:enter-end="opacity-100 scale-100 rotate-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 rotate-0"
                            x-transition:leave-end="opacity-0 scale-75 -rotate-45"
                            class="absolute inset-0 w-5 h-5 text-amber-500 group-hover:text-amber-600 transition-colors duration-200"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                        
                        <!-- Moon Icon (Dark Mode) -->
                        <svg 
                            x-show="darkMode"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-75 -rotate-45"
                            x-transition:enter-end="opacity-100 scale-100 rotate-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 rotate-0"
                            x-transition:leave-end="opacity-0 scale-75 rotate-45"
                            class="absolute inset-0 w-5 h-5 text-indigo-400 group-hover:text-indigo-300 transition-colors duration-200"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </div>
                    
                    <!-- Ripple effect -->
                    <span class="absolute inset-0 rounded-lg bg-gradient-to-r from-amber-400/20 to-indigo-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                </button>
                
                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-deep-slate dark:hover:text-gray-300 hover:bg-sage-green/5 dark:hover:bg-sage-green/10 focus:outline-none transition-all duration-300 ease-out transform hover:scale-105">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Header Actions -->
            <div class="flex items-center sm:hidden space-x-2">
                <!-- Dark/Light Mode Toggler (Mobile) -->
                <button 
                    @click="darkMode = !darkMode"
                    class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 ease-in-out group focus:outline-none focus:ring-2 focus:ring-sage-green/50"
                    :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <!-- Icon Container -->
                    <div class="relative w-5 h-5">
                        <!-- Sun Icon (Light Mode) -->
                        <svg 
                            x-show="!darkMode"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-75 rotate-45"
                            x-transition:enter-end="opacity-100 scale-100 rotate-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 rotate-0"
                            x-transition:leave-end="opacity-0 scale-75 -rotate-45"
                            class="absolute inset-0 w-5 h-5 text-amber-500 group-hover:text-amber-600 transition-colors duration-200"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                        
                        <!-- Moon Icon (Dark Mode) -->
                        <svg 
                            x-show="darkMode"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-75 -rotate-45"
                            x-transition:enter-end="opacity-100 scale-100 rotate-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 rotate-0"
                            x-transition:leave-end="opacity-0 scale-75 rotate-45"
                            class="absolute inset-0 w-5 h-5 text-indigo-400 group-hover:text-indigo-300 transition-colors duration-200"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </div>
                    
                    <!-- Ripple effect -->
                    <span class="absolute inset-0 rounded-lg bg-gradient-to-r from-amber-400/20 to-indigo-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                </button>

                <!-- Profile Menu (Mobile) -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 ease-in-out group focus:outline-none focus:ring-2 focus:ring-sage-green/50">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-deep-slate dark:group-hover:text-gray-100">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>

<!-- Modern Bottom Navigation (Mobile Only) -->
<div class="mobile-bottom-nav sm:hidden">
    <div class="flex items-center justify-around px-2 py-2">
        <!-- Dashboard Tab -->
        <a href="{{ route('dashboard') }}" 
           class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'mobile-nav-active' : 'mobile-nav-inactive' }}"
           aria-label="{{ __('Dashboard') }}">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
            </svg>
            <!-- Active indicator -->
            @if(request()->routeIs('dashboard'))
                <div class="absolute bottom-1 w-1.5 h-1.5 bg-sage-green rounded-full"></div>
            @endif
        </a>
        
        <!-- Applications Tab -->
        <a href="{{ route('applications.index') }}" 
           class="mobile-nav-item {{ request()->routeIs('applications.index') ? 'mobile-nav-active' : 'mobile-nav-inactive' }}"
           aria-label="{{ __('Applications') }}">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <!-- Active indicator -->
            @if(request()->routeIs('applications.index'))
                <div class="absolute bottom-1 w-1.5 h-1.5 bg-sage-green rounded-full"></div>
            @endif
        </a>
    </div>
</div>

<!-- Bottom padding for mobile content to account for bottom navigation -->
<div class="mobile-content-padding"></div>