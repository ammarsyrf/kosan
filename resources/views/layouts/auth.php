<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Autentikasi — Kost Management System') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'slide-up': 'slideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'pulse-glow': 'pulseGlow 3s ease-in-out infinite',
                        'float': 'float 5s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(16px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        pulseGlow: { '0%, 100%': { opacity: '0.2', transform: 'scale(1)' }, '50%': { opacity: '0.5', transform: 'scale(1.08)' } },
                        float: { '0%, 100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-10px)' } },
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="flex flex-col justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8 text-slate-800 antialiased bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 relative overflow-hidden">
    <!-- Top Progress Bar -->
    <div id="top-progress-bar" class="fixed top-0 left-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-teal-400 z-[9999] transition-all duration-300 w-0 opacity-0 pointer-events-none"></div>

    <!-- Ambient Background Accents -->
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none animate-float"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-teal-500/15 rounded-full blur-3xl pointer-events-none animate-float" style="animation-delay: -2.5s;"></div>

    <div class="relative z-10 sm:mx-auto sm:w-full sm:max-w-md text-center animate-slide-up">
        <!-- Logo -->
        <a href="<?= url() ?>" class="inline-flex items-center justify-center space-x-3 group mb-4">
            <div class="w-12 h-12 bg-gradient-to-tr from-blue-600 via-indigo-600 to-teal-400 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform duration-200">
                <i data-lucide="home" class="w-6 h-6"></i>
            </div>
        </a>
        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
            <?= e(config('app.name', 'Kost Management')) ?>
        </h2>
        <p class="mt-1.5 text-xs sm:text-sm text-slate-400">
            Sistem Manajemen Kos Personal &bullet; Masuk ke Portal
        </p>
    </div>

    <div class="relative z-10 mt-6 sm:mx-auto sm:w-full sm:max-w-md animate-slide-up" style="animation-delay: 0.1s;">
        <div class="bg-white/95 backdrop-blur-xl py-8 px-6 sm:px-10 shadow-2xl border border-slate-200/80 rounded-3xl">
            <?php if ($success = session('_flash.success')): ?>
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center space-x-2.5">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <span class="font-medium"><?= e($success) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error = session('_flash.error')): ?>
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center space-x-2.5">
                    <i data-lucide="alert-octagon" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                    <span class="font-medium"><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="<?= url() ?>" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white transition">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Halaman Utama
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const progressBar = document.getElementById('top-progress-bar');
        if (progressBar) {
            progressBar.style.opacity = '1';
            progressBar.style.width = '40%';
            window.addEventListener('load', () => {
                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressBar.style.opacity = '0';
                    setTimeout(() => { progressBar.style.width = '0'; }, 300);
                }, 200);
            });
        }

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                if (progressBar) {
                    progressBar.style.opacity = '1';
                    progressBar.style.width = '75%';
                }
                const btn = this.querySelector('button[type="submit"]');
                if (btn) btn.classList.add('opacity-85', 'cursor-wait');
            });
        });
    </script>
</body>
</html>
