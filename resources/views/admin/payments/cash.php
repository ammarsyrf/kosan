<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('admin/payments') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Pembayaran
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Catat Pembayaran Tunai (Cash)</h1>
        <p class="text-slate-500 text-sm">Pencatatan uang tunai yang diterima langsung oleh pengelola kos.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="<?= url('admin/payments/cash') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Penyewa <span class="text-rose-500">*</span></label>
                <select name="tenant_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">-- Pilih Penyewa --</option>
                    <?php foreach ($tenants as $t): ?>
                        <option value="<?= (int)$t->id ?>">
                            <?= e($t->name) ?> (<?= e($t->phone ?: '-') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nominal Diterima (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Contoh: 1500000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pembayaran <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Target ID Invoice (Opsional jika langsung dialokasikan)</label>
                <input type="number" name="invoice_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="ID Invoice">
                <p class="text-xs text-slate-500 mt-1">Kosongkan jika ingin dialokasikan manual nanti pada halaman review.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Contoh: Diterima tunai di kantor kos"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="<?= url('admin/payments') ?>" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">Simpan Pembayaran Tunai</button>
            </div>
        </form>
    </div>
</div>

