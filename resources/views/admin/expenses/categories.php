<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kategori Pengeluaran</h1>
        <p class="text-slate-500 text-sm">Kelola kategori untuk klasifikasi pengeluaran operasional kos.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/expenses') ?>" class="inline-flex items-center gap-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-4 py-2.5 rounded-lg text-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Pengeluaran
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Tambah Kategori Baru</h3>
            <form method="POST" action="<?= url('admin/expenses/categories') ?>" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Nama Kategori *</label>
                    <input type="text" name="name" required placeholder="Contoh: Pajak & Perizinan" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3" placeholder="Keterangan singkat kategori ini..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-slate-700">Status Aktif</span>
                    </label>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 text-sm">
                Daftar Kategori Pengeluaran
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                        <tr>
                            <th class="py-3 px-4">Nama Kategori</th>
                            <th class="py-3 px-4">Deskripsi</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Transaksi</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4 font-semibold text-slate-800">
                                    <?= e($cat->name) ?>
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-500 max-w-xs">
                                    <?= e($cat->description ?? '-') ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $cat->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' ?>">
                                        <?= $cat->isActive() ? 'Aktif' : 'Non-aktif' ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center text-xs font-semibold text-slate-700">
                                    <?= $cat->expenses_count ?>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <form method="POST" action="<?= url('admin/expenses/categories/' . $cat->id . '/delete') ?>" onsubmit="return confirm('Hapus kategori ini? Kategori yang memiliki transaksi tidak dapat dihapus.');" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-xs font-medium text-rose-600 hover:underline <?= $cat->expenses_count > 0 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
