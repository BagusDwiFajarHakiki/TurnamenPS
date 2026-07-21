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
    <nav class="<?php echo e(request()->is('/') ? 'nav-at-top' : ''); ?>" id="mainNav" data-is-home="<?php echo e(request()->is('/') ? 'true' : 'false'); ?>" style="flex-shrink: 0; width: 100%; padding: 0;">
        <div style="max-width: 1200px; margin: 0 auto; width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 1rem clamp(0.75rem, 3vw, 1.5rem);">
            <a href="/" wire:navigate class="nav-brand" style="text-transform: none; letter-spacing: 0; gap: 0.5rem;"><img src="/images/logo.png" alt="Infinity Boxzone" style="height: 32px; width: auto;"></a>
            <div class="nav-links">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard('player')->check()): ?>
                    <a href="<?php echo e(route('player.dashboard')); ?>" wire:navigate class="nav-link nav-auth-link <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>"><?php echo e(__('Dasbor')); ?></a>
                <?php else: ?>
                    <a href="<?php echo e(route('player.login')); ?>" wire:navigate class="nav-link nav-auth-link <?php echo e(request()->routeIs('player.login') ? 'active' : ''); ?>"><?php echo e(__('Login')); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <!-- Language and Theme Controls -->
                <div style="display: flex; align-items: center; gap: 0.25rem; border-left: 1px solid var(--border-color); padding-left: 1rem; margin-left: 0.5rem;">
                    
                    <!-- Theme Toggle Button -->
                    <button id="themeToggleBtn" onclick="toggleTheme()" style="position: relative; width: 44px; height: 24px; border-radius: 24px; background: var(--bg-surface); border: 1px solid var(--border-color); cursor: pointer; padding: 0; transition: background-color 0.3s; display: flex; align-items: center; margin-left: 0.75rem;" title="Toggle Light/Dark Mode">
                        <div id="themeToggleSlider" style="position: absolute; left: 2px; width: 18px; height: 18px; border-radius: 50%; background: var(--primary); transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1); box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div id="scrollContainer" style="flex-grow: 1; overflow-x: hidden;">
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
                    slider.style.transform = 'translateX(20px)';
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
<?php /**PATH C:\laragon\www\TurnamenPS\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>