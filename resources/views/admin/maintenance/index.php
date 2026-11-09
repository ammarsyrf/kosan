<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Laporan Maintenance & Perbaikan</h1>
        <p class="text-slate-500 text-sm">Kelola keluhan kerusakan fasilitas kos dan kamar dari para penghuni.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
    <div class="p-4 bg-slate-50 border-b border-slate-200">
        <form method="GET" action="<?= url('admin/maintenance') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block text-slate-600 font-semibold mb-1">Status</label>
                <select name="status" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg bg-white">
                    <option value="">Semua Status</option>
                    <option value="submitted" <?= ($filters['status'] ?? '') === 'submitted' ? 'selected' : '' ?>>Submitted (Baru)</option>
                    <option value="reviewed" <?= ($filters['status'] ?? '') === 'reviewed' ? 'selected' : '' ?>>Reviewed (Ditinjau)</option>
                    <option value="in_progress" <?= ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress (Dikerjakan)</option>
                    <option value="waiting" <?= ($filters['status'] ?? '') === 'waiting' ? 'selected' : '' ?>>Waiting (Menunggu Sparepart/Teknisi)</option>
                    <option value="resolved" <?= ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved (Selesai)</option>
                    <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected (Ditolak)</option>
                </select>
            </div>
            <div>
                <label class="block text-slate-600 font-semibold mb-1">Prioritas</label>
                <select name="priority" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg bg-white">
                    <option value="">Semua Prioritas</option>
                    <option value="low" <?= ($filters['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= ($filters['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="urgent" <?= ($filters['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                </select>
            </div>
            <div>
                <label class="block text-slate-600 font-semibold mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg bg-white">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c->id ?>" <?= ($filters['category_id'] ?? '') == $c->id ? 'selected' : '' ?>><?= e($c->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-slate-600 font-semibold mb-1">Kamar</label>
                <select name="room_id" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg bg-white">
                    <option value="">Semua Kamar</option>
                    <?php foreach ($rooms as $r): ?>
                        <option value="<?= $r->id ?>" <?= ($filters['room_id'] ?? '') == $r->id ? 'selected' : '' ?>>Kamar <?= e($r->room_number) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-900 transition">Filter</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-3 px-4">ID / Tanggal</th>
                    <th class="py-3 px-4">Kamar & Penyewa</th>
                    <th class="py-3 px-4">Kategori & Judul Laporan</th>
                    <th class="py-3 px-4 text-center">Prioritas</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-center">Foto</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada laporan perbaikan/kerusakan fasilitas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $req): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 whitespace-nowrap text-xs">
                                <div class="font-bold text-slate-900 font-mono">#<?= $req->id ?></div>
                                <div class="text-slate-400"><?= date('d M Y H:i', strtotime($req->created_at)) ?></div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">Kamar <?= e($req->room_number) ?></div>
                                <div class="text-xs text-slate-500"><?= e($req->tenant_name) ?> (<?= e($req->tenant_phone) ?>)</div>
                            </td>
                            <td class="py-3 px-4 max-w-sm">
                                <div class="text-xs font-semibold text-blue-600"><?= e($req->category_name) ?></div>
                                <div class="font-bold text-slate-800 truncate" title="<?= e($req->title) ?>"><?= e($req->title) ?></div>
                                <div class="text-xs text-slate-500 truncate" title="<?= e($req->description) ?>"><?= e($req->description) ?></div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php
                                $pBadge = match($req->priority) {
                                    'urgent' => 'bg-rose-100 text-rose-800 border-rose-300 font-bold animate-pulse',
                                    'high' => 'bg-orange-100 text-orange-800 border-orange-200 font-semibold',
                                    'medium' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border uppercase <?= $pBadge ?>">
                                    <?= e($req->priority) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php
                                $sBadge = match($req->status) {
                                    'resolved' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
                                    'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200 font-medium',
                                    'reviewed' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'waiting' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs border capitalize <?= $sBadge ?>">
                                    <?= str_replace('_', ' ', e($req->status)) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center text-xs font-semibold text-slate-600">
                                <?= count($req->attachments) ?>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="<?= url('admin/maintenance/' . $req->id) ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold text-xs rounded-lg transition">
                                    Detail & Tindak Lanjut
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
