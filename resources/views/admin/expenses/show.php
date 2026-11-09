<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Detail Pengeluaran #<?= $expense->id ?></h1>
        <p class="text-slate-500 text-sm">Informasi lengkap pengeluaran operasional.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= url('admin/expenses/' . $expense->id . '/edit') ?>" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="edit" class="w-4 h-4"></i>
            Edit
        </a>
        <a href="<?= url('admin/expenses') ?>" class="inline-flex items-center gap-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-4 py-2 rounded-lg text-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 text-sm">
                Informasi Transaksi
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-3">
                    <div>
                        <div class="text-xs text-slate-500 uppercase font-semibold">Kategori</div>
                        <div class="font-bold text-slate-800 text-base mt-0.5"><?= e($expense->category_name) ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase font-semibold">Nominal Biaya</div>
                        <div class="font-extrabold text-blue-700 text-xl mt-0.5"><?= $expense->formattedAmount() ?></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-3">
                    <div>
                        <div class="text-xs text-slate-500 uppercase font-semibold">Tanggal Transaksi</div>
                        <div class="font-medium text-slate-800 text-sm mt-0.5"><?= date('d F Y', strtotime($expense->transaction_date)) ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase font-semibold">Metode Pembayaran</div>
                        <div class="font-semibold uppercase text-xs mt-1 text-slate-700"><?= e($expense->payment_method) ?></div>
                    </div>
                </div>

                <div class="border-b border-slate-100 pb-3">
                    <div class="text-xs text-slate-500 uppercase font-semibold">Deskripsi</div>
                    <div class="text-sm text-slate-800 mt-1 leading-relaxed"><?= nl2br(e($expense->description)) ?></div>
                </div>

                <?php if (!empty($expense->vendor)): ?>
                    <div class="border-b border-slate-100 pb-3">
                        <div class="text-xs text-slate-500 uppercase font-semibold">Vendor / Toko</div>
                        <div class="text-sm font-medium text-slate-800 mt-1"><?= e($expense->vendor) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($expense->notes)): ?>
                    <div class="border-b border-slate-100 pb-3">
                        <div class="text-xs text-slate-500 uppercase font-semibold">Catatan</div>
                        <div class="text-sm text-slate-600 mt-1"><?= nl2br(e($expense->notes)) ?></div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-4 text-xs text-slate-400 pt-2">
                    <div>Dicatat oleh: <span class="font-semibold text-slate-600"><?= e($expense->creator_name) ?></span></div>
                    <div>Waktu sistem: <?= date('d M Y H:i:s', strtotime($expense->created_at)) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 text-sm">
                Bukti Transaksi
            </div>
            <div class="p-6 text-center">
                <?php if (!empty($expense->receipt_path)): ?>
                    <div class="mb-4">
                        <i data-lucide="file-check" class="w-12 h-12 text-emerald-500 mx-auto mb-2"></i>
                        <p class="text-xs text-slate-500">File bukti tersimpan secara aman di private storage server.</p>
                    </div>
                    <a href="<?= url('admin/expenses/' . $expense->id . '/receipt') ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        Lihat / Buka Bukti
                    </a>
                <?php else: ?>
                    <div class="py-6 text-slate-400 text-xs">
                        <i data-lucide="file-x" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                        Tidak ada bukti fisik yang dilampirkan.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
