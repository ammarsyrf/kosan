<div class="space-y-6">
    <!-- Header & Period Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-2">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Executive Suite
                </span>
                <span class="text-xs text-slate-400">Performa Keuangan & Operasional</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">Executive Owner Dashboard</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Pemantauan arus kas bersih, okupansi kamar, dan indikator kesehatan bisnis kost.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Period Selector Form -->
            <form method="GET" action="<?= url('owner/dashboard') ?>" class="flex flex-wrap items-center gap-2 bg-white p-1.5 rounded-2xl border border-slate-200/90 shadow-sm">
                <select name="filter" onchange="this.form.submit()" class="text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    <option value="this_month" <?= ($filter ?? '') === 'this_month' ? 'selected' : '' ?>>Bulan Ini</option>
                    <option value="last_month" <?= ($filter ?? '') === 'last_month' ? 'selected' : '' ?>>Bulan Lalu</option>
                    <option value="today" <?= ($filter ?? '') === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                    <option value="this_year" <?= ($filter ?? '') === 'this_year' ? 'selected' : '' ?>>Tahun Ini</option>
                    <option value="custom" <?= ($filter ?? '') === 'custom' ? 'selected' : '' ?>>Kustom...</option>
                </select>

                <?php if (($filter ?? '') === 'custom'): ?>
                    <input type="date" name="date_from" value="<?= e($date_from ?? '') ?>" class="text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 text-slate-700">
                    <span class="text-xs text-slate-400">s/d</span>
                    <input type="date" name="date_to" value="<?= e($date_to ?? '') ?>" class="text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 text-slate-700">
                    <button type="submit" class="text-xs font-bold bg-blue-600 text-white px-3 py-1.5 rounded-xl hover:bg-blue-700 transition">
                        Terapkan
                    </button>
                <?php endif; ?>
            </form>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <a href="<?= url('admin/payments/cash') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/25 hover:shadow-lg transition transform active:scale-95">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Catat Bayar Tunai</span>
                </a>
                <a href="<?= url('admin/reports/export/financial') . '?date_from=' . urlencode($date_from ?? '') . '&date_to=' . urlencode($date_to ?? '') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md shadow-emerald-500/25 hover:shadow-lg transition transform active:scale-95">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Export Keuangan (CSV)</span>
                </a>
                <a href="<?= url('admin/reports/export/operational') ?>" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold px-4 py-2.5 rounded-xl text-xs shadow-sm hover:shadow transition">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-teal-600"></i>
                    <span>Export Operasional</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Period Info Banner -->
    <div class="bg-gradient-to-r from-blue-50/80 to-indigo-50/80 border border-blue-200/80 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-blue-900 gap-2 shadow-sm">
        <div class="flex items-center gap-2 font-medium">
            <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
            <span>Periode Aktif: <strong><?= e($date_from ?? '') ?></strong> s/d <strong><?= e($date_to ?? '') ?></strong></span>
        </div>
        <div class="text-blue-700 text-[11px] font-medium">
            Dibandingkan periode sebelumnya (<?= e($comparison['prev_period']['from'] ?? '') ?> s/d <?= e($comparison['prev_period']['to'] ?? '') ?>)
        </div>
    </div>

    <!-- 1. Executive Financial Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pendapatan Terverifikasi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-5 flex flex-col justify-between card-interactive">
            <div>
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pendapatan Masuk</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                        <i data-lucide="arrow-down-left" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    <?= 'Rp ' . number_format((float)($financial['total_income'] ?? 0), 0, ',', '.') ?>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500"><?= (int)($financial['verified_payments_count'] ?? 0) ?> verified</span>
                <span class="font-extrabold <?= ($comparison['income_change_pct'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                    <?= ($comparison['income_change_pct'] ?? 0) >= 0 ? '+' : '' ?><?= $comparison['income_change_pct'] ?? 0 ?>%
                </span>
            </div>
        </div>

        <!-- Pengeluaran Operasional -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-5 flex flex-col justify-between card-interactive">
            <div>
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pengeluaran Kost</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shadow-sm">
                        <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    <?= 'Rp ' . number_format((float)($financial['total_expense'] ?? 0), 0, ',', '.') ?>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500"><?= (int)($financial['expenses_count'] ?? 0) ?> transaksi</span>
                <span class="font-extrabold <?= ($comparison['expense_change_pct'] ?? 0) <= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                    <?= ($comparison['expense_change_pct'] ?? 0) >= 0 ? '+' : '' ?><?= $comparison['expense_change_pct'] ?? 0 ?>%
                </span>
            </div>
        </div>

        <!-- Arus Kas Bersih (Net Cash Flow) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-5 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Net Cash Flow</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black tracking-tight <?= ($financial['net_cash_flow'] ?? 0) >= 0 ? 'text-blue-600' : 'text-rose-600' ?>">
                    <?= 'Rp ' . number_format((float)($financial['net_cash_flow'] ?? 0), 0, ',', '.') ?>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Pendapatan - Beban</span>
                <span class="font-extrabold <?= ($comparison['net_change_pct'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                    <?= ($comparison['net_change_pct'] ?? 0) >= 0 ? '+' : '' ?><?= $comparison['net_change_pct'] ?? 0 ?>%
                </span>
            </div>
        </div>

        <!-- Piutang Berjalan & Tagihan -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-5 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center justify-between text-slate-500 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Piutang Berjalan</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 tracking-tight">
                    <?= 'Rp ' . number_format((float)($financial['total_receivables'] ?? 0), 0, ',', '.') ?>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Tagihan: Rp <?= number_format((float)($financial['total_billed'] ?? 0), 0, ',', '.') ?></span>
                <span class="font-bold text-slate-700"><?= (int)($financial['invoices_count'] ?? 0) ?> inv</span>
            </div>
        </div>
    </div>

    <!-- 2. Operational Metrics & Alert Badges -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kost Occupancy Status Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Okupansi Kamar</h3>
                    <span class="text-xs px-2.5 py-1 rounded-full font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                        <?= $operations['occupancy_rate'] ?? 0 ?>% Terisi
                    </span>
                </div>

                <!-- Occupancy Visual Bar -->
                <div class="w-full bg-slate-100 rounded-full h-3 mb-4 overflow-hidden flex">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-3 rounded-full transition-all duration-500" style="width: <?= min(100, max(0, $operations['occupancy_rate'] ?? 0)) ?>%"></div>
                </div>

                <div class="grid grid-cols-3 gap-2.5 text-center pt-2 border-t border-slate-100">
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                        <div class="text-xl font-black text-emerald-700"><?= (int)($operations['occupied_rooms'] ?? 0) ?></div>
                        <div class="text-[10px] font-bold text-emerald-600 uppercase mt-0.5">Terisi</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-xl font-black text-slate-700"><?= (int)($operations['vacant_rooms'] ?? 0) ?></div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">Kosong</div>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-100">
                        <div class="text-xl font-black text-amber-700"><?= (int)($operations['maintenance_rooms'] ?? 0) ?></div>
                        <div class="text-[10px] font-bold text-amber-600 uppercase mt-0.5">Perbaikan</div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Total Kapasitas: <strong class="text-slate-800"><?= (int)($operations['total_rooms'] ?? 0) ?> Kamar</strong></span>
                <span>Kontrak Aktif: <strong class="text-slate-800"><?= (int)($operations['active_contracts_count'] ?? 0) ?></strong></span>
            </div>
        </div>

        <!-- Executive Action & Alerts -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-6 lg:col-span-2">
            <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="bell" class="w-4 h-4 text-amber-500"></i>
                Indikator Perhatian & Alert Operasional
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <a href="<?= url('admin/payments') ?>" class="p-3.5 rounded-2xl border border-slate-200/90 hover:border-blue-400 hover:bg-blue-50/40 transition flex items-center justify-between group">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Verifikasi Bayar</div>
                        <div class="text-xl font-black text-slate-900 mt-0.5"><?= (int)($alerts['pending_payments_count'] ?? 0) ?></div>
                    </div>
                    <?php if (($alerts['pending_payments_count'] ?? 0) > 0): ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 animate-pulse">Action</span>
                    <?php else: ?>
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                    <?php endif; ?>
                </a>

                <a href="<?= url('admin/invoices') ?>" class="p-3.5 rounded-2xl border border-slate-200/90 hover:border-rose-400 hover:bg-rose-50/40 transition flex items-center justify-between group">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Tagihan Overdue</div>
                        <div class="text-xl font-black <?= ($alerts['overdue_invoices_count'] ?? 0) > 0 ? 'text-rose-600' : 'text-slate-900' ?> mt-0.5">
                            <?= (int)($alerts['overdue_invoices_count'] ?? 0) ?>
                        </div>
                    </div>
                    <?php if (($alerts['overdue_invoices_count'] ?? 0) > 0): ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">Overdue</span>
                    <?php else: ?>
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                    <?php endif; ?>
                </a>

                <a href="<?= url('admin/maintenance') ?>" class="p-3.5 rounded-2xl border border-slate-200/90 hover:border-amber-400 hover:bg-amber-50/40 transition flex items-center justify-between group">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Kerusakan Urgent</div>
                        <div class="text-xl font-black <?= ($alerts['urgent_maintenance_count'] ?? 0) > 0 ? 'text-amber-600' : 'text-slate-900' ?> mt-0.5">
                            <?= (int)($alerts['urgent_maintenance_count'] ?? 0) ?>
                        </div>
                    </div>
                    <?php if (($alerts['urgent_maintenance_count'] ?? 0) > 0): ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">Prioritas</span>
                    <?php else: ?>
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                    <?php endif; ?>
                </a>

                <a href="<?= url('admin/invoices') ?>" class="p-3.5 rounded-2xl border border-slate-200/90 hover:border-blue-400 hover:bg-blue-50/40 transition flex items-center justify-between group">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Due Dalam 7 Hari</div>
                        <div class="text-xl font-black text-slate-900 mt-0.5"><?= (int)($alerts['upcoming_due_invoices_count'] ?? 0) ?></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">Invoice</span>
                </a>

                <a href="<?= url('admin/contracts') ?>" class="p-3.5 rounded-2xl border border-slate-200/90 hover:border-blue-400 hover:bg-blue-50/40 transition flex items-center justify-between group">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">Kontrak Habis (30h)</div>
                        <div class="text-xl font-black text-slate-900 mt-0.5"><?= (int)($operations['expiring_contracts_count'] ?? 0) ?></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">Penyewa</span>
                </a>

                <a href="<?= url('admin/notifications') ?>" class="p-3.5 rounded-2xl border border-slate-200/90 hover:border-blue-400 hover:bg-blue-50/40 transition flex items-center justify-between group">
                    <div>
                        <div class="text-xs text-slate-500 font-medium">WA Gagal / Retry</div>
                        <div class="text-xl font-black <?= ($alerts['failed_whatsapp_count'] ?? 0) > 0 ? 'text-rose-600' : 'text-slate-900' ?> mt-0.5">
                            <?= (int)($alerts['failed_whatsapp_count'] ?? 0) ?>
                        </div>
                    </div>
                    <?php if (($alerts['failed_whatsapp_count'] ?? 0) > 0): ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">Check</span>
                    <?php else: ?>
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. Monthly Financial Trend & Expense Category Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Financial Trend (6 Bulan) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-6">
            <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-4 flex items-center justify-between">
                <span>Tren Keuangan (6 Bulan Terakhir)</span>
                <span class="text-[11px] font-medium text-slate-400">Pendapatan vs Pengeluaran</span>
            </h3>

            <?php if (empty($monthly_trend)): ?>
                <div class="py-12 text-center text-slate-400 text-xs">Belum ada riwayat transaksi 6 bulan terakhir.</div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php
                        $maxVal = 1;
                        foreach ($monthly_trend as $mt) {
                            $maxVal = max($maxVal, $mt['income'], $mt['expense']);
                        }
                    ?>
                    <?php foreach ($monthly_trend as $mt): ?>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800"><?= e($mt['label']) ?></span>
                                <div class="space-x-3 text-[11px]">
                                    <span class="text-emerald-600 font-bold">Masuk: Rp <?= number_format($mt['income'], 0, ',', '.') ?></span>
                                    <span class="text-rose-600 font-bold">Keluar: Rp <?= number_format($mt['expense'], 0, ',', '.') ?></span>
                                    <span class="font-black <?= $mt['net'] >= 0 ? 'text-blue-600' : 'text-rose-700' ?>">
                                        Net: Rp <?= number_format($mt['net'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden flex gap-1">
                                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: <?= round(($mt['income'] / $maxVal) * 100) ?>%"></div>
                                <div class="bg-rose-400 h-2.5 rounded-full transition-all duration-500" style="width: <?= round(($mt['expense'] / $maxVal) * 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Expenses by Category Breakdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 p-6">
            <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-4 flex items-center justify-between">
                <span>Komposisi Pengeluaran Berdasarkan Kategori</span>
                <span class="text-[11px] font-medium text-slate-400">Periode Terpilih</span>
            </h3>

            <?php if (empty($financial['expenses_by_category'])): ?>
                <div class="py-12 text-center text-slate-400 text-xs">Tidak ada transaksi pengeluaran pada periode ini.</div>
            <?php else: ?>
                <div class="space-y-3.5">
                    <?php
                        $totalExp = max(1.0, (float)($financial['total_expense'] ?? 1.0));
                    ?>
                    <?php foreach ($financial['expenses_by_category'] as $cat): ?>
                        <?php $pct = round(((float)$cat['total'] / $totalExp) * 100, 1); ?>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800"><?= e($cat['name']) ?> <span class="text-slate-400 font-normal">(<?= (int)$cat['count'] ?>x)</span></span>
                                <span class="font-bold text-slate-900">Rp <?= number_format((float)$cat['total'], 0, ',', '.') ?> <span class="text-rose-600 font-bold">(<?= $pct ?>%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-rose-500 to-red-500 h-2 rounded-full transition-all duration-500" style="width: <?= min(100, $pct) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 4. Revenue Per Room Breakdown & Recent Activity Audit -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Per Room Breakdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col">
            <div class="p-4 sm:px-6 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Pendapatan Per Kamar (Periode Ini)</h3>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Kamar</th>
                            <th class="py-3 px-4">Nama Kamar</th>
                            <th class="py-3 px-4 text-right">Pendapatan Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($revenue_per_room)): ?>
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-400 text-xs">Belum ada pendapatan terverifikasi untuk periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($revenue_per_room as $rpr): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4 font-extrabold text-slate-900 text-xs">Kamar <?= e($rpr['room_number']) ?></td>
                                    <td class="py-3 px-4 text-xs font-medium text-slate-600"><?= e($rpr['room_name'] ?? 'Standar') ?></td>
                                    <td class="py-3 px-4 text-right font-extrabold text-xs <?= (float)$rpr['total_revenue'] > 0 ? 'text-emerald-600' : 'text-slate-400' ?>">
                                        Rp <?= number_format((float)$rpr['total_revenue'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity Logs Audit -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col">
            <div class="p-4 sm:px-6 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Aktivitas Terakhir Sistem</h3>
                <span class="text-[10px] font-bold uppercase text-slate-400 px-2 py-0.5 bg-slate-100 rounded-md">Audit Trail</span>
            </div>
            <div class="p-4 sm:px-6 flex-1 space-y-3">
                <?php if (empty($recent_logs)): ?>
                    <div class="py-8 text-center text-slate-400 text-xs">Belum ada catatan aktivitas baru.</div>
                <?php else: ?>
                    <?php foreach ($recent_logs as $log): ?>
                        <div class="text-xs border-b border-slate-100 pb-3 last:border-none last:pb-0 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5">
                                <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] mt-0.5 flex-shrink-0">
                                    <?= strtoupper(substr($log['user_name'] ?? 'S', 0, 1)) ?>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800"><?= e($log['user_name'] ?? 'System') ?></span>
                                    <span class="text-slate-600 ml-1"><?= e($log['description'] ?? $log['action'] ?? '-') ?></span>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono whitespace-nowrap"><?= e($log['created_at']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
