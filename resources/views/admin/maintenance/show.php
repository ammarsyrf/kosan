<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-mono font-bold bg-slate-200 text-slate-800 px-2 py-0.5 rounded">#<?= $req->id ?></span>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800"><?= e($req->category_name) ?></span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800"><?= e($req->title) ?></h1>
        <p class="text-slate-500 text-sm">Kamar <?= e($req->room_number) ?> &bull; Penghuni: <?= e($req->tenant_name) ?> (<?= e($req->tenant_phone) ?>)</p>
    </div>
    <a href="<?= url('admin/maintenance') ?>" class="inline-flex items-center gap-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-4 py-2.5 rounded-lg text-sm transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Daftar
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Deskripsi Kerusakan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Deskripsi Kerusakan</h3>
            <p class="text-slate-800 leading-relaxed text-sm whitespace-pre-line"><?= e($req->description) ?></p>

            <?php if (!empty($req->attachments)): ?>
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Foto / Bukti Lampiran</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php foreach ($req->attachments as $att): ?>
                            <a href="<?= url('admin/maintenance/attachments/' . $att->id) ?>" target="_blank" class="p-3 border border-slate-200 rounded-lg bg-slate-50 hover:bg-slate-100 transition flex items-center gap-2 text-xs font-medium text-blue-600">
                                <i data-lucide="image" class="w-4 h-4 text-slate-500 flex-shrink-0"></i>
                                <span class="truncate"><?= e($att->file_name) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Timeline Histori & Perkembangan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 text-sm">
                Riwayat Perkembangan & Tindak Lanjut
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <?php foreach ($req->updates as $up): ?>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-2.5 h-2.5 rounded-full <?= $up->isInternal() ? 'bg-amber-500' : 'bg-blue-600' ?>"></div>
                                <div class="w-0.5 flex-1 bg-slate-200 my-1"></div>
                            </div>
                            <div class="flex-1 pb-4">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-xs text-slate-800"><?= e($up->user_name) ?></span>
                                    <span class="text-[10px] uppercase font-semibold text-slate-400">(<?= e($up->user_role ?: 'User') ?>)</span>
                                    <span class="text-xs text-slate-400">&bull; <?= date('d M Y H:i', strtotime($up->created_at)) ?></span>
                                    <?php if ($up->isInternal()): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                            Catatan Internal
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($up->status_changed_to): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                            Status: <?= e($up->status_changed_to) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-1.5 text-xs text-slate-700 leading-relaxed bg-slate-50/70 p-3 rounded-lg border border-slate-100">
                                    <?= nl2br(e($up->notes)) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Form Tambah Catatan Perkembangan -->
                <div class="mt-4 pt-4 border-t border-slate-200">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tambah Catatan / Tindak Lanjut</h4>
                    <form method="POST" action="<?= url('admin/maintenance/' . $req->id . '/updates') ?>" class="space-y-3">
                        <?= csrf_field() ?>
                        <textarea name="notes" rows="3" required placeholder="Tuliskan catatan perbaikan atau informasi lanjutan..." class="w-full border border-slate-300 rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-blue-500"></textarea>

                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                <span class="text-xs font-medium text-slate-700">Tandai sebagai Catatan Internal (Hanya Pengelola, tidak terlihat oleh Penyewa)</span>
                            </label>

                            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                Kirim Catatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Pengaturan Status & Prioritas -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Ubah Status Perbaikan</h3>
            <form method="POST" action="<?= url('admin/maintenance/' . $req->id . '/status') ?>" class="space-y-3">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status Baru</label>
                    <select name="status" class="w-full border border-slate-300 rounded-lg p-2 text-xs font-medium bg-white">
                        <?php foreach ($allowedStatuses as $st): ?>
                            <option value="<?= $st ?>" <?= $req->status === $st ? 'selected' : '' ?>>
                                <?= ucfirst(str_replace('_', ' ', $st)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan / Solusi (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Keterangan tindakan atau sparepart..." class="w-full border border-slate-300 rounded-lg p-2 text-xs"></textarea>
                </div>
                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                    Perbarui Status
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Tingkat Prioritas</h3>
            <form method="POST" action="<?= url('admin/maintenance/' . $req->id . '/priority') ?>" class="space-y-3">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Prioritas</label>
                    <select name="priority" class="w-full border border-slate-300 rounded-lg p-2 text-xs font-medium bg-white">
                        <?php foreach ($allowedPriorities as $pr): ?>
                            <option value="<?= $pr ?>" <?= $req->priority === $pr ? 'selected' : '' ?>>
                                <?= ucfirst($pr) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full py-2 bg-slate-700 hover:bg-slate-800 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                    Ubah Prioritas
                </button>
            </form>
        </div>
    </div>
</div>
