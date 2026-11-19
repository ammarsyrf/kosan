<?php
    $bizName = setting('business_name', 'Kost Management Personal');
    $bizAddress = setting('business_address', 'Jl. Contoh Properti Kos No. 123, Indonesia');
    $bizPhone = setting('business_phone', '081234567890');
    $bizEmail = setting('business_email', 'admin@kostmanagement.com');
    $bankName = setting('bank_name', 'Bank Central Asia (BCA)');
    $bankNumber = setting('bank_account_number', '8820-1928-3921');
    $bankHolder = setting('bank_account_holder', 'Pengelola Kost');

    $isPaid = $invoice->isPaid();
    $isOverdue = $invoice->isOverdue();
    $isUnpaid = $invoice->isUnpaid();
?>

<!-- Print-specific CSS: Pristine A4 Output without UI chrome -->
<style>
@media print {
    body {
        background-color: #ffffff !important;
        color: #0f172a !important;
        font-size: 12px !important;
    }
    #app-sidebar, 
    #sidebar-backdrop, 
    header, 
    nav, 
    .no-print,
    #top-progress-bar {
        display: none !important;
    }
    .print-sheet {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    @page {
        size: A4 portrait;
        margin: 15mm 15mm 15mm 15mm;
    }
}
</style>

<div class="max-w-4xl mx-auto space-y-6 pb-12">
    <!-- Top Action Toolbar (Hidden in Print) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white/80 backdrop-blur-md p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <a href="<?= url('admin/invoices') ?>" class="text-xs font-bold text-slate-500 hover:text-blue-600 inline-flex items-center gap-1.5 transition mb-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Daftar Invoice</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Invoice #<?= e($invoice->invoice_number) ?></h1>
                <?php if ($isPaid): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Lunas
                    </span>
                <?php elseif ($isOverdue): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Jatuh Tempo
                    </span>
                <?php elseif ($invoice->status === 'partially_paid'): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-purple-100 text-purple-800 border border-purple-300">
                        <i data-lucide="pie-chart" class="w-3.5 h-3.5"></i> Sebagian
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Bayar
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Tombol Cetak / Unduh PDF -->
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm hover:shadow-md transition cursor-pointer">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak / Unduh PDF</span>
            </button>

            <!-- Tombol Export Excel -->
            <a href="<?= url('admin/invoices/' . $invoice->id . '/export') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm hover:shadow-md transition">
                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                <span>Export ke Excel</span>
            </a>

            <!-- Tombol Batalkan Invoice -->
            <?php if ($invoice->paid_amount <= 0 && !in_array($invoice->status, ['paid', 'cancelled', 'void'], true)): ?>
                <form action="<?= url('admin/invoices/' . $invoice->id . '/cancel') ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan tagihan invoice ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition cursor-pointer">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                        <span>Batalkan</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- MAIN INVOICE PAPER (Pristine Business Statement Design) -->
    <div class="print-sheet bg-white rounded-3xl shadow-xl border border-slate-200/90 p-8 sm:p-12 relative overflow-hidden space-y-8">
        
        <!-- PAID WATERMARK STAMP (Authentic Seal) -->
        <?php if ($isPaid): ?>
            <div class="absolute top-12 right-12 pointer-events-none select-none z-10 opacity-80 sm:opacity-90 transform rotate-[-8deg] hidden sm:block">
                <div class="border-4 border-emerald-600 border-dashed rounded-2xl px-6 py-2.5 text-center bg-emerald-50/70 backdrop-blur-xs shadow-md">
                    <div class="text-[10px] font-black tracking-widest text-emerald-700 uppercase">OFFICIALLY VERIFIED</div>
                    <div class="text-2xl font-black tracking-wider text-emerald-600 flex items-center justify-center gap-1">
                        <i data-lucide="check-check" class="w-6 h-6"></i>
                        <span>LUNAS</span>
                    </div>
                    <div class="text-[9px] font-bold text-emerald-800 tracking-tight"><?= e(date('d M Y', strtotime($invoice->updated_at))) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Letterhead: Business Identity & Invoice Meta -->
        <div class="flex flex-col sm:flex-row justify-between gap-6 pb-6 border-b-2 border-slate-100">
            <!-- Left: Kos Brand Details -->
            <div class="space-y-2 max-w-sm">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                        <i data-lucide="home" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight leading-none"><?= e($bizName) ?></h2>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Smart Living & Kost Management</span>
                    </div>
                </div>
                <div class="text-xs text-slate-500 space-y-0.5 leading-relaxed pt-1">
                    <p class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i> <?= e($bizAddress) ?></p>
                    <p class="flex items-center gap-1.5"><i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i> <?= e($bizPhone) ?> &bull; <?= e($bizEmail) ?></p>
                </div>
            </div>

            <!-- Right: Invoice Metadata -->
            <div class="sm:text-right space-y-1 sm:max-w-xs">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">Tagihan Resmi</span>
                <div class="text-xl sm:text-2xl font-black font-mono text-slate-900 tracking-tight">
                    #<?= e($invoice->invoice_number) ?>
                </div>
                <div class="text-xs text-slate-600 space-y-0.5">
                    <p><span class="text-slate-400">Tanggal Terbit:</span> <strong class="text-slate-800"><?= date('d F Y', strtotime($invoice->issue_date)) ?></strong></p>
                    <p>
                        <span class="text-slate-400">Jatuh Tempo:</span> 
                        <strong class="<?= $isOverdue ? 'text-rose-600' : 'text-slate-800' ?>">
                            <?= date('d F Y', strtotime($invoice->due_date)) ?>
                        </strong>
                    </p>
                    <p><span class="text-slate-400">No. Kontrak:</span> <strong class="text-slate-800 font-mono"><?= e($invoice->contract_number ?: '-') ?></strong></p>
                </div>
            </div>
        </div>

        <!-- 2-Column Information: Bill To & Payment Instructions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-2xl bg-slate-50/70 border border-slate-200/80">
            <!-- Ditagihkan Kepada (Penyewa) -->
            <div class="space-y-2">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>Tujuan Penagihan (Penyewa)</span>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-black text-slate-900 leading-tight"><?= e($invoice->tenant_name) ?></h3>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg font-bold bg-blue-100 text-blue-800 border border-blue-200">
                            <i data-lucide="key" class="w-3 h-3 text-amber-500"></i>
                            Kamar <?= e($invoice->room_number) ?>
                        </span>
                        <span class="text-slate-500"><?= e($invoice->tenant_phone ?: '-') ?></span>
                    </div>
                    <div class="text-xs text-slate-500 pt-1">
                        <span class="text-slate-400">Periode Sewa:</span> 
                        <strong class="text-slate-700"><?= date('d M Y', strtotime($invoice->period_start)) ?> s/d <?= date('d M Y', strtotime($invoice->period_end)) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Rekening Pembayaran Resmi -->
            <div class="space-y-2 md:border-l md:border-slate-200 md:pl-6">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <i data-lucide="credit-card" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Rekening Pembayaran Resmi</span>
                </div>
                <div class="space-y-1 text-xs text-slate-700">
                    <div class="font-bold text-slate-900 text-sm"><?= e($bankName) ?></div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-sm font-black text-slate-900 bg-white px-2.5 py-0.5 rounded-lg border border-slate-300 select-all" id="bank-acc-num">
                            <?= e($bankNumber) ?>
                        </span>
                        <button type="button" onclick="navigator.clipboard.writeText('<?= e($bankNumber) ?>'); alert('Nomor rekening berhasil disalin!');" class="no-print p-1 text-slate-400 hover:text-blue-600 transition" title="Salin No. Rekening">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                    <div class="text-slate-500">Atas Nama: <strong class="text-slate-800"><?= e($bankHolder) ?></strong></div>
                    <div class="text-[10px] text-slate-400 italic">Sertakan No. Invoice pada berita transfer</div>
                </div>
            </div>
        </div>

        <!-- Rincian Item Tagihan (Items Table) -->
        <div class="space-y-3">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Rincian Item Tagihan</span>
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-100/80 text-[11px] font-black uppercase tracking-wider text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">No.</th>
                            <th class="py-3.5 px-4">Deskripsi Layanan / Item</th>
                            <th class="py-3.5 px-4 w-32 text-center">Durasi / Unit</th>
                            <th class="py-3.5 px-4 text-right w-44">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        <?php foreach ($invoice->items as $idx => $item): ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4 text-center text-slate-400 font-mono text-xs"><?= $idx + 1 ?></td>
                                <td class="py-3 px-4 text-slate-900 font-semibold">
                                    <?= e($item->description) ?>
                                </td>
                                <td class="py-3 px-4 text-center text-slate-500 text-xs">
                                    1 Bulan
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    <?= $item->formattedAmount() ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-slate-50/80 border-t-2 border-slate-200 text-xs sm:text-sm">
                        <tr>
                            <td colspan="3" class="py-2.5 px-4 text-right font-bold text-slate-500">Subtotal Tagihan:</td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-800"><?= $invoice->formattedTotal() ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="py-2 px-4 text-right font-bold text-slate-500">Total Pembayaran Terverifikasi:</td>
                            <td class="py-2 px-4 text-right font-mono font-extrabold text-emerald-600"><?= $invoice->formattedPaid() ?></td>
                        </tr>
                        <tr class="border-t border-slate-300 bg-slate-100 font-black">
                            <td colspan="3" class="py-3 px-4 text-right text-slate-900 text-sm">Sisa Tagihan (Balance):</td>
                            <td class="py-3 px-4 text-right font-mono text-base sm:text-lg <?= $invoice->balance > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                                <?= $invoice->formattedBalance() ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Riwayat Pembayaran (Payment Allocations History) -->
        <?php if (!empty($invoice->payments)): ?>
            <div class="space-y-3 pt-2">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <i data-lucide="badge-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Riwayat Pembayaran Terverifikasi</span>
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-emerald-200 bg-emerald-50/20">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-emerald-50 text-[10px] font-black uppercase tracking-wider text-emerald-800 border-b border-emerald-200">
                            <tr>
                                <th class="py-2.5 px-4">No. Transaksi</th>
                                <th class="py-2.5 px-4">Tanggal Bayar</th>
                                <th class="py-2.5 px-4">Metode</th>
                                <th class="py-2.5 px-4">Verifikator</th>
                                <th class="py-2.5 px-4 text-right">Nominal Dialokasikan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-100 text-slate-700">
                            <?php foreach ($invoice->payments as $p): ?>
                                <tr>
                                    <td class="py-2.5 px-4 font-mono font-bold text-slate-900"><?= e($p['payment_number']) ?></td>
                                    <td class="py-2.5 px-4"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                                    <td class="py-2.5 px-4 uppercase font-semibold text-slate-600"><?= e($p['payment_method']) ?></td>
                                    <td class="py-2.5 px-4 text-slate-500"><?= e($p['verifier_name'] ?? 'Admin') ?></td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-600">
                                        Rp <?= number_format((float)$p['allocated_amount'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Terms & Digital Signature Block -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-6 border-t border-slate-200 text-xs text-slate-500">
            <!-- Left: Ketentuan Pembayaran -->
            <div class="space-y-2">
                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Ketentuan & Kebijakan Kost:</h4>
                <ul class="list-disc list-inside space-y-1 text-slate-500 text-[11px] leading-relaxed">
                    <li>Pembayaran tagihan sewa bulanan wajib dilunasi sebelum tanggal jatuh tempo.</li>
                    <li>Bukti transfer bank dapat diunggah melalui Portal Mandiri Penghuni atau dikonfirmasi ke admin.</li>
                    <li>Kwitansi dan invoice ini sah sebagai bukti penagihan resmi dari pengelola kost.</li>
                </ul>
            </div>

            <!-- Right: Authorized Signature Box -->
            <div class="sm:text-right space-y-8 flex flex-col justify-between sm:items-end">
                <div class="space-y-0.5">
                    <p class="text-slate-400">Dikeluarkan secara resmi oleh:</p>
                    <p class="font-extrabold text-slate-900 text-sm"><?= e($bizName) ?></p>
                </div>
                <div class="pt-6 border-t border-slate-300 w-48 sm:text-center">
                    <p class="font-bold text-slate-900">Manajemen Pengelola Kos</p>
                    <p class="text-[10px] text-slate-400">Authorized Signature & Seal</p>
                </div>
            </div>
        </div>

    </div>
</div>
