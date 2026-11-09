<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 uppercase tracking-wide">
                    Operasional Harian
                </span>
                <span class="text-xs text-slate-500">Pusat Kendali Pengelola Kost</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mt-1">Admin Operations Dashboard</h1>
            <p class="text-slate-500 text-sm">Kelola kamar, verifikasi pembayaran, tangani kerusakan, dan pantau kontrak penyewa.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= url('admin/payments/cash') ?>" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-3 py-2 rounded-xl text-xs shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Catat Bayar Tunai
            </a>
            <a href="<?= url('admin/expenses/create') ?>" class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold px-3 py-2 rounded-xl text-xs shadow-sm transition">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                Catat Pengeluaran
            </a>
            <a href="<?= url('admin/reports/export/operational') ?>" class="inline-flex items-center gap-1.5 bg-slate-700 hover:bg-slate-800 text-white font-semibold px-3 py-2 rounded-xl text-xs shadow-sm transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Export Operasional (CSV)
            </a>
        </div>
    </div>

    <!-- 1. Operational Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kamar Terisi</div>
            <div class="text-2xl font-bold text-emerald-600"><?= (int)($operations['occupied_rooms'] ?? 0) ?> / <?= (int)($operations['total_rooms'] ?? 0) ?></div>
            <div class="text-xs text-slate-500 mt-1">Okupansi: <?= $operations['occupancy_rate'] ?? 0 ?>%</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kamar Kosong</div>
            <div class="text-2xl font-bold text-slate-700"><?= (int)($operations['vacant_rooms'] ?? 0) ?></div>
            <div class="text-xs text-slate-500 mt-1">Siap huni</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Booking Pending</div>
            <div class="text-2xl font-bold <?= ($operations['pending_bookings_count'] ?? 0) > 0 ? 'text-amber-600' : 'text-slate-700' ?>">
                <?= (int)($operations['pending_bookings_count'] ?? 0) ?>
            </div>
            <div class="text-xs text-slate-500 mt-1">Menunggu konfirmasi</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Perlu Verifikasi</div>
            <div class="text-2xl font-bold <?= ($alerts['pending_payments_count'] ?? 0) > 0 ? 'text-rose-600' : 'text-slate-700' ?>">
                <?= (int)($alerts['pending_payments_count'] ?? 0) ?>
            </div>
            <div class="text-xs text-slate-500 mt-1">Bukti transfer masuk</div>
        </div>
    </div>

    <!-- 2. Priority Action Lists (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Payment Verifications Queue -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Antrean Verifikasi Pembayaran</h3>
                </div>
                <a href="<?= url('admin/payments') ?>" class="text-xs text-blue-600 hover:underline font-semibold">Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if (empty($pending_payments)): ?>
                    <div class="p-6 text-center text-slate-400 text-xs">
                        <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 text-emerald-400"></i>
                        Semua pembayaran telah diverifikasi.
                    </div>
                <?php else: ?>
                    <?php foreach ($pending_payments as $pp): ?>
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                            <div>
                                <div class="font-bold text-slate-800 text-sm"><?= e($pp['tenant_name'] ?? 'Penyewa') ?></div>
                                <div class="text-xs text-slate-500 font-mono"><?= e($pp['payment_number']) ?> &bull; <?= e($pp['payment_date']) ?></div>
                                <div class="text-xs text-emerald-600 font-bold mt-0.5">Rp <?= number_format((float)$pp['amount'], 0, ',', '.') ?> (<?= strtoupper(e($pp['payment_method'])) ?>)</div>
                            </div>
                            <a href="<?= url('admin/payments/' . $pp['id']) ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                                Verifikasi
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Urgent Maintenance Tickets -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="wrench" class="w-4 h-4 text-rose-500"></i>
                    <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Laporan Kerusakan Mendesak</h3>
                </div>
                <a href="<?= url('admin/maintenance') ?>" class="text-xs text-blue-600 hover:underline font-semibold">Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if (empty($urgent_maintenance)): ?>
                    <div class="p-6 text-center text-slate-400 text-xs">
                        <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 text-emerald-400"></i>
                        Tidak ada laporan kerusakan prioritas tinggi yang tertunda.
                    </div>
                <?php else: ?>
                    <?php foreach ($urgent_maintenance as $um): ?>
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $um['priority'] === 'urgent' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' ?>">
                                        <?= e($um['priority']) ?>
                                    </span>
                                    <span class="font-bold text-slate-800 text-sm"><?= e($um['title']) ?></span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    <?= $um['room_number'] ? 'Kamar ' . e($um['room_number']) : 'Fasilitas Umum' ?> &bull; Pelapor: <?= e($um['tenant_name'] ?? '-') ?>
                                </div>
                            </div>
                            <a href="<?= url('admin/maintenance/' . $um['id']) ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition">
                                Tindak Lanjut
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 3. Expiring Contracts & Operational Activity Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expiring Contracts within 30 days -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                    <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Kontrak Akan Berakhir (30 Hari)</h3>
                </div>
                <a href="<?= url('contracts') ?>" class="text-xs text-blue-600 hover:underline font-semibold">Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if (empty($expiring_contracts)): ?>
                    <div class="p-6 text-center text-slate-400 text-xs">
                        Tidak ada kontrak sewa yang akan berakhir dalam 30 hari ke depan.
                    </div>
                <?php else: ?>
                    <?php foreach ($expiring_contracts as $ec): ?>
                        <?php $daysLeft = max(0, (int)ceil((strtotime($ec['end_date']) - time()) / 86400)); ?>
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                            <div>
                                <div class="font-bold text-slate-800 text-sm"><?= e($ec['tenant_name']) ?> (Kamar <?= e($ec['room_number']) ?>)</div>
                                <div class="text-xs text-slate-500 font-mono"><?= e($ec['contract_number']) ?> &bull; Berakhir <?= e($ec['end_date']) ?></div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $daysLeft <= 7 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= $daysLeft ?> hari lagi
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Operational Activity Logs -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Log Aktivitas Operasional</h3>
            </div>
            <div class="p-4 space-y-3">
                <?php if (empty($recent_logs)): ?>
                    <div class="py-6 text-center text-slate-400 text-xs">Belum ada catatan aktivitas.</div>
                <?php else: ?>
                    <?php foreach ($recent_logs as $log): ?>
                        <div class="text-xs border-b border-slate-100 pb-2.5 last:border-none last:pb-0 flex items-start justify-between gap-2">
                            <div>
                                <span class="font-bold text-slate-800"><?= e($log['user_name'] ?? 'System') ?></span>
                                <span class="text-slate-600 ml-1"><?= e($log['description'] ?? $log['action'] ?? '-') ?></span>
                            </div>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap"><?= e($log['created_at']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
