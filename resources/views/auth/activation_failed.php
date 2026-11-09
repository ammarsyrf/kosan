<div class="text-center py-4 space-y-4">
    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto shadow-sm">
        <i data-lucide="key" class="w-6 h-6"></i>
    </div>
    
    <div>
        <h3 class="text-lg font-bold text-slate-900">Aktivasi Akun Penghuni</h3>
        <p class="text-xs text-slate-500 leading-relaxed max-w-sm mx-auto mt-1">
            <?= e($error ?? 'Token aktivasi tidak disertakan.') ?>
        </p>
    </div>

    <!-- Kotak Edukasi Cara Kerja -->
    <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4 text-left space-y-2.5 max-w-md mx-auto text-xs">
        <div class="font-bold text-slate-800 flex items-center gap-1.5">
            <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
            <span>Bagaimana Cara Mendapatkan Tautan?</span>
        </div>
        <p class="text-slate-600 leading-relaxed text-[11px]">
            Tautan aktivasi dibuat otomatis dan dikirimkan ke <strong>WhatsApp</strong> Anda saat Admin/Owner mendaftarkan Anda sebagai anak kost baru.
        </p>
        <div class="pt-1.5 border-t border-slate-200/80">
            <span class="text-[11px] font-semibold text-slate-700 block mb-1.5">Punya kode token manual?</span>
            <form method="GET" action="<?= url('activate') ?>" class="flex gap-2">
                <input type="text" name="token" required placeholder="Tempel token aktivasi di sini..." 
                       class="flex-1 px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="px-3.5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition">
                    Buka
                </button>
            </form>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 flex items-center justify-center gap-3">
        <?php $bizPhone = preg_replace('/[^0-9]/', '', (string)setting('business_phone', '081234567890')); ?>
        <?php if (str_starts_with($bizPhone, '0')) $bizPhone = '62' . substr($bizPhone, 1); ?>
        <a href="https://wa.me/<?= e($bizPhone) ?>?text=Halo%20Admin%20Kost,%20saya%20ingin%20meminta%20tautan%20aktivasi%20akun%20penghuni." target="_blank"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
            <span>Hubungi Admin WA</span>
        </a>
        <a href="<?= url('login') ?>" class="inline-flex items-center px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition">
            Ke Halaman Masuk
        </a>
    </div>
</div>

