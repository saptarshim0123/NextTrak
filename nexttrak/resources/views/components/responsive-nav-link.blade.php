@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-sage-green dark:border-sage-green text-start text-base font-medium text-deep-slate dark:text-sage-green bg-sage-green/5 dark:bg-sage-green/10 focus:outline-none focus:text-deep-slate dark:focus:text-sage-green focus:bg-sage-green/10 dark:focus:bg-sage-green/20 focus:border-sage-green/80 dark:focus:border-sage-green/80 nav-link-smooth nav-hover-smooth hover:scale-[1.02] hover:translate-x-[2px]'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-deep-slate dark:hover:text-gray-200 hover:bg-sage-green/5 dark:hover:bg-sage-green/10 hover:border-sage-green/60 dark:hover:border-sage-green/60 focus:outline-none focus:text-deep-slate dark:focus:text-gray-200 focus:bg-sage-green/5 dark:focus:bg-sage-green/10 focus:border-sage-green/60 dark:focus:border-sage-green/60 nav-link-smooth nav-hover-smooth hover:scale-[1.02] hover:translate-x-[2px]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
