<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Manajemen Pembayaran</h1>
        <p class="text-slate-500 text-sm">Verifikasi pengajuan pembayaran sewa dan pencatatan pembayaran tunai.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/payments/cash') ?>" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="banknote" class="w-4 h-4"></i>
            Catat Pembayaran Tunai (Cash)
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50">
        <div class="flex items-center gap-2">
            <a href="<?= url('admin/payments') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= empty($currentStatus) ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Semua</a>
            <a href="<?= url('admin/payments?status=pending') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= $currentStatus === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Menunggu Verifikasi</a>
            <a href="<?= url('admin/payments?status=verified') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= $currentStatus === 'verified' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Terverifikasi</a>
            <a href="<?= url('admin/payments?status=rejected') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= $currentStatus === 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Ditolak</a>
            <a href="<?= url('admin/payments?status=reversed') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= $currentStatus === 'reversed' ? 'bg-purple-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Reversed</a>
        </div>
        <span class="text-xs font-semibold text-slate-500">Total: <strong><?= count($payments) ?></strong></span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-3 px-4">No. Transaksi</th>
                    <th class="py-3 px-4">Penyewa</th>
                    <th class="py-3 px-4">Metode</th>
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Nominal</th>
                    <th class="py-3 px-4">Bukti Bayar</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">Belum ada transaksi pembayaran.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-mono font-medium text-slate-900"><?= e($p->payment_number) ?></td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800"><?= e($p->tenant_name) ?></div>
                                <div class="text-xs text-slate-400"><?= e($p->tenant_phone ?: '-') ?></div>
                            </td>
                            <td class="py-3 px-4 uppercase text-xs font-semibold <?= $p->payment_method === 'cash' ? 'text-emerald-700' : 'text-blue-700' ?>">
                                <?= e($p->payment_method) ?>
                            </td>
                            <td class="py-3 px-4 text-xs"><?= e($p->payment_date) ?></td>
                            <td class="py-3 px-4 font-bold text-slate-900"><?= $p->formattedAmount() ?></td>
                            <td class="py-3 px-4 text-xs">
                                <?php if ($p->proof_file): ?>
                                    <a href="<?= url('admin/payments/' . $p->id . '/proof') ?>" target="_blank" class="text-blue-600 hover:underline inline-flex items-center gap-1 font-medium">
                                        <i data-lucide="paperclip" class="w-3.5 h-3.5"></i> Lihat Bukti
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php
                                $badgeClass = match ($p->status) {
                                    'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'verified' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    'rejected' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                    'reversed' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                    default => 'bg-slate-100 text-slate-700'
                                };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                    <?= ucfirst(e($p->status)) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="<?= url('admin/payments/' . $p->id) ?>" class="px-3 py-1 text-xs font-medium rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                    Detail & Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

