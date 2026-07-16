<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Infinity Boxzone'); ?></title>
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
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>?v=<?php echo e(filemtime(public_path('css/app.css'))); ?>">
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body style="display: flex; flex-direction: column; height: 100vh; overflow: hidden; margin: 0;">
    <!-- Use a simple script to handle transparent nav on home page -->
    <nav id="mainNav" style="flex-shrink: 0; max-width: 1200px; margin: 0 auto; width: 100%; padding-left: clamp(0.75rem, 3vw, 1.5rem); padding-right: clamp(0.75rem, 3vw, 1.5rem); padding-top: 1rem; padding-bottom: 1rem;">
        <a href="/" wire:navigate class="nav-brand" style="text-transform: none; letter-spacing: 0; gap: 0.5rem;"><img src="/images/logo.png" alt="Infinity Boxzone" style="height: 32px; width: auto;"> <span>INFINITY BOXZONE</span></a>

        <div x-data="{ open: false }" style="position: relative;">
            <button @click="open = !open" style="width: 34px; height: 34px; border-radius: 50%; background: var(--bg-surface); border: 1px solid var(--border-color); color: var(--primary); font-weight: 800; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: border-color 0.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
                <?php echo e(strtoupper(substr(auth('player')->user()->name ?? 'P', 0, 1))); ?>

            </button>
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 transform scale-95 -translate-y-1" x-transition:enter-end="opacity-100 transform scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none; position: absolute; right: 0; top: calc(100% + 0.5rem); min-width: 200px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); z-index: 999; overflow: hidden;">
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color);">
                    <div style="font-weight: 700; font-size: 0.82rem; color: var(--text-main);"><?php echo e(auth('player')->user()->name ?? ''); ?></div>
                    <div style="font-size: 0.68rem; color: var(--text-muted); margin-top: 0.1rem;"><?php echo e(auth('player')->user()->phone ?? ''); ?></div>
                </div>
                <div style="padding: 0.35rem;">
                    <div style="font-size: 0.62rem; font-weight: 700; color: var(--text-muted); padding: 0.3rem 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo e(__('Bahasa')); ?></div>
                    <div style="display: flex; gap: 0.25rem; padding: 0 0.65rem 0.35rem;">
                        <a href="/set-locale/id" style="flex: 1; text-align: center; padding: 0.25rem 0; border-radius: 6px; font-size: 0.72rem; font-weight: 700; text-decoration: none; transition: all 0.15s; <?php echo e(app()->getLocale() == 'id' ? 'background: var(--primary); color: #000;' : 'background: transparent; color: var(--text-muted); border: 1px solid var(--border-color);'); ?>">ID</a>
                        <a href="/set-locale/en" style="flex: 1; text-align: center; padding: 0.25rem 0; border-radius: 6px; font-size: 0.72rem; font-weight: 700; text-decoration: none; transition: all 0.15s; <?php echo e(app()->getLocale() == 'en' ? 'background: var(--primary); color: #000;' : 'background: transparent; color: var(--text-muted); border: 1px solid var(--border-color);'); ?>">EN</a>
                    </div>
                    <div style="font-size: 0.62rem; font-weight: 700; color: var(--text-muted); padding: 0.3rem 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo e(__('Tema')); ?></div>
                    <button onclick="toggleTheme()" style="width: 100%; text-align: left; padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: none; background: transparent; color: var(--text-main); cursor: pointer; display: flex; align-items: center; gap: 0.4rem; transition: background 0.15s;" onmouseover="this.style.background='var(--primary-glow)'" onmouseout="this.style.background='transparent'">
                        <span id="themeToggleIcon">☀️</span>
                        <span><?php echo e(__('Ganti Tema')); ?></span>
                    </button>
                    <button onclick="togglePaymentDrawer(true); open = false;" style="width: 100%; text-align: left; padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: none; background: transparent; color: var(--text-main); cursor: pointer; display: flex; align-items: center; gap: 0.4rem; transition: background 0.15s;" onmouseover="this.style.background='var(--primary-glow)'" onmouseout="this.style.background='transparent'">
                        💳 <?php echo e(__('Pembayaran')); ?>

                    </button>
                    <div style="height: 1px; background: var(--border-color); margin: 0.35rem 0;"></div>
                    <a href="<?php echo e(route('player.logout')); ?>" style="display: block; padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: var(--danger); text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
                        🚪 <?php echo e(__('Keluar')); ?>

                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div id="scrollContainer" style="flex-grow: 1; overflow-y: auto; overflow-x: hidden;">
        <main>
            <?php echo e($slot); ?>

        </main>

        <footer style="text-align: center; padding: 2rem; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.9rem; margin-top: 4rem;">
            &copy; <?php echo e(date('Y')); ?> Infinity Boxzone. All Rights Reserved.
        </footer>
    </div>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


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
<?php /**PATH C:\laragon\www\TurnamenPS\resources\views/components/layouts/player.blade.php ENDPATH**/ ?>