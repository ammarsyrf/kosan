<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Laporan Perbaikan Kamar</h1>
        <p class="text-slate-500 text-sm">Laporkan kendala kerusakan fasilitas kos atau kamar untuk segera diperbaiki pengelola.</p>
    </div>
    <a href="<?= url('tenant/maintenance/create') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg transition transform active:scale-95 cursor-pointer">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span>Buat Laporan Baru</span>
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 text-sm flex items-center justify-between">
        <span>Daftar Laporan Kerusakan Anda</span>
        <span class="text-xs font-semibold text-slate-500">Total: <?= count($requests ?? []) ?> Laporan</span>
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
                        <td colspan="6" class="py-12 text-center">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto shadow-xs">
                                    <i data-lucide="wrench" class="w-6 h-6"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-800">Belum Ada Laporan Kerusakan</div>
                                <p class="text-xs text-slate-400 leading-relaxed">
                                    Semua fasilitas kamar Anda dalam kondisi baik. Jika ada kendala (AC, air, lampu, dll), buat laporan langsung di sini.
                                </p>
                                <div class="pt-1">
                                    <a href="<?= url('tenant/maintenance/create') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                        <span>Laporkan Kerusakan Sekarang</span>
                                    </a>
                                </div>
                            </div>
                        </td>
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
