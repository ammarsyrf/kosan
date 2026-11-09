<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="<?= url('admin/payments') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
                &larr; Kembali ke Daftar Pembayaran
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Pembayaran #<?= e($payment->payment_number) ?></h1>
            <p class="text-slate-500 text-sm">Status: <span class="font-semibold uppercase text-blue-600"><?= e($payment->status) ?></span></p>
        </div>
    </div>

    <!-- Payment Detail Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Informasi Pembayar</h2>
            <div class="space-y-2 text-sm text-slate-700">
                <div class="flex justify-between border-b border-slate-100 pb-1">
                    <span class="text-slate-500">Nama Penyewa:</span>
                    <span class="font-bold text-slate-900"><?= e($payment->tenant_name) ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-1">
                    <span class="text-slate-500">No. Telepon:</span>
                    <span><?= e($payment->tenant_phone ?: '-') ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-1">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-semibold uppercase text-slate-800"><?= e($payment->payment_method) ?></span>
                </div>
                <div class="flex justify-between pb-1">
                    <span class="text-slate-500">Catatan Pembayar:</span>
                    <span><?= e($payment->notes ?: '-') ?></span>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Rincian Transaksi</h2>
            <div class="space-y-2 text-sm text-slate-700">
                <div class="flex justify-between border-b border-slate-100 pb-1">
                    <span class="text-slate-500">Nominal Pembayaran:</span>
                    <span class="font-bold text-emerald-600 text-lg"><?= $payment->formattedAmount() ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-1">
                    <span class="text-slate-500">Tanggal Bayar:</span>
                    <span><?= e($payment->payment_date) ?></span>
                </div>
                <div class="flex justify-between pb-1">
                    <span class="text-slate-500">Bukti Transfer:</span>
                    <?php if ($payment->proof_file): ?>
                        <a href="<?= url('admin/payments/' . $payment->id . '/proof') ?>" target="_blank" class="text-blue-600 hover:underline font-semibold inline-flex items-center gap-1">
                            <i data-lucide="external-link" class="w-4 h-4"></i> Buka File Bukti
                        </a>
                    <?php else: ?>
                        <span class="text-slate-400">Tidak ada (Tunai/Langsung)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Form for Pending Payments -->
    <?php if ($payment->isPending()): ?>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
            <h3 class="font-bold text-amber-900 mb-2">Verifikasi & Alokasikan ke Tagihan</h3>
            <p class="text-sm text-amber-800 mb-4">Pilih tagihan (invoice) yang akan dilunasi/dikurangi dengan pembayaran ini. Total alokasi tidak boleh melebihi <?= $payment->formattedAmount() ?>.</p>

            <form action="<?= url('admin/payments/' . $payment->id . '/verify') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div class="space-y-2">
                    <?php if (empty($unpaidInvoices)): ?>
                        <p class="text-xs text-rose-600 italic">Penyewa ini tidak memiliki invoice yang sedang belum lunas (unpaid/partially_paid).</p>
                    <?php else: ?>
                        <?php foreach ($unpaidInvoices as $inv): ?>
                            <div class="flex items-center justify-between p-3 rounded-lg bg-white border border-amber-200 text-sm">
                                <div>
                                    <div class="font-semibold text-slate-800">#<?= e($inv->invoice_number) ?> (Periode <?= e($inv->period_start) ?> s/d <?= e($inv->period_end) ?>)</div>
                                    <div class="text-xs text-slate-500">Sisa Tagihan: <strong class="text-rose-600"><?= $inv->formattedBalance() ?></strong></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="invoice_ids[]" value="<?= (int)$inv->id ?>">
                                    <span class="text-xs text-slate-500">Alokasikan:</span>
                                    <input type="number" step="0.01" name="allocated_amounts[]" value="<?= min($payment->amount, $inv->balance) ?>" max="<?= $inv->balance ?>" class="w-32 rounded-lg border-slate-300 text-sm">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="pt-2 flex items-center justify-between">
                    <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                        Verifikasi Pembayaran & Alokasikan
                    </button>
                </div>
            </form>

            <hr class="my-4 border-amber-200">

            <!-- Reject Form -->
            <form action="<?= url('admin/payments/' . $payment->id . '/reject') ?>" method="POST" class="flex items-center gap-3">
                <?= csrf_field() ?>
                <input type="text" name="rejection_reason" required placeholder="Alasan penolakan (misal: bukti palsu/tidak terbaca)" class="flex-1 rounded-lg border-slate-300 text-sm">
                <button type="submit" onclick="return confirm('Tolak pembayaran ini?')" class="px-4 py-2 text-sm font-semibold rounded-lg text-white bg-rose-600 hover:bg-rose-700 transition">
                    Tolak Pembayaran
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Allocation History (for verified payments) -->
    <?php if (!empty($payment->allocations)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Alokasi Tagihan</h3>
                <?php if ($payment->isVerified()): ?>
                    <!-- Reversal Button -->
                    <form action="<?= url('admin/payments/' . $payment->id . '/reverse') ?>" method="POST" onsubmit="return confirm('PENTING: Reversal akan membatalkan alokasi pembayaran ini dan mengembalikan saldo tagihan terkait. Lanjutkan?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="reason" value="Koreksi transaksi administratif">
                        <button type="submit" class="text-xs font-semibold px-3 py-1 rounded bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition">
                            Reversal / Koreksi Transaksi
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No. Invoice Terkait</th>
                        <th class="py-3 px-4">Total Invoice</th>
                        <th class="py-3 px-4">Nominal Dialokasikan</th>
                        <th class="py-3 px-4">Waktu Alokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($payment->allocations as $alloc): ?>
                        <tr>
                            <td class="py-3 px-4 font-mono font-medium text-slate-900">#<?= e($alloc['invoice_number']) ?></td>
                            <td class="py-3 px-4 font-medium text-slate-800">Rp <?= number_format((float)$alloc['total_amount'], 0, ',', '.') ?></td>
                            <td class="py-3 px-4 font-bold text-emerald-600">Rp <?= number_format((float)$alloc['allocated_amount'], 0, ',', '.') ?></td>
                            <td class="py-3 px-4 text-xs text-slate-500"><?= e($alloc['allocated_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Adjustments History (if any) -->
    <?php if (!empty($adjustments)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Histori Reversal & Penyesuaian</h3>
            </div>
            <div class="divide-y divide-slate-100">
                <?php foreach ($adjustments as $adj): ?>
                    <div class="p-4 text-sm">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                            <span class="font-bold uppercase text-purple-700 px-2 py-0.5 rounded bg-purple-50"><?= e($adj['adjustment_type']) ?></span>
                            <span><?= e($adj['adjusted_at']) ?> oleh <?= e($adj['adjuster_name']) ?></span>
                        </div>
                        <div class="font-semibold text-slate-800">Nominal: Rp <?= number_format((float)$adj['amount'], 0, ',', '.') ?></div>
                        <p class="text-xs text-slate-600 mt-1">Alasan: <?= e($adj['reason']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

