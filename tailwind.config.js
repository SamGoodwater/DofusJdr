import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],
    plugins: [require("@tailwindcss/typography"), require("daisyui")],

    // daisyUI config (optional - here are the default values)
    daisyui: {
        themes: [
            {
                CustomDarkTheme: {
                    primary: "#60a5fa",
                    secondary: "#e2e8f0",
                    accent: "#38bdf8",
                    neutral: "#1e293b",
                    "base-100": "#0f172a",
                    "base-200": "#1e293b",
                    "base-300": "#334155",
                    info: "#38bdf8",
                    success: "#2dd4bf",
                    warning: "#facc15",
                    error: "#f87171",
                },
                CustomLightTheme: {
                    primary: "#2563eb",
                    secondary: "#475569",
                    accent: "#0ea5e9",
                    neutral: "#e2e8f0",
                    "base-100": "#f1f5f9",
                    "base-300": "#e2e8f0",
                    "base-200": "#cbd5e1",
                    info: "#06b6d4",
                    success: "#14b8a6",
                    warning: "#eab308",
                    error: "#f97316",
                },
            },
            "dark",
            "light",
        ],
        darkTheme: "CustomDarkTheme", // name of one of the included themes for dark mode
        base: true, // applies background color and foreground color for root element by default
        styled: true, // include daisyUI colors and design decisions for all components
        utils: true, // adds responsive and modifier utility classes
        prefix: "", // prefix for daisyUI classnames (components, modifiers and responsive class names. Not colors)
        logs: true, // Shows info about daisyUI version and used config in the console when building your CSS
        themeRoot: ":root", // The element that receives theme color CSS variables
    },

    theme: {
        extend: {
            fontFamily: {
                body: [
                    "Lato",
                    "ui-sans-serif",
                    "system-ui",
                    "-apple-system",
                    "system-ui",
                    "Segoe UI",
                    "Roboto",
                    "Helvetica Neue",
                    "Arial",
                    "Noto Sans",
                    "sans-serif",
                    "Apple Color Emoji",
                    "Segoe UI Emoji",
                    "Segoe UI Symbol",
                    "Noto Color Emoji",
                ],
                sans: [
                    "Lato",
                    "ui-sans-serif",
                    "system-ui",
                    "-apple-system",
                    "system-ui",
                    "Segoe UI",
                    "Roboto",
                    "Helvetica Neue",
                    "Arial",
                    "Noto Sans",
                    "sans-serif",
                    "Apple Color Emoji",
                    "Segoe UI Emoji",
                    "Segoe UI Symbol",
                    "Noto Color Emoji",
                ],
            },
        },
    },
};
