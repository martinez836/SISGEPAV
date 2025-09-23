import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'farm-green': '#2d5016',
                'farm-light-green': '#4ade80',
                'farm-orange': '#f97316',
                'farm-yellow': '#fbbf24',
                'farm-brown': '#92400e',
                'farm-cream': '#fefce8',
            },
            animation: {
                'float': 'float 3s ease-in-out infinite',
                'fade-in': 'fadeIn 0.8s ease-out',
                'slide-up': 'slideUp 0.6s ease-out',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                fadeIn: {
                    from: { opacity: '0' },
                    to: { opacity: '1' },
                },
                slideUp: {
                    from: { transform: 'translateY(30px)', opacity: '0' },
                    to: { transform: 'translateY(0)', opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};
