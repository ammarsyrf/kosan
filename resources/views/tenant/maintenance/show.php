<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-mono font-bold bg-slate-200 text-slate-800 px-2 py-0.5 rounded">#<?= $req->id ?></span>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800"><?= e($req->category_name) ?></span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800"><?= e($req->title) ?></h1>
        <p class="text-slate-500 text-sm">Unit: Kamar <?= e($req->room_number) ?> &bull; Tanggal Laporan: <?= date('d M Y H:i', strtotime($req->created_at)) ?></p>
    </div>
    <a href="<?= url('tenant/maintenance') ?>" class="inline-flex items-center gap-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-4 py-2.5 rounded-lg text-sm transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Deskripsi Laporan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Deskripsi Kerusakan</h3>
            <p class="text-slate-800 text-sm leading-relaxed whitespace-pre-line"><?= e($req->description) ?></p>

            <?php if (!empty($req->attachments)): ?>
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Foto Lampiran</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php foreach ($req->attachments as $att): ?>
                            <a href="<?= url('tenant/maintenance/attachments/' . $att->id) ?>" target="_blank" class="p-3 border border-slate-200 rounded-lg bg-slate-50 hover:bg-slate-100 transition flex items-center gap-2 text-xs font-medium text-blue-600">
                                <i data-lucide="image" class="w-4 h-4 text-slate-500 flex-shrink-0"></i>
                                <span class="truncate"><?= e($att->file_name) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Timeline Tindak Lanjut -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Riwayat Perkembangan Perbaikan</h3>

            <div class="space-y-5">
                <?php foreach ($req->updates as $up): ?>
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div>
                            <div class="w-0.5 flex-1 bg-slate-200 my-1"></div>
                        </div>
                        <div class="flex-1 pb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-xs text-slate-800"><?= e($up->user_name) ?></span>
                                <span class="text-xs text-slate-400">&bull; <?= date('d M Y H:i', strtotime($up->created_at)) ?></span>
                                <?php if ($up->status_changed_to): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                        Status: <?= ucfirst(str_replace('_', ' ', e($up->status_changed_to))) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-1 text-xs text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <?= nl2br(e($up->notes)) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Tambah Informasi Lanjutan -->
            <?php if (!$req->isResolved() && !$req->isRejected()): ?>
                <div class="mt-6 pt-4 border-t border-slate-200">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Beri Keterangan / Informasi Tambahan</h4>
                    <form method="POST" action="<?= url('tenant/maintenance/' . $req->id . '/updates') ?>" class="space-y-3">
                        <?= csrf_field() ?>
                        <textarea name="notes" rows="3" required placeholder="Contoh: Teknisi sudah datang memeriksa, menunggu sparepart..." class="w-full border border-slate-300 rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-blue-500"></textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                Kirim Update
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Info Card -->
    <div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Status Saat Ini</h3>

            <div>
                <div class="text-xs text-slate-400 uppercase font-semibold">Status Penanganan</div>
                <div class="mt-1">
                    <?php
                    $sBadge = match($req->status) {
                        'resolved' => 'bg-emerald-100 text-emerald-800 font-bold',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'reviewed' => 'bg-purple-100 text-purple-800',
                        'waiting' => 'bg-amber-100 text-amber-800',
                        'rejected' => 'bg-rose-100 text-rose-800',
                        default => 'bg-slate-100 text-slate-700',
                    };
                    ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase <?= $sBadge ?>">
                        <?= str_replace('_', ' ', e($req->status)) ?>
                    </span>
                </div>
            </div>

            <div>
                <div class="text-xs text-slate-400 uppercase font-semibold">Tingkat Prioritas</div>
                <div class="mt-1 text-sm font-bold text-slate-800 capitalize"><?= e($req->priority) ?></div>
            </div>

            <?php if ($req->resolved_at): ?>
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800">
                    <div class="font-bold flex items-center gap-1.5 mb-1">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        Perbaikan Telah Selesai
                    </div>
                    <div>Selesai pada: <?= date('d M Y H:i', strtotime($req->resolved_at)) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
