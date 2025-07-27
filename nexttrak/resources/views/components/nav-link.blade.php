@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-sage-green dark:border-sage-green text-sm font-medium leading-5 text-deep-slate dark:text-gray-100 focus:outline-none focus:border-sage-green/80 nav-link-smooth nav-hover-smooth hover:scale-105 hover:translate-y-[-1px]'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-deep-slate dark:hover:text-gray-300 hover:border-sage-green/60 dark:hover:border-sage-green/60 focus:outline-none focus:text-deep-slate dark:focus:text-gray-300 focus:border-sage-green/60 dark:focus:border-sage-green/60 nav-link-smooth nav-hover-smooth hover:scale-105 hover:translate-y-[-1px]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
