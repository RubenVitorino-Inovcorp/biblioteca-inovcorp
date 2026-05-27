import defaultTheme from 'tailwindcss/defaultTheme';
import typography from '@tailwindcss/typography';
import daisyui from "daisyui";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './app/Enums/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Certifica-te de incluir o <link> da Manrope no teu app.blade.php
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [typography, daisyui]
};
