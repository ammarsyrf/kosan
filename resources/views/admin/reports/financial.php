<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="<?= url('owner/dashboard') ?>" class="text-xs text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mt-1">Laporan Keuangan & Arus Kas</h1>
            <p class="text-slate-500 text-sm">Rincian pendapatan terverifikasi, pengeluaran operasional, dan arus kas bersih kost.</p>
        </div>

        <!-- Filter & Export -->
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="<?= url('admin/reports/financial') ?>" class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm">
                <input type="date" name="date_from" value="<?= e($date_from) ?>" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-700">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" name="date_to" value="<?= e($date_to) ?>" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-700">
                <button type="submit" class="text-xs font-semibold bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>
            </form>

            <a href="<?= url('admin/reports/export/financial') . '?date_from=' . urlencode($date_from) . '&date_to=' . urlencode($date_to) ?>" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-2 rounded-xl text-xs shadow-sm transition">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Tagihan Diterbitkan</div>
            <div class="text-2xl font-bold text-slate-800">Rp <?= number_format($summary['total_billed'], 0, ',', '.') ?></div>
            <div class="text-xs text-slate-500 mt-1"><?= $summary['invoices_count'] ?> invoice</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pendapatan Masuk (Verified)</div>
            <div class="text-2xl font-bold text-emerald-600">Rp <?= number_format($summary['total_income'], 0, ',', '.') ?></div>
            <div class="text-xs text-slate-500 mt-1"><?= $summary['verified_payments_count'] ?> pembayaran</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pengeluaran Operasional</div>
            <div class="text-2xl font-bold text-rose-600">Rp <?= number_format($summary['total_expense'], 0, ',', '.') ?></div>
            <div class="text-xs text-slate-500 mt-1"><?= $summary['expenses_count'] ?> transaksi beban</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Arus Kas Bersih (Net)</div>
            <div class="text-2xl font-bold <?= $summary['net_cash_flow'] >= 0 ? 'text-blue-600' : 'text-rose-600' ?>">
                Rp <?= number_format($summary['net_cash_flow'], 0, ',', '.') ?>
            </div>
            <div class="text-xs text-slate-500 mt-1">Pendapatan - Pengeluaran</div>
        </div>
    </div>

    <!-- Tables Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expenses by Category -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Rincian Pengeluaran per Kategori</h3>
            </div>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Jumlah Transaksi</th>
                        <th class="py-3 px-4 text-right">Total Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($summary['expenses_by_category'])): ?>
                        <tr><td colspan="3" class="py-6 text-center text-slate-400">Tidak ada pengeluaran pada periode ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($summary['expenses_by_category'] as $c): ?>
                            <tr>
                                <td class="py-3 px-4 font-semibold text-slate-800"><?= e($c['name']) ?></td>
                                <td class="py-3 px-4 text-center"><?= (int)$c['count'] ?></td>
                                <td class="py-3 px-4 text-right font-bold text-rose-600">Rp <?= number_format((float)$c['total'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Payment Methods Breakdown -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Metode Penerimaan Pembayaran</h3>
            </div>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">Metode Pembayaran</th>
                        <th class="py-3 px-4 text-center">Transaksi</th>
                        <th class="py-3 px-4 text-right">Total Penerimaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($summary['payments_by_method'])): ?>
                        <tr><td colspan="3" class="py-6 text-center text-slate-400">Tidak ada pembayaran masuk pada periode ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($summary['payments_by_method'] as $pm): ?>
                            <tr>
                                <td class="py-3 px-4 font-semibold uppercase text-slate-800"><?= e($pm['payment_method']) ?></td>
                                <td class="py-3 px-4 text-center"><?= (int)$pm['count'] ?></td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-600">Rp <?= number_format((float)$pm['total'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
