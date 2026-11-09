<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="<?= url('admin/contracts') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
                &larr; Kembali ke Daftar Kontrak
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Kontrak #<?= e($contract->contract_number) ?></h1>
            <p class="text-slate-500 text-sm">Status: <span class="font-semibold uppercase text-blue-600"><?= e($contract->status) ?></span></p>
        </div>
    </div>

    <!-- Contract Overview Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Informasi Kamar & Penyewa</h2>
            <div class="space-y-2 text-sm text-slate-700">
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Kamar:</span>
                    <span class="font-semibold text-slate-900"><?= e($contract->room_number) ?> (<?= e($contract->room_name) ?>)</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Nama Penyewa:</span>
                    <span class="font-semibold text-slate-900"><?= e($contract->tenant_name) ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">No. Telepon:</span>
                    <span class="text-slate-900"><?= e($contract->tenant_phone ?: '-') ?></span>
                </div>
                <div class="flex justify-between pb-2">
                    <span class="text-slate-500">Catatan:</span>
                    <span class="text-slate-800"><?= e($contract->notes ?: '-') ?></span>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Ketentuan Sewa & Tanggal</h2>
            <div class="space-y-2 text-sm text-slate-700">
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Tarif Bulanan (Snapshot):</span>
                    <span class="font-bold text-blue-600"><?= $contract->formattedPrice() ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Billing Anchor Day:</span>
                    <span class="font-semibold text-slate-900">Setiap tanggal <?= (int)$contract->billing_anchor_day ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Periode Sewa:</span>
                    <span class="text-slate-900 font-medium"><?= e($contract->start_date) ?> s/d <?= e($contract->end_date) ?></span>
                </div>
                <div class="flex justify-between pb-2">
                    <span class="text-slate-500">Diterbitkan Pada:</span>
                    <span class="text-slate-600"><?= e($contract->created_at) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Audit History -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Histori Perubahan Kontrak</h3>
        </div>
        <div class="divide-y divide-slate-100">
            <?php if (empty($histories)): ?>
                <div class="p-6 text-center text-slate-400 text-sm">Belum ada catatan histori.</div>
            <?php else: ?>
                <?php foreach ($histories as $h): ?>
                    <div class="p-4 hover:bg-slate-50/50 transition">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                            <span class="font-semibold text-slate-800 uppercase px-2 py-0.5 rounded bg-slate-100">
                                <?= e($h->action) ?>
                            </span>
                            <span><?= e($h->created_at) ?></span>
                        </div>
                        <p class="text-sm text-slate-700 mt-1"><?= e($h->notes ?: '-') ?></p>
                        <?php if ($h->old_end_date || $h->new_end_date || $h->old_rent_price || $h->new_rent_price): ?>
                            <div class="mt-2 text-xs text-slate-500 flex flex-wrap gap-4">
                                <?php if ($h->old_end_date && $h->new_end_date): ?>
                                    <span>Periode: <?= e($h->old_end_date) ?> &rarr; <?= e($h->new_end_date) ?></span>
                                <?php endif; ?>
                                <?php if ($h->old_rent_price && $h->new_rent_price): ?>
                                    <span>Tarif: Rp <?= number_format((float)$h->old_rent_price, 0, ',', '.') ?> &rarr; Rp <?= number_format((float)$h->new_rent_price, 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
