<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Pengeluaran Operasional</h1>
        <p class="text-slate-500 text-sm">Pencatatan biaya perawatan kos, listrik, air, internet, dan operasional harian.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/expenses/categories') ?>" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="tag" class="w-4 h-4"></i>
            Kategori Pengeluaran
        </a>
        <a href="<?= url('admin/expenses/create') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Catat Pengeluaran
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
    <div class="p-4 bg-slate-50 border-b border-slate-200">
        <form method="GET" action="<?= url('admin/expenses') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block text-slate-600 font-semibold mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="<?= e($filters['date_from'] ?? '') ?>" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block text-slate-600 font-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="<?= e($filters['date_to'] ?? '') ?>" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block text-slate-600 font-semibold mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg bg-white">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c->id ?>" <?= ($filters['category_id'] ?? '') == $c->id ? 'selected' : '' ?>>
                            <?= e($c->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-slate-600 font-semibold mb-1">Metode Bayar</label>
                <select name="payment_method" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg bg-white">
                    <option value="">Semua Metode</option>
                    <option value="cash" <?= ($filters['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash / Tunai</option>
                    <option value="transfer" <?= ($filters['payment_method'] ?? '') === 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-slate-600 font-semibold mb-1">Cari</label>
                    <input type="text" name="q" value="<?= e($filters['search'] ?? '') ?>" placeholder="Deskripsi/vendor..." class="w-full px-3 py-1.5 border border-slate-300 rounded-lg">
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-900 transition">Filter</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Kategori</th>
                    <th class="py-3 px-4">Deskripsi / Vendor</th>
                    <th class="py-3 px-4">Metode</th>
                    <th class="py-3 px-4 text-right">Nominal</th>
                    <th class="py-3 px-4 text-center">Bukti</th>
                    <th class="py-3 px-4">Dicatat Oleh</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($expenses)): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">Belum ada catatan pengeluaran operasional.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($expenses as $exp): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 whitespace-nowrap text-xs font-medium text-slate-800">
                                <?= date('d M Y', strtotime($exp->transaction_date)) ?>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    <?= e($exp->category_name) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <div class="font-semibold text-slate-800 truncate" title="<?= e($exp->description) ?>"><?= e($exp->description) ?></div>
                                <?php if (!empty($exp->vendor)): ?>
                                    <div class="text-xs text-slate-400">Vendor: <?= e($exp->vendor) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-xs uppercase font-medium <?= $exp->payment_method === 'cash' ? 'text-emerald-700' : 'text-blue-700' ?>">
                                <?= e($exp->payment_method) ?>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900 whitespace-nowrap">
                                <?= $exp->formattedAmount() ?>
                            </td>
                            <td class="py-3 px-4 text-center text-xs">
                                <?php if (!empty($exp->receipt_path)): ?>
                                    <a href="<?= url('admin/expenses/' . $exp->id . '/receipt') ?>" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        Bukti
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-300">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500 whitespace-nowrap">
                                <?= e($exp->creator_name) ?>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap space-x-2">
                                <a href="<?= url('admin/expenses/' . $exp->id) ?>" class="text-xs font-medium text-blue-600 hover:underline">Detail</a>
                                <a href="<?= url('admin/expenses/' . $exp->id . '/edit') ?>" class="text-xs font-medium text-amber-600 hover:underline">Edit</a>
                                <form method="POST" action="<?= url('admin/expenses/' . $exp->id . '/delete') ?>" onsubmit="return confirm('Hapus catatan pengeluaran ini?');" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
            <span class="text-slate-500">Menampilkan <?= count($expenses) ?> dari total <?= $total ?> transaksi</span>
            <div class="flex items-center gap-1">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="<?= url('admin/expenses?' . http_build_query(array_merge($filters, ['page' => $p]))) ?>" class="px-2.5 py-1 rounded font-medium <?= $p === $page ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
