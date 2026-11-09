<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Laporan Perbaikan Kamar</h1>
        <p class="text-slate-500 text-sm">Laporkan kendala kerusakan fasilitas kos atau kamar untuk segera diperbaiki pengelola.</p>
    </div>
    <?php if ($activeContract): ?>
        <a href="<?= url('tenant/maintenance/create') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Buat Laporan Baru
        </a>
    <?php endif; ?>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 text-sm">
        Daftar Laporan Kerusakan Anda
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-3 px-4">No. Laporan</th>
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Kamar</th>
                    <th class="py-3 px-4">Kategori & Judul</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Anda belum pernah mengajukan laporan perbaikan fasilitas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 text-xs">#<?= $r->id ?></td>
                            <td class="py-3 px-4 text-xs"><?= date('d M Y', strtotime($r->created_at)) ?></td>
                            <td class="py-3 px-4 text-xs font-semibold text-slate-800">Kamar <?= e($r->room_number) ?></td>
                            <td class="py-3 px-4 max-w-sm">
                                <span class="text-xs font-semibold text-blue-600"><?= e($r->category_name) ?></span>
                                <div class="font-bold text-slate-800 text-sm"><?= e($r->title) ?></div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php
                                $sBadge = match($r->status) {
                                    'resolved' => 'bg-emerald-100 text-emerald-800 font-bold',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'reviewed' => 'bg-purple-100 text-purple-800',
                                    'waiting' => 'bg-amber-100 text-amber-800',
                                    'rejected' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= $sBadge ?>">
                                    <?= str_replace('_', ' ', e($r->status)) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="<?= url('tenant/maintenance/' . $r->id) ?>" class="text-xs font-semibold text-blue-600 hover:underline">
                                    Lihat Perkembangan &rarr;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
