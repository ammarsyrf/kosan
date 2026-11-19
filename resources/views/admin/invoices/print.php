<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice #<?= e($invoice->invoice_number) ?> - <?= e(setting('business_name', 'Kost Zenerie')) ?></title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }
        @media print {
            html, body {
                background: #ffffff !important;
                color: #0f172a !important;
                font-size: 11px !important;
                line-height: 1.35 !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print { display: none !important; }
            .print-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: avoid !important;
            }
            .print-sheet > * + * {
                margin-top: 10px !important;
            }
            .print-compact-box {
                padding: 8px 12px !important;
                margin-top: 8px !important;
                border-radius: 12px !important;
            }
            .print-table th {
                padding: 5px 8px !important;
                font-size: 10px !important;
            }
            .print-table td {
                padding: 4px 8px !important;
                font-size: 10.5px !important;
            }
            .print-footer-section {
                padding-top: 8px !important;
                margin-top: 8px !important;
                gap: 16px !important;
            }
            .print-stamp {
                top: 2px !important;
                right: 180px !important;
                transform: rotate(-6deg) scale(0.85) !important;
                opacity: 0.85 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 antialiased p-4 sm:p-8" onload="lucide.createIcons(); setTimeout(() => window.print(), 350);">

    <!-- Action Bar (Hidden in Print) -->
    <div class="no-print max-w-4xl mx-auto mb-4 flex items-center justify-between bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm">
        <a href="<?= url('admin/invoices/' . $invoice->id) ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-blue-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Detail
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition cursor-pointer">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak / Simpan PDF
            </button>
            <a href="<?= url('admin/invoices/' . $invoice->id . '/export') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Export Excel
            </a>
        </div>
    </div>

    <?php
        $bizName = setting('business_name', 'Kost Zenerie');
        $bizAddress = setting('business_address', 'Jl. Contoh Properti Kos No. 123, Indonesia');
        $bizPhone = setting('business_phone', '081234567890');
        $bizEmail = setting('business_email', 'admin@kostmanagement.com');
        $bankName = setting('bank_name', 'Bank Central Asia (BCA)');
        $bankNumber = setting('bank_account_number', '8820-1928-3921');
        $bankHolder = setting('bank_account_holder', 'Pengelola Kost');

        $isPaid = $invoice->isPaid();
        $isOverdue = $invoice->isOverdue();
    ?>

    <!-- Main Printable Invoice Paper (Strict Single Page Fit) -->
    <div class="print-sheet max-w-4xl mx-auto bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-200 print:shadow-none print:border-none print:p-0 print:rounded-none relative overflow-hidden space-y-5">
        
        <!-- PAID WATERMARK STAMP (Neatly Positioned without text overlap) -->
        <?php if ($isPaid): ?>
            <div class="print-stamp absolute top-8 right-48 pointer-events-none select-none z-10 opacity-85 transform rotate-[-6deg] hidden sm:block">
                <div class="border-4 border-emerald-600 border-dashed rounded-2xl px-5 py-1.5 text-center bg-emerald-50/80 shadow-sm">
                    <div class="text-[9px] font-black tracking-widest text-emerald-700 uppercase">OFFICIALLY VERIFIED</div>
                    <div class="text-xl font-black tracking-wider text-emerald-600">✓ LUNAS</div>
                    <div class="text-[8px] font-bold text-emerald-800 tracking-tight"><?= e(date('d M Y', strtotime($invoice->updated_at))) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Letterhead -->
        <div class="flex justify-between items-start pb-4 border-b-2 border-slate-100 relative">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold">
                        <i data-lucide="home" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-none"><?= e($bizName) ?></h2>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Tagihan Resmi Pengelolaan Kos</span>
                    </div>
                </div>
                <div class="text-xs text-slate-500 space-y-0.5 leading-relaxed pt-0.5">
                    <p><?= e($bizAddress) ?></p>
                    <p><?= e($bizPhone) ?> &bull; <?= e($bizEmail) ?></p>
                </div>
            </div>

            <div class="text-right space-y-1 shrink-0">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">INVOICE</span>
                <div class="text-lg sm:text-xl font-black font-mono text-slate-900 tracking-tight">
                    #<?= e($invoice->invoice_number) ?>
                </div>
                <div class="text-xs text-slate-600 space-y-0.5">
                    <p><span class="text-slate-400">Terbit:</span> <strong class="text-slate-800"><?= date('d F Y', strtotime($invoice->issue_date)) ?></strong></p>
                    <p><span class="text-slate-400">Jatuh Tempo:</span> <strong class="<?= $isOverdue ? 'text-rose-600' : 'text-slate-800' ?>"><?= date('d F Y', strtotime($invoice->due_date)) ?></strong></p>
                    <p><span class="text-slate-400">Kontrak:</span> <strong class="text-slate-800 font-mono"><?= e($invoice->contract_number ?: '-') ?></strong></p>
                </div>
            </div>
        </div>

        <!-- 2-Column Info -->
        <div class="print-compact-box grid grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
            <div>
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Tujuan Penagihan (Penyewa)</div>
                <div class="space-y-0.5">
                    <h3 class="text-sm sm:text-base font-black text-slate-900"><?= e($invoice->tenant_name) ?></h3>
                    <div class="text-xs text-slate-600">
                        <strong>Kamar <?= e($invoice->room_number) ?></strong> &bull; <?= e($invoice->tenant_phone ?: '-') ?>
                    </div>
                    <div class="text-xs text-slate-500 pt-0.5">
                        Periode: <strong><?= date('d M Y', strtotime($invoice->period_start)) ?> s/d <?= date('d M Y', strtotime($invoice->period_end)) ?></strong>
                    </div>
                </div>
            </div>

            <div class="border-l border-slate-200 pl-4">
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Rekening Pembayaran Resmi</div>
                <div class="space-y-0.5 text-xs text-slate-700">
                    <div class="font-bold text-slate-900"><?= e($bankName) ?></div>
                    <div class="font-mono text-xs sm:text-sm font-black text-slate-900"><?= e($bankNumber) ?></div>
                    <div class="text-slate-500 text-[11px]">A/N: <strong class="text-slate-800"><?= e($bankHolder) ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-1.5">
            <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Rincian Item Tagihan</div>
            <table class="print-table w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-[10px] font-black uppercase text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3 w-10 text-center">No.</th>
                        <th class="py-2.5 px-3">Deskripsi</th>
                        <th class="py-2.5 px-3 w-28 text-center">Durasi</th>
                        <th class="py-2.5 px-3 text-right w-40">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($invoice->items as $idx => $item): ?>
                        <tr>
                            <td class="py-2 px-3 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="py-2 px-3 text-slate-900 font-semibold"><?= e($item->description) ?></td>
                            <td class="py-2 px-3 text-center text-slate-500">1 Bulan</td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-slate-900"><?= $item->formattedAmount() ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs">
                    <tr>
                        <td colspan="3" class="py-1.5 px-3 text-right font-bold text-slate-500">Subtotal Tagihan:</td>
                        <td class="py-1.5 px-3 text-right font-mono font-bold text-slate-800"><?= $invoice->formattedTotal() ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="py-1.5 px-3 text-right font-bold text-slate-500">Total Pembayaran Terverifikasi:</td>
                        <td class="py-1.5 px-3 text-right font-mono font-extrabold text-emerald-600"><?= $invoice->formattedPaid() ?></td>
                    </tr>
                    <tr class="border-t border-slate-300 bg-slate-100 font-black">
                        <td colspan="3" class="py-2 px-3 text-right text-slate-900 text-xs sm:text-sm">Sisa Tagihan (Balance):</td>
                        <td class="py-2 px-3 text-right font-mono text-sm sm:text-base <?= $invoice->balance > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                            <?= $invoice->formattedBalance() ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payments History (if available) -->
        <?php if (!empty($invoice->payments)): ?>
            <div class="space-y-1 pt-0.5">
                <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Riwayat Pembayaran Terverifikasi</div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/20 overflow-hidden">
                    <table class="print-table w-full text-left text-xs">
                        <thead class="bg-emerald-50 text-[10px] font-black uppercase text-emerald-800 border-b border-emerald-200">
                            <tr>
                                <th class="py-2 px-3">No. Transaksi</th>
                                <th class="py-2 px-3">Tanggal</th>
                                <th class="py-2 px-3">Metode</th>
                                <th class="py-2 px-3">Verifikator</th>
                                <th class="py-2 px-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-100 text-slate-700">
                            <?php foreach ($invoice->payments as $p): ?>
                                <tr>
                                    <td class="py-1.5 px-3 font-mono font-bold text-slate-900"><?= e($p['payment_number']) ?></td>
                                    <td class="py-1.5 px-3"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                                    <td class="py-1.5 px-3 uppercase text-slate-600"><?= e($p['payment_method']) ?></td>
                                    <td class="py-1.5 px-3 text-slate-500"><?= e($p['verifier_name'] ?? 'Admin') ?></td>
                                    <td class="py-1.5 px-3 text-right font-mono font-bold text-emerald-600">
                                        Rp <?= number_format((float)$p['allocated_amount'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Terms & Signature -->
        <div class="print-footer-section grid grid-cols-2 gap-4 pt-3 border-t border-slate-200 text-xs text-slate-500">
            <div class="space-y-0.5">
                <div class="font-bold text-slate-800 uppercase tracking-wider text-[10px]">Ketentuan Pembayaran:</div>
                <p class="text-[10px] leading-relaxed">Tagihan sewa bulanan wajib diselesaikan sebelum jatuh tempo. Kwitansi ini sah sebagai bukti tagihan resmi dari pengelola kost.</p>
            </div>
            <div class="text-right space-y-2 flex flex-col justify-between items-end">
                <p class="text-slate-400 text-[10px]">Pengelola Resmi: <strong class="text-slate-700"><?= e($bizName) ?></strong></p>
                <div class="pt-2 border-t border-slate-300 w-44 text-center">
                    <p class="font-bold text-slate-900 text-[11px]">Manajemen Pengelola Kos</p>
                    <p class="text-[9px] text-slate-400">Authorized Signature & Stamp</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
