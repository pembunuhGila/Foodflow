import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                display: ["Plus Jakarta Sans", ...defaultTheme.fontFamily.sans],
            },

            colors: {
                primary: {
                    DEFAULT: "#2563EB",
                    dark: "#1D4ED8",
                    light: "#60A5FA",
                    subtle: "#EFF6FF",
                },
                secondary: "#0284C7",
                tertiary: "#E0F2FE",
                ink: "#0F172A",
                muted: "#64748B",
                canvas: "#F8FAFC",
                line: "#E2E8F0",

                success: { bg: "#DCFCE7", text: "#15803D" },
                warning: { bg: "#FEF3C7", text: "#B45309" },
                danger: { bg: "#FEE2E2", text: "#B91C1C" },
                info: { bg: "#EFF6FF", text: "#1D4ED8" },

                wash: {
                    from: "#F8FAFC",
                    via: "#FFFFFF",
                    to: "#F1F5F9",
                },
            },

            fontSize: {
                h1: [
                    "32px",
                    {
                        lineHeight: "40px",
                        letterSpacing: "-0.02em",
                        fontWeight: "600",
                    },
                ],
                h2: [
                    "22px",
                    {
                        lineHeight: "28px",
                        letterSpacing: "-0.01em",
                        fontWeight: "600",
                    },
                ],
                h3: ["18px", { lineHeight: "24px", fontWeight: "600" }],
                body: ["14px", { lineHeight: "20px" }],
                small: ["13px", { lineHeight: "18px" }],
                label: [
                    "11px",
                    {
                        lineHeight: "16px",
                        letterSpacing: "0.05em",
                        fontWeight: "700",
                    },
                ],
            },

            borderRadius: {
                card: "16px",
                input: "8px",
                btn: "8px",
            },

            boxShadow: {
                card: "0 1px 3px 0 rgba(15, 23, 42, 0.05), 0 1px 2px -1px rgba(15, 23, 42, 0.03)",
                cardHover: "0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.04)",
                dropdown: "0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.04)",
            },
        },
    },

    plugins: [forms],
};
