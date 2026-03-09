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
                // Background colors
                'bg-base': 'var(--color-bg-base)',
                'bg-card': 'var(--color-bg-card)',
                'bg-card-inner': 'var(--color-bg-card-inner)',
                'bg-hover': 'var(--color-bg-hover)',
                
                // Border colors
                'border-primary': 'var(--color-border)',
                'border-card': 'var(--color-border-card)',
                
                // Text colors
                'text-primary': 'var(--color-text-primary)',
                'text-secondary': 'var(--color-text-secondary)',
                'text-muted': 'var(--color-text-muted)',
                
                // Brand colors
                'brand': 'var(--color-brand)',
                'brand-light': 'var(--color-brand-light)',
                
                // Progress colors
                'progress-track': 'var(--color-progress-track)',
            }
        },
    },

    plugins: [forms],
};
