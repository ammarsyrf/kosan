<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Manajemen Booking</h1>
        <p class="text-slate-500 text-sm">Kelola pemesanan kamar sebelum diterbitkan menjadi kontrak sewa.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/bookings/create') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Tambah Booking
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50">
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total:</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700"><?= count($bookings) ?></span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Kamar</th>
                    <th class="py-3 px-4">Penyewa</th>
                    <th class="py-3 px-4">Periode Booking</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Kedaluwarsa</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data booking.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-mono font-medium text-slate-900">#<?= (int)$b->id ?></td>
                            <td class="py-3 px-4 font-semibold text-slate-800"><?= e($b->room_number) ?></td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-800"><?= e($b->tenant_name) ?></div>
                                <div class="text-xs text-slate-400"><?= e($b->tenant_phone) ?></div>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <div><?= e($b->start_date) ?> s/d <?= e($b->end_date) ?></div>
                            </td>
                            <td class="py-3 px-4">
                                <?php
                                $badgeClass = match ($b->status) {
                                    'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                    'confirmed' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    'converted' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'cancelled' => 'bg-slate-100 text-slate-700 border border-slate-200',
                                    'expired' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                    default => 'bg-slate-100 text-slate-700'
                                };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                    <?= ucfirst(e($b->status)) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <?php if ($b->isConfirmed()): ?>
                                    <span class="text-emerald-600 font-medium">Tetap Aktif (Confirmed)</span>
                                <?php elseif ($b->expires_at): ?>
                                    <span class="<?= $b->isExpired() && $b->isPending() ? 'text-rose-600 font-bold' : 'text-slate-600' ?>">
                                        <?= e(date('d M Y H:i', strtotime($b->expires_at))) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if ($b->isPending() && !$b->isExpired()): ?>
                                        <form action="<?= url('admin/bookings/' . $b->id . '/confirm') ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-2.5 py-1 text-xs font-medium rounded bg-emerald-600 hover:bg-emerald-700 text-white transition">Konfirmasi</button>
                                        </form>
                                        <form action="<?= url('admin/bookings/' . $b->id . '/cancel') ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" onclick="return confirm('Batalkan booking ini?')" class="px-2.5 py-1 text-xs font-medium rounded bg-slate-200 hover:bg-slate-300 text-slate-700 transition">Batal</button>
                                        </form>
                                    <?php elseif ($b->isConfirmed()): ?>
                                        <form action="<?= url('admin/bookings/' . $b->id . '/convert') ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-2.5 py-1 text-xs font-medium rounded bg-blue-600 hover:bg-blue-700 text-white transition">Jadikan Kontrak</button>
                                        </form>
                                        <form action="<?= url('admin/bookings/' . $b->id . '/cancel') ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" onclick="return confirm('Batalkan booking ini?')" class="px-2.5 py-1 text-xs font-medium rounded bg-slate-200 hover:bg-slate-300 text-slate-700 transition">Batal</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400">Selesai</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
