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
                    DEFAULT: "#7C5CE0",
                    dark: "#6B46C1",
                },
                secondary: "#4C2FB5",
                tertiary: "#F5D0FE",
                ink: "#1E1B4B",
                muted: "#6B7280",
                canvas: "#F7F5FD",
                line: "#ECE8F7",

                success: { bg: "#DCFCE7", text: "#15803D" },
                warning: { bg: "#FEF3C7", text: "#B45309" },
                danger: { bg: "#FEE2E2", text: "#B91C1C" },
                info: { bg: "#EDE9FE", text: "#6D28D9" },
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
                    "12px",
                    {
                        lineHeight: "16px",
                        letterSpacing: "0.06em",
                        fontWeight: "700",
                    },
                ],
            },

            borderRadius: {
                card: "20px",
                input: "12px",
            },

            boxShadow: {
                card: "0px 8px 24px -4px rgba(124, 92, 224, 0.06), 0px 2px 6px -1px rgba(30, 27, 75, 0.03)",
            },
        },
    },

    plugins: [forms],
};
