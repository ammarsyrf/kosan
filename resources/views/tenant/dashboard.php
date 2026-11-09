<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Selamat Datang, <?= e($tenant->name ?? 'Penyewa') ?></h1>
            <p class="text-slate-500 text-sm">Kelola masa sewa, tagihan, dan pembayaran kamar Anda.</p>
        </div>
        <div>
            <a href="<?= url('tenant/payment') ?>" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Kirim Bukti Pembayaran
            </a>
        </div>
    </div>

    <?php if (!$activeContract): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center max-w-lg mx-auto">
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                <i data-lucide="home" class="w-6 h-6"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800 mb-2">Belum Ada Kontrak Aktif</h2>
            <p class="text-slate-500 text-sm mb-4">Saat ini Anda tidak memiliki kontrak sewa kamar yang sedang aktif.</p>
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
                            Perpanjangan Menunggu Persetujuan
                        </span>
                    <?php endif; ?>

                    <?php if (!$pendingCheckout): ?>
                        <a href="<?= url('tenant/early-checkout') ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Ajukan Early Checkout
                        </a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                            Checkout Menunggu Persetujuan
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tenant Invoices -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Tagihan Saya</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4">Jatuh Tempo</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Sisa Tagihan</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">Belum ada tagihan yang diterbitkan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td class="py-3 px-4 font-mono font-medium text-slate-900"><?= e($inv->invoice_number) ?></td>
                                <td class="py-3 px-4 text-xs"><?= e($inv->period_start) ?> s/d <?= e($inv->period_end) ?></td>
                                <td class="py-3 px-4 text-xs font-medium <?= $inv->isOverdue() ? 'text-rose-600 font-bold' : '' ?>"><?= e($inv->due_date) ?></td>
                                <td class="py-3 px-4 font-semibold text-slate-900"><?= $inv->formattedTotal() ?></td>
                                <td class="py-3 px-4 font-bold <?= $inv->balance > 0 ? 'text-rose-600' : 'text-emerald-600' ?>"><?= $inv->formattedBalance() ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        <?= ucfirst(str_replace('_', ' ', e($inv->status))) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tenant Payments History -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Histori Pembayaran Saya</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">No. Transaksi</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada riwayat pembayaran.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td class="py-3 px-4 font-mono font-medium text-slate-900"><?= e($p->payment_number) ?></td>
                                <td class="py-3 px-4 text-xs"><?= e($p->payment_date) ?></td>
                                <td class="py-3 px-4 uppercase text-xs font-semibold"><?= e($p->payment_method) ?></td>
                                <td class="py-3 px-4 font-bold text-slate-900"><?= $p->formattedAmount() ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        <?= ucfirst(e($p->status)) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
