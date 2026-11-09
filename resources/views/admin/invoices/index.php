<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Tagihan & Invoice</h1>
        <p class="text-slate-500 text-sm">Kelola tagihan sewa bulanan dan biaya operasional kos.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/invoices/create') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Buat Invoice Manual
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50">
        <div class="flex items-center gap-2">
            <a href="<?= url('admin/invoices') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= empty($currentStatus) ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Semua</a>
            <a href="<?= url('admin/invoices?status=unpaid') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= $currentStatus === 'unpaid' ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Belum Bayar</a>
            <a href="<?= url('admin/invoices?status=partially_paid') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= $currentStatus === 'partially_paid' ? 'bg-purple-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Sebagian</a>
            <a href="<?= url('admin/invoices?status=paid') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= $currentStatus === 'paid' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Lunas</a>
            <a href="<?= url('admin/invoices?status=overdue') ?>" class="px-3 py-1 rounded-lg text-xs font-semibold <?= $currentStatus === 'overdue' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>">Jatuh Tempo</a>
        </div>
        <span class="text-xs font-semibold text-slate-500">Total: <strong><?= count($invoices) ?></strong></span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-3 px-4">No. Invoice</th>
                    <th class="py-3 px-4">Penyewa / Kamar</th>
                    <th class="py-3 px-4">Periode</th>
                    <th class="py-3 px-4">Jatuh Tempo</th>
                    <th class="py-3 px-4">Total</th>
                    <th class="py-3 px-4">Sisa Tagihan</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">Belum ada invoice yang tercatat.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($invoices as $inv): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-mono font-medium text-slate-900"><?= e($inv->invoice_number) ?></td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800"><?= e($inv->tenant_name) ?></div>
                                <div class="text-xs text-slate-500">Kamar <?= e($inv->room_number) ?></div>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <?= e($inv->period_start) ?> s/d <?= e($inv->period_end) ?>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <span class="<?= $inv->isOverdue() ? 'text-rose-600 font-bold' : 'text-slate-700' ?>">
                                    <?= e($inv->due_date) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-900"><?= $inv->formattedTotal() ?></td>
                            <td class="py-3 px-4 font-bold <?= $inv->balance > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                                <?= $inv->formattedBalance() ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php
                                $badgeClass = match ($inv->status) {
                                    'unpaid' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'partially_paid' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                    'paid' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    'overdue' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                    'cancelled', 'void' => 'bg-slate-100 text-slate-600 border border-slate-200',
                                    default => 'bg-slate-100 text-slate-700'
                                };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', e($inv->status))) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="<?= url('admin/invoices/' . $inv->id) ?>" class="px-3 py-1 text-xs font-medium rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

