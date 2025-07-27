import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'ivory-sand': '#F9F6EE', // Background
                'sage-green': '#8A9A5B', // Primary Accent
                'deep-slate': '#2F4F4F', // Text
                // Custom status colors - using standard Tailwind colors that match your specifications
                'status-accepted-bg': '#D1FAE5',
                'status-accepted-light': '#065F46',
                'status-accepted-dark-bg': '#064E3B',
                'status-accepted-dark-text': '#10B981',
                
                'status-interviewing-bg': '#FEF9C3',
                'status-interviewing-light': '#9A3412',
                'status-interviewing-dark-bg': '#78350F',
                'status-interviewing-dark-text': '#F59E0B',
                
                'status-rejected-bg': '#FEE2E2',
                'status-rejected-light': '#991B1B',
                'status-rejected-dark-bg': '#7F1D1D',
                'status-rejected-dark-text': '#EF4444',
                
                'status-applied-bg': '#F5F5F4',
                'status-applied-light': '#3F3F46',
                'status-applied-dark-bg': '#3F3F46',
                'status-applied-dark-text': '#D4D4D8',
            }
        },
    },

    plugins: [forms],
};
