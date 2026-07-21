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
<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">
    <!-- Use a simple script to handle transparent nav on home page -->
    <nav id="mainNav" style="flex-shrink: 0; width: 100%; padding: 0;">
        <div style="max-width: 1200px; margin: 0 auto; width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 1rem clamp(0.75rem, 3vw, 1.5rem);">
            <a href="/" wire:navigate class="nav-brand" style="text-transform: none; letter-spacing: 0; gap: 0.5rem;"><img src="/images/logo.png" alt="Infinity Boxzone" style="height: 32px; width: auto;"></a>

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
                        <div style="font-size: 0.62rem; font-weight: 700; color: var(--text-muted); padding: 0.3rem 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo e(__('Tema')); ?></div>
                        <button onclick="toggleTheme()" style="width: 100%; text-align: left; padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: none; background: transparent; color: var(--text-main); cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: background 0.15s;" onmouseover="this.style.background='var(--primary-glow)'" onmouseout="this.style.background='transparent'">
                            <span><?php echo e(__('Ganti Tema')); ?></span>
                            <div style="position: relative; width: 32px; height: 18px; border-radius: 18px; background: var(--bg-main); border: 1px solid var(--border-color); display: flex; align-items: center;">
                                <div id="themeToggleSlider" style="position: absolute; left: 2px; width: 12px; height: 12px; border-radius: 50%; background: var(--primary); transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1); box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></div>
                            </div>
                        </button>
                        <button onclick="togglePaymentDrawer(true); open = false;" style="width: 100%; text-align: left; padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: none; background: transparent; color: var(--text-main); cursor: pointer; display: flex; align-items: center; gap: 0.4rem; transition: background 0.15s;" onmouseover="this.style.background='var(--primary-glow)'" onmouseout="this.style.background='transparent'">
                            <?php echo e(__('Pembayaran')); ?>

                        </button>
                        <div style="height: 1px; background: var(--border-color); margin: 0.35rem 0;"></div>
                        <a href="<?php echo e(route('player.logout')); ?>" style="display: block; padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: var(--danger); text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
                            <?php echo e(__('Keluar')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div id="scrollContainer" style="flex-grow: 1; overflow-x: hidden;">
        <main>
            <?php echo e($slot); ?>

        </main>

        <footer style="text-align: center; padding: 2rem; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.9rem; margin-top: 4rem;">
            &copy; <?php echo e(date('Y')); ?> <a href="https://sindelarastechnology.my.id" target="_blank">SinderalaS Technology</a>. All Rights Reserved.
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

        function initTheme() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'light') {
                document.documentElement.classList.add('light-theme');
            } else {
                document.documentElement.classList.remove('light-theme');
            }
            updateThemeIcon(theme);
        }

        function updateThemeIcon(theme) {
            const slider = document.getElementById('themeToggleSlider');
            if (slider) {
                if (theme === 'light') {
                    slider.style.transform = 'translateX(16px)';
                    slider.style.background = 'var(--bg-main)';
                } else {
                    slider.style.transform = 'translateX(0)';
                    slider.style.background = 'var(--primary)';
                }
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
            if (nav && nav.dataset.isHome === 'true') {
                if (window.scrollY > 50) {
                    nav.classList.remove('nav-at-top');
                } else {
                    nav.classList.add('nav-at-top');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initTheme();
            handleScroll();
            window.addEventListener('scroll', handleScroll);
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