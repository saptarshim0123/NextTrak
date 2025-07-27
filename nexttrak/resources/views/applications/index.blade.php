<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 lg:p-8 space-y-6">
                    {{-- Header with Add Button --}}
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-deep-slate dark:text-gray-200">Applications</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Track your job applications</p>
                        </div>
                        <a href="{{ route('applications.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-sage-green hover:bg-sage-green/90 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                            Add Application
                        </a>
                    </div>
                    @forelse ($applications as $application)
                        {{-- Application Card --}}
                        <div
                            class="bg-white dark:bg-gray-800/50 p-4 sm:p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            {{-- Company Logo and Info --}}
                            <div class="flex items-center space-x-4 flex-grow">
                                {{-- Company Logo --}}
                                <div class="flex-shrink-0">
                                    @if($application->company && $application->company->logo_url)
                                        <img src="{{ $application->company->logo_url }}" 
                                             alt="{{ $application->company->name }} logo"
                                             class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                                             onerror="this.src='https://via.placeholder.com/48x48/6B7280/FFFFFF?text={{ substr($application->company_name, 0, 1) }}'">
                                    @else
                                        {{-- Custom company or no logo - show placeholder with first letter --}}
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                            <span class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                                {{ substr($application->company_name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Main Info --}}
                                <div class="flex-grow">
                                    <h3 class="text-lg font-bold text-deep-slate dark:text-gray-200">
                                        {{ $application->company_name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $application->job_title }}</p>
                                    <div class="mt-3 flex items-center gap-x-4 text-xs">
                                        <span
                                            class="text-gray-500">{{ $application->application_date->format('M d, Y') }}</span>
                                        <span
                                            class="status-badge border {{ $application->status_color['border'] }}"
                                            style="{{ $application->status_color['bg_style'] }} {{ $application->status_color['text_style'] }}">
                                            <svg class="absolute left-2 top-1/2 -translate-y-1/2 h-1.5 w-1.5" viewBox="0 0 6 6"
                                                aria-hidden="true">
                                                <circle cx="3" cy="3" r="3" fill="currentColor" />
                                            </svg>
                                            {{ $application->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Delete Button --}}
                            <div class="flex-shrink-0 ml-4">
                                <button
                                    class="w-10 h-10 rounded-lg bg-red-500/10 hover:bg-red-500/20 flex items-center justify-center">
                                    <x-heroicon-o-trash class="w-5 h-5 text-red-600" />
                                </button>
                            </div>
                        </div>
                    @empty
                        {{-- Empty State --}}
                        <div class="text-center py-12">
                            <div class="inline-block bg-ivory-sand dark:bg-gray-800 p-6 rounded-full">
                                <x-heroicon-o-inbox-arrow-down class="w-12 h-12 text-sage-green" />
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-deep-slate dark:text-gray-200">No Applications Yet
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">You can start tracking by adding a new
                                application.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>