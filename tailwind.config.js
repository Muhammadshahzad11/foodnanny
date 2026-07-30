import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            gridTemplateColumns: {
                'auto-fill-170': 'repeat(auto-fill, minmax(170px, 1fr))',
                'auto-fill-140': 'repeat(auto-fill, minmax(140px, 1fr))',
            },
            backgroundImage: {
                stock: "url('/images/default/theme/stock-badge.png')",
                earning: "url('/public/images/default/theme/earning.png')",
                installer: "url('/public/images/default/theme/installer.jpg')"
            },
            backgroundSize: {
                "size-full": "100%"
            },
            fontFamily: {
                "primary": ["var(--primary-font)"],
                "secondary": ["var(--secondary-font)"],
                "awesome": ["'Font Awesome 6 Free'"],
                "admin": ["var(--admin-font)"],
                "icon": ["var(--icon-font)"]
            },
            screens: {
                'mobile': {'min': '0px', 'max': '639px'},
                'tablet': {'min': '640px', 'max': '766px'},
                'laptop': {'min': '767px', 'max': '1023px'},
                'desktop': {'min': '1024px', 'max': '1279px'},
                'monitor': {'min': '1280px', 'max': '1535px'},

                'max-sm': {'min': '0px', 'max': '639px'},
                'max-md': {'min': '0px', 'max': '766px'},
                'max-lg': {'min': '0px', 'max': '1023px'},
                'max-xl': {'min': '0px', 'max': '1279px'},

                'xh': {'min': '0px', 'max': '766px'},
                'xst': {'min': '0px', 'max': '640px'},
                'xsd': {'min': '0px', 'max': '450px'},
            },
            colors: {
                success: "#1AB759",
                danger: "#E93C3C",
                focus: "#006CC0",
                mate: "#F7F7FC",
                placeholder: "rgb(var(--placeholder) / <alpha-value>)",
                primary: "rgb(var(--primary) / <alpha-value>)",
                secondary: "rgb(var(--secondary) / <alpha-value>)",
                paragraph: "rgba(var(--paragraph) / <alpha-value>)",
                heading: "rgb(var(--heading) / <alpha-value>)",
                text: "rgb(var(--paragraph) / <alpha-value>)",
                'border-light': "var(--border-light)",
                'border-deep': "var(--border-deep)",
                admin: {
                    red: "#FB4E4E",
                    sky: "#007FE3",
                    pink: "#FF4773",
                    blue: "#426EFF",
                    green: "#2AC769",
                    orange: "#FF8C39",
                    yellow: "#F6A609",
                    indigo: "#8262FE",
                    purple: "#A953FF",
                }
            },
            minWidth: {
                '3xl': '48rem',
            },
            lineHeight: {
                '11': "2.75rem",
                '12': "3rem",
            },
            zIndex: {
                "60": "60",
                "70": "70",
                "80": "80",
            },
            dropShadow: {
                foodtype: "1px 4px 3px rgba(0, 0, 0, 0.25)",
            },
            boxShadow: {
                "xs": '0px 6px 32px rgba(0, 0, 0, 0.04)',
                "xst": "0px 4px 40px rgba(23, 31, 70, 0.16)",
                "card": "0px 0px 10px rgba(0, 0, 0, 0.04)",
                "hover": "0px 8px 40px rgba(23, 31, 70, 0.08)",
                "filter": "0px 8px 16px rgba(23, 31, 70, 0.08)",
                "paper": "0px 15px 40px rgba(73, 72, 72, 0.1)",
                "b-paper": "0px 15px 40px rgba(73, 72, 72, 0.1)",
                "t-paper": "0px -15px 40px rgba(73, 72, 72, 0.1)",
                "indicate": "0px 2px 6px rgb(var(--primary) / 0.46)",
                "avatar": "0px 6px 10px 0px rgba(255, 0, 107, 0.15)",
                "badge": "0px 4px 16px rgba(126, 133, 142, 0.16)",
                "sidebar": "15px 0px 25px 0px rgba(0, 0, 0, 0.05)",
                "sidebar-right": "15px 0px 25px 0px rgb(0 0 0 / 12%)",
                "db-sidebar-left": "-15px 0px 20px 0px rgba(0, 0, 0, 0.04)",
                "db-sidebar-right": "15px 0px 20px 0px rgba(0, 0, 0, 0.04)",
                "pink": "0px 6px 15px rgba(253, 0, 99, 0.25)",
                "orange": "0px 6px 15px rgb(var(--primary) / 0.25)",
                "purple": "0px 6px 15px rgba(147, 83, 222, 0.25)",
                "blue": "0px 6px 15px rgba(0, 114, 244, 0.25)",
                "sidebar-left": "-15px 0px 25px 0px rgb(0 0 0 / 12%)",
                "checkRound": "0 2px 4px 0 rgb(105 108 255 / 40%)",
                "cookies": "0px 15px 40px 0px rgba(73, 72, 72, 0.16)",
                "action": "0px 6px 10px rgb(var(--primary) / 0.24)",
                "db-card": "0 2px 6px 0 rgb(67 89 113 / 12%)",
                "widget": "0px 4px 32px rgba(0, 0, 0, 0.06)",
                "cart": "0px 6px 10px rgb(var(--primary) / 0.34)",
                "btn-primary": "0px 8px 15px rgb(var(--primary) / 0.18)",
                "btn-secondary": "0px 4px 8px rgba(0, 0, 0, 0.04), 0px 0px 2px rgba(0, 0, 0, 0.06), 0px 0px 1px rgba(0, 0, 0, 0.04)"
            },
        },
    },
    plugins: [],
};
