<div class="max-w-2xl mx-auto px-4 sm:px-0 py-4 animate-in fade-in duration-300">
    <!-- Breadcrumb & Top Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="<?= url('tenant/dashboard') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors bg-white hover:bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200/80 shadow-xs mb-3 group">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5"></i>
                <span>Kembali ke Dashboard</span>
            </a>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    Perpanjangan Kontrak
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Ajukan Perpanjangan Sewa</h1>
            <p class="text-slate-500 text-sm mt-1">Perpanjang masa sewa kamar Anda secara praktis sebelum masa kontrak berakhir.</p>
        </div>
    </div>

    <!-- Active Contract Info Card -->
    <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-blue-500/5 border border-blue-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/25 shrink-0">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider block">Kontrak Aktif Saat Ini</span>
                <h3 class="text-base font-bold text-slate-900">Kamar <?= e($contract->room_number) ?> &bull; #<?= e($contract->contract_number) ?></h3>
                <p class="text-xs text-slate-500 mt-0.5">Berakhir pada: <strong class="text-slate-800"><?= date('d F Y', strtotime($contract->end_date)) ?></strong></p>
            </div>
        </div>
        <div class="sm:text-right">
            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-full border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Status Berjalan
            </span>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/80 overflow-hidden">
        <div class="p-6 sm:p-8">
            <form action="<?= url('tenant/extension') ?>" method="POST" class="space-y-6" id="form-extension">
                <?= csrf_field() ?>

                <div>
                    <label for="months" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Pilih Durasi Perpanjangan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="months" id="months" required class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 py-3 pr-10 text-sm font-medium text-slate-800 shadow-xs focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition cursor-pointer">
                            <option value="1">1 Bulan — Perpanjangan Standar Bulanan</option>
                            <option value="3">3 Bulan — Paket Kuartal</option>
                            <option value="6">6 Bulan — Paket Semester</option>
                            <option value="12">12 Bulan (1 Tahun) — Paket Tahunan Penuh</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-blue-500 shrink-0"></i>
                        <span>Setelah pengajuan dikirim, pengelola akan menerbitkan perpanjangan kontrak dan tagihan sewa baru.</span>
                    </p>
                </div>

                <!-- Footer Actions -->
                <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="<?= url('tenant/dashboard') ?>" class="px-5 py-2.5 text-sm font-semibold rounded-xl text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" 
                            id="btn-submit-ext" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/35 active:scale-95 transition-all cursor-pointer">
                        <i data-lucide="send" class="w-4 h-4" id="ext-icon"></i>
                        <span id="ext-label">Kirim Pengajuan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-extension');
    const btn = document.getElementById('btn-submit-ext');
    const label = document.getElementById('ext-label');
    const icon = document.getElementById('ext-icon');

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
