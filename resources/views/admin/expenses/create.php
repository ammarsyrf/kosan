<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Catat Pengeluaran Baru</h1>
        <p class="text-slate-500 text-sm">Tambahkan pencatatan biaya operasional kos beserta bukti transaksi.</p>
    </div>
    <a href="<?= url('admin/expenses') ?>" class="inline-flex items-center gap-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-4 py-2.5 rounded-lg text-sm transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
    <form method="POST" action="<?= url('admin/expenses') ?>" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Kategori Pengeluaran *</label>
                <select name="category_id" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c->id ?>"><?= e($c->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Tanggal Transaksi *</label>
                <input type="date" name="transaction_date" required value="<?= date('Y-m-d') ?>" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Nominal Biaya (Rp) *</label>
                <input type="number" step="0.01" min="1" name="amount" required placeholder="Contoh: 250000" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Metode Pembayaran *</label>
                <select name="payment_method" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="cash">Cash / Tunai</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Deskripsi Pengeluaran *</label>
            <input type="text" name="description" required placeholder="Contoh: Pembelian token listrik kos atau servis AC kamar 201" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Nama Vendor / Toko (Opsional)</label>
                <input type="text" name="vendor" placeholder="Contoh: Toko Listrik Sejahtera, Indomaret" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Upload Bukti / Struk (Opsional)</label>
                <input type="file" name="receipt" accept="image/jpeg,image/png,image/webp,application/pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-lg">
                <span class="text-[10px] text-slate-400">Format: JPG, PNG, WEBP, PDF (Maks. 5 MB)</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
            <textarea name="notes" rows="3" placeholder="Informasi detail tambahan terkait pengeluaran ini..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                <i data-lucide="check" class="w-4 h-4"></i>
                Simpan Pengeluaran
            </button>
        </div>
    </form>
</div>
