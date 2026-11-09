<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Kamar</h1>
            <p class="text-sm text-slate-500 mt-1">
                Kapasitas usaha: <?= count($rooms) ?> dari maksimal 9 kamar terdaftar.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= url('admin/facilities') ?>" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                <i data-lucide="sparkles" class="w-4 h-4 mr-1.5 text-slate-500"></i> Kelola Fasilitas
            </a>
            <?php if (count($rooms) < 10): ?>
                <a href="<?= url('admin/rooms/create') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i> Tambah Kamar
                </a>
            <?php else: ?>
                <span class="inline-flex items-center px-3 py-2 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-xs font-medium">
                    <i data-lucide="alert-triangle" class="w-4 h-4 mr-1.5"></i> Kapasitas Penuh (< 10)
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">No. Kamar</th>
                        <th class="px-6 py-3.5">Nama & Tipe</th>
                        <th class="px-6 py-3.5">Harga Sewa / Bulan</th>
                        <th class="px-6 py-3.5">Fasilitas</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($rooms)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Belum ada data kamar. Silakan tambahkan kamar pertama Anda.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rooms as $room): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <?= e($room->room_number) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800"><?= e($room->name) ?></div>
                                    <?php if ($room->description): ?>
                                        <div class="text-xs text-slate-400 mt-0.5 truncate max-w-xs"><?= e($room->description) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    <?= e($room->formattedPrice()) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        <?php if (!empty($room->facilities)): ?>
                                            <?php foreach ($room->facilities as $fac): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600">
                                                    <?= e($fac['name']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 italic">Tanpa fasilitas</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $statusBadges = [
                                        'available' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'occupied' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'maintenance' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'inactive' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    ];
                                    $statusLabels = [
                                        'available' => 'Tersedia',
                                        'occupied' => 'Terisi',
                                        'maintenance' => 'Perbaikan',
                                        'inactive' => 'Nonaktif',
                                    ];
                                    $badgeClass = $statusBadges[$room->status] ?? 'bg-slate-100 text-slate-600';
                                    $label = $statusLabels[$room->status] ?? ucfirst($room->status);
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border <?= $badgeClass ?>">
                                        <?= e($label) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="<?= url("admin/rooms/{$room->id}/edit") ?>" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">
                                        Ubah
                                    </a>
                                    <?php if ($room->status !== 'occupied'): ?>
                                        <form method="POST" action="<?= url("admin/rooms/{$room->id}/delete") ?>" class="inline" onsubmit="return confirm('Hapus kamar <?= e($room->room_number) ?>? Data kamar yang dihapus tidak dapat dipulihkan.')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs ml-2">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

