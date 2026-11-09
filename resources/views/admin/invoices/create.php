<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('admin/invoices') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Invoice
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Buat Tagihan Manual</h1>
        <p class="text-slate-500 text-sm">Terbitkan invoice manual untuk sewa bulan pertama, deposit, atau biaya penyesuaian khusus.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="<?= url('admin/invoices') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label for="contract_id" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>Pilih Kontrak Sewa Aktif</span>
                    <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="file-check" class="w-4 h-4"></i>
                    </div>
                    <select name="contract_id" id="contract_id" required 
                            class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-10 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                        <option value="">-- Pilih Kontrak Sewa --</option>
                        <?php foreach ($contracts as $c): ?>
                            <option value="<?= (int)$c->id ?>">
                                #<?= e($c->contract_number) ?> &bull; Kamar <?= e($c->room_number) ?> (<?= e($c->tenant_name) ?>) &bull; Tarif: <?= $c->formattedPrice() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Awal Periode Tagihan <span class="text-rose-500">*</span></label>
                    <input type="date" name="period_start" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Akhir Periode Tagihan <span class="text-rose-500">*</span></label>
                    <input type="date" name="period_end" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Terbit (Issue Date) <span class="text-rose-500">*</span></label>
                    <input type="date" name="issue_date" value="<?= date('Y-m-d') ?>" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jatuh Tempo (Due Date) <span class="text-rose-500">*</span></label>
                    <input type="date" name="due_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="border-t border-slate-200 pt-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-slate-800">Rincian Item Tagihan</h2>
                    <span class="text-xs text-slate-500">Minimal 1 item</span>
                </div>
                <div class="space-y-3" id="items-container">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-2">
                            <input type="text" name="item_description[]" placeholder="Deskripsi item (contoh: Biaya Sewa Kamar Bulan Pertama)" required class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div>
                            <input type="number" step="0.01" name="item_amount[]" placeholder="Nominal (Rp)" required class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_first_month_manual" id="first_month" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="first_month" class="text-sm text-slate-700 font-medium">Tandai sebagai Tagihan Bulan Pertama (Tidak menggeser billing anchor bulanan)</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Catatan untuk penyewa"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="<?= url('admin/invoices') ?>" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">Terbitkan Invoice</button>
            </div>
        </form>
    </div>
</div>

