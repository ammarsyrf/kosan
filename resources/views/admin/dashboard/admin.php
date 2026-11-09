<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-2">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    Operasional Harian
                </span>
                <span class="text-xs text-slate-400">Pusat Kendali Pengelola Kost</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">
                Admin Operations Dashboard
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
                Kelola kamar, verifikasi pembayaran, tangani kerusakan, dan pantau kontrak penyewa.
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="<?= url('admin/payments/cash') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/25 hover:shadow-lg transition transform active:scale-95">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Catat Bayar Tunai</span>
            </a>
            <a href="<?= url('admin/expenses/create') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md shadow-rose-500/25 hover:shadow-lg transition transform active:scale-95">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Catat Pengeluaran</span>
            </a>
            <a href="<?= url('admin/reports/export/operational') ?>" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold px-4 py-2.5 rounded-xl text-xs shadow-sm hover:shadow transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-teal-600"></i>
                <span>Export Operasional (CSV)</span>
            </a>
        </div>
    </div>

    <!-- 1. Operational Quick Stats (High-Contrast Metric Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kamar Terisi -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar Terisi</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shadow-sm">
                        <i data-lucide="door-closed" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-900 tracking-tight">
                    <?= (int)($operations['occupied_rooms'] ?? 0) ?> <span class="text-base font-semibold text-slate-400">/ <?= (int)($operations['total_rooms'] ?? 0) ?></span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 space-y-1.5">
                <div class="flex items-center justify-between text-xs font-semibold">
                    <span class="text-slate-500">Okupansi</span>
                    <span class="text-emerald-600 font-bold"><?= $operations['occupancy_rate'] ?? 0 ?>%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: <?= min(100, max(0, (float)($operations['occupancy_rate'] ?? 0))) ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Kamar Kosong -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar Kosong</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-sm">
                        <i data-lucide="key" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-900 tracking-tight">
                    <?= (int)($operations['vacant_rooms'] ?? 0) ?> <span class="text-xs font-semibold text-slate-400">Unit</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs text-blue-600 font-medium">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <span>Siap disewakan & huni</span>
            </div>
        </div>

        <!-- Booking Pending -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Booking Pending</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold shadow-sm">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black <?= ($operations['pending_bookings_count'] ?? 0) > 0 ? 'text-amber-600' : 'text-slate-900' ?> tracking-tight">
                    <?= (int)($operations['pending_bookings_count'] ?? 0) ?>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Menunggu konfirmasi</span>
                <?php if (($operations['pending_bookings_count'] ?? 0) > 0): ?>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Perlu Review</span>
                <?php else: ?>
                    <span class="text-slate-400">Nihil</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Perlu Verifikasi Pembayaran -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perlu Verifikasi</span>
                    <div class="w-10 h-10 rounded-xl <?= ($alerts['pending_payments_count'] ?? 0) > 0 ? 'bg-rose-50 text-rose-600 ring-2 ring-rose-200' : 'bg-slate-50 text-slate-600' ?> flex items-center justify-center font-bold shadow-sm">
                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-black <?= ($alerts['pending_payments_count'] ?? 0) > 0 ? 'text-rose-600' : 'text-slate-900' ?> tracking-tight">
                    <?= (int)($alerts['pending_payments_count'] ?? 0) ?>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Bukti transfer masuk</span>
                <?php if (($alerts['pending_payments_count'] ?? 0) > 0): ?>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 animate-pulse">Action Required</span>
                <?php else: ?>
                    <span class="text-emerald-600 font-medium flex items-center gap-1">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Bersih
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. Priority Action Queues (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Payment Verifications Queue -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col">
            <div class="p-4 sm:px-6 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-xs sm:text-sm uppercase tracking-wider">Antrean Verifikasi Pembayaran</h3>
                    <?php if (!empty($pending_payments)): ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            <?= count($pending_payments) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <a href="<?= url('admin/payments') ?>" class="text-xs text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-1">
                    Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                <?php if (empty($pending_payments)): ?>
                    <div class="p-8 text-center space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-700">Tidak Ada Pembayaran Pending</div>
                        <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Seluruh transaksi transfer bank dan QRIS telah diverifikasi oleh pengelola.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pending_payments as $pp): ?>
                        <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-slate-50/80 transition gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white font-bold text-xs flex items-center justify-center shadow-sm flex-shrink-0">
                                    <?= strtoupper(substr($pp['tenant_name'] ?? 'P', 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm"><?= e($pp['tenant_name'] ?? 'Penyewa') ?></div>
                                    <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                        <?= e($pp['payment_number']) ?> &bull; <?= e($pp['payment_date']) ?>
                                    </div>
                                    <div class="text-xs text-emerald-600 font-extrabold mt-0.5">
                                        Rp <?= number_format((float)$pp['amount'], 0, ',', '.') ?>
                                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 ml-1">
                                            <?= strtoupper(e($pp['payment_method'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= url('admin/payments/' . $pp['id']) ?>" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-500/20 transition whitespace-nowrap">
                                Verifikasi &rarr;
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Urgent Maintenance Tickets -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col">
            <div class="p-4 sm:px-6 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i data-lucide="wrench" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-xs sm:text-sm uppercase tracking-wider">Laporan Kerusakan Mendesak</h3>
                    <?php if (!empty($urgent_maintenance)): ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                            <?= count($urgent_maintenance) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <a href="<?= url('admin/maintenance') ?>" class="text-xs text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-1">
                    Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                <?php if (empty($urgent_maintenance)): ?>
                    <div class="p-8 text-center space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-700">Fasilitas Kost Aman & Terawat</div>
                        <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Tidak ada laporan kerusakan mendesak yang menunggu penanganan teknisi.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($urgent_maintenance as $um): ?>
                        <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-slate-50/80 transition gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase <?= $um['priority'] === 'urgent' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-amber-100 text-amber-800 border border-amber-200' ?>">
                                        <?= e($um['priority']) ?>
                                    </span>
                                    <span class="font-bold text-slate-900 text-sm"><?= e($um['title']) ?></span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                    <span class="font-semibold text-slate-700"><?= $um['room_number'] ? 'Kamar ' . e($um['room_number']) : 'Fasilitas Umum' ?></span>
                                    <span>&bull;</span>
                                    <span>Pelapor: <?= e($um['tenant_name'] ?? '-') ?></span>
                                </div>
                            </div>
                            <a href="<?= url('admin/maintenance/' . $um['id']) ?>" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition whitespace-nowrap">
                                Tindak Lanjut &rarr;
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 3. Expiring Contracts & Operational Activity Logs (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expiring Contracts within 30 days -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col">
            <div class="p-4 sm:px-6 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-xs sm:text-sm uppercase tracking-wider">Kontrak Akan Berakhir (30 Hari)</h3>
                </div>
                <a href="<?= url('contracts') ?>" class="text-xs text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-1">
                    Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                <?php if (empty($expiring_contracts)): ?>
                    <div class="p-8 text-center space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto">
                            <i data-lucide="calendar-check" class="w-6 h-6"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-700">Semua Kontrak Sewa Aktif Aman</div>
                        <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Tidak ada kontrak sewa yang akan jatuh tempo dalam kurun waktu 30 hari ke depan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($expiring_contracts as $ec): ?>
                        <?php $daysLeft = max(0, (int)ceil((strtotime($ec['end_date']) - time()) / 86400)); ?>
                        <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-slate-50/80 transition gap-3">
                            <div>
                                <div class="font-bold text-slate-900 text-sm"><?= e($ec['tenant_name']) ?> <span class="text-xs font-semibold text-blue-600">(Kamar <?= e($ec['room_number']) ?>)</span></div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    <?= e($ec['contract_number']) ?> &bull; Berakhir: <?= e($ec['end_date']) ?>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $daysLeft <= 7 ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-amber-100 text-amber-800 border border-amber-200' ?> whitespace-nowrap">
                                <?= $daysLeft ?> hari lagi
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Operational Activity Logs -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden flex flex-col">
            <div class="p-4 sm:px-6 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-xs sm:text-sm uppercase tracking-wider">Log Aktivitas Operasional</h3>
                </div>
            </div>

            <div class="p-4 sm:px-6 flex-1 space-y-3">
                <?php if (empty($recent_logs)): ?>
                    <div class="py-8 text-center text-slate-400 text-xs">Belum ada catatan aktivitas baru hari ini.</div>
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
