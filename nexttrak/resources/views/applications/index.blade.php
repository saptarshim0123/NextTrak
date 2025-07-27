<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 lg:p-8 space-y-6">
                    @forelse ($applications as $application)
                        {{-- Application Card --}}
                        <div
                            class="bg-white dark:bg-gray-800/50 p-4 sm:p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            {{-- Main Info --}}
                            <div class="flex-grow">
                                <h3 class="text-lg font-bold text-deep-slate dark:text-gray-200">
                                    {{ $application->company_name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $application->job_title }}</p>
                                <div class="mt-3 flex items-center gap-x-4 text-xs">
                                    <span
                                        class="text-gray-500">{{ $application->application_date->format('M d, Y') }}</span>
                                    <span
                                        class="relative inline-flex items-center rounded-full px-2 py-1 font-semibold bg-sage-green/10 text-sage-green">
                                        <svg class="absolute -left-1 top-1/2 -translate-y-1/2 h-1.5 w-1.5" viewBox="0 0 6 6"
                                            aria-hidden="true">
                                            <circle cx="3" cy="3" r="3" fill="currentColor" />
                                        </svg>
                                        {{ $application->status }}
                                    </span>
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