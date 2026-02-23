tailwind.config = {
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "sans-serif"],
            },
            colors: {
                primary: "#dc2626",
                secondary: "#1e293b",
            },
            animation: {
                "fade-in-up": "fadeInUp 0.6s ease-out",
                "slide-in-left": "slideInLeft 0.6s ease-out",
                "slide-in-right": "slideInRight 0.6s ease-out",
                "scale-up": "scaleUp 0.3s ease-out",
                float: "float 3s ease-in-out infinite",
                "scroll-right": "scrollRight 40s linear infinite",
                "scroll-left": "scrollLeft 40s linear infinite",
                pulse: "pulse 2s infinite",
                spin: "spin 1s linear infinite",
            },
            keyframes: {
                fadeInUp: {
                    "0%": {
                        opacity: "0",
                        transform: "translateY(20px)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateY(0)",
                    },
                },
                slideInLeft: {
                    "0%": {
                        opacity: "0",
                        transform: "translateX(-20px)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateX(0)",
                    },
                },
                slideInRight: {
                    "0%": {
                        opacity: "0",
                        transform: "translateX(20px)",
                    },
                    "100%": {
                        opacity: "1",
                        transform: "translateX(0)",
                    },
                },
                scaleUp: {
                    "0%": {
                        transform: "scale(1)",
                    },
                    "100%": {
                        transform: "scale(1.05)",
                    },
                },
                float: {
                    "0%, 100%": {
                        transform: "translateY(0)",
                    },
                    "50%": {
                        transform: "translateY(-10px)",
                    },
                },
                scrollRight: {
                    "0%": {
                        transform: "translateX(0)",
                    },
                    "100%": {
                        transform: "translateX(-50%)",
                    },
                },
                scrollLeft: {
                    "0%": {
                        transform: "translateX(-50%)",
                    },
                    "100%": {
                        transform: "translateX(0)",
                    },
                },
                pulse: {
                    "0%, 100%": {
                        transform: "scale(1)",
                    },
                    "50%": {
                        transform: "scale(1.1)",
                    },
                },
                spin: {
                    to: {
                        transform: "rotate(360deg)",
                    },
                },
            },
            screens: {
                xs: "375px",
                sm: "640px",
                md: "768px",
                lg: "1024px",
                xl: "1280px",
                "2xl": "1536px",
                "3xl": "1920px",
            },
        },
    },
};
