<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="<?= url('admin/invoices') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
                &larr; Kembali ke Daftar Invoice
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Invoice #<?= e($invoice->invoice_number) ?></h1>
            <p class="text-slate-500 text-sm">Status: <span class="font-semibold uppercase text-blue-600"><?= e($invoice->status) ?></span></p>
        </div>
        <div class="flex items-center gap-2">
            <?php if ($invoice->paid_amount <= 0 && !in_array($invoice->status, ['paid', 'cancelled', 'void'], true)): ?>
                <form action="<?= url('admin/invoices/' . $invoice->id . '/cancel') ?>" method="POST" onsubmit="return confirm('Yakin ingin membatalkan invoice ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition">
                        Batalkan Invoice
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Invoice Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Tujuan Tagihan (Penyewa)</h2>
            <div class="space-y-1.5 text-sm text-slate-700">
                <div class="font-bold text-slate-900 text-base"><?= e($invoice->tenant_name) ?></div>
                <div>Kamar: <strong><?= e($invoice->room_number) ?></strong></div>
                <div>No. Telepon: <?= e($invoice->tenant_phone ?: '-') ?></div>
                <div class="text-xs text-slate-500">Kontrak Terkait: #<?= e($invoice->contract_number) ?></div>
            </div>
        </div>

        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Informasi Tanggal</h2>
            <div class="space-y-1.5 text-sm text-slate-700">
                <div class="flex justify-between border-b border-slate-100 pb-1">
                    <span class="text-slate-500">Periode Sewa:</span>
                    <span class="font-medium"><?= e($invoice->period_start) ?> s/d <?= e($invoice->period_end) ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-1">
                    <span class="text-slate-500">Tanggal Terbit:</span>
                    <span><?= e($invoice->issue_date) ?></span>
                </div>
                <div class="flex justify-between pb-1">
                    <span class="text-slate-500">Jatuh Tempo:</span>
                    <span class="font-bold <?= $invoice->isOverdue() ? 'text-rose-600' : 'text-slate-900' ?>">
                        <?= e($invoice->due_date) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Rincian Item Tagihan</h3>
        </div>
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="py-3 px-4">Deskripsi</th>
                    <th class="py-3 px-4 text-right">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($invoice->items as $item): ?>
                    <tr>
                        <td class="py-3 px-4 text-slate-800"><?= e($item->description) ?></td>
                        <td class="py-3 px-4 text-right font-medium text-slate-900"><?= $item->formattedAmount() ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200 text-sm">
                <tr>
                    <td class="py-2 px-4 text-right font-semibold text-slate-600">Total Tagihan:</td>
                    <td class="py-2 px-4 text-right font-bold text-slate-900"><?= $invoice->formattedTotal() ?></td>
                </tr>
                <tr>
                    <td class="py-2 px-4 text-right font-semibold text-slate-600">Telah Dibayar (Verified):</td>
                    <td class="py-2 px-4 text-right font-bold text-emerald-600"><?= $invoice->formattedPaid() ?></td>
                </tr>
                <tr class="border-t border-slate-200 bg-slate-100/60">
                    <td class="py-3 px-4 text-right font-bold text-slate-800">Sisa Tagihan (Balance):</td>
                    <td class="py-3 px-4 text-right font-bold text-base <?= $invoice->balance > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                        <?= $invoice->formattedBalance() ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

