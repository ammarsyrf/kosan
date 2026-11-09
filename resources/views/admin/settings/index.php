<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                    Konfigurasi Beranda
                </span>
                <span class="text-xs text-slate-400">Dynamic Content Management</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Pengaturan Beranda & Profil Kos</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Ubah nama kos, kontak, teks hero beranda, dan nomor rekening pembayaran secara langsung.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= url() ?>" target="_blank" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs shadow-xs transition">
                <i data-lucide="external-link" class="w-4 h-4 text-blue-600"></i>
                <span>Lihat Halaman Beranda</span>
            </a>
            <a href="<?= url('admin/rooms') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/25 transition">
                <i data-lucide="door-closed" class="w-4 h-4"></i>
                <span>Kelola Kamar Kos</span>
            </a>
        </div>
    </div>

    <form method="POST" action="<?= url('admin/settings') ?>" class="space-y-6">
        <?= csrf_field() ?>

        <!-- SECTION 1: PROFIL & IDENTITAS KOS -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="building" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Identitas & Kontak Kos</h2>
                    <p class="text-xs text-slate-500">Informasi nama bisnis, no WhatsApp, dan alamat yang tampil di seluruh sistem.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="business_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Kos / Properti <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="business_name" name="business_name" required
                        value="<?= e($settings['business_name'] ?? 'Kost Management System') ?>"
                        placeholder="Contoh: Kos Nyaman Harmoni"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <p class="mt-1 text-[11px] text-slate-400">Tampil pada logo navbar, judul halaman, dan header pesan WhatsApp.</p>
                </div>

                <div>
                    <label for="business_phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor WhatsApp Admin / Owner <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="business_phone" name="business_phone" required
                        value="<?= e($settings['business_phone'] ?? '081234567890') ?>"
                        placeholder="Contoh: 081234567890"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <p class="mt-1 text-[11px] text-slate-400">Digunakan untuk tombol "Hubungi Pengelola" di beranda dan invoice.</p>
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

        <!-- SECTION 2: KONTEN HERO BERANDA (LANDING PAGE) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                    <i data-lucide="layout" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Konten Hero Landing Page</h2>
                    <p class="text-xs text-slate-500">Atur teks utama yang pertama kali dibaca oleh calon penghuni saat membuka website.</p>
                </div>
            </div>

            <div>
                <label for="landing_hero_badge" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Teks Badge Kecil di Atas Judul
                </label>
                <input type="text" id="landing_hero_badge" name="landing_hero_badge"
                    value="<?= e($settings['landing_hero_badge'] ?? 'Kost Management Monolith • High-Performance Edition') ?>"
                    placeholder="Contoh: Kost Eksklusif Siap Huni • Surabaya Timur"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>

            <div>
                <label for="landing_hero_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Judul Utama Headline Beranda <span class="text-rose-500">*</span>
                </label>
                <textarea id="landing_hero_title" name="landing_hero_title" rows="2" required
                    placeholder="Contoh: Otomasi Penuh Kos Modern Tanpa Repot, Rapi, & Real-Time"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"><?= e($settings['landing_hero_title'] ?? 'Otomasi Penuh Kos Modern Tanpa Repot, Rapi, & Real-Time') ?></textarea>
                <p class="mt-1 text-[11px] text-slate-400">Judul besar dengan gradien elegan di bagian atas beranda.</p>
            </div>

            <div>
                <label for="landing_hero_subtitle" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Deskripsi / Subtitle di Bawah Judul
                </label>
                <textarea id="landing_hero_subtitle" name="landing_hero_subtitle" rows="3"
                    placeholder="Contoh: Platform operasional kos lengkap: otomatisasi tagihan WhatsApp Fonnte, pemantauan hunian terintegrasi, pembukuan arus kas bersih, serta portal mandiri bagi seluruh penghuni."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"><?= e($settings['landing_hero_subtitle'] ?? 'Platform operasional kos lengkap: otomatisasi tagihan WhatsApp Fonnte, pemantauan hunian terintegrasi, pembukuan arus kas bersih, serta portal mandiri bagi seluruh penghuni.') ?></textarea>
            </div>
        </div>

        <!-- SECTION 3: REKENING PEMBAYARAN -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Rekening Tujuan Pembayaran Sewa</h2>
                    <p class="text-xs text-slate-500">Informasi rekening yang dikirimkan via WhatsApp dan tertera pada portal pembayaran penghuni.</p>
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
                        Atas Nama (Pemilik)
                    </label>
                    <input type="text" id="bank_account_holder" name="bank_account_holder"
                        value="<?= e($settings['bank_account_holder'] ?? 'Owner Kos') ?>"
                        placeholder="Contoh: Budi Gunawan"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
            </div>
        </div>

        <!-- SECTION 4: KEBIJAKAN BOOKING -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-7 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Batas Waktu Booking</h2>
                    <p class="text-xs text-slate-500">Masa tunggu calon penghuni sebelum kamar otomatis dibebaskan kembali jika belum bayar.</p>
                </div>
            </div>

            <div class="max-w-xs">
                <label for="booking_duration_hours" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Durasi Expired Booking (Jam)
                </label>
                <div class="relative">
                    <input type="number" id="booking_duration_hours" name="booking_duration_hours" min="1" max="168"
                        value="<?= e($settings['booking_duration_hours'] ?? '24') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition pr-16">
                    <span class="absolute right-4 top-2.5 text-xs font-bold text-slate-400 pointer-events-none">Jam</span>
                </div>
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-between pt-2">
            <a href="<?= url('owner/dashboard') ?>" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Dashboard
            </a>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-6 py-3 rounded-xl text-xs shadow-md shadow-blue-500/25 hover:shadow-lg transition transform active:scale-95">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Pengaturan</span>
                </button>
            </div>
        </div>
    </form>
</div>
