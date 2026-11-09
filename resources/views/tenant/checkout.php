<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('tenant/dashboard') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
            &larr; Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Ajukan Early Checkout</h1>
        <p class="text-slate-500 text-sm">Pengajuan berhenti menyewa lebih awal sebelum tanggal kontrak berakhir.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-100 text-sm text-rose-900">
            <div class="font-bold">Kontrak Saat Ini: #<?= e($contract->contract_number) ?></div>
            <div>Kamar: <strong><?= e($contract->room_number) ?></strong></div>
            <div>Jadwal Berakhir Asli: <strong><?= e($contract->end_date) ?></strong></div>
            <p class="text-xs text-rose-700 mt-2">
                * Catatan: Kebijakan pengembalian dana (refund) ditentukan secara manual oleh pengelola kos sesuai kesepakatan dan ketentuan yang berlaku.
            </p>
        </div>

        <form action="<?= url('tenant/early-checkout') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Checkout <span class="text-rose-500">*</span></label>
                <input type="date" name="requested_checkout_date" required min="<?= date('Y-m-d') ?>" max="<?= e($contract->end_date) ?>" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <p class="text-xs text-slate-500 mt-1">Tanggal harus sebelum atau sama dengan akhir kontrak.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Alasan Berhenti Lebih Awal <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Contoh: Pindah tugas ke luar kota"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="<?= url('tenant/dashboard') ?>" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition">Kirim Pengajuan Checkout</button>
            </div>
        </form>
    </div>
</div>
