<?php
/** @var array $stats */
/** @var array $rooms */
/** @var string|null $role_zone */
?>

</div>

<!-- Full-Width Fluid Hero Section with Edge-to-Edge Obsidian Horizon -->
<div class="relative -mt-8 sm:-mt-12 overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white border-b border-slate-800/80 shadow-2xl" id="hero-interactive-zone">
    <!-- Ambient Multidimensional Aurora Glows -->
    <div class="absolute -top-40 left-10 w-[550px] h-[550px] bg-gradient-to-tr from-blue-600/25 via-indigo-500/15 to-teal-400/20 rounded-full blur-[130px] pointer-events-none animate-float"></div>
    <div class="absolute top-1/4 right-0 w-[550px] h-[550px] bg-gradient-to-bl from-teal-500/20 via-emerald-600/15 to-indigo-600/10 rounded-full blur-[130px] pointer-events-none animate-float" style="animation-delay: -3.5s;"></div>
    <div class="absolute inset-0 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.05] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 lg:py-24 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            
            <!-- LEFT COLUMN: Left-Aligned Editorial Headlines & CTAs -->
            <div class="lg:col-span-7 space-y-7 animate-slide-up text-left">
                <!-- Luxury Status Badge -->
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/[0.08] backdrop-blur-xl border border-white/15 text-xs font-extrabold text-teal-300 shadow-xl hover:border-teal-400/40 transition">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-400"></span>
                    </span>
                    <span class="tracking-wide"><?= e(setting('landing_hero_badge', 'Kost Management Monolith • High-Performance Edition')) ?></span>
                </div>

                <!-- Majestic Dynamic Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.1] text-white">
                    <?= nl2br(e(setting('landing_hero_title', "Otomasi Penuh Kos Modern\nTanpa Repot, Rapi, & Real-Time"))) ?>
                </h1>

                <!-- Editorial Subtitle -->
                <p class="text-slate-300 text-sm sm:text-base lg:text-lg max-w-xl leading-relaxed font-normal">
                    <?= e(setting('landing_hero_subtitle', 'Platform operasional kos lengkap: otomatisasi tagihan WhatsApp Fonnte, pemantauan hunian terintegrasi, pembukuan arus kas bersih, serta portal mandiri bagi seluruh penghuni.')) ?>
                </p>

                <!-- Value Props Micro-List -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1 text-xs text-slate-300 font-medium">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center shrink-0">
                            <i data-lucide="check" class="w-3 h-3"></i>
                        </div>
                        <span>Pengingat WhatsApp Otomatis (H-7 s/d H+1)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-teal-500/20 text-teal-400 border border-teal-500/40 flex items-center justify-center shrink-0">
                            <i data-lucide="check" class="w-3 h-3"></i>
                        </div>
                        <span>Anti-Bentrok Jadwal Reservasi Kamar</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/40 flex items-center justify-center shrink-0">
                            <i data-lucide="check" class="w-3 h-3"></i>
                        </div>
                        <span>Portal Penghuni Mandiri (Bayar & Perbaikan)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/40 flex items-center justify-center shrink-0">
                            <i data-lucide="check" class="w-3 h-3"></i>
                        </div>
                        <span>Arus Kas & Laba Bersih Khusus Owner</span>
                    </div>
                </div>

                <!-- Action CTA Cluster -->
                <div class="pt-3 flex flex-wrap items-center gap-3.5">
                    <?php if (auth_check()): ?>
                        <?php $u = auth_user(); ?>
                        <?php if (strtolower($u['role'] ?? '') === 'owner'): ?>
                            <a href="<?= url('owner/dashboard') ?>" class="px-7 py-3.5 rounded-2xl font-black text-xs sm:text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-xl shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Buka Executive Dashboard
                            </a>
                        <?php elseif (strtolower($u['role'] ?? '') === 'admin'): ?>
                            <a href="<?= url('admin/dashboard') ?>" class="px-7 py-3.5 rounded-2xl font-black text-xs sm:text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-xl shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Buka Admin Dashboard
                            </a>
                        <?php else: ?>
                            <a href="<?= url('tenant/dashboard') ?>" class="px-7 py-3.5 rounded-2xl font-black text-xs sm:text-sm bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white shadow-xl shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                                <i data-lucide="home" class="w-4 h-4"></i> Buka Portal Penghuni
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="#kamar-kost" class="px-7 py-3.5 rounded-2xl font-black text-xs sm:text-sm bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:opacity-95 text-white shadow-2xl shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 active:scale-95 inline-flex items-center gap-2">
                            <i data-lucide="door-closed" class="w-4 h-4 text-amber-300"></i> Jelajahi Kamar Siap Huni
                        </a>
                        <a href="<?= url('login') ?>" class="px-6 py-3.5 rounded-2xl font-bold text-xs sm:text-sm bg-white/10 hover:bg-white/15 backdrop-blur-xl border border-white/20 text-white transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2 shadow-lg">
                            <i data-lucide="log-in" class="w-4 h-4 text-slate-300"></i> Masuk ke Portal
                        </a>
                    <?php endif; ?>

                    <a href="#alur-sewa" class="px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm bg-white/5 hover:bg-white/10 backdrop-blur-xl border border-white/10 text-slate-300 hover:text-white transition-all inline-flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4 text-teal-400"></i> Alur Sewa
                    </a>
                </div>
            </div>

            <!-- RIGHT COLUMN: Interactive 3D Canvas Smart Kosan Architecture -->
            <div class="lg:col-span-5 relative flex items-center justify-center">
                <!-- Glowing Ambient Backdrop Box -->
                <div class="absolute inset-0 bg-gradient-to-tr from-teal-500/10 via-indigo-500/10 to-blue-500/5 rounded-3xl blur-2xl pointer-events-none"></div>

                <!-- 3D Canvas Frame -->
                <div class="relative w-full aspect-square max-w-[460px] lg:max-w-[500px] rounded-3xl bg-slate-950/60 backdrop-blur-2xl border border-slate-800/80 shadow-2xl overflow-hidden group">
                    
                    <!-- 3D Interactive Canvas -->
                    <canvas id="kosan-canvas-3d" class="w-full h-full block cursor-grab active:cursor-grabbing"></canvas>

                    <!-- Floating Ambient HUD Cards -->
                    <!-- HUD Top Right: Active Room -->
                    <div class="absolute top-4 right-4 px-3 py-1.5 rounded-xl bg-slate-900/90 backdrop-blur-md border border-slate-700/80 text-[11px] font-semibold text-slate-200 shadow-xl flex items-center gap-2 pointer-events-none animate-float">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Unit 101 &bull; Smart Living Aktif</span>
                    </div>

                    <!-- HUD Bottom Left: CCTV Security -->
                    <div class="absolute bottom-4 left-4 px-3 py-1.5 rounded-xl bg-slate-900/90 backdrop-blur-md border border-slate-700/80 text-[11px] font-semibold text-slate-200 shadow-xl flex items-center gap-2 pointer-events-none animate-float" style="animation-delay: -2s;">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-teal-400"></i>
                        <span>CCTV & Keamanan 24 Jam</span>
                    </div>

                    <!-- HUD Micro Instruction Pill -->
                    <div class="absolute bottom-4 right-4 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[10px] text-slate-400 pointer-events-none flex items-center gap-1">
                        <i data-lucide="rotate-3d" class="w-3 h-3 text-teal-400"></i>
                        <span>Gerakkan kursor</span>
                    </div>
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

    <!-- ROOM INVENTORY & SHOWCASE SECTION (UI/UX PRO MAX) -->
    <?php if (!empty($rooms)): ?>
        <section id="kamar-kost" class="space-y-6 pt-6 reveal-on-scroll">
            <!-- Section Header & Filter Controls -->
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 pb-2">
                <div>
                    <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-blue-600 uppercase tracking-wider bg-blue-50 px-3 py-1 rounded-full border border-blue-200 shadow-xs">
                        <i data-lucide="door-closed" class="w-3.5 h-3.5 text-blue-600"></i>
                        <span>Inventaris Kamar & Ketersediaan</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-2">
                        Pilihan Unit Kamar Siap Huni
                    </h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1 max-w-xl">
                        Kamar kost eksklusif dengan pencahayaan alami, interior modern berfurnitur lengkap, dan transparansi tarif tanpa biaya tersembunyi.
                    </p>
                </div>

                <!-- Interactive Filter Tabs (Mobile-Friendly Horizontal Swipe) -->
                <div class="flex items-center gap-2 bg-slate-100/90 p-1 rounded-2xl border border-slate-200/80 shadow-xs text-xs font-bold overflow-x-auto no-scrollbar max-w-full">
                    <button type="button" onclick="filterRoomInventory('all')" id="btn-filter-all" class="room-filter-btn active px-3.5 py-1.5 rounded-xl bg-white text-slate-900 shadow-xs border border-slate-200 transition cursor-pointer whitespace-nowrap shrink-0">
                        Semua Unit (<?= count($rooms) ?>)
                    </button>
                    <button type="button" onclick="filterRoomInventory('floor-1')" id="btn-filter-floor-1" class="room-filter-btn px-3.5 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 transition cursor-pointer whitespace-nowrap shrink-0">
                        Lantai 1
                    </button>
                    <button type="button" onclick="filterRoomInventory('floor-2')" id="btn-filter-floor-2" class="room-filter-btn px-3.5 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 transition cursor-pointer whitespace-nowrap shrink-0">
                        Lantai 2
                    </button>
                    <button type="button" onclick="filterRoomInventory('available')" id="btn-filter-available" class="room-filter-btn px-3.5 py-1.5 rounded-xl text-emerald-700 hover:text-emerald-900 transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap shrink-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Tersedia
                    </button>
                </div>
            </div>

            <!-- Room Grid -->
            <div id="room-cards-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($rooms as $index => $room): ?>
                    <?php
                        $rNum = (string)$room->room_number;
                        $floor = (int)($room->floor ?? (str_starts_with($rNum, '2') ? 2 : 1));
                        $isAvailable = ($room->status ?? 'available') === 'available';

                        // Dynamic room specs from Database
                        $typeName = $room->name;
                        $size = !empty($room->size) ? $room->size : '3x4 m²';
                        
                        // Dynamic facility list from room assignments
                        $facilityList = !empty($room->facilities) 
                            ? array_column($room->facilities, 'name') 
                            : ['AC 1/2 PK', 'KM Dalam', 'WiFi Fiber', 'Kasur Springbed'];

                        // Dynamic badge style based on tier / price
                        $badgeStyle = 'bg-blue-500/20 text-blue-200 border-blue-400/30';
                        if (stripos($room->name, 'Executive') !== false || $room->current_price >= 2000000) {
                            $badgeStyle = 'bg-amber-500/20 text-amber-200 border-amber-400/30';
                        } elseif (stripos($room->name, 'VIP') !== false || $room->current_price >= 1800000) {
                            $badgeStyle = 'bg-purple-500/20 text-purple-200 border-purple-400/30';
                        } elseif (stripos($room->name, 'Deluxe') !== false || $room->current_price >= 1650000) {
                            $badgeStyle = 'bg-indigo-500/20 text-indigo-200 border-indigo-400/30';
                        }

                        // Local/uploaded photo with graceful fallback
                        $photoUrl = $room->photo_url 
                            ? url($room->photo_url) 
                            : (file_exists(BASE_PATH . "/public/images/rooms/room-{$rNum}.jpg") 
                                ? url("images/rooms/room-{$rNum}.jpg") 
                                : 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80');
                    ?>
                    <div class="room-inventory-card bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-2xl hover:border-blue-400/70 transition-all duration-300 overflow-hidden flex flex-col justify-between group reveal-on-scroll stagger-<?= ($index % 4) + 1 ?> h-full"
                         data-floor="<?= $floor ?>"
                         data-status="<?= $isAvailable ? 'available' : 'occupied' ?>">

                        <!-- Image Showcase Header (Strict Uniform 3:2 Aspect Ratio & Center Cropped) -->
                        <div class="relative aspect-[3/2] w-full overflow-hidden bg-slate-100 skeleton shrink-0">
                            <img src="<?= e($photoUrl) ?>" 
                                 alt="Kamar <?= e($rNum) ?> - <?= e($typeName) ?>" 
                                 loading="lazy"
                                 onload="this.classList.remove('opacity-0'); this.closest('.skeleton')?.classList.remove('skeleton')"
                                 class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-108 opacity-0">

                            <!-- Top Gradient for Header Contrast -->
                            <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-slate-950/70 to-transparent pointer-events-none"></div>

                            <!-- Bottom Gradient for Overlay Contrast -->
                            <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent pointer-events-none"></div>

                            <!-- Floating Room Number Badge (Top Left) -->
                            <div class="absolute top-3 left-3 bg-slate-950/75 backdrop-blur-md border border-white/20 text-white text-[11px] font-black px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5">
                                <i data-lucide="key" class="w-3 h-3 text-amber-400"></i>
                                <span>KAMAR <?= e($rNum) ?></span>
                            </div>

                            <!-- Availability Badge (Top Right) -->
                            <div class="absolute top-3 right-3">
                                <?php if ($isAvailable): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500/90 backdrop-blur-md text-white border border-emerald-300 shadow-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Tersedia
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-900/80 backdrop-blur-md text-slate-300 border border-white/10 shadow-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                        Disewa
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Room Type & Floor Tag (Bottom Left Overlay) -->
                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider backdrop-blur-md border <?= $badgeStyle ?>">
                                    <?= e($typeName) ?>
                                </span>
                                <span class="text-[11px] font-medium text-slate-200 flex items-center gap-1">
                                    <i data-lucide="building" class="w-3 h-3 text-slate-300"></i>
                                    Lantai <?= $floor ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-bold text-slate-900 text-sm group-hover:text-blue-600 transition-colors">
                                        <?= e($room->name) ?>
                                    </h3>
                                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0 flex items-center gap-1">
                                        <i data-lucide="maximize-2" class="w-3 h-3 text-slate-400"></i>
                                        <?= e($size) ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed">
                                    <?= e($room->description ?: 'Kamar siap huni dengan ventilasi alami, lingkungan tenang, dan fasilitas lengkap.') ?>
                                </p>
                            </div>

                            <!-- Facilities Chips -->
                            <div class="pt-3 border-t border-slate-100">
                                <div class="flex flex-wrap gap-1.5">
                                    <?php foreach ($facilityList as $fac): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-50 text-slate-600 border border-slate-200/80">
                                            <i data-lucide="check" class="w-2.5 h-2.5 text-emerald-500"></i>
                                            <?= e($fac) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Price & Action Footer -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none">Tarif Sewa</div>
                                    <div class="text-base sm:text-lg font-black text-slate-900 tracking-tight font-mono mt-0.5">
                                        <?= $room->formattedPrice() ?><span class="text-[11px] font-semibold text-slate-400">/bln</span>
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    <?php if ($isAvailable): ?>
                                        <?php
                                            $bizPhone = preg_replace('/[^0-9]/', '', (string)setting('business_phone', '081234567890'));
                                            if (str_starts_with($bizPhone, '0')) $bizPhone = '62' . substr($bizPhone, 1);
                                            $bizName = setting('business_name', 'Kost Management');
                                            $waMsg = "Halo Pengelola {$bizName}, saya tertarik untuk booking / survei Kamar {$rNum} ({$typeName}) seharga {$room->formattedPrice()}/bln. Apakah unit masih tersedia?";
                                            $waBookingUrl = "https://wa.me/{$bizPhone}?text=" . rawurlencode($waMsg);
                                        ?>
                                        <?php if (auth_check() && in_array(strtolower(auth_user()['role'] ?? ''), ['owner', 'admin'], true)): ?>
                                            <a href="<?= url('admin/bookings/create?room_id=' . $room->id) ?>" 
                                               class="inline-flex items-center gap-1.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow-md shadow-blue-500/20 hover:shadow-lg transition transform active:scale-95">
                                                <span>Booking Unit</span>
                                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= e($waBookingUrl) ?>" target="_blank"
                                               class="inline-flex items-center gap-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow-md shadow-emerald-500/20 hover:shadow-lg transition transform active:scale-95">
                                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                                <span>Pesan via WA</span>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed">
                                            <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>Terisi</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Room Filter Client-Side Script -->
        <script>
        function filterRoomInventory(filter) {
            const cards = document.querySelectorAll('.room-inventory-card');
            const buttons = document.querySelectorAll('.room-filter-btn');

            // Toggle active pill button styling
            buttons.forEach(btn => {
                btn.classList.remove('active', 'bg-white', 'text-slate-900', 'shadow-xs', 'border', 'border-slate-200');
                btn.classList.add('text-slate-600');
            });

            const activeBtn = document.getElementById('btn-filter-' + filter);
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-white', 'text-slate-900', 'shadow-xs', 'border', 'border-slate-200');
                activeBtn.classList.remove('text-slate-600');
            }

            // Filter cards with smooth micro-animation
            cards.forEach(card => {
                const floor = card.getAttribute('data-floor');
                const status = card.getAttribute('data-status');
                let match = false;

                if (filter === 'all') match = true;
                else if (filter === 'floor-1' && floor === '1') match = true;
                else if (filter === 'floor-2' && floor === '2') match = true;
                else if (filter === 'available' && status === 'available') match = true;

                if (match) {
                    card.style.display = 'flex';
                    card.classList.add('reveal-on-scroll', 'is-revealed');
                } else {
                    card.style.display = 'none';
                }
            });
        }
        </script>
    <?php endif; ?>

    <!-- ARCHITECTURE & OPERATIONAL OVERVIEW SECTION -->
    <section class="space-y-6 pt-4 reveal-on-scroll">
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

        <!-- TATA TERTIB & ATURAN HUNIAN KOS (HOUSE RULES) -->
        <div id="aturan-kos" class="mt-14 space-y-6 reveal-on-scroll">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-indigo-600 uppercase tracking-wider bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200 shadow-xs">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-indigo-600"></i>
                    <span>Kenyamanan & Keamanan Bersama</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Tata Tertib & Aturan Tinggal di Kos
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm">
                    Dirancang demi menjaga privasi, ketenangan waktu istirahat, kebersihan, dan keselamatan seluruh penghuni kos.
                </p>
            </div>

            <!-- Rules Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 1. Jam Tenang & Tamu -->
                <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-indigo-300 transition-all duration-300 space-y-3 group flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center font-bold group-hover:scale-110 transition-transform shadow-xs">
                                <i data-lucide="moon" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Jam Tenang
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Jam Tenang & Kunjungan Tamu</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Jam tenang berlaku pukul <strong>22.00 - 06.00 WIB</strong> untuk istirahat optimal. Tamu dipersilakan diterima di ruang tamu komunal dan mohon kembali sebelum pukul 22.00 WIB.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] font-semibold text-slate-600">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-indigo-500 shrink-0"></i>
                        <span>Akses mandiri 24 jam untuk penghuni</span>
                    </div>
                </div>

                <!-- 2. Keamanan & Kunci Akses -->
                <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 space-y-3 group flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold group-hover:scale-110 transition-transform shadow-xs">
                                <i data-lucide="key-round" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                Keamanan
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Keamanan & Kunci Akses</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Pintu utama dilengkapi <strong>Smart Access Lock 24 Jam</strong> dan pantauan kamera CCTV 24/7 di area publik serta parkiran. Kunci kamar bersifat pribadi dan dilarang dipinjamkan.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] font-semibold text-slate-600">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-blue-500 shrink-0"></i>
                        <span>CCTV 24/7 di koridor & parkiran</span>
                    </div>
                </div>

                <!-- 3. Kebersihan & Area Bersama -->
                <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 space-y-3 group flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold group-hover:scale-110 transition-transform shadow-xs">
                                <i data-lucide="sparkles" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Kebersihan
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Kebersihan & Dapur Bersama</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Area bersama (dapur, dispenser, kulkas bersama, dan jemuran) wajib dirapikan dan dibersihkan kembali setelah digunakan. Sampah kamar diikat rapi dan diletakkan di tempat sampah utama.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] font-semibold text-slate-600">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i>
                        <span>Pembuangan sampah rutin setiap pagi</span>
                    </div>
                </div>

                <!-- 4. Pembayaran & Notice Period -->
                <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-amber-300 transition-all duration-300 space-y-3 group flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold group-hover:scale-110 transition-transform shadow-xs">
                                <i data-lucide="calendar-check" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                Pembayaran
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Sewa & Pemberitahuan Checkout</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Sewa bulanan dibayarkan tepat waktu sebelum tanggal jatuh tempo (*billing anchor*). Jika berencana selesai sewa / keluar, mohon konfirmasi ke pengelola minimal <strong>H-14 sebelum tanggal tagihan</strong>.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] font-semibold text-slate-600">
                        <i data-lucide="message-square" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                        <span>Pengingat WhatsApp H-7, H-3, & Hari-H</span>
                    </div>
                </div>

                <!-- 5. Hemat Energi & Fasilitas Listrik -->
                <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-teal-300 transition-all duration-300 space-y-3 group flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center font-bold group-hover:scale-110 transition-transform shadow-xs">
                                <i data-lucide="zap" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200">
                                Fasilitas
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Penggunaan Listrik & Elektronik</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Mohon matikan AC, lampu, dan peralatan elektronik saat meninggalkan kamar demi keselamatan bersama. Penggunaan alat daya tinggi (seperti dispenser pemanas/oven pribadi) harap dikonfirmasikan ke pengelola.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] font-semibold text-slate-600">
                        <i data-lucide="power" class="w-3.5 h-3.5 text-teal-500 shrink-0"></i>
                        <span>Instalasi listrik standar SNI & aman</span>
                    </div>
                </div>

                <!-- 6. Larangan Mutlak (Zero Tolerance) -->
                <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-rose-300 transition-all duration-300 space-y-3 group flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center font-bold group-hover:scale-110 transition-transform shadow-xs">
                                <i data-lucide="ban" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                Larangan Mutlak
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Bebas Asap Rokok & Narkoba</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Dilarang keras membawa narkotika, minuman keras, zat berbahaya, senjata tajam, atau berjudi. Seluruh kamar ber-AC bersifat <strong>Non-Smoking Room (Bebas Asap Rokok)</strong>; area merokok khusus tersedia di luar.
                        </p>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] font-semibold text-rose-600">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-rose-500 shrink-0"></i>
                        <span>Pelanggaran berakibat pemutusan sewa sepihak</span>
                    </div>
                </div>
            </div>

            <!-- Agreement Assurance Banner -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-teal-300 shrink-0">
                        <i data-lucide="file-check-2" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs text-slate-300 leading-snug">
                        Seluruh ketentuan di atas tercantum dalam <strong>Kontrak Sewa Digital Resmi</strong> dan disepakati bersama sebelum penyerahan kunci kamar.
                    </p>
                </div>
                <a href="#kamar-kost" class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold text-slate-900 bg-teal-300 hover:bg-teal-200 transition shadow-xs text-center">
                    Pilih Kamar Sekarang
                </a>
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

<!-- Interactive 3D Kosan Canvas Engine with Smooth Cursor Tracking -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('kosan-canvas-3d');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const heroZone = document.getElementById('hero-interactive-zone') || canvas;

    let width = 0, height = 0, dpr = 1;
    let targetRotY = -0.45;
    let targetRotX = 0.38;
    let currentRotY = -0.45;
    let currentRotX = 0.38;
    let isHovered = false;

    // Handle high DPI display
    function resize() {
        const rect = canvas.getBoundingClientRect();
        dpr = window.devicePixelRatio || 1;
        width = rect.width;
        height = rect.height;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        ctx.scale(dpr, dpr);
    }
    window.addEventListener('resize', resize);
    resize();

    // Mouse Tracking over hero
    heroZone.addEventListener('mousemove', function(e) {
        const rect = heroZone.getBoundingClientRect();
        const nx = ((e.clientX - rect.left) / rect.width) * 2 - 1; // -1 to 1
        const ny = ((e.clientY - rect.top) / rect.height) * 2 - 1;

        // Interactive tilt based on cursor position
        targetRotY = -0.45 + (nx * 0.45);
        targetRotX = 0.38 + (ny * 0.25);
        isHovered = true;
    });

    heroZone.addEventListener('mouseleave', function() {
        isHovered = false;
    });

    // Touch support for mobile
    let touchStartX = 0, touchStartY = 0;
    canvas.addEventListener('touchstart', function(e) {
        if (e.touches.length > 0) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }
    }, { passive: true });

    canvas.addEventListener('touchmove', function(e) {
        if (e.touches.length > 0) {
            const dx = e.touches[0].clientX - touchStartX;
            const dy = e.touches[0].clientY - touchStartY;
            targetRotY += dx * 0.005;
            targetRotX += dy * 0.005;
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }
    }, { passive: true });

    // 3D Geometry Utilities
    function project(p, rotY, rotX, scale, cx, cy) {
        // Rotate around Y
        const cosY = Math.cos(rotY), sinY = Math.sin(rotY);
        const x1 = p[0] * cosY - p[2] * sinY;
        const z1 = p[0] * sinY + p[2] * cosY;

        // Rotate around X
        const cosX = Math.cos(rotX), sinX = Math.sin(rotX);
        const y2 = p[1] * cosX - z1 * sinX;
        const z2 = p[1] * sinX + z1 * cosX;

        // Orthographic with subtle depth perspective
        const distance = 480;
        const fov = distance / (distance + z2 * 0.5);

        return [
            cx + x1 * scale * fov,
            cy - y2 * scale * fov,
            z2 // depth for sorting
        ];
    }

    // Modern 3D Kosan Architecture Model Data
    // Building consists of:
    // 1. Foundation Platform
    // 2. Ground Floor + Rooms
    // 3. First Floor + Balcony
    // 4. Rooftop Canopy + Solar Panels
    // 5. Windows & Doors with Glow
    const boxes = [
        // Ground Foundation Slab (x, y, z, w, h, d, colors: [top, left, right, front, back])
        { x: 0, y: -90, z: 0, w: 220, h: 14, d: 200, col: ['#1e293b', '#0f172a', '#1e293b', '#334155', '#0f172a'] },
        // Main Ground Floor Body
        { x: 0, y: -38, z: 0, w: 170, h: 90, d: 150, col: ['#0f172a', '#1e293b', '#334155', '#1e293b', '#0f172a'] },
        // Ground Floor Entrance Porch Pillar
        { x: 65, y: -45, z: 85, w: 12, h: 80, d: 12, col: ['#38bdf8', '#0284c7', '#0369a1', '#38bdf8', '#0284c7'] },
        // Mid-floor Cantilever Balcony Slab
        { x: 10, y: 10, z: 15, w: 195, h: 12, d: 175, col: ['#0ea5e9', '#0284c7', '#0369a1', '#38bdf8', '#0284c7'] },
        // Upper Floor Main Body (Living Units)
        { x: -5, y: 62, z: -5, w: 160, h: 92, d: 140, col: ['#1e293b', '#334155', '#475569', '#1e293b', '#0f172a'] },
        // Upper Balcony Glass Railing
        { x: 55, y: 32, z: 65, w: 85, h: 32, d: 6, col: ['rgba(56,189,248,0.7)', 'rgba(14,165,233,0.5)', 'rgba(56,189,248,0.6)', 'rgba(56,189,248,0.8)', 'rgba(56,189,248,0.5)'] },
        // Rooftop Modern Canopy / Solar Pergola
        { x: -5, y: 120, z: -5, w: 175, h: 10, d: 155, col: ['#14b8a6', '#0d9488', '#0f766e', '#2dd4bf', '#0d9488'] },
        // Rooftop Solar Array Pod
        { x: -30, y: 132, z: -20, w: 80, h: 8, d: 70, col: ['#0284c7', '#0369a1', '#075985', '#38bdf8', '#0369a1'] },
        // Smart Antenna / Communication Node
        { x: 60, y: 148, z: -50, w: 6, h: 48, d: 6, col: ['#10b981', '#059669', '#047857', '#34d399', '#059669'] }
    ];

    // Windows with warm/cyber ambient glows (x, y, z, w, h, facing)
    const windows = [
        // Ground Floor Windows
        { x: -40, y: -35, z: 76, w: 34, h: 45, col: '#38bdf8', glow: 'rgba(56,189,248,0.45)' },
        { x: 15, y: -35, z: 76, w: 34, h: 45, col: '#facc15', glow: 'rgba(250,204,21,0.45)' },
        // Main Entry Door (Glass)
        { x: 65, y: -50, z: 76, w: 26, h: 62, col: '#2dd4bf', glow: 'rgba(45,212,191,0.4)' },
        // Upper Floor Windows (Occupied & Available Rooms)
        { x: -45, y: 65, z: 66, w: 34, h: 42, col: '#34d399', glow: 'rgba(52,211,153,0.5)' },
        { x: 0, y: 65, z: 66, w: 34, h: 42, col: '#38bdf8', glow: 'rgba(56,189,248,0.45)' },
        { x: 45, y: 65, z: 66, w: 34, h: 42, col: '#f59e0b', glow: 'rgba(245,158,11,0.5)' }
    ];

    // Floating Orbiting Data Particles
    const particles = [];
    for (let i = 0; i < 24; i++) {
        particles.push({
            radius: 120 + Math.random() * 90,
            angle: Math.random() * Math.PI * 2,
            speed: 0.008 + Math.random() * 0.012,
            y: (Math.random() - 0.5) * 180,
            size: 2 + Math.random() * 2.5,
            color: i % 3 === 0 ? '#38bdf8' : (i % 3 === 1 ? '#2dd4bf' : '#34d399')
        });
    }

    // Animation Render Loop
    let time = 0;
    function render() {
        requestAnimationFrame(render);
        time += 0.016;

        // Smooth inertia lerp for mouse rotation
        currentRotY += (targetRotY - currentRotY) * 0.06;
        currentRotX += (targetRotX - currentRotX) * 0.06;

        // Auto gentle idle breathing sway when cursor is still
        const idleSwayY = isHovered ? 0 : Math.sin(time * 0.8) * 0.06;
        const idleSwayX = isHovered ? 0 : Math.cos(time * 0.6) * 0.03;
        const rotY = currentRotY + idleSwayY;
        const rotX = currentRotX + idleSwayX;

        ctx.clearRect(0, 0, width, height);

        const cx = width / 2;
        const cy = height / 2 + 15;
        const scale = Math.min(width, height) / 440;

        // Draw Ambient Isometric Ground Grid Lines
        ctx.save();
        ctx.strokeStyle = 'rgba(56, 189, 248, 0.08)';
        ctx.lineWidth = 1;
        for (let gx = -180; gx <= 180; gx += 45) {
            const p1 = project([gx, -90, -180], rotY, rotX, scale, cx, cy);
            const p2 = project([gx, -90, 180], rotY, rotX, scale, cx, cy);
            ctx.beginPath();
            ctx.moveTo(p1[0], p1[1]);
            ctx.lineTo(p2[0], p2[1]);
            ctx.stroke();
        }
        for (let gz = -180; gz <= 180; gz += 45) {
            const p1 = project([-180, -90, gz], rotY, rotX, scale, cx, cy);
            const p2 = project([180, -90, gz], rotY, rotX, scale, cx, cy);
            ctx.beginPath();
            ctx.moveTo(p1[0], p1[1]);
            ctx.lineTo(p2[0], p2[1]);
            ctx.stroke();
        }
        ctx.restore();

        // Build list of all polygons for depth sorting (Painter's algorithm)
        const renderList = [];

        boxes.forEach(b => {
            const hw = b.w / 2, hh = b.h / 2, hd = b.d / 2;
            const x = b.x, y = b.y, z = b.z;

            // 8 vertices of box
            const v = [
                [x - hw, y - hh, z - hd], // 0
                [x + hw, y - hh, z - hd], // 1
                [x + hw, y + hh, z - hd], // 2
                [x - hw, y + hh, z - hd], // 3
                [x - hw, y - hh, z + hd], // 4
                [x + hw, y - hh, z + hd], // 5
                [x + hw, y + hh, z + hd], // 6
                [x - hw, y + hh, z + hd]  // 7
            ].map(p => project(p, rotY, rotX, scale, cx, cy));

            // Faces: [indices, color]
            // Top (+Y): 3, 2, 6, 7
            // Front (+Z): 4, 5, 6, 7
            // Right (+X): 1, 2, 6, 5
            // Left (-X): 0, 3, 7, 4
            const faces = [
                { pts: [v[3], v[2], v[6], v[7]], col: b.col[0], depth: (v[3][2] + v[2][2] + v[6][2] + v[7][2]) / 4 },
                { pts: [v[4], v[5], v[6], v[7]], col: b.col[3], depth: (v[4][2] + v[5][2] + v[6][2] + v[7][2]) / 4 },
                { pts: [v[1], v[2], v[6], v[5]], col: b.col[2], depth: (v[1][2] + v[2][2] + v[6][2] + v[5][2]) / 4 },
                { pts: [v[0], v[3], v[7], v[4]], col: b.col[1], depth: (v[0][2] + v[3][2] + v[7][2] + v[4][2]) / 4 }
            ];

            faces.forEach(f => renderList.push(f));
        });

        // Add Windows to render list
        windows.forEach(w => {
            const hw = w.w / 2, hh = w.h / 2;
            const v = [
                [w.x - hw, w.y - hh, w.z],
                [w.x + hw, w.y - hh, w.z],
                [w.x + hw, w.y + hh, w.z],
                [w.x - hw, w.y + hh, w.z]
            ].map(p => project(p, rotY, rotX, scale, cx, cy));

            renderList.push({
                pts: v,
                col: w.col,
                glow: w.glow,
                isWindow: true,
                depth: (v[0][2] + v[1][2] + v[2][2] + v[3][2]) / 4 + 1
            });
        });

        // Depth sort: back to front
        renderList.sort((a, b) => a.depth - b.depth);

        // Render Polygons
        renderList.forEach(f => {
            ctx.beginPath();
            ctx.moveTo(f.pts[0][0], f.pts[0][1]);
            for (let i = 1; i < f.pts.length; i++) {
                ctx.lineTo(f.pts[i][0], f.pts[i][1]);
            }
            ctx.closePath();

            if (f.isWindow) {
                // Window with subtle glowing gradient fill
                ctx.save();
                ctx.fillStyle = f.col;
                ctx.shadowColor = f.glow;
                ctx.shadowBlur = 14;
                ctx.fill();
                ctx.lineWidth = 1.5;
                ctx.strokeStyle = '#ffffff';
                ctx.stroke();

                // Window mullions (crossbars)
                const midX = (f.pts[0][0] + f.pts[1][0] + f.pts[2][0] + f.pts[3][0]) / 4;
                const midY = (f.pts[0][1] + f.pts[1][1] + f.pts[2][1] + f.pts[3][1]) / 4;
                ctx.beginPath();
                ctx.moveTo((f.pts[0][0] + f.pts[3][0]) / 2, (f.pts[0][1] + f.pts[3][1]) / 2);
                ctx.lineTo((f.pts[1][0] + f.pts[2][0]) / 2, (f.pts[1][1] + f.pts[2][1]) / 2);
                ctx.strokeStyle = 'rgba(15, 23, 42, 0.6)';
                ctx.lineWidth = 1.5;
                ctx.stroke();
                ctx.restore();
            } else {
                ctx.fillStyle = f.col;
                ctx.fill();
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.12)';
                ctx.lineWidth = 1;
                ctx.stroke();
            }
        });

        // Orbiting Smart Data Particles
        particles.forEach(pt => {
            pt.angle += pt.speed;
            const px = Math.cos(pt.angle) * pt.radius;
            const pz = Math.sin(pt.angle) * pt.radius;
            const pr = project([px, pt.y, pz], rotY, rotX, scale, cx, cy);

            ctx.save();
            ctx.beginPath();
            ctx.arc(pr[0], pr[1], pt.size * (scale * 0.9), 0, Math.PI * 2);
            ctx.fillStyle = pt.color;
            ctx.shadowColor = pt.color;
            ctx.shadowBlur = 10;
            ctx.fill();
            ctx.restore();
        });
    }

    render();
});
</script>
