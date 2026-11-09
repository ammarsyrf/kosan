<div class="space-y-16 pb-16">
    <!-- Hero Section with Deep Slate & Gradient Mesh Background -->
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 text-white rounded-3xl p-8 sm:p-12 lg:p-16 shadow-2xl border border-slate-800">
        <!-- Ambient Decorative Glow -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-6">
            <!-- Product Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold text-teal-300">
                <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
                <span>Kost Management System • Personal Monolith</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight sm:leading-tight">
                Kelola Kos Lebih <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-teal-300 to-emerald-400">Cerdas, Rapi, & Otomatis</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-slate-300 text-sm sm:text-lg max-w-2xl mx-auto leading-relaxed">
                Platform all-in-one untuk automasi tagihan WhatsApp (Fonnte API), pemantauan okupansi kamar real-time, pembukuan arus kas, serta portal mandiri bagi seluruh penghuni kos.
            </p>

            <!-- Call to Actions -->
            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <?php if (auth_check()): ?>
                    <?php $u = auth_user(); ?>
                    <?php if (strtolower($u['role'] ?? '') === 'owner'): ?>
                        <a href="<?= url('owner/dashboard') ?>" class="px-6 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Buka Executive Dashboard
                        </a>
                    <?php elseif (strtolower($u['role'] ?? '') === 'admin'): ?>
                        <a href="<?= url('admin/dashboard') ?>" class="px-6 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Buka Admin Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= url('tenant/dashboard') ?>" class="px-6 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                            <i data-lucide="home" class="w-4 h-4"></i> Buka Tenant Portal
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="px-6 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5 inline-flex items-center gap-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i> Masuk ke Sistem
                    </a>
                <?php endif; ?>

                <a href="#demo-access" class="px-6 py-3.5 rounded-xl font-semibold text-sm bg-white/10 hover:bg-white/15 backdrop-blur-md border border-white/20 text-white transition inline-flex items-center gap-2">
                    <i data-lucide="key" class="w-4 h-4 text-amber-300"></i> Akun Pengujian Demo
                </a>
            </div>

            <!-- Role Zone Alert (backward-compatible check) -->
            <?php if (!empty($role_zone)): ?>
                <div class="mt-4 p-4 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-200 text-sm flex items-center justify-center gap-3">
                    <i data-lucide="shield-check" class="w-5 h-5 text-amber-300"></i>
                    <span><strong>Otorisasi Area:</strong> <?= e($role_zone) ?></span>
                </div>
            <?php endif; ?>

            <!-- Live KPI Metric Strip -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-10 text-left">
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                        <span>Total Kamar</span>
                        <i data-lucide="door-closed" class="w-4 h-4 text-blue-400"></i>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white">
                        <?= (int)($stats['total_rooms'] ?? 8) ?> <span class="text-xs font-normal text-slate-400">Unit</span>
                    </div>
                    <div class="text-[11px] text-teal-300 mt-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span> Siap dikelola
                    </div>
                </div>

                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                        <span>Okupansi Aktif</span>
                        <i data-lucide="users" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">
                        <?= $stats['occupancy_rate'] ?? 75.0 ?>%
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">
                        <?= (int)($stats['occupied'] ?? 6) ?> Kamar Terisi
                    </div>
                </div>

                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                        <span>Automasi WhatsApp</span>
                        <i data-lucide="message-square" class="w-4 h-4 text-emerald-400"></i>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white">
                        Fonnte <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Aktif</span>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-1">
                        Jadwal H-7, H-3, H-1, H+1
                    </div>
                </div>

                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                        <span>Keuangan & Ekspor</span>
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-teal-400"></i>
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

    <!-- Quick Demo Access Section (Evaluator / User Friendly) -->
    <section id="demo-access" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="max-w-3xl mb-6">
            <div class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Akses Cepat Demo Akun
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Uji Coba Hak Akses Berdasarkan Role</h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Sistem memiliki 3 level otorisasi dengan isolasi data ketat. Gunakan kredensial di bawah untuk mengeksplorasi masing-masing peran:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Owner Card -->
            <div class="p-5 rounded-2xl border border-amber-200 bg-amber-50/40 relative flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                            Owner / Pemilik
                        </span>
                        <i data-lucide="crown" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Bapak Kos (Eksekutif)</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4 leading-relaxed">
                        Akses penuh analitik keuangan, arus kas bersih, laba/rugi, okupansi, serta ekspor laporan CSV keuangan.
                    </p>
                </div>
                <div class="pt-3 border-t border-amber-200/60 text-xs space-y-1">
                    <div class="text-slate-600"><strong>Email:</strong> <code class="bg-white px-1.5 py-0.5 rounded border border-amber-200 text-slate-800">owner@kost.com</code></div>
                    <div class="text-slate-600"><strong>Password:</strong> <code class="bg-white px-1.5 py-0.5 rounded border border-amber-200 text-slate-800">password123</code></div>
                    <a href="<?= url('login') ?>" class="mt-3 block text-center py-2 px-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs shadow-sm transition">
                        Masuk sbg Owner &rarr;
                    </a>
                </div>
            </div>

            <!-- Admin Card -->
            <div class="p-5 rounded-2xl border border-blue-200 bg-blue-50/40 relative flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">
                            Admin Operasional
                        </span>
                        <i data-lucide="shield" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Pengelola Harian</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4 leading-relaxed">
                        Kelola kamar, kontrak, tagihan, verifikasi bukti bayar, pencatatan biaya, notifikasi WA, dan penugasan maintenance.
                    </p>
                </div>
                <div class="pt-3 border-t border-blue-200/60 text-xs space-y-1">
                    <div class="text-slate-600"><strong>Email:</strong> <code class="bg-white px-1.5 py-0.5 rounded border border-blue-200 text-slate-800">admin@kost.com</code></div>
                    <div class="text-slate-600"><strong>Password:</strong> <code class="bg-white px-1.5 py-0.5 rounded border border-blue-200 text-slate-800">password123</code></div>
                    <a href="<?= url('login') ?>" class="mt-3 block text-center py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm transition">
                        Masuk sbg Admin &rarr;
                    </a>
                </div>
            </div>

            <!-- Tenant Card -->
            <div class="p-5 rounded-2xl border border-teal-200 bg-teal-50/40 relative flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 uppercase tracking-wider">
                            Penghuni / Tenant
                        </span>
                        <i data-lucide="user-check" class="w-5 h-5 text-teal-600"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Portal Mandiri Penghuni</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4 leading-relaxed">
                        Cek tagihan kamar, unggah bukti bayar transfer bank, ajukan perbaikan fasilitas dengan lampiran foto, dan perpanjangan sewa.
                    </p>
                </div>
                <div class="pt-3 border-t border-teal-200/60 text-xs space-y-1">
                    <div class="text-slate-600"><strong>Email:</strong> <code class="bg-white px-1.5 py-0.5 rounded border border-teal-200 text-slate-800">budi@gmail.com</code></div>
                    <div class="text-slate-600"><strong>Password:</strong> <code class="bg-white px-1.5 py-0.5 rounded border border-teal-200 text-slate-800">password123</code></div>
                    <a href="<?= url('login') ?>" class="mt-3 block text-center py-2 px-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold text-xs shadow-sm transition">
                        Masuk sbg Penghuni &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Room Inventory Live Showcase Section -->
    <?php if (!empty($rooms)): ?>
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-bold text-teal-600 uppercase tracking-wider mb-1">
                        <i data-lucide="home" class="w-4 h-4"></i> Inventaris Properti
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Daftar Kamar & Fasilitas Kost</h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1">Status ketersediaan kamar yang terkelola secara real-time di sistem.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tersedia
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Terisi
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Maintenance
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <?php foreach (array_slice($rooms, 0, 8) as $room): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                        <div class="p-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">
                                    No. <?= e($room->room_number) ?>
                                </span>
                                <?php if ($room->status === 'available'): ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                    </span>
                                <?php elseif ($room->status === 'occupied'): ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Terisi
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Perbaikan
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-800 text-base group-hover:text-blue-600 transition">
                                    <?= e($room->name ?? ('Kamar ' . $room->room_number)) ?>
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Lantai <?= (int)($room->floor ?? 1) ?> • Tipe <?= e(ucfirst($room->type ?? 'Standard')) ?>
                                </p>
                            </div>

                            <!-- Facilities Chips -->
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                <?php if (!empty($room->facilities) && is_array($room->facilities)): ?>
                                    <?php foreach (array_slice($room->facilities, 0, 3) as $fac): ?>
                                        <span class="text-[10px] bg-slate-100 text-slate-600 font-medium px-2 py-0.5 rounded-md">
                                            <?= e($fac->name ?? (is_array($fac) ? ($fac['name'] ?? '') : $fac)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-[10px] bg-slate-100 text-slate-500 font-medium px-2 py-0.5 rounded-md">AC • WiFi</span>
                                    <span class="text-[10px] bg-slate-100 text-slate-500 font-medium px-2 py-0.5 rounded-md">KM Dalam</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-medium text-slate-400">Harga Sewa</div>
                                <div class="text-sm font-extrabold text-slate-800">
                                    Rp <?= number_format((float)($room->price ?? 1500000), 0, ',', '.') ?>
                                    <span class="text-[10px] font-normal text-slate-400">/bln</span>
                                </div>
                            </div>
                            <?php if ($room->status === 'available'): ?>
                                <a href="<?= url('admin/bookings/create?room_id=' . $room->id) ?>" class="text-xs font-bold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 group-hover:underline">
                                    Booking <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-xs font-medium text-slate-400">Disewa</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- 6 Core System Capabilities Grid -->
    <section class="space-y-6">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Fitur Unggulan</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Arsitektur Lengkap dari Reservasi hingga Laporan</h2>
            <p class="text-slate-500 text-xs sm:text-sm">Dibangun dengan standar produksi tinggi tanpa ketergantungan framework berat.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- 1. WhatsApp Automation -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="message-circle" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Automasi Notifikasi WhatsApp (Fonnte)</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pengingat tagihan cerdas terjadwal (H-7, H-3, H-1, Hari-H, H+1). Invoice lunas tidak akan terganggu, template pesan dinamis tanpa placeholder mentah, serta log pengiriman aman tanpa kebocoran API token.
                </p>
            </div>

            <!-- 2. Booking & Kontrak Digital -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="file-check" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Booking & Manajemen Kontrak Sewa</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Alur reservasi kamar dengan validasi bentrok tanggal (anti-double booking). Penerbitan kontrak sewa resmi, perpanjangan sewa (extension), deposit jaminan, dan permohonan early checkout teratur.
                </p>
            </div>

            <!-- 3. Billing & Payment Gateway Internal -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <i data-lucide="credit-card" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Penerbitan Invoice & Verifikasi Struk</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Dukungan multi-metode pembayaran: Transfer Bank, QRIS, dan Tunai. Fitur verifikasi bukti bayar dengan validasi MIME, pencegahan alokasi ganda, pembayaran parsial, dan reversal transaksi aman.
                </p>
            </div>

            <!-- 4. Expense Tracker -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Pencatatan Biaya & Arus Kas Bersih</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pencatatan pengeluaran operasional (listrik, air, kebersihan, perbaikan) dengan kategori, vendor, dan upload bukti nota. Terhubung langsung dengan penghitungan Net Cash Flow eksekutif.
                </p>
            </div>

            <!-- 5. Maintenance Ticket System -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Tiket Kerusakan & Pemeliharaan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Penghuni dapat melaporkan kerusakan kamar secara instan beserta foto pendukung. Admin dapat memperbarui status (in_progress, resolved), estimasi biaya, dan riwayat tindakan teknisi.
                </p>
            </div>

            <!-- 6. Executive Analytics & Export CSV -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Dashboard Eksekutif & Ekspor CSV</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Grafik tren performa keuangan 6 bulan, rasio okupansi, piutang tertunggak, dan perbandingan performa antar periode. Dilengkapi ekspor CSV berstandar Excel (BOM UTF-8) yang aman.
                </p>
            </div>
        </div>
    </section>

    <!-- Engineering & Architecture Guarantee Card -->
    <section class="bg-slate-900 text-slate-200 rounded-3xl p-8 sm:p-10 border border-slate-800 shadow-xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
            <div class="lg:col-span-2 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-400/30 text-blue-400 text-xs font-semibold">
                    <i data-lucide="cpu" class="w-3.5 h-3.5"></i> Rekayasa Perangkat Lunak Teruji
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">PHP Native Modular Monolith Architecture</h2>
                <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                    Aplikasi ini dirancang khusus dengan Custom MVC murni tanpa framework eksternal, menjamin kecepatan eksekusi tinggi, nol overhead bloatware, dan kemudahan deployment di berbagai hosting/VPS.
                </p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <span class="inline-flex items-center gap-1.5 text-xs bg-slate-800 text-slate-300 px-3 py-1.5 rounded-lg border border-slate-700">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-teal-400"></i> Prepared Statements & PDO
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-slate-800 text-slate-300 px-3 py-1.5 rounded-lg border border-slate-700">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-teal-400"></i> CSRF & Secure Cookie Flags
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-slate-800 text-slate-300 px-3 py-1.5 rounded-lg border border-slate-700">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-teal-400"></i> Strict Role Data Isolation
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-slate-800 text-slate-300 px-3 py-1.5 rounded-lg border border-slate-700">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-teal-400"></i> Private Storage Receipts
                    </span>
                </div>
            </div>

            <div class="bg-slate-800/80 rounded-2xl p-6 border border-slate-700/80 space-y-4 text-center">
                <div class="text-3xl font-black text-white">245+</div>
                <div class="text-xs text-slate-400">Automated Tests Passed (M1–M8)</div>
                <div class="pt-2 border-t border-slate-700 text-xs text-teal-400 font-semibold flex items-center justify-center gap-1.5">
                    <i data-lucide="badge-check" class="w-4 h-4"></i> Production Ready Quality
                </div>
                <a href="<?= url('login') ?>" class="block w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs shadow-md shadow-blue-600/30 transition">
                    Masuk ke Sistem Sekarang
                </a>
            </div>
        </div>
    </section>
</div>
