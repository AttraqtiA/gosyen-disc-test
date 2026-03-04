<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tes Kepribadian')</title>

    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const shouldDark = saved === 'dark';
            document.documentElement.classList.toggle('dark', shouldDark);
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        brand: {
                            50: '#eef6ff',
                            100: '#d9ebff',
                            200: '#badbff',
                            300: '#8fc4ff',
                            400: '#5ca6ff',
                            500: '#2f88ff',
                            600: '#1d6fe7',
                            700: '#1a5abc',
                            800: '#1c4e9a',
                            900: '#1d447d'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .dark .bg-white { background-color: rgb(15 23 42) !important; }
        .dark .bg-slate-50 { background-color: rgb(2 6 23) !important; }
        .dark .bg-slate-100 { background-color: rgb(15 23 42) !important; }
        .dark .text-slate-900 { color: rgb(241 245 249) !important; }
        .dark .text-slate-800 { color: rgb(226 232 240) !important; }
        .dark .text-slate-700 { color: rgb(203 213 225) !important; }
        .dark .text-slate-600 { color: rgb(148 163 184) !important; }
        .dark .text-slate-500 { color: rgb(148 163 184) !important; }
        .dark .border-slate-100,
        .dark .border-slate-200,
        .dark .border-slate-300 { border-color: rgb(51 65 85) !important; }
        .dark input,
        .dark select,
        .dark textarea {
            background-color: rgb(2 6 23) !important;
            color: rgb(226 232 240) !important;
            border-color: rgb(51 65 85) !important;
        }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 font-sans transition-colors duration-200 dark:bg-slate-900 dark:text-slate-100">
    <div class="min-h-screen bg-gradient-to-b from-slate-100 via-slate-50 to-slate-100 transition-colors duration-200 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800">
        @yield('content')
    </div>

    <script>
        (function () {
            const buttons = document.querySelectorAll('[data-theme-toggle]');
            const darkLabel = 'Aktifkan dark mode';
            const lightLabel = 'Aktifkan light mode';

            function isDark() {
                return document.documentElement.classList.contains('dark');
            }

            function render() {
                const dark = isDark();
                buttons.forEach((button) => {
                    const moon = button.querySelector('[data-theme-icon="moon"]');
                    const sun = button.querySelector('[data-theme-icon="sun"]');
                    if (moon) moon.classList.toggle('hidden', dark);
                    if (sun) sun.classList.toggle('hidden', !dark);
                    button.setAttribute('aria-label', dark ? lightLabel : darkLabel);
                    button.setAttribute('title', dark ? lightLabel : darkLabel);
                });
            }

            buttons.forEach((button) => {
                button.addEventListener('click', function () {
                    const nextDark = !isDark();
                    document.documentElement.classList.toggle('dark', nextDark);
                    localStorage.setItem('theme', nextDark ? 'dark' : 'light');
                    render();
                });
            });

            render();
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    @yield('scripts')
</body>
</html>
