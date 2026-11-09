<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Selamat Datang, <?= e($tenant->name ?? 'Penyewa') ?></h1>
            <p class="text-slate-500 text-sm">Kelola masa sewa, pengajuan perpanjangan, dan informasi kamar Anda.</p>
        </div>
    </div>

    <?php if (!$activeContract): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center max-w-lg mx-auto">
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                <i data-lucide="home" class="w-6 h-6"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800 mb-2">Belum Ada Kontrak Aktif</h2>
            <p class="text-slate-500 text-sm mb-4">Saat ini Anda tidak memiliki kontrak sewa kamar yang sedang aktif. Silakan hubungi pengelola kos jika Anda sudah memesan kamar.</p>
        </div>
    <?php else: ?>
        <!-- Active Contract Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-900 to-indigo-800 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-400 text-emerald-950 uppercase tracking-wide">
                        Kontrak Aktif
                    </span>
                    <h2 class="text-2xl font-bold mt-2">Kamar <?= e($activeContract->room_number) ?></h2>
                    <p class="text-blue-200 text-sm font-mono mt-0.5">No. Kontrak: <?= e($activeContract->contract_number) ?></p>
                </div>
                <div class="text-right">
                    <div class="text-xs uppercase tracking-wider text-blue-200">Tarif Sewa</div>
                    <div class="text-2xl font-bold"><?= $activeContract->formattedPrice() ?> <span class="text-sm font-normal text-blue-200">/ bulan</span></div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-200">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Periode Kontrak</span>
                    <div class="text-slate-800 font-semibold mt-1"><?= e($activeContract->start_date) ?> s/d <?= e($activeContract->end_date) ?></div>
                    <span class="text-xs text-slate-500">Berakhir dalam <?= max(0, (int)ceil((strtotime($activeContract->end_date) - time()) / 86400)) ?> hari lagi</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal Tagihan Rutin</span>
                    <div class="text-blue-600 font-bold mt-1">Setiap Tanggal <?= (int)$activeContract->billing_anchor_day ?></div>
                    <span class="text-xs text-slate-500">Jatuh tempo pembayaran bulanan</span>
                </div>
            </div>

            <!-- Action buttons for Tenant -->
            <div class="p-6 bg-slate-50 flex flex-wrap items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    Perlu perpanjang masa tinggal atau checkout lebih awal?
                </div>
                <div class="flex items-center gap-3">
                    <?php if (!$pendingExtension): ?>
                        <a href="<?= url('tenant/extension') ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                            Ajukan Perpanjangan
                        </a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            Pengajuan Perpanjangan Menunggu Persetujuan
                        </span>
                    <?php endif; ?>

                    <?php if (!$pendingCheckout): ?>
                        <a href="<?= url('tenant/early-checkout') ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Ajukan Early Checkout
                        </a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                            Pengajuan Checkout Menunggu Persetujuan
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Past Contracts -->
    <?php if (!empty($pastContracts)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="font-bold text-slate-800 mb-4 text-sm uppercase tracking-wider">Riwayat Kontrak Sebelumnya</h3>
            <div class="space-y-3">
                <?php foreach ($pastContracts as $pc): ?>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 text-sm">
                        <div>
                            <div class="font-medium text-slate-800">Kamar <?= e($pc->room_number) ?> (No. <?= e($pc->contract_number) ?>)</div>
                            <div class="text-xs text-slate-500"><?= e($pc->start_date) ?> s/d <?= e($pc->end_date) ?></div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase bg-slate-200 text-slate-700">
                            <?= e($pc->status) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
