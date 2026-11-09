<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kontrak Sewa Kamar</h1>
        <p class="text-slate-500 text-sm">Daftar seluruh perjanjian sewa aktif maupun riwayat masa lalu.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/contracts/create') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Buat Kontrak Langsung
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Kontrak: <strong class="text-blue-600"><?= count($contracts) ?></strong></span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="py-3 px-4">No. Kontrak</th>
                    <th class="py-3 px-4">Kamar</th>
                    <th class="py-3 px-4">Penyewa</th>
                    <th class="py-3 px-4">Periode Kontrak</th>
                    <th class="py-3 px-4">Tarif Bulanan</th>
                    <th class="py-3 px-4">Tgl Tagihan</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($contracts)): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">Belum ada kontrak sewa yang tercatat.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contracts as $c): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-mono font-medium text-slate-900"><?= e($c->contract_number) ?></td>
                            <td class="py-3 px-4 font-semibold text-slate-800"><?= e($c->room_number) ?></td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-800"><?= e($c->tenant_name) ?></div>
                                <div class="text-xs text-slate-400"><?= e($c->tenant_phone) ?></div>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <div><?= e($c->start_date) ?> s/d <?= e($c->end_date) ?></div>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-900">
                                <?= $c->formattedPrice() ?>
                            </td>
                            <td class="py-3 px-4 text-xs font-semibold text-blue-700">
                                Tgl <?= (int)$c->billing_anchor_day ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php
                                $badgeClass = match ($c->status) {
                                    'active' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    'terminated' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                    'completed' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'extended' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                    default => 'bg-slate-100 text-slate-700'
                                };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                                    <?= ucfirst(e($c->status)) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="<?= url('admin/contracts/' . $c->id) ?>" class="px-3 py-1 text-xs font-medium rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
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
