<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Fasilitas Kamar</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar master fasilitas yang dapat dialokasikan ke masing-masing kamar.</p>
        </div>
        <a href="<?= url('admin/rooms') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Kembali ke Kamar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Facility Form -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 sticky top-24">
                <h2 class="text-base font-bold text-slate-900">Tambah Fasilitas</h2>
                <form method="POST" action="<?= url('admin/facilities') ?>" class="space-y-4">
                    <?= csrf_field() ?>

                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Fasilitas <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                            placeholder="Contoh: Kulkas Mini"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label for="icon" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                            Nama Icon (Lucide)
                        </label>
                        <input type="text" id="icon" name="icon" value="check"
                            placeholder="tv, wifi, bed, wind"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                            Keterangan Tambahan
                        </label>
                        <textarea id="description" name="description" rows="2"
                            placeholder="Spesifikasi fasilitas"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition shadow-sm">
                        Simpan Fasilitas
                    </button>
                </form>
            </div>
        </div>

        <!-- Facilities List Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Nama Fasilitas</th>
                            <th class="px-6 py-3.5">Deskripsi</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($facilities)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-400">
                                    Belum ada fasilitas tersimpan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($facilities as $fac): ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                                <i data-lucide="<?= e($fac->icon ?: 'check') ?>" class="w-4 h-4"></i>
                                            </div>
                                            <span class="font-semibold text-slate-800"><?= e($fac->name) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        <?= e($fac->description ?: '-') ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="<?= url("admin/facilities/{$fac->id}/delete") ?>" class="inline" onsubmit="return confirm('Hapus fasilitas <?= e($fac->name) ?>?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

