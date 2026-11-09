<?php
/** @var array $stats */
/** @var array $rooms */
/** @var string|null $role_zone */
?>

</div>

<!-- Full-Width Fluid Hero Section with Edge-to-Edge Obsidian Horizon -->
<div class="relative -mt-8 sm:-mt-12 overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white border-b border-slate-800/80 shadow-2xl">
    <!-- Ambient Multidimensional Aurora Glows -->
    <div class="absolute -top-40 left-1/4 w-[600px] h-[600px] bg-gradient-to-tr from-blue-600/30 via-indigo-500/20 to-teal-400/20 rounded-full blur-[130px] pointer-events-none animate-float"></div>
    <div class="absolute top-1/3 -right-20 w-[500px] h-[500px] bg-gradient-to-bl from-teal-500/20 via-emerald-600/15 to-transparent rounded-full blur-[120px] pointer-events-none animate-float" style="animation-delay: -3s;"></div>
    <div class="absolute inset-0 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.06] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-28 relative z-10 space-y-12">
        <div class="max-w-4xl mx-auto text-center space-y-7 animate-slide-up">
            
            <!-- Luxury Status Badge -->
            <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-white/[0.08] backdrop-blur-xl border border-white/15 text-xs font-extrabold text-teal-300 shadow-2xl hover:border-teal-400/40 transition">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-400"></span>
                </span>
                <span class="tracking-wide">Kost Management Monolith • High-Performance Edition</span>
            </div>

            <!-- Majestic Dynamic Headline -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight leading-[1.08] text-white">
                Otomasi Penuh Kos Modern <br class="hidden sm:inline" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-teal-300 to-emerald-400 drop-shadow-sm">
                    Tanpa Repot, Rapi, & Real-Time
                </span>
            </h1>

            <!-- Editorial Subtitle -->
            <p class="text-slate-300 text-base sm:text-xl max-w-2xl mx-auto leading-relaxed font-normal">
                Sistem pengelolaan kamar, otomatisasi penagihan WhatsApp Fonnte, pemantauan arus kas, serta portal mandiri penghuni dalam satu aplikasi terpadu.
            </p>

            <!-- Action CTA Cluster -->
            <div class="pt-2 flex flex-wrap items-center justify-center gap-4">
                <?php if (auth_check()): ?>
                    <?php $u = auth_user(); ?>
                    <?php if (strtolower($u['role'] ?? '') === 'owner'): ?>
                        <a href="<?= url('owner/dashboard') ?>" class="px-8 py-4 rounded-2xl font-black text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-xl shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2.5">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Buka Executive Dashboard
                        </a>
                    <?php elseif (strtolower($u['role'] ?? '') === 'admin'): ?>
                        <a href="<?= url('admin/dashboard') ?>" class="px-8 py-4 rounded-2xl font-black text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-xl shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2.5">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Buka Admin Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= url('tenant/dashboard') ?>" class="px-8 py-4 rounded-2xl font-black text-sm bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white shadow-xl shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2.5">
                            <i data-lucide="home" class="w-4 h-4"></i> Buka Portal Penghuni
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="#kamar-kost" class="px-8 py-4 rounded-2xl font-black text-sm bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:opacity-95 text-white shadow-2xl shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-2.5">
                        <i data-lucide="door-closed" class="w-4 h-4 text-amber-300"></i> Jelajahi Kamar Siap Huni
                    </a>
                    <a href="<?= url('login') ?>" class="px-7 py-4 rounded-2xl font-bold text-sm bg-white/10 hover:bg-white/15 backdrop-blur-xl border border-white/20 text-white transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2 shadow-lg">
                        <i data-lucide="log-in" class="w-4 h-4 text-slate-300"></i> Masuk ke Portal
                    </a>
                <?php endif; ?>

                <a href="#alur-sewa" class="px-7 py-4 rounded-2xl font-bold text-sm bg-white/5 hover:bg-white/10 backdrop-blur-xl border border-white/10 text-slate-300 hover:text-white transition-all inline-flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4 text-teal-400"></i> Alur Sewa Praktis
                </a>
            </div>
        </div>

        <!-- Floating Glassmorphic Metric Matrix (Full-Width Responsive) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 pt-6">
            <!-- Card 1 -->
            <div class="bg-slate-900/80 backdrop-blur-2xl border border-white/10 rounded-3xl p-5 sm:p-6 hover:border-blue-500/50 hover:bg-slate-900/95 transition-all duration-300 shadow-xl group">
                <div class="flex items-center justify-between text-slate-400 text-xs mb-3 font-semibold">
                    <span>Total Kamar</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="door-closed" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-white">
                    <?= (int)($stats['total_rooms'] ?? 8) ?> <span class="text-xs font-normal text-slate-400">Unit</span>
                </div>
                <div class="text-[11px] text-teal-300 mt-2 flex items-center gap-1.5 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span> Siap dikelola otomatis
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-slate-900/80 backdrop-blur-2xl border border-white/10 rounded-3xl p-5 sm:p-6 hover:border-emerald-500/50 hover:bg-slate-900/95 transition-all duration-300 shadow-xl group">
                <div class="flex items-center justify-between text-slate-400 text-xs mb-3 font-semibold">
                    <span>Okupansi Aktif</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="pie-chart" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-emerald-400">
                    <?= $stats['occupancy_rate'] ?? 12.5 ?>%
                </div>
                <div class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <?= (int)($stats['occupied'] ?? 1) ?> Kamar Terisi
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-slate-900/80 backdrop-blur-2xl border border-white/10 rounded-3xl p-5 sm:p-6 hover:border-teal-500/50 hover:bg-slate-900/95 transition-all duration-300 shadow-xl group">
                <div class="flex items-center justify-between text-slate-400 text-xs mb-3 font-semibold">
                    <span>Automasi WhatsApp</span>
                    <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-white flex items-center gap-2">
                    Fonnte
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Aktif
                    </span>
                </div>
                <div class="text-[11px] text-slate-400 mt-2 font-medium">
                    Jadwal H-7, H-3, H-1, Hari-H, H+1
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-slate-900/80 backdrop-blur-2xl border border-white/10 rounded-3xl p-5 sm:p-6 hover:border-indigo-500/50 hover:bg-slate-900/95 transition-all duration-300 shadow-xl group">
                <div class="flex items-center justify-between text-slate-400 text-xs mb-3 font-semibold">
                    <span>Laporan Arus Kas</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-white">
                    CSV <span class="text-xs font-normal text-slate-400">Export</span>
                </div>
                <div class="text-[11px] text-teal-300 mt-2 font-medium">
                    BOM UTF-8 Excel Ready
                </div>
            </div>
        </div>

    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-20">

    <!-- SECTION 1: 3 LANGKAH ALUR SEWA PRAKTIS -->
    <section id="alur-sewa" class="space-y-8 reveal-on-scroll">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-blue-600 uppercase tracking-wider bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Alur Reservasi Praktis
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Hunian Nyaman dalam 3 Langkah Mudah
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm">
                Seluruh proses mulai dari pemilihan kamar, kontrak resmi, hingga pembayaran dikelola secara digital dan transparan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
            <!-- STEP 1 -->
            <div class="p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-blue-400 transition-all duration-300 relative group reveal-on-scroll stagger-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="w-10 h-10 rounded-2xl bg-blue-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md shadow-blue-500/25">
                            01
                        </span>
                        <span class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-100">
                            Pilih Unit
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Eksplorasi Kamar Siap Huni</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Tinjau ketersediaan kamar secara real-time, foto interior asli, fasilitas lengkap (AC, Kamar Mandi Dalam, Springbed), dan harga sewa transparan tanpa biaya tersembunyi.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Foto Asli</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">AC Dingin</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">WiFi 100 Mbps</span>
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-teal-400 transition-all duration-300 relative group reveal-on-scroll stagger-2 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="w-10 h-10 rounded-2xl bg-teal-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md shadow-teal-500/25">
                            02
                        </span>
                        <span class="text-[11px] font-bold text-teal-600 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-100">
                            Kontrak Resmi
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Booking & Kontrak Digital</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Reservasi kamar aman dengan validasi anti-bentrok tanggal. Kontrak sewa resmi diterbitkan otomatis lengkap dengan masa aktif dan tanggal tagihan rutin.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Anti-Double Booking</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Kontrak Otomatis</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Deposit Terdata</span>
                </div>
            </div>

            <!-- STEP 3 -->
            <div class="p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-indigo-400 transition-all duration-300 relative group reveal-on-scroll stagger-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="w-10 h-10 rounded-2xl bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md shadow-indigo-500/25">
                            03
                        </span>
                        <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100">
                            Tinggal Nyaman
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Portal Mandiri & Notifikasi WA</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Penghuni menikmati portal khusus untuk cek tagihan, unggah bukti transfer kamera, ajukan perpanjangan sewa, dan lapor kendala kamar via tiket teknisi.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">WhatsApp Reminder</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Lapor Kerusakan Foto</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Konfirmasi Cepat</span>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: STANDAR FASILITAS UNGGULAN -->
    <section class="space-y-6 pt-2 reveal-on-scroll">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-teal-600 uppercase tracking-wider bg-teal-50 px-3 py-1 rounded-full border border-teal-100">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Standar Kenyamanan Maksimal
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Fasilitas Terintegrasi untuk Ketenangan Tinggal
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm">
                Semua unit kos dirawat secara berkala untuk menjamin kenyamanan profesional muda dan mahasiswa.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
            <!-- 1. WiFi -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-teal-300 transition-all space-y-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold group-hover:scale-105 transition-transform">
                    <i data-lucide="wifi" class="w-5 h-5"></i>
                </div>
                <h4 class="font-bold text-slate-900 text-sm">Internet Fiber 100 Mbps</h4>
                <p class="text-[11px] text-slate-500 leading-relaxed">Unlimited kecepatan tinggi tanpa buffering untuk meeting, streaming, & gaming.</p>
            </div>

            <!-- 2. Keamanan -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-300 transition-all space-y-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold group-hover:scale-105 transition-transform">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <h4 class="font-bold text-slate-900 text-sm">Keamanan & CCTV 24 Jam</h4>
                <p class="text-[11px] text-slate-500 leading-relaxed">Akses gerbang kartu/kunci aman dengan pengawasan kamera CCTV di seluruh koridor.</p>
            </div>

            <!-- 3. Kamar Mandi & Water Heater -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-300 transition-all space-y-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold group-hover:scale-105 transition-transform">
                    <i data-lucide="droplets" class="w-5 h-5"></i>
                </div>
                <h4 class="font-bold text-slate-900 text-sm">Water Heater & KM Dalam</h4>
                <p class="text-[11px] text-slate-500 leading-relaxed">Air hangat instan, kloset duduk higienis, dan sirkulasi udara kamar mandi lancar.</p>
            </div>

            <!-- 4. Area Bersama & Kebersihan -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-300 transition-all space-y-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold group-hover:scale-105 transition-transform">
                    <i data-lucide="sparkle" class="w-5 h-5"></i>
                </div>
                <h4 class="font-bold text-slate-900 text-sm">Dapur & Laundry Bersama</h4>
                <p class="text-[11px] text-slate-500 leading-relaxed">Dilengkapi kompor gas, kulkas komunal, dispenser air minum, dan area jemur terlindung.</p>
            </div>
        </div>
    </section>

    <!-- ROOM INVENTORY & SHOWCASE SECTION -->
    <?php if (!empty($rooms)): ?>
        <section id="kamar-kost" class="space-y-6 pt-4 reveal-on-scroll">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-teal-600 uppercase tracking-wider bg-teal-50 px-3 py-1 rounded-full border border-teal-100">
                        <i data-lucide="door-closed" class="w-3.5 h-3.5"></i> Inventaris Kamar
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                        Daftar Kamar & Ketersediaan
                    </h2>
                    <p class="text-slate-500 text-xs sm:text-sm">
                        Kamar kost eksklusif siap huni dengan fasilitas lengkap, foto interior, dan harga transparan.
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

            <!-- Room Grid: High-res Card Aesthetics with Architectural Gradients & Lazy Loaded Photos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach (array_slice($rooms, 0, 8) as $idx => $room): ?>
                    <?php $stagger = ($idx % 4) + 1; ?>
                    <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 flex flex-col justify-between group reveal-on-scroll stagger-<?= $stagger ?>">
                        <div>
                            <!-- Visual Banner with Lazy Loaded Room Photo & Shimmer Skeleton -->
                            <div class="h-36 relative overflow-hidden bg-slate-900 skeleton">
                                <img src="https://images.unsplash.com/photo-<?= (((int)$room->room_number % 2) === 0) ? '1522771739844-6a9f6d5f14af' : '1595526114035-0d45ed16cfbf' ?>?auto=format&fit=crop&w=500&q=80"
                                    loading="lazy"
                                    alt="Kamar <?= e($room->room_number) ?>"
                                    class="w-full h-full object-cover opacity-0 transition-all duration-500 relative z-10 group-hover:scale-105"
                                    onload="this.parentElement.classList.remove('skeleton'); this.classList.remove('opacity-0'); this.classList.add('opacity-80');" />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent z-20 pointer-events-none"></div>
                                <div class="absolute inset-0 p-4 flex flex-col justify-between z-30 pointer-events-none">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-black tracking-wider bg-white/20 backdrop-blur-md text-white border border-white/25 shadow-sm">
                                            KAMAR <?= e($room->room_number) ?>
                                        </span>
                                        <?php if ($room->status === 'available'): ?>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-300 bg-emerald-950/70 backdrop-blur-md px-2.5 py-1 rounded-full border border-emerald-400/40 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Siap Huni
                                            </span>
                                        <?php elseif ($room->status === 'occupied'): ?>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-300 bg-blue-950/70 backdrop-blur-md px-2.5 py-1 rounded-full border border-blue-400/40 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Terisi
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-300 bg-amber-950/70 backdrop-blur-md px-2.5 py-1 rounded-full border border-amber-400/40 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Maintenance
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-white text-xs font-semibold flex items-center gap-2 drop-shadow">
                                        <i data-lucide="bed" class="w-4 h-4 text-teal-300"></i>
                                        <span>Lantai <?= (int)($room->floor ?? 1) ?> • Deluxe Single</span>
                                    </div>
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
    <section class="space-y-8 pt-4 reveal-on-scroll">
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
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 space-y-3 group reveal-on-scroll stagger-1">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="message-circle" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Automasi WhatsApp (Fonnte API)</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pengingat tagihan otomatis (H-7, H-3, H-1, Hari-H, H+1). Invoice lunas tidak akan terganggu, template dinamis, dan retry queue aman tanpa mengekspos token API.
                </p>
            </div>

            <!-- 2. Booking & Kontrak Digital -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 space-y-3 group reveal-on-scroll stagger-2">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="file-check" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Booking & Kontrak Sewa Digital</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Alur reservasi kamar dengan validasi bentrok tanggal (anti-double booking). Penerbitan kontrak sewa resmi, perpanjangan sewa (extension), dan permohonan early checkout.
                </p>
            </div>

            <!-- 3. Billing & Payment Gateway Internal -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-indigo-300 transition-all duration-300 space-y-3 group reveal-on-scroll stagger-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="credit-card" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Penagihan & Verifikasi Pembayaran</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Dukungan Transfer Bank dan Tunai. Verifikasi struk transfer dengan validasi MIME, pencegahan alokasi ganda, pembayaran parsial, dan reversal transaksi aman.
                </p>
            </div>

            <!-- 4. Expense Tracker -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-rose-300 transition-all duration-300 space-y-3 group reveal-on-scroll stagger-1">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Pencatatan Biaya & Arus Kas Bersih</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pencatatan pengeluaran operasional (listrik, air, kebersihan, pemeliharaan) dengan kategori, vendor, dan struk nota. Terhubung ke penghitungan laba bersih Owner.
                </p>
            </div>

            <!-- 5. Maintenance Ticket System -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-amber-300 transition-all duration-300 space-y-3 group reveal-on-scroll stagger-2">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Tiket Kerusakan & Pemeliharaan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Penghuni dapat melaporkan kerusakan kamar beserta lampiran foto. Admin dapat memperbarui status penanganan, estimasi biaya, dan riwayat tindakan teknisi.
                </p>
            </div>

            <!-- 6. Executive Analytics & Export CSV -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-teal-300 transition-all duration-300 space-y-3 group reveal-on-scroll stagger-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center font-bold shadow-sm group-hover:scale-110 transition-transform">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base">Eksekutif Analitik & Ekspor CSV</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Visualisasi okupansi, pendapatan per kamar, arus kas, dan ekspor spreadsheet terenkripsi BOM UTF-8 yang kompatibel langsung dengan Microsoft Excel.
                </p>
            </div>
        </div>

        <!-- Luxury Bottom Call-to-Action Section -->
        <div class="mt-12 rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white p-8 sm:p-12 relative overflow-hidden border border-slate-800 shadow-2xl reveal-on-scroll">
            <!-- Ambient Glow Blobs -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl mx-auto text-center space-y-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-white/10 text-teal-300 border border-white/15 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                    Ekosistem Manajemen Kos Modern
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Kelola Properti Kos Lebih Cerdas, Rapi, & Real-Time
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl mx-auto">
                    Mulai dari pencatatan sewa, verifikasi pembayaran struk bank, pelaporan kerusakan kamar, hingga laporan keuangan laba/rugi eksekutif dalam satu platform terpadu.
                </p>

                <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                    <a href="#kamar-kost" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-xs sm:text-sm text-slate-950 bg-gradient-to-r from-teal-300 to-emerald-400 hover:opacity-95 shadow-lg shadow-teal-500/25 transition transform active:scale-95">
                        <i data-lucide="door-closed" class="w-4 h-4"></i>
                        <span>Lihat Kamar Tersedia</span>
                    </a>
                    <a href="<?= url('login') ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-white/10 hover:bg-white/15 border border-white/20 backdrop-blur-md transition active:scale-95">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Masuk ke Portal</span>
                    </a>
                </div>

                <!-- Feature Pillars Micro Badges -->
                <div class="pt-6 border-t border-white/10 grid grid-cols-2 sm:grid-cols-4 gap-3 text-left">
                    <div class="flex items-center gap-2 text-xs text-slate-300">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                        <span class="truncate">Validasi Role Ketat</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-300">
                        <i data-lucide="message-square" class="w-4 h-4 text-teal-400 shrink-0"></i>
                        <span class="truncate">WhatsApp Fonnte API</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-300">
                        <i data-lucide="calculator" class="w-4 h-4 text-blue-400 shrink-0"></i>
                        <span class="truncate">Arus Kas & Laba Bersih</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-300">
                        <i data-lucide="download" class="w-4 h-4 text-amber-400 shrink-0"></i>
                        <span class="truncate">Ekspor BOM UTF-8 CSV</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
