import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/css/**/*.css',
        './modules/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            minWidth: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '100': '28rem',
                '200': '56rem',
                '300': '84rem',
                '400': '112rem',
                '1/5': '20%',
                '1/4': '25%',
                '1/3': '33.3333%',
                '1/2': '50%',
                '2/3': '66.6667%',
                '3/4': '75%',
                '4/5': '80%',
                '9/10': '90%'
            },
            minHeight: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '192': '48rem',
                '384': '96rem',
                '1/5': '20vh',
                '1/4': '25vh',
                '1/3': '33.3333vh',
                '1/2': '50vh',
                '2/3': '67.6666vh',
                '3/4': '75vh',
                '4/5': '80vh',
                '9/10': '90vh',
            },
            maxWidth: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '100': '28rem',
                '200': '56rem',
                '300': '84rem',
                '400': '112rem',
                '1/5': '20%',
                '1/4': '25%',
                '1/3': '33.3333%',
                '1/2': '50%',
                '2/3': '66.6667%',
                '3/4': '75%',
                '4/5': '80%',
                '9/10': '90%'
               },
            maxHeight: {
                '1': '.25rem',
                '2': '.5rem',
                '3': '.75rem',
                '4': '1rem',
                '8': '2rem',
                '12': '3rem',
                '14': '3.5rem',
                '16': '4rem',
                '18': '4.5rem',
                '20': '5rem',
                '24': '6rem',
                '36': '9rem',
                '48': '12rem',
                '72': '18rem',
                '96': '24rem',
                '192': '48rem',
                '384': '96rem',
                '1/5': '20vh',
                '1/4': '25vh',
                '1/3': '33.3333vh',
                '1/2': '50vh',
                '2/3': '67.6666vh',
                '3/4': '75vh',
                '4/5': '80vh',
                '9/10': '90vh',
            },
        },
    },
    plugins: [
        forms,
        require("daisyui")
    ],
    daisyui: {
        themes: [
            {
                light: {

                    "primary": "#63f289",

                    "secondary": "#f4be8b",

                    "accent": "#e8af53",

                    "neutral": "#272031",

                    "base-100": "#f3eff6",

                    "info": "#799edc",

                    "success": "#13a094",

                    "warning": "#f7ba64",

                    "error": "#ec796f",
                             },
            },
            {
                'newdark': {
                    "primary": "#f6ff7f",

                    "secondary": "#2dd4bf",

                    "accent": "#07772c",

                    "neutral": "#16151e",

                    "base-100": "#2e3447",

                    "info": "#2b9cde",

                    "success": "#31dd8a",

                    "warning": "#f5c751",

                    "error": "#ea8a7b",
                },
            },
            "cmyk"
        ],
    },
};
