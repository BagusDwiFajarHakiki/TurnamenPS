<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Infinity Boxzone' }}</title>
    <link rel="icon" type="image/png" href="/images/logo.png">
    
    <!-- Theme Initializer Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'light') {
                document.documentElement.classList.add('light-theme');
            }
        })();
    </script>
    
    <!-- Meta Description for SEO -->
    <meta name="description" content="Infinity Boxzone - Turnamen PlayStation 3 & 4 eFootball. Ikuti turnamen, pantau klasemen langsung, bagan pertandingan, dan dapatkan notifikasi antrean realtime.">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @livewireStyles
</head>
<body style="display: flex; flex-direction: column; height: 100vh; overflow: hidden; margin: 0;">
    <!-- Use a simple script to handle transparent nav on home page -->
    <nav class="{{ request()->is('/') ? 'nav-at-top' : '' }}" id="mainNav" data-is-home="{{ request()->is('/') ? 'true' : 'false' }}" style="flex-shrink: 0; max-width: 1200px; margin: 0 auto; width: 100%;">
        <a href="/" wire:navigate class="nav-brand" style="text-transform: none; letter-spacing: 0; gap: 0.5rem;"><img src="/images/logo.png" alt="Infinity Boxzone" style="height: 32px; width: auto;"> <span>INFINITY BOXZONE</span></a>
        <div class="nav-links">
            @auth('player')
                <a href="{{ route('player.dashboard') }}" wire:navigate class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">{{ __('Dasbor') }}</a>
            @else
                <a href="{{ route('player.login') }}" wire:navigate class="nav-link {{ request()->routeIs('player.login') ? 'active' : '' }}">{{ __('Login') }}</a>
            @endauth

            <!-- Language Switcher -->
            <div style="display: flex; align-items: center; gap: 0.25rem; border-left: 1px solid var(--border-color); padding-left: 1rem; margin-left: 0.5rem;">
                <a href="/set-locale/id" class="nav-link" style="font-size: 0.85rem; {{ app()->getLocale() == 'id' ? 'color: var(--primary); font-weight: bold;' : '' }}">ID</a>
                <span style="color: var(--text-muted)">|</span>
                <a href="/set-locale/en" class="nav-link" style="font-size: 0.85rem; {{ app()->getLocale() == 'en' ? 'color: var(--primary); font-weight: bold;' : '' }}">EN</a>
                
                <!-- Theme Toggle Button -->
                <button id="themeToggleBtn" onclick="toggleTheme()" style="background: none; border: none; cursor: pointer; padding: 0 0.5rem; display: flex; align-items: center; color: var(--text-main); font-size: 1.1rem; margin-left: 0.75rem; transition: color 0.2s;" title="Toggle Light/Dark Mode">
                    <span id="themeToggleIcon">☀️</span>
                </button>
            </div>
        </div>
    </nav>

    <div id="scrollContainer" style="flex-grow: 1; overflow-y: auto; overflow-x: hidden;">
        <main>
            {{ $slot }}
        </main>

        <footer style="text-align: center; padding: 2rem; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.9rem; margin-top: 4rem;">
            &copy; {{ date('Y') }} Infinity Boxzone. All Rights Reserved.
        </footer>
    </div>

    @livewireScripts

    <!-- Theme Script -->
    <script>
        function toggleTheme() {
            const isLight = document.documentElement.classList.toggle('light-theme');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            updateThemeIcon(isLight ? 'light' : 'dark');
        }

        function updateThemeIcon(theme) {
            const iconSpan = document.getElementById('themeToggleIcon');
            if (iconSpan) {
                iconSpan.textContent = theme === 'light' ? '🌙' : '☀️';
            }
        }

        function initTheme() {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            updateThemeIcon(currentTheme);
            if (currentTheme === 'light') {
                document.documentElement.classList.add('light-theme');
            } else {
                document.documentElement.classList.remove('light-theme');
            }
        }

        function togglePaymentDrawer(open) {
            const drawer = document.getElementById('paymentDrawer');
            const backdrop = document.getElementById('paymentDrawerBackdrop');
            if (drawer && backdrop) {
                if (open) {
                    drawer.classList.add('active');
                    backdrop.classList.add('active');
                    document.body.style.overflow = 'hidden';
                } else {
                    drawer.classList.remove('active');
                    backdrop.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        }

        function handleScroll() {
            const nav = document.getElementById('mainNav');
            const scrollContainer = document.getElementById('scrollContainer');
            if (nav && nav.dataset.isHome === 'true' && scrollContainer) {
                if (scrollContainer.scrollTop > 50) {
                    nav.classList.remove('nav-at-top');
                } else {
                    nav.classList.add('nav-at-top');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initTheme();
            handleScroll();
            const scrollContainer = document.getElementById('scrollContainer');
            if (scrollContainer) {
                scrollContainer.addEventListener('scroll', handleScroll);
            }
        });
        
        document.addEventListener('livewire:navigated', () => {
            initTheme();
            const nav = document.getElementById('mainNav');
            if (nav) {
                const isHome = window.location.pathname === '/';
                nav.dataset.isHome = isHome ? 'true' : 'false';
            }
            handleScroll();
        });
    </script>
</body>
</html>
