<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Log Notifikasi WhatsApp</h1>
        <p class="text-slate-500 text-sm">Histori pengiriman notifikasi pengingat tagihan, status pengiriman Fonnte, dan pengiriman ulang (retry).</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/notifications/templates') ?>" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            Template Pesan
        </a>
        <a href="<?= url('admin/notifications/settings') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="settings" class="w-4 h-4"></i>
            Pengaturan Jadwal
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?= url('admin/notifications') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= empty($currentStatus) ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Semua</a>
            <a href="<?= url('admin/notifications?status=sent') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $currentStatus === 'sent' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Terkirim (Sent)</a>
            <a href="<?= url('admin/notifications?status=pending') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $currentStatus === 'pending' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Pending</a>
            <a href="<?= url('admin/notifications?status=retrying') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $currentStatus === 'retrying' ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Retrying</a>
            <a href="<?= url('admin/notifications?status=failed') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $currentStatus === 'failed' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Gagal (Failed)</a>
        </div>
        <form method="GET" action="<?= url('admin/notifications') ?>" class="flex items-center gap-2">
            <?php if (!empty($currentStatus)): ?>
                <input type="hidden" name="status" value="<?= e($currentStatus) ?>">
            <?php endif; ?>
            <input type="text" name="phone" value="<?= e($currentPhone ?? '') ?>" placeholder="Cari No. WhatsApp..." class="px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded-lg hover:bg-slate-900 transition">Cari</button>
            <?php if (!empty($currentPhone)): ?>
                <a href="<?= url('admin/notifications' . (!empty($currentStatus) ? '?status=' . $currentStatus : '')) ?>" class="text-xs text-slate-500 hover:underline">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-4">No. Penerima</th>
                    <th class="py-3 px-4">Isi Pesan</th>
                    <th class="py-3 px-4">Idempotency Key</th>
                    <th class="py-3 px-4 text-center">Retry</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada riwayat pengiriman notifikasi WhatsApp.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 text-xs whitespace-nowrap">
                                <div class="font-medium text-slate-800"><?= date('d M Y', strtotime($log->created_at)) ?></div>
                                <div class="text-slate-400"><?= date('H:i:s', strtotime($log->created_at)) ?></div>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs font-semibold text-slate-800">
                                <?= e($log->phone) ?>
                            </td>
                            <td class="py-3 px-4 max-w-xs truncate text-xs text-slate-700" title="<?= e($log->message) ?>">
                                <?= e(mb_strimwidth($log->message, 0, 75, '...')) ?>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-500 max-w-[150px] truncate" title="<?= e($log->idempotency_key) ?>">
                                <?= e($log->idempotency_key) ?>
                            </td>
                            <td class="py-3 px-4 text-center text-xs font-semibold <?= $log->retry_count > 0 ? 'text-amber-600' : 'text-slate-400' ?>">
                                <?= $log->retry_count ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php
                                $badgeClass = match($log->status) {
                                    'sent' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'retrying' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border <?= $badgeClass ?>">
                                    <?= ucfirst($log->status) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <?php if ($log->status !== 'sent'): ?>
                                    <form method="POST" action="<?= url('admin/notifications/retry/' . $log->id) ?>" onsubmit="return confirm('Kirim ulang pesan ini ke nomor <?= e($log->phone) ?>?');" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium bg-amber-600 hover:bg-amber-700 text-white rounded transition shadow-sm">
                                            <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                            Retry
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-xs text-emerald-600 font-medium inline-flex items-center gap-1">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Terkirim
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
