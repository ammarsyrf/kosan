<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name', 'Kost Management System')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'slide-up': 'slideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'slide-down': 'slideDown 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'pulse-glow': 'pulseGlow 3s ease-in-out infinite',
                        'float': 'float 5s ease-in-out infinite',
                        'shimmer': 'shimmer 1.8s infinite linear',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(16px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideDown: {
                            '0%': { opacity: '0', transform: 'translateY(-12px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.3', transform: 'scale(1)' },
                            '50%': { opacity: '0.7', transform: 'scale(1.08)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Smooth Scrolling */
        html { scroll-behavior: smooth; }

        /* Loading Skeleton Shimmer */
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.8s infinite linear;
        }
        .skeleton-dark {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 200% 100%;
            animation: shimmer 1.8s infinite linear;
        }

        /* Scroll-Reveal Dynamic Animations */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1), transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-on-scroll.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* Staggered Delay for Grid Elements */
        .stagger-1 { transition-delay: 0.08s; }
        .stagger-2 { transition-delay: 0.16s; }
        .stagger-3 { transition-delay: 0.24s; }
        .stagger-4 { transition-delay: 0.32s; }

        /* Interactive Card Hover Elevation */
        .card-interactive {
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .card-interactive:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 32px -12px rgba(15, 23, 42, 0.12);
        }

        /* Smooth Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full text-slate-800 antialiased bg-slate-50 selection:bg-blue-600 selection:text-white">

    <!-- Top Loading Progress Indicator -->
    <div id="top-progress-bar" class="fixed top-0 left-0 h-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-400 z-[9999] transition-all duration-300 w-0 opacity-0 pointer-events-none"></div>

<?php if (auth_check()): ?>
    <?php
    $u = auth_user();
    $role = strtolower($u['role'] ?? 'guest');
    $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

    $isActive = function(string $path) use ($currentUri): bool {
        $trimmedPath = trim($path, '/');
        $trimmedUri = trim($currentUri, '/');
        if ($trimmedPath === '') {
            return $trimmedUri === '' || $trimmedUri === 'home';
        }
        return str_starts_with($trimmedUri, $trimmedPath);
    };

    $linkClass = function(string $path) use ($isActive): string {
        return $isActive($path)
            ? 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold bg-blue-600 text-white shadow-md shadow-blue-600/30 transition'
            : 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800/80 transition';
    };
    ?>

    <!-- Authenticated App Shell with Modern Dark Sidebar -->
    <div class="min-h-full flex">
        <!-- Backdrop Overlay on Mobile -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

        <!-- Desktop & Mobile Sidebar -->
        <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out shadow-2xl lg:shadow-none">
            <!-- Sidebar Header & Logo -->
            <div class="flex flex-col flex-1 overflow-hidden">
                <div class="h-16 px-5 border-b border-slate-800/80 flex items-center justify-between flex-shrink-0">
                    <a href="<?= url() ?>" class="flex items-center space-x-3 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-teal-400 flex items-center justify-center text-white shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform flex-shrink-0">
                            <i data-lucide="home" class="w-5 h-5"></i>
                        </div>
                        <div class="flex flex-col whitespace-nowrap overflow-hidden">
                            <span class="font-extrabold text-white text-sm tracking-tight leading-none group-hover:text-blue-400 transition-colors">
                                <?= e(config('app.name', 'Kost Management')) ?>
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <?= strtoupper(e($role)) ?> SUITE
                            </span>
                        </div>
                    </a>
                    <button id="close-sidebar-btn" type="button" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Navigation List (Categorized with Clean Headers) -->
                <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">
                    <!-- SECTION 1: UTAMA -->
                    <div class="space-y-1">
                        <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Pusat Kendali</div>
                        <?php if ($role === 'owner'): ?>
                            <a href="<?= url('owner/dashboard') ?>" class="<?= $linkClass('owner/dashboard') ?>">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                <span>Executive Dashboard</span>
                            </a>
                            <a href="<?= url('admin/reports/financial') ?>" class="<?= $linkClass('admin/reports/financial') ?>">
                                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                                <span>Laporan Keuangan Kas</span>
                            </a>
                        <?php elseif ($role === 'admin'): ?>
                            <a href="<?= url('admin/dashboard') ?>" class="<?= $linkClass('admin/dashboard') ?>">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                <span>Operations Dashboard</span>
                            </a>
                        <?php else: ?>
                            <a href="<?= url('tenant/dashboard') ?>" class="<?= $linkClass('tenant/dashboard') ?>">
                                <i data-lucide="home" class="w-4 h-4"></i>
                                <span>Dashboard Saya</span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (in_array($role, ['owner', 'admin'], true)): ?>
                        <!-- SECTION 2: PROPERTI & PENGHUNI -->
                        <div class="space-y-1">
                            <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Properti & Kamar</div>
                            <a href="<?= url('admin/rooms') ?>" class="<?= $linkClass('admin/rooms') ?>">
                                <i data-lucide="door-closed" class="w-4 h-4"></i>
                                <span>Kelola Kamar Kost</span>
                            </a>
                            <a href="<?= url('admin/facilities') ?>" class="<?= $linkClass('admin/facilities') ?>">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                <span>Fasilitas Kost</span>
                            </a>
                            <a href="<?= url('admin/tenants') ?>" class="<?= $linkClass('admin/tenants') ?>">
                                <i data-lucide="users" class="w-4 h-4"></i>
                                <span>Data Penghuni</span>
                            </a>
                        </div>

                        <!-- SECTION 3: SEWA & TRANSAKSI -->
                        <div class="space-y-1">
                            <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Sewa & Tagihan</div>
                            <a href="<?= url('admin/bookings') ?>" class="<?= $linkClass('admin/bookings') ?>">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>Booking Kamar</span>
                            </a>
                            <a href="<?= url('admin/contracts') ?>" class="<?= $linkClass('admin/contracts') ?>">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                <span>Kontrak Sewa</span>
                            </a>
                            <a href="<?= url('admin/invoices') ?>" class="<?= $linkClass('admin/invoices') ?>">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                                <span>Tagihan (Invoice)</span>
                            </a>
                            <a href="<?= url('admin/payments') ?>" class="<?= $linkClass('admin/payments') ?>">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                <span>Verifikasi Pembayaran</span>
                            </a>
                        </div>

                        <!-- SECTION 4: OPERASIONAL & BIAYA -->
                        <div class="space-y-1">
                            <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Operasional</div>
                            <a href="<?= url('admin/notifications') ?>" class="<?= $linkClass('admin/notifications') ?>">
                                <i data-lucide="message-square" class="w-4 h-4"></i>
                                <span>WhatsApp Automation</span>
                            </a>
                            <a href="<?= url('admin/expenses') ?>" class="<?= $linkClass('admin/expenses') ?>">
                                <i data-lucide="wallet" class="w-4 h-4"></i>
                                <span>Pengeluaran Biaya</span>
                            </a>
                            <a href="<?= url('admin/maintenance') ?>" class="<?= $linkClass('admin/maintenance') ?>">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                                <span>Tiket Perbaikan</span>
                            </a>
                        </div>
                    <?php elseif ($role === 'tenant'): ?>
                        <!-- SECTION TENANT -->
                        <div class="space-y-1">
                            <div class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Layanan Penyewa</div>
                            <a href="<?= url('tenant/maintenance') ?>" class="<?= $linkClass('tenant/maintenance') ?>">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                                <span>Lapor Kerusakan</span>
                            </a>
                            <a href="<?= url('tenant/payment') ?>" class="<?= $linkClass('tenant/payment') ?>">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                <span>Unggah Bukti Bayar</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>

            <!-- Sidebar User Profile Footer -->
            <div class="p-3 border-t border-slate-800 bg-slate-950/50">
                <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700/60 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                            <?= strtoupper(substr($u['name'] ?? $u['email'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white truncate leading-none"><?= e($u['name'] ?? $u['email']) ?></div>
                            <div class="text-[10px] font-semibold text-blue-400 uppercase tracking-wider mt-0.5"><?= e($role) ?></div>
                        </div>
                    </div>
                    <form method="POST" action="<?= url('logout') ?>" class="inline flex-shrink-0">
                        <?= csrf_field() ?>
                        <button type="submit" title="Keluar dari sistem" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-700/60 transition">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Right Side: Content Area with Slim Topbar -->
        <div class="flex-1 lg:pl-64 flex flex-col min-h-screen min-w-0">
            <!-- Slim Top Header -->
            <header class="h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/90 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <button id="open-sidebar-btn" type="button" class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none" aria-label="Buka Menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <a href="<?= url() ?>" class="hover:text-blue-600 transition">Kost Management</a>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span class="text-slate-800 font-bold"><?= e($title ?? 'Dashboard') ?></span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sistem Aktif & Terlindungi
                    </span>
                    <span class="text-xs text-slate-400 hidden sm:inline font-medium">
                        <?= date('d M Y') ?>
                    </span>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <?php if ($success = session('_flash.success')): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            if (window.gooeyToast) window.gooeyToast.success(<?= json_encode($success) ?>);
                        });
                    </script>
                <?php endif; ?>

                <?php if ($error = session('_flash.error')): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            if (window.gooeyToast) window.gooeyToast.error(<?= json_encode($error) ?>);
                        });
                    </script>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </main>

            <!-- Slim Dashboard Footer -->
            <footer class="bg-white border-t border-slate-200/80 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2 mt-auto">
                <div>&copy; <?= date('Y') ?> <?= e(config('app.name', 'Kost Management System')) ?> — Personal Edition.</div>
                <div class="flex items-center gap-3">
                    <span>PHP Native Monolith</span>
                    <span>&bull;</span>
                    <span>Fonnte WhatsApp</span>
                </div>
            </footer>
        </div>
    </div>

<?php else: ?>
    <!-- Public Visitor Layout (Landing Page): Full-Width Immersive Glass Navigation -->
    <header class="bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/80 sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <a href="<?= url() ?>" class="flex items-center space-x-3.5 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-500 via-indigo-500 to-teal-400 p-0.5 shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform duration-300">
                    <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-teal-300">
                        <i data-lucide="home" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="flex flex-col whitespace-nowrap">
                    <span class="font-black text-white tracking-tight text-lg leading-none group-hover:text-teal-300 transition-colors">
                        <?= e(config('app.name', 'Kost Management')) ?>
                    </span>
                    <span class="text-[10px] font-extrabold text-teal-400 tracking-wider uppercase mt-1 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Smart Living Monolith
                    </span>
                </div>
            </a>

            <!-- Public Nav Links with Glow Pills -->
            <nav class="hidden md:flex items-center gap-1.5 p-1.5 bg-slate-900/90 border border-slate-800 rounded-2xl shadow-inner">
                <a href="#kamar-kost" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all flex items-center gap-1.5">
                    <i data-lucide="door-closed" class="w-3.5 h-3.5 text-teal-400"></i> Katalog Kamar
                </a>
                <a href="#demo-access" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-blue-400"></i> Uji Coba Demo
                </a>
                <a href="<?= url('activate') ?>" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all flex items-center gap-1.5">
                    <i data-lucide="key" class="w-3.5 h-3.5 text-amber-400"></i> Aktivasi Akun
                </a>
            </nav>

            <a href="<?= url('login') ?>" class="inline-flex items-center px-5 py-2.5 text-xs font-extrabold tracking-wide rounded-xl text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:opacity-95 shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-0.5 active:scale-95">
                <i data-lucide="log-in" class="w-3.5 h-3.5 mr-1.5"></i> Masuk ke Portal
            </a>
        </div>
    </header>

    <main class="flex-1 w-full">
        <?php if ($success = session('_flash.success')): ?>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.gooeyToast) window.gooeyToast.success(<?= json_encode($success) ?>);
                });
            </script>
        <?php endif; ?>

        <?php if ($error = session('_flash.error')): ?>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.gooeyToast) window.gooeyToast.error(<?= json_encode($error) ?>);
                });
            </script>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <footer class="bg-slate-950 text-slate-400 border-t border-slate-800/80 mt-auto py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between text-xs gap-6">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-500 to-teal-400 p-0.5 shadow-sm">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center text-teal-300">
                        <i data-lucide="home" class="w-4 h-4"></i>
                    </div>
                </div>
                <div>
                    <span class="font-bold text-white text-sm block"><?= e(config('app.name', 'Kost Management System')) ?></span>
                    <span class="text-[11px] text-slate-500">Sistem Operasional Kos Modern &bull; Personal Monolith Edition</span>
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-4 text-slate-400 text-xs">
                <span class="flex items-center gap-1.5"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i> Role Isolation Active</span>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5"><i data-lucide="message-square" class="w-3.5 h-3.5 text-teal-400"></i> Fonnte WhatsApp API</span>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5"><i data-lucide="database" class="w-3.5 h-3.5 text-blue-400"></i> MySQL PDO Prepared</span>
            </div>
            <div class="text-slate-500 text-[11px]">
                &copy; <?= date('Y') ?> Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>
<?php endif; ?>

    <script>
        lucide.createIcons();

        // Responsive Off-Canvas Sidebar Toggle
        const openBtn = document.getElementById('open-sidebar-btn');
        const closeBtn = document.getElementById('close-sidebar-btn');
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const progressBar = document.getElementById('top-progress-bar');

        function toggleSidebar(open) {
            if (!sidebar || !backdrop) return;
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        if (openBtn) openBtn.addEventListener('click', () => toggleSidebar(true));
        if (closeBtn) closeBtn.addEventListener('click', () => toggleSidebar(false));
        if (backdrop) backdrop.addEventListener('click', () => toggleSidebar(false));

        // Smooth Page Load Progress Bar Animation
        if (progressBar) {
            progressBar.style.opacity = '1';
            progressBar.style.width = '35%';
            setTimeout(() => { progressBar.style.width = '80%'; }, 150);
            window.addEventListener('load', () => {
                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressBar.style.opacity = '0';
                    setTimeout(() => { progressBar.style.width = '0'; }, 300);
                }, 200);
            });
        }

        // Top Scroll Reading Depth Indicator
        window.addEventListener('scroll', () => {
            const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            if (height > 0 && progressBar) {
                const scrolled = (winScroll / height) * 100;
                progressBar.style.opacity = winScroll > 20 ? '0.9' : '0';
                progressBar.style.width = scrolled + '%';
            }
        }, { passive: true });

        // Auto Scroll-Reveal Observer (Smooth Viewport Fade-and-Slide)
        const revealElements = document.querySelectorAll('.reveal-on-scroll');
        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.08,
                rootMargin: '0px 0px -40px 0px'
            });

            revealElements.forEach(el => revealObserver.observe(el));
        } else {
            revealElements.forEach(el => el.classList.add('is-revealed'));
        }

        // Image Lazy Loading with Skeleton Shimmer Placeholder
        document.querySelectorAll('img[loading="lazy"]').forEach(img => {
            const skeletonWrap = img.closest('.skeleton');
            if (img.complete && img.naturalHeight !== 0) {
                if (skeletonWrap) skeletonWrap.classList.remove('skeleton');
                img.classList.remove('opacity-0');
            } else {
                img.addEventListener('load', function() {
                    if (skeletonWrap) skeletonWrap.classList.remove('skeleton');
                    this.classList.remove('opacity-0');
                    this.classList.add('opacity-100');
                });
            }
        });

        // Global Form Submission Progress & Loading Feedback
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                if (progressBar) {
                    progressBar.style.opacity = '1';
                    progressBar.style.width = '70%';
                }
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.classList.add('opacity-85', 'cursor-wait');
                }
            });
        });
    </script>

    <?php require dirname(__DIR__) . '/partials/gooey_notifications.php'; ?>
</body>
</html>
