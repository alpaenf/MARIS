import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // MARIS Design System — teal/green Material tokens
                primary:            '#005c55',
                'primary-container':'#0f766e',
                'on-primary':       '#ffffff',
                'on-primary-container': '#a3faef',
                'inverse-primary':  '#80d5cb',
                'primary-fixed':    '#9cf2e8',
                'primary-fixed-dim':'#80d5cb',
                'on-primary-fixed': '#00201d',
                'on-primary-fixed-variant': '#00504a',

                secondary:          '#006b5f',
                'secondary-container': '#6df5e1',
                'on-secondary':     '#ffffff',
                'on-secondary-container': '#006f64',
                'secondary-fixed':  '#71f8e4',
                'secondary-fixed-dim': '#4fdbc8',
                'on-secondary-fixed': '#00201c',
                'on-secondary-fixed-variant': '#005048',

                tertiary:           '#005e27',
                'tertiary-container': '#007a35',
                'on-tertiary':      '#ffffff',
                'on-tertiary-container': '#a1ffaf',
                'tertiary-fixed':   '#6bff8f',
                'tertiary-fixed-dim': '#4ae176',
                'on-tertiary-fixed': '#002109',
                'on-tertiary-fixed-variant': '#005321',

                background:         '#f7f9fb',
                'on-background':    '#191c1e',

                surface:            '#f7f9fb',
                'surface-bright':   '#f7f9fb',
                'surface-dim':      '#d8dadc',
                'surface-variant':  '#e0e3e5',
                'surface-container-lowest': '#ffffff',
                'surface-container-low':    '#f2f4f6',
                'surface-container':        '#eceef0',
                'surface-container-high':   '#e6e8ea',
                'surface-container-highest':'#e0e3e5',
                'on-surface':       '#191c1e',
                'on-surface-variant': '#3e4947',
                'inverse-surface':  '#2d3133',
                'inverse-on-surface': '#eff1f3',

                outline:            '#6e7977',
                'outline-variant':  '#bdc9c6',

                error:              '#ba1a1a',
                'error-container':  '#ffdad6',
                'on-error':         '#ffffff',
                'on-error-container': '#93000a',

                'surface-tint':     '#006a63',
            },
            borderRadius: {
                DEFAULT: '0.25rem',
                lg:      '0.5rem',
                xl:      '0.75rem',
                '2xl':   '1rem',
                full:    '9999px',
            },
        },
    },

    plugins: [forms],
};
