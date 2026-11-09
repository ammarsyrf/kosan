<div class="max-w-2xl mx-auto px-4 sm:px-0 py-4 animate-in fade-in duration-300">
    <!-- Breadcrumb & Top Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="<?= url('tenant/dashboard') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors bg-white hover:bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200/80 shadow-xs mb-3 group">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5"></i>
                <span>Kembali ke Dashboard</span>
            </a>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                    Terminasi Awal
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Ajukan Early Checkout</h1>
            <p class="text-slate-500 text-sm mt-1">Pengajuan berhenti menyewa lebih awal sebelum tanggal berakhir kontrak resmi.</p>
        </div>
    </div>

    <!-- Active Contract Warning Card -->
    <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-rose-500/10 via-amber-500/10 to-rose-500/5 border border-rose-200/80 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-500/25 shrink-0">
                <i data-lucide="log-out" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider block">Kontrak Terkait</span>
                <h3 class="text-base font-bold text-slate-900">Kamar <?= e($contract->room_number) ?> &bull; #<?= e($contract->contract_number) ?></h3>
                <p class="text-xs text-slate-600 mt-1">Jadwal Berakhir Asli: <strong class="text-slate-900"><?= date('d F Y', strtotime($contract->end_date)) ?></strong></p>
                <p class="text-[11px] text-rose-700 mt-2 bg-rose-100/70 p-2 rounded-lg border border-rose-200">
                    &bull; Kebijakan pengembalian deposit/dana sewa (refund) akan dihitung dan dikonfirmasi langsung oleh pengelola kos.
                </p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/80 overflow-hidden">
        <div class="p-6 sm:p-8">
            <form action="<?= url('tenant/early-checkout') ?>" method="POST" class="space-y-6" id="form-checkout">
                <?= csrf_field() ?>

                <div>
                    <label for="requested_checkout_date" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Rencana Tanggal Checkout <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl shadow-xs">
                        <input type="date" 
                               name="requested_checkout_date" 
                               id="requested_checkout_date" 
                               required 
                               min="<?= date('Y-m-d') ?>" 
                               max="<?= e($contract->end_date) ?>" 
                               class="w-full rounded-xl border border-slate-300 py-2.5 px-4 text-sm font-medium text-slate-900 focus:border-rose-500 focus:outline-hidden focus:ring-3 focus:ring-rose-500/15 transition cursor-pointer">
                    </div>
                    <p class="text-xs text-slate-500 mt-1.5">Pilih tanggal rencana keluar kamar sebelum masa kontrak berakhir.</p>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Alasan Berhenti Lebih Awal <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="reason" 
                              id="reason" 
                              rows="3" 
                              required 
                              placeholder="Contoh: Pindah tugas kantor ke luar kota / Menyelesaikan studi kuliah..." 
                              class="w-full rounded-xl border border-slate-300 p-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-rose-500 focus:outline-hidden focus:ring-3 focus:ring-rose-500/15 transition"></textarea>
                </div>

                <!-- Footer Actions -->
                <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="<?= url('tenant/dashboard') ?>" class="px-5 py-2.5 text-sm font-semibold rounded-xl text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" 
                            id="btn-submit-checkout" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-rose-600 via-rose-700 to-rose-800 hover:from-rose-700 hover:to-rose-900 shadow-md shadow-rose-500/25 hover:shadow-lg hover:shadow-rose-500/35 active:scale-95 transition-all cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4" id="checkout-icon"></i>
                        <span id="checkout-label">Kirim Pengajuan Checkout</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-checkout');
    const btn = document.getElementById('btn-submit-checkout');
    const label = document.getElementById('checkout-label');
    const icon = document.getElementById('checkout-icon');

    if (form && btn) {
        form.addEventListener('submit', function() {
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-wait');
            label.textContent = 'Memproses...';
            if (icon) icon.classList.add('animate-spin');
        });
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
});
</script>
