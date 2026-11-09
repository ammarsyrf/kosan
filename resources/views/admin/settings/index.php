<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                    CMS & Konfigurasi Beranda
                </span>
                <span class="text-xs text-slate-400">100% Dynamic Content</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Pengaturan Konten Beranda & Kos</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Kelola seluruh teks halaman utama (Index), alur reservasi, fasilitas bersama, tata tertib, dan rekening bank.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= url() ?>" target="_blank" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs shadow-xs transition">
                <i data-lucide="external-link" class="w-4 h-4 text-blue-600"></i>
                <span>Lihat Beranda</span>
            </a>
            <a href="<?= url('admin/rooms') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/25 transition">
                <i data-lucide="door-closed" class="w-4 h-4"></i>
                <span>Kelola Kamar Kos</span>
            </a>
        </div>
    </div>

    <!-- Quick Navigation Anchor Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs">
        <a href="#section-identity" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold hover:border-blue-400 hover:text-blue-600 shrink-0 transition flex items-center gap-1.5 shadow-2xs">
            <i data-lucide="building" class="w-3.5 h-3.5 text-blue-500"></i> Profil & Kontak
        </a>
        <a href="#section-hero" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold hover:border-teal-400 hover:text-teal-600 shrink-0 transition flex items-center gap-1.5 shadow-2xs">
            <i data-lucide="layout" class="w-3.5 h-3.5 text-teal-500"></i> Hero Beranda
        </a>
        <a href="#section-steps" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold hover:border-indigo-400 hover:text-indigo-600 shrink-0 transition flex items-center gap-1.5 shadow-2xs">
            <i data-lucide="list-ordered" class="w-3.5 h-3.5 text-indigo-500"></i> Alur 3 Langkah
        </a>
        <a href="#section-facilities" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold hover:border-emerald-400 hover:text-emerald-600 shrink-0 transition flex items-center gap-1.5 shadow-2xs">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-500"></i> Fasilitas Bersama
        </a>
        <a href="#section-rules" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold hover:border-rose-400 hover:text-rose-600 shrink-0 transition flex items-center gap-1.5 shadow-2xs">
            <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-rose-500"></i> Tata Tertib (6)
        </a>
        <a href="#section-payment" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold hover:border-amber-400 hover:text-amber-600 shrink-0 transition flex items-center gap-1.5 shadow-2xs">
            <i data-lucide="credit-card" class="w-3.5 h-3.5 text-amber-500"></i> Rekening Bank
        </a>
    </div>

    <form method="POST" action="<?= url('admin/settings') ?>" class="space-y-6">
        <?= csrf_field() ?>

        <!-- SECTION 1: PROFIL & IDENTITAS KOS -->
        <div id="section-identity" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="building" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">1. Identitas & Kontak Kos</h2>
                    <p class="text-xs text-slate-500">Informasi nama bisnis, WhatsApp pengelola, dan alamat yang tampil di seluruh sistem.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="business_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Kos / Properti <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="business_name" name="business_name" required
                        value="<?= e($settings['business_name'] ?? 'Kost Management System') ?>"
                        placeholder="Contoh: Kost Griya Harmoni Surabaya"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <p class="mt-1 text-[11px] text-slate-400">Tampil pada logo navbar, judul halaman, dan header pesan WhatsApp.</p>
                </div>

                <div>
                    <label for="business_phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor WhatsApp Pengelola / Owner <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="business_phone" name="business_phone" required
                        value="<?= e($settings['business_phone'] ?? '081234567890') ?>"
                        placeholder="Contoh: 081234567890"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <p class="mt-1 text-[11px] text-slate-400">Digunakan untuk tombol "Hubungi Pengelola" di beranda dan nota invoice.</p>
                </div>
            </div>

            <div>
                <label for="business_address" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Lengkap Kos
                </label>
                <textarea id="business_address" name="business_address" rows="2"
                    placeholder="Contoh: Jl. Sukolilo No. 12, Sukolilo, Surabaya, Jawa Timur"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"><?= e($settings['business_address'] ?? 'Jl. Kost Nyaman No. 10') ?></textarea>
            </div>
        </div>

        <!-- SECTION 2: KONTEN HERO BERANDA -->
        <div id="section-hero" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                    <i data-lucide="layout" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">2. Hero Banner Beranda</h2>
                    <p class="text-xs text-slate-500">Teks utama yang pertama kali dilihat oleh pengunjung website di bagian paling atas.</p>
                </div>
            </div>

            <div>
                <label for="landing_hero_badge" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Teks Badge Mini di Atas Judul
                </label>
                <input type="text" id="landing_hero_badge" name="landing_hero_badge"
                    value="<?= e($settings['landing_hero_badge'] ?? 'Kost Management Monolith • High-Performance Edition') ?>"
                    placeholder="Contoh: Hunian Eksklusif Siap Huni • Nyaman & Strategis"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>

            <div>
                <label for="landing_hero_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Judul Headline Utama Beranda <span class="text-rose-500">*</span>
                </label>
                <textarea id="landing_hero_title" name="landing_hero_title" rows="2" required
                    placeholder="Contoh: Otomasi Penuh Kos Modern Tanpa Repot, Rapi, & Real-Time"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"><?= e($settings['landing_hero_title'] ?? 'Otomasi Penuh Kos Modern Tanpa Repot, Rapi, & Real-Time') ?></textarea>
            </div>

            <div>
                <label for="landing_hero_subtitle" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Deskripsi / Subtitle di Bawah Judul
                </label>
                <textarea id="landing_hero_subtitle" name="landing_hero_subtitle" rows="3"
                    placeholder="Contoh: Platform operasional kos lengkap: otomatisasi tagihan WhatsApp, pemantauan hunian terintegrasi, dan portal mandiri bagi seluruh penghuni."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"><?= e($settings['landing_hero_subtitle'] ?? 'Platform operasional kos lengkap: otomatisasi tagihan WhatsApp Fonnte, pemantauan hunian terintegrasi, pembukuan arus kas bersih, serta portal mandiri bagi seluruh penghuni.') ?></textarea>
            </div>
        </div>

        <!-- SECTION 3: ALUR RESERVASI (3 LANGKAH) -->
        <div id="section-steps" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <i data-lucide="list-ordered" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">3. Alur Reservasi (3 Langkah Mudah)</h2>
                    <p class="text-xs text-slate-500">Teks panduan sewa bagi calon penghuni baru pada beranda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="landing_steps_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Judul Utama Alur
                    </label>
                    <input type="text" id="landing_steps_title" name="landing_steps_title"
                        value="<?= e($settings['landing_steps_title'] ?? 'Hunian Nyaman dalam 3 Langkah Mudah') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
                <div>
                    <label for="landing_steps_subtitle" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Subjudul Alur
                    </label>
                    <input type="text" id="landing_steps_subtitle" name="landing_steps_subtitle"
                        value="<?= e($settings['landing_steps_subtitle'] ?? 'Seluruh proses mulai dari pemilihan kamar, kontrak resmi, hingga pembayaran dikelola secara digital dan transparan.') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
            </div>

            <!-- Steps 1, 2, 3 Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <!-- Step 1 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center gap-2 font-bold text-xs text-blue-600">
                        <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs">01</span>
                        <span>Langkah 1</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Langkah 1</label>
                        <input type="text" name="landing_step1_title"
                            value="<?= e($settings['landing_step1_title'] ?? 'Eksplorasi Kamar Siap Huni') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi Langkah 1</label>
                        <textarea name="landing_step1_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_step1_desc'] ?? 'Tinjau ketersediaan kamar secara real-time, foto interior asli, fasilitas lengkap (AC, Kamar Mandi Dalam, Springbed), dan harga sewa transparan tanpa biaya tersembunyi.') ?></textarea>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center gap-2 font-bold text-xs text-indigo-600">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs">02</span>
                        <span>Langkah 2</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Langkah 2</label>
                        <input type="text" name="landing_step2_title"
                            value="<?= e($settings['landing_step2_title'] ?? 'Booking & Kontrak Digital') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi Langkah 2</label>
                        <textarea name="landing_step2_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_step2_desc'] ?? 'Reservasi kamar aman dengan validasi anti-bentrok tanggal. Kontrak sewa resmi diterbitkan otomatis lengkap dengan masa aktif dan tanggal tagihan rutin.') ?></textarea>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center gap-2 font-bold text-xs text-teal-600">
                        <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs">03</span>
                        <span>Langkah 3</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Langkah 3</label>
                        <input type="text" name="landing_step3_title"
                            value="<?= e($settings['landing_step3_title'] ?? 'Portal Mandiri & Notifikasi WA') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi Langkah 3</label>
                        <textarea name="landing_step3_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_step3_desc'] ?? 'Penghuni menikmati portal khusus untuk cek tagihan, unggah bukti transfer kamera, ajukan perpanjangan sewa, dan lapor kendala kamar via tiket teknisi.') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: FASILITAS BERSAMA KOS (4 BOX) -->
        <div id="section-facilities" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">4. Fasilitas Bersama Kos (4 Keunggulan)</h2>
                    <p class="text-xs text-slate-500">Tampilkan fasilitas utama seperti WiFi, CCTV, KM Dalam, Dapur/Laundry di beranda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="landing_fac_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Judul Seksi Fasilitas
                    </label>
                    <input type="text" id="landing_fac_title" name="landing_fac_title"
                        value="<?= e($settings['landing_fac_title'] ?? 'Fasilitas Terintegrasi untuk Ketenangan Tinggal') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
                <div>
                    <label for="landing_fac_subtitle" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Subjudul Seksi Fasilitas
                    </label>
                    <input type="text" id="landing_fac_subtitle" name="landing_fac_subtitle"
                        value="<?= e($settings['landing_fac_subtitle'] ?? 'Semua unit kos dirawat secara berkala untuk menjamin kenyamanan profesional muda dan mahasiswa.') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <!-- Fasilitas 1 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-teal-700 flex items-center gap-2">
                        <i data-lucide="wifi" class="w-4 h-4 text-teal-600"></i> Fasilitas 1 (Internet / WiFi)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul</label>
                        <input type="text" name="landing_fac1_title"
                            value="<?= e($settings['landing_fac1_title'] ?? 'Internet Fiber 100 Mbps') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_fac1_desc" rows="2"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_fac1_desc'] ?? 'Unlimited kecepatan tinggi tanpa buffering untuk meeting, streaming, & gaming.') ?></textarea>
                    </div>
                </div>

                <!-- Fasilitas 2 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-blue-700 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i> Fasilitas 2 (Keamanan & CCTV)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul</label>
                        <input type="text" name="landing_fac2_title"
                            value="<?= e($settings['landing_fac2_title'] ?? 'Keamanan & CCTV 24 Jam') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_fac2_desc" rows="2"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_fac2_desc'] ?? 'Akses gerbang kartu/kunci aman dengan pengawasan kamera CCTV di seluruh koridor.') ?></textarea>
                    </div>
                </div>

                <!-- Fasilitas 3 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-amber-700 flex items-center gap-2">
                        <i data-lucide="droplets" class="w-4 h-4 text-amber-600"></i> Fasilitas 3 (Kamar Mandi / Water Heater)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul</label>
                        <input type="text" name="landing_fac3_title"
                            value="<?= e($settings['landing_fac3_title'] ?? 'Water Heater & KM Dalam') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_fac3_desc" rows="2"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_fac3_desc'] ?? 'Air hangat instan, kloset duduk higienis, dan sirkulasi udara kamar mandi lancar.') ?></textarea>
                    </div>
                </div>

                <!-- Fasilitas 4 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-indigo-700 flex items-center gap-2">
                        <i data-lucide="sparkle" class="w-4 h-4 text-indigo-600"></i> Fasilitas 4 (Dapur & Fasilitas Bersama)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul</label>
                        <input type="text" name="landing_fac4_title"
                            value="<?= e($settings['landing_fac4_title'] ?? 'Dapur & Laundry Bersama') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_fac4_desc" rows="2"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_fac4_desc'] ?? 'Dilengkapi kompor gas, kulkas komunal, dispenser air minum, dan area jemur terlindung.') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 5: TATA TERTIB & ATURAN HUNIAN KOS (6 ATURAN) -->
        <div id="section-rules" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">5. Tata Tertib & Aturan Kos (6 Aturan)</h2>
                    <p class="text-xs text-slate-500">Kebijakan hunian yang ditampilkan di bagian bawah beranda kos untuk kenyamanan bersama.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="landing_rules_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Judul Seksi Tata Tertib
                    </label>
                    <input type="text" id="landing_rules_title" name="landing_rules_title"
                        value="<?= e($settings['landing_rules_title'] ?? 'Tata Tertib & Aturan Tinggal di Kos') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
                <div>
                    <label for="landing_rules_subtitle" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Subjudul Seksi Tata Tertib
                    </label>
                    <input type="text" id="landing_rules_subtitle" name="landing_rules_subtitle"
                        value="<?= e($settings['landing_rules_subtitle'] ?? 'Dirancang demi menjaga privasi, ketenangan waktu istirahat, kebersihan, dan keselamatan seluruh penghuni kos.') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
                <!-- Aturan 1 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-indigo-700 flex items-center gap-1.5">
                        <i data-lucide="moon" class="w-4 h-4 text-indigo-600"></i> Aturan 1 (Jam Tenang)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Aturan 1</label>
                        <input type="text" name="landing_rule1_title"
                            value="<?= e($settings['landing_rule1_title'] ?? 'Jam Tenang & Kunjungan Tamu') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_rule1_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_rule1_desc'] ?? 'Jam tenang berlaku pukul 22.00 - 06.00 WIB untuk istirahat optimal. Tamu dipersilakan diterima di ruang tamu komunal dan mohon kembali sebelum pukul 22.00 WIB.') ?></textarea>
                    </div>
                </div>

                <!-- Aturan 2 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-blue-700 flex items-center gap-1.5">
                        <i data-lucide="key-round" class="w-4 h-4 text-blue-600"></i> Aturan 2 (Keamanan)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Aturan 2</label>
                        <input type="text" name="landing_rule2_title"
                            value="<?= e($settings['landing_rule2_title'] ?? 'Keamanan & Kunci Akses') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_rule2_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_rule2_desc'] ?? 'Pintu utama dilengkapi Smart Access Lock 24 Jam dan pantauan kamera CCTV 24/7 di area publik serta parkiran. Kunci kamar bersifat pribadi dan dilarang dipinjamkan.') ?></textarea>
                    </div>
                </div>

                <!-- Aturan 3 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-emerald-700 flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-4 h-4 text-emerald-600"></i> Aturan 3 (Kebersihan)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Aturan 3</label>
                        <input type="text" name="landing_rule3_title"
                            value="<?= e($settings['landing_rule3_title'] ?? 'Kebersihan & Dapur Bersama') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_rule3_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_rule3_desc'] ?? 'Area bersama (dapur, dispenser, kulkas bersama, dan jemuran) wajib dirapikan dan dibersihkan kembali setelah digunakan. Sampah kamar diikat rapi dan diletakkan di tempat sampah utama.') ?></textarea>
                    </div>
                </div>

                <!-- Aturan 4 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-amber-700 flex items-center gap-1.5">
                        <i data-lucide="calendar-check" class="w-4 h-4 text-amber-600"></i> Aturan 4 (Pembayaran)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Aturan 4</label>
                        <input type="text" name="landing_rule4_title"
                            value="<?= e($settings['landing_rule4_title'] ?? 'Sewa & Pemberitahuan Checkout') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_rule4_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_rule4_desc'] ?? 'Sewa bulanan dibayarkan tepat waktu sebelum tanggal jatuh tempo (billing anchor). Jika berencana selesai sewa / keluar, mohon konfirmasi ke pengelola minimal H-14 sebelum tanggal tagihan.') ?></textarea>
                    </div>
                </div>

                <!-- Aturan 5 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-teal-700 flex items-center gap-1.5">
                        <i data-lucide="zap" class="w-4 h-4 text-teal-600"></i> Aturan 5 (Listrik & Elektronik)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Aturan 5</label>
                        <input type="text" name="landing_rule5_title"
                            value="<?= e($settings['landing_rule5_title'] ?? 'Penggunaan Listrik & Elektronik') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_rule5_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_rule5_desc'] ?? 'Mohon matikan AC, lampu, dan peralatan elektronik saat meninggalkan kamar demi keselamatan bersama. Penggunaan alat daya tinggi (seperti dispenser pemanas/oven pribadi) harap dikonfirmasikan ke pengelola.') ?></textarea>
                    </div>
                </div>

                <!-- Aturan 6 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="font-bold text-xs text-rose-700 flex items-center gap-1.5">
                        <i data-lucide="ban" class="w-4 h-4 text-rose-600"></i> Aturan 6 (Larangan Mutlak)
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Aturan 6</label>
                        <input type="text" name="landing_rule6_title"
                            value="<?= e($settings['landing_rule6_title'] ?? 'Bebas Asap Rokok & Narkoba') ?>"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                        <textarea name="landing_rule6_desc" rows="3"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= e($settings['landing_rule6_desc'] ?? 'Dilarang keras membawa narkotika, minuman keras, zat berbahaya, senjata tajam, atau berjudi. Seluruh kamar ber-AC bersifat Non-Smoking Room (Bebas Asap Rokok); area merokok khusus tersedia di luar.') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 6: REKENING PEMBAYARAN & KEBIJAKAN -->
        <div id="section-payment" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">6. Rekening Tujuan Pembayaran & Durasi Booking</h2>
                    <p class="text-xs text-slate-500">Nomor rekening transfer yang tertera pada invoice, WhatsApp, dan portal bayar.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="bank_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Bank
                    </label>
                    <input type="text" id="bank_name" name="bank_name"
                        value="<?= e($settings['bank_name'] ?? 'BCA') ?>"
                        placeholder="Contoh: BCA / Mandiri / BNI"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="bank_account_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor Rekening
                    </label>
                    <input type="text" id="bank_account_number" name="bank_account_number"
                        value="<?= e($settings['bank_account_number'] ?? '1234567890') ?>"
                        placeholder="Contoh: 8201928192"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="bank_account_holder" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Atas Nama (Pemilik Rekening)
                    </label>
                    <input type="text" id="bank_account_holder" name="bank_account_holder"
                        value="<?= e($settings['bank_account_holder'] ?? 'Owner Kos') ?>"
                        placeholder="Contoh: Budi Gunawan"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
            </div>

            <div class="max-w-xs pt-2">
                <label for="booking_duration_hours" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Durasi Expired Booking (Jam)
                </label>
                <div class="relative">
                    <input type="number" id="booking_duration_hours" name="booking_duration_hours" min="1" max="168"
                        value="<?= e($settings['booking_duration_hours'] ?? '24') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition pr-16">
                    <span class="absolute right-4 top-2.5 text-xs font-bold text-slate-400 pointer-events-none">Jam</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Batas toleransi calon penghuni menyelesaikan pembayaran awal.</p>
            </div>
        </div>

        <!-- Sticky Bottom Save Bar -->
        <div class="sticky bottom-4 z-20 p-4 rounded-2xl bg-slate-900/95 backdrop-blur-md text-white border border-slate-800 shadow-2xl flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold shrink-0">
                    <i data-lucide="check-check" class="w-5 h-5"></i>
                </div>
                <div class="hidden sm:block">
                    <p class="text-xs font-bold">Siap Menerapkan Perubahan?</p>
                    <p class="text-[11px] text-slate-400">Semua teks yang diedit akan langsung aktif di halaman depan (Index) seketika.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="<?= url('owner/dashboard') ?>" class="text-xs font-semibold text-slate-300 hover:text-white px-3 py-2 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/25 hover:shadow-lg transition transform active:scale-95">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Seluruh Pengaturan</span>
                </button>
            </div>
        </div>
    </form>
</div>
