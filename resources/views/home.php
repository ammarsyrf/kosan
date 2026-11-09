<?php
/** @var array $stats */
/** @var array $rooms */
/** @var string|null $role_zone */
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-20">

    <!-- HERO SECTION: Ultra-Modern Obsidian & Indigo Gradient with Glassmorphism -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white p-8 sm:p-14 lg:p-16 shadow-2xl border border-slate-800/80">
        <!-- Ambient Decorative Lighting Glows -->
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-blue-600/25 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:24px_24px] opacity-[0.07] pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-6">
            <!-- Platform Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-bold text-teal-300 shadow-inner">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sistem Manajemen Kost Modern • Smart Living Monolith</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight sm:leading-tight">
                Kendali Penuh Properti Kos <br class="hidden sm:inline" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-teal-300 to-emerald-400">
                    Otomatis, Rapi, & Real-Time
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="text-slate-300 text-sm sm:text-lg max-w-2xl mx-auto leading-relaxed font-normal">
                Platform operasional kos lengkap: automasi penagihan via WhatsApp (Fonnte API), pemantauan okupansi kamar terintegrasi, pembukuan arus kas bersih, serta portal mandiri bagi seluruh penghuni.
            </p>

            <!-- Action Buttons -->
            <div class="pt-4 flex flex-wrap items-center justify-center gap-3.5">
                <?php if (auth_check()): ?>
                    <?php $u = auth_user(); ?>
                    <?php if (strtolower($u['role'] ?? '') === 'owner'): ?>
                        <a href="<?= url('owner/dashboard') ?>" class="px-7 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Buka Executive Dashboard
                        </a>
                    <?php elseif (strtolower($u['role'] ?? '') === 'admin'): ?>
                        <a href="<?= url('admin/dashboard') ?>" class="px-7 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Buka Admin Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= url('tenant/dashboard') ?>" class="px-7 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white shadow-lg shadow-teal-500/30 transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                            <i data-lucide="home" class="w-4 h-4"></i> Buka Portal Penghuni
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="#demo-access" class="px-7 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:opacity-95 text-white shadow-lg shadow-blue-600/30 transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4"></i> Coba Akun Demo Sekarang
                    </a>
                    <a href="<?= url('login') ?>" class="px-6 py-3.5 rounded-xl font-semibold text-sm bg-white/10 hover:bg-white/15 backdrop-blur-md border border-white/20 text-white transition inline-flex items-center gap-2">
                        <i data-lucide="log-in" class="w-4 h-4 text-slate-300"></i> Masuk Akun
                    </a>
                <?php endif; ?>

                <a href="#kamar-kost" class="px-6 py-3.5 rounded-xl font-semibold text-sm bg-white/5 hover:bg-white/10 backdrop-blur-md border border-white/10 text-slate-200 transition inline-flex items-center gap-2">
                    <i data-lucide="door-closed" class="w-4 h-4 text-teal-400"></i> Lihat Kamar Tersedia
                </a>
            </div>

            <!-- Live KPI Metric Cards Strip -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-8 text-left">
                <!-- Card 1 -->
                <div class="bg-white/[0.04] backdrop-blur-xl border border-white/10 rounded-2xl p-4 sm:p-5 hover:bg-white/[0.07] transition shadow-lg">
                    <div class="flex items-center justify-between text-slate-400 text-xs mb-1.5 font-medium">
                        <span>Total Kamar</span>
                        <div class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center">
                            <i data-lucide="door-closed" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white">
                        <?= (int)($stats['total_rooms'] ?? 8) ?> <span class="text-xs font-normal text-slate-400">Unit</span>
                    </div>
                    <div class="text-[11px] text-teal-300 mt-1 flex items-center gap-1.5 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span> Siap dikelola
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white/[0.04] backdrop-blur-xl border border-white/10 rounded-2xl p-4 sm:p-5 hover:bg-white/[0.07] transition shadow-lg">
                    <div class="flex items-center justify-between text-slate-400 text-xs mb-1.5 font-medium">
                        <span>Okupansi Aktif</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <i data-lucide="pie-chart" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">
                        <?= $stats['occupancy_rate'] ?? 12.5 ?>%
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <?= (int)($stats['occupied'] ?? 1) ?> Kamar Terisi
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white/[0.04] backdrop-blur-xl border border-white/10 rounded-2xl p-4 sm:p-5 hover:bg-white/[0.07] transition shadow-lg">
                    <div class="flex items-center justify-between text-slate-400 text-xs mb-1.5 font-medium">
                        <span>Automasi WhatsApp</span>
                        <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-2">
                        Fonnte
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Aktif
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">
                        Jadwal H-7, H-3, H-1, H+1
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white/[0.04] backdrop-blur-xl border border-white/10 rounded-2xl p-4 sm:p-5 hover:bg-white/[0.07] transition shadow-lg">
                    <div class="flex items-center justify-between text-slate-400 text-xs mb-1.5 font-medium">
                        <span>Laporan Keuangan</span>
                        <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white">
                        CSV <span class="text-xs font-normal text-slate-400">Export</span>
                    </div>
                    <div class="text-[11px] text-teal-300 mt-1">
                        BOM UTF-8 Excel Ready
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DEMO ACCESS SECTION: 1-Click Role Exploration Cards -->
    <section id="demo-access" class="space-y-6">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-blue-600 uppercase tracking-wider bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Uji Coba Cepat Sistem
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Eksplorasi Hak Akses Berdasarkan 3 Peran
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm">
                Sistem memiliki isolasi otorisasi data ketat. Klik tombol masuk instan pada masing-masing peran untuk menguji alur kerja nyata:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 1. OWNER CARD -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-amber-200/90 shadow-sm hover:shadow-xl hover:shadow-amber-500/10 hover:border-amber-400 transition-all duration-200 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-105 transition-transform">
                            <i data-lucide="crown" class="w-6 h-6"></i>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full bg-amber-100 text-amber-800">
                            Eksekutif / Pemilik
                        </span>
                    </div>

                    <div>
                        <h3 class="font-extrabold text-slate-900 text-lg">Bapak Kos (Owner)</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Akses penuh analitik keuangan eksekutif, margin laba kotor, arus kas bersih, serta ekspor CSV rahasia.
                        </p>
                    </div>

                    <ul class="space-y-2 text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-amber-500"></i> Executive Net Cash Flow & Revenue
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-amber-500"></i> Laporan Keuangan Kas Lengkap
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-amber-500"></i> Ekspor CSV Khusus Owner (Protected)
                        </li>
                    </ul>

                    <!-- Credential Pill -->
                    <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-200/60 text-xs font-mono text-slate-700 space-y-0.5">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Email:</span>
                            <span class="font-bold text-slate-900">owner@kost.com</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Password:</span>
                            <span class="font-bold text-slate-900">password123</span>
                        </div>
                    </div>
                </div>

                <!-- Instant 1-Click Login Form -->
                <form method="POST" action="<?= url('login') ?>" class="mt-6">
                    <?= csrf_field() ?>
                    <input type="hidden" name="email" value="owner@kost.com">
                    <input type="hidden" name="password" value="password123">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white shadow-md shadow-amber-500/25 transition transform active:scale-95 flex items-center justify-center gap-1.5">
                        <span>Masuk Langsung sbg Owner</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>

            <!-- 2. ADMIN CARD -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-blue-200/90 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-400 transition-all duration-200 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-105 transition-transform">
                            <i data-lucide="shield" class="w-6 h-6"></i>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                            Pengelola Harian
                        </span>
                    </div>

                    <div>
                        <h3 class="font-extrabold text-slate-900 text-lg">Admin Operasional</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Manajemen kamar, kontrak sewa, verifikasi pembayaran, pencatatan pengeluaran, WhatsApp, dan perbaikan.
                        </p>
                    </div>

                    <ul class="space-y-2 text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-blue-500"></i> Kelola Kamar, Penghuni, & Fasilitas
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-blue-500"></i> Verifikasi Transfer Bank & Kas Tunai
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-blue-500"></i> Disposisi Kerusakan & Biaya Operasional
                        </li>
                    </ul>

                    <!-- Credential Pill -->
                    <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200/60 text-xs font-mono text-slate-700 space-y-0.5">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Email:</span>
                            <span class="font-bold text-slate-900">admin@kost.com</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Password:</span>
                            <span class="font-bold text-slate-900">password123</span>
                        </div>
                    </div>
                </div>

                <!-- Instant 1-Click Login Form -->
                <form method="POST" action="<?= url('login') ?>" class="mt-6">
                    <?= csrf_field() ?>
                    <input type="hidden" name="email" value="admin@kost.com">
                    <input type="hidden" name="password" value="password123">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/25 transition transform active:scale-95 flex items-center justify-center gap-1.5">
                        <span>Masuk Langsung sbg Admin</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>

            <!-- 3. TENANT CARD -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-teal-200/90 shadow-sm hover:shadow-xl hover:shadow-teal-500/10 hover:border-teal-400 transition-all duration-200 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-105 transition-transform">
                            <i data-lucide="user-check" class="w-6 h-6"></i>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full bg-teal-100 text-teal-800">
                            Penyewa Kos
                        </span>
                    </div>

                    <div>
                        <h3 class="font-extrabold text-slate-900 text-lg">Penghuni (Budi Gunawan)</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Cek tagihan sewa, kirim bukti bayar, perpanjangan sewa (extension), ajukan perbaikan kamar, dan early checkout.
                        </p>
                    </div>

                    <ul class="space-y-2 text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-teal-500"></i> Tagihan Kamar & Upload Bukti Bayar
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-teal-500"></i> Lapor Kerusakan Kamar dengan Foto
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-teal-500"></i> Ajukan Perpanjangan / Early Checkout
                        </li>
                    </ul>

                    <!-- Credential Pill -->
                    <div class="p-3 bg-teal-50/60 rounded-xl border border-teal-200/60 text-xs font-mono text-slate-700 space-y-0.5">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Email:</span>
                            <span class="font-bold text-slate-900">budi@gmail.com</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Password:</span>
                            <span class="font-bold text-slate-900">password123</span>
                        </div>
                    </div>
                </div>

                <!-- Instant 1-Click Login Form -->
                <form method="POST" action="<?= url('login') ?>" class="mt-6">
                    <?= csrf_field() ?>
                    <input type="hidden" name="email" value="budi@gmail.com">
                    <input type="hidden" name="password" value="password123">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white shadow-md shadow-teal-500/25 transition transform active:scale-95 flex items-center justify-center gap-1.5">
                        <span>Masuk Langsung sbg Penghuni</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- ROOM INVENTORY & SHOWCASE SECTION -->
    <?php if (!empty($rooms)): ?>
        <section id="kamar-kost" class="space-y-6 pt-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-teal-600 uppercase tracking-wider bg-teal-50 px-3 py-1 rounded-full border border-teal-100">
                        <i data-lucide="door-closed" class="w-3.5 h-3.5"></i> Inventaris Kamar
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                        Daftar Kamar & Ketersediaan
                    </h2>
                    <p class="text-slate-500 text-xs sm:text-sm">
                        Kamar kost eksklusif siap huni dengan fasilitas lengkap dan harga transparan.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Tersedia
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Disewa
                    </span>
                </div>
            </div>

            <!-- Room Grid: High-res Card Aesthetics with Architectural Gradients -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach (array_slice($rooms, 0, 8) as $room): ?>
                    <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-200 flex flex-col justify-between group">
                        <div>
                            <!-- Visual Banner with Gradient Backdrop -->
                            <div class="h-32 bg-gradient-to-tr from-slate-900 via-indigo-950 to-slate-800 p-4 relative flex flex-col justify-between overflow-hidden">
                                <div class="absolute -right-8 -bottom-8 w-28 h-28 bg-blue-500/20 rounded-full blur-xl pointer-events-none"></div>
                                <div class="flex items-center justify-between relative z-10">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black tracking-wider bg-white/10 backdrop-blur-md text-white border border-white/20">
                                        KAMAR <?= e($room->room_number) ?>
                                    </span>
                                    <?php if ($room->status === 'available'): ?>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-300 bg-emerald-950/60 backdrop-blur-md px-2.5 py-1 rounded-full border border-emerald-400/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Siap Huni
                                        </span>
                                    <?php elseif ($room->status === 'occupied'): ?>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-300 bg-blue-950/60 backdrop-blur-md px-2.5 py-1 rounded-full border border-blue-400/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Terisi
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-300 bg-amber-950/60 backdrop-blur-md px-2.5 py-1 rounded-full border border-amber-400/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Maintenance
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="relative z-10 text-slate-300 text-xs font-medium flex items-center gap-2">
                                    <i data-lucide="bed" class="w-4 h-4 text-teal-300"></i>
                                    <span>Lantai <?= (int)($room->floor ?? 1) ?> • Deluxe Single</span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 space-y-3">
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base group-hover:text-blue-600 transition-colors">
                                        <?= e($room->name ?? ('Kamar ' . $room->room_number)) ?>
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">
                                        <?= e($room->description ?: 'Kamar nyaman dan bersih dengan sirkulasi udara baik.') ?>
                                    </p>
                                </div>

                                <!-- Amenities Chips -->
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <?php if (!empty($room->facilities) && is_array($room->facilities)): ?>
                                        <?php foreach (array_slice($room->facilities, 0, 3) as $fac): ?>
                                            <span class="text-[10px] bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-md border border-slate-200/60">
                                                <?= e($fac->name ?? (is_array($fac) ? ($fac['name'] ?? '') : $fac)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-[10px] bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-md border border-slate-200/60">AC</span>
                                        <span class="text-[10px] bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-md border border-slate-200/60">KM Dalam</span>
                                        <span class="text-[10px] bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-md border border-slate-200/60">WiFi</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="px-5 py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Harga Sewa</div>
                                <div class="text-sm font-black text-slate-900">
                                    Rp <?= number_format((float)($room->price ?? 1500000), 0, ',', '.') ?>
                                    <span class="text-[10px] font-normal text-slate-400">/bln</span>
                                </div>
                            </div>

                            <?php if ($room->status === 'available'): ?>
                                <a href="<?= url('admin/bookings/create?room_id=' . $room->id) ?>" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm inline-flex items-center gap-1 group-hover:scale-105 transform">
                                    Booking <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-xl bg-slate-200 text-slate-500 text-xs font-semibold">
                                    Disewa
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- 6 CORE PILLARS CAPABILITIES -->
    <section class="space-y-8 pt-4">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-blue-600 uppercase tracking-wider bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                <i data-lucide="layers" class="w-3.5 h-3.5"></i> Fitur Unggulan
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Arsitektur Lengkap dari Reservasi hingga Laporan Kas
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm">
                Dirancang spesifik untuk kosan Indonesia dengan akurasi finansial dan keamanan level produksi.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- 1. WhatsApp Automation -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-200 space-y-3 group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="message-circle" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Automasi WhatsApp (Fonnte API)</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pengingat tagihan otomatis (H-7, H-3, H-1, Hari-H, H+1). Invoice lunas tidak akan terganggu, template dinamis, dan retry queue aman tanpa mengekspos token API.
                </p>
            </div>

            <!-- 2. Booking & Kontrak Digital -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-200 space-y-3 group">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="file-check" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Booking & Kontrak Sewa Digital</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Alur reservasi kamar dengan validasi bentrok tanggal (anti-double booking). Penerbitan kontrak sewa resmi, perpanjangan sewa (extension), dan permohonan early checkout.
                </p>
            </div>

            <!-- 3. Billing & Payment Gateway Internal -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-indigo-300 transition-all duration-200 space-y-3 group">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="credit-card" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Penagihan & Verifikasi Pembayaran</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Dukungan Transfer Bank dan Tunai. Verifikasi struk transfer dengan validasi MIME, pencegahan alokasi ganda, pembayaran parsial, dan reversal transaksi aman.
                </p>
            </div>

            <!-- 4. Expense Tracker -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-rose-300 transition-all duration-200 space-y-3 group">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Pencatatan Biaya & Arus Kas Bersih</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pencatatan pengeluaran operasional (listrik, air, kebersihan, pemeliharaan) dengan kategori, vendor, dan struk nota. Terhubung ke penghitungan laba bersih Owner.
                </p>
            </div>

            <!-- 5. Maintenance Ticket System -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-amber-300 transition-all duration-200 space-y-3 group">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Tiket Kerusakan & Pemeliharaan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Penghuni dapat melaporkan kerusakan kamar beserta lampiran foto. Admin dapat memperbarui status penanganan, estimasi biaya, dan riwayat tindakan teknisi.
                </p>
            </div>

            <!-- 6. Executive Analytics & Export CSV -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-teal-300 transition-all duration-200 space-y-3 group">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Eksekutif Analitik & Ekspor CSV</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Visualisasi okupansi, pendapatan per kamar, arus kas, dan ekspor spreadsheet terenkripsi BOM UTF-8 yang kompatibel langsung dengan Microsoft Excel.
                </p>
            </div>
        </div>
    </section>

    <!-- LOADING SKELETON & UI SHIMMER SHOWCASE -->
    <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-indigo-600 uppercase tracking-wider bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">
                    <i data-lucide="activity" class="w-3.5 h-3.5"></i> UI Feedback States
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">
                    Loading Skeleton Shimmer Animation
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm">
                    Komponen skeleton reaktif yang otomatis memberikan feedback visual saat memuat data kamar, tagihan, dan laporan kas.
                </p>
            </div>
            <button type="button" onclick="toggleSkeletonDemo()" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition inline-flex items-center gap-2 active:scale-95 shadow-sm">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span id="skeleton-toggle-text">Toggle Loading Shimmer</span>
            </button>
        </div>

        <div id="skeleton-demo-box" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Skeleton Card 1 -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="h-6 w-24 rounded-lg skeleton"></div>
                    <div class="h-5 w-16 rounded-full skeleton"></div>
                </div>
                <div class="h-28 rounded-2xl skeleton"></div>
                <div class="space-y-2 pt-1">
                    <div class="h-4 w-3/4 rounded skeleton"></div>
                    <div class="h-3 w-1/2 rounded skeleton"></div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div class="h-5 w-20 rounded skeleton"></div>
                    <div class="h-7 w-20 rounded-xl skeleton"></div>
                </div>
            </div>

            <!-- Skeleton Card 2 -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="h-6 w-28 rounded-lg skeleton"></div>
                    <div class="h-5 w-14 rounded-full skeleton"></div>
                </div>
                <div class="h-28 rounded-2xl skeleton"></div>
                <div class="space-y-2 pt-1">
                    <div class="h-4 w-4/5 rounded skeleton"></div>
                    <div class="h-3 w-2/3 rounded skeleton"></div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div class="h-5 w-24 rounded skeleton"></div>
                    <div class="h-7 w-20 rounded-xl skeleton"></div>
                </div>
            </div>

            <!-- Skeleton Card 3 -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="h-6 w-20 rounded-lg skeleton"></div>
                    <div class="h-5 w-16 rounded-full skeleton"></div>
                </div>
                <div class="h-28 rounded-2xl skeleton"></div>
                <div class="space-y-2 pt-1">
                    <div class="h-4 w-2/3 rounded skeleton"></div>
                    <div class="h-3 w-1/3 rounded skeleton"></div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div class="h-5 w-20 rounded skeleton"></div>
                    <div class="h-7 w-20 rounded-xl skeleton"></div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function toggleSkeletonDemo() {
            const box = document.getElementById('skeleton-demo-box');
            const txt = document.getElementById('skeleton-toggle-text');
            if (!box) return;
            const skeletons = box.querySelectorAll('.skeleton');
            skeletons.forEach(s => {
                s.classList.toggle('opacity-30');
            });
            if (txt) {
                txt.textContent = txt.textContent.includes('Shimmer') ? 'Shimmer Aktif (Klik Reset)' : 'Toggle Loading Shimmer';
            }
        }
    </script>
</div>
