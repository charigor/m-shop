 import defaultTheme from 'tailwindcss/defaultTheme';
 import forms from '@tailwindcss/forms';
// export const darkModeKey = "darkMode";
//
// export const styleKey = "style";
//
// export const containerMaxW = "xl:max-w-6xl xl:mx-auto";
// /** @type {import('tailwindcss').Config} */
// export default {
//     content: [
//         './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
//         './storage/framework/views/*.php',
//         './resources/views/**/*.blade.php',
//         './resources/js/**/*.vue',
//     ],
//
//     theme: {
//         extend: {
//             fontFamily: {
//                 sans: ['Figtree', ...defaultTheme.fontFamily.sans],
//             },
//         },
//     },
//
//     plugins: [forms],
// };
/* eslint-env node */

const plugin = require("tailwindcss/plugin");
const colors = require("tailwindcss/colors")
module.exports = {
    safelist: [
        'bg-amber-700',
        'bg-emerald-100',
        'bg-emerald-200',
        'bg-emerald-300',
        'bg-emerald-400',
        'bg-emerald-500',
        'bg-emerald-600',
        'bg-emerald-700',
        'bg-emerald-800',
        'bg-yellow-100',
        'bg-yellow-200',
        'bg-yellow-300',
        'bg-yellow-400',
        'bg-yellow-500',
        'bg-yellow-600',
        'bg-yellow-700',
        'bg-yellow-800',
        'bg-red-700',
    ],
    content: [
        "./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}",
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/vue-tailwind-datepicker/**/*.js',
    ],
    darkMode: "class", // or 'media' or 'class'
    theme: {
        asideScrollbars: {
            light: "light",
            gray: "gray",
        },
        extend: {
            zIndex: {
                "-1": "-1",
            },
            flexGrow: {
                5: "5",
            },
            maxHeight: {
                "screen-menu": "calc(100vh - 3.5rem)",
                modal: "calc(100vh - 160px)",
            },
            transitionProperty: {
                position: "right, left, top, bottom, margin, padding",
                textColor: "color",
            },
            keyframes: {
                "fade-out": {
                    from: { opacity: 1 },
                    to: { opacity: 0 },
                },
                "fade-in": {
                    from: { opacity: 0 },
                    to: { opacity: 1 },
                },
            },
            animation: {
                "fade-out": "fade-out 250ms ease-in-out",
                "fade-in": "fade-in 250ms ease-in-out",
            },
            colors: {
                "vtd-primary": colors.sky, // Light mode Datepicker color
                "vtd-secondary": colors.gray, // Dark mode Datepicker color

            },
            display: ["group-hover"],
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        plugin(function ({ matchUtilities, theme }) {
            matchUtilities(
                {
                    "aside-scrollbars": (value) => {
                        const track = value === "light" ? "100" : "900";
                        const thumb = value === "light" ? "300" : "600";
                        const color = value === "light" ? "gray" : value;

                        return {
                            scrollbarWidth: "thin",
                            scrollbarColor: `${theme(`colors.${color}.${thumb}`)} ${theme(
                                `colors.${color}.${track}`
                            )}`,
                            "&::-webkit-scrollbar": {
                                width: "8px",
                                height: "8px",
                            },
                            "&::-webkit-scrollbar-track": {
                                backgroundColor: theme(`colors.${color}.${track}`),
                            },
                            "&::-webkit-scrollbar-thumb": {
                                borderRadius: "0.25rem",
                                backgroundColor: theme(`colors.${color}.${thumb}`),
                            },
                        };
                    },
                },
                { values: theme("asideScrollbars") }
            );
        }),
    ],
};
