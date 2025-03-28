const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                dark: {
                    'primary': '#1a1a1a',
                    'secondary': '#2d2d2d',
                    'accent': '#3d3d3d'
                }
            },
            transitionProperty: {
                'colors': 'background-color, border-color, color, fill, stroke',
            }
        },
    },
    plugins: [require('@tailwindcss/forms')],
}; 