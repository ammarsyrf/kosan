<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice #<?= e($invoice->invoice_number) ?> - <?= e(setting('business_name', 'Kost Management')) ?></title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 antialiased p-4 sm:p-8" onload="lucide.createIcons(); setTimeout(() => window.print(), 350);">

    <!-- Action Bar (Hidden in Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
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
        $bizName = setting('business_name', 'Kost Management Personal');
        $bizAddress = setting('business_address', 'Jl. Contoh Properti Kos No. 123, Indonesia');
        $bizPhone = setting('business_phone', '081234567890');
        $bizEmail = setting('business_email', 'admin@kostmanagement.com');
        $bankName = setting('bank_name', 'Bank Central Asia (BCA)');
        $bankNumber = setting('bank_account_number', '8820-1928-3921');
        $bankHolder = setting('bank_account_holder', 'Pengelola Kost');

        $isPaid = $invoice->isPaid();
        $isOverdue = $invoice->isOverdue();
    ?>

    <!-- Main Printable Invoice Paper -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200 print:shadow-none print:border-none print:p-0 print:rounded-none relative overflow-hidden space-y-8">
        
        <!-- PAID WATERMARK STAMP -->
        <?php if ($isPaid): ?>
            <div class="absolute top-10 right-10 pointer-events-none select-none z-10 opacity-85 transform rotate-[-8deg]">
                <div class="border-4 border-emerald-600 border-dashed rounded-2xl px-6 py-2.5 text-center bg-emerald-50/70 shadow-sm">
                    <div class="text-[9px] font-black tracking-widest text-emerald-700 uppercase">OFFICIALLY VERIFIED</div>
                    <div class="text-2xl font-black tracking-wider text-emerald-600">✓ LUNAS</div>
                    <div class="text-[9px] font-bold text-emerald-800 tracking-tight"><?= e(date('d M Y', strtotime($invoice->updated_at))) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Letterhead -->
        <div class="flex justify-between items-start pb-6 border-b-2 border-slate-100">
            <div class="space-y-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold">
                        <i data-lucide="home" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight leading-none"><?= e($bizName) ?></h2>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Tagihan Resmi Pengelolaan Kos</span>
                    </div>
                </div>
                <div class="text-xs text-slate-500 space-y-0.5 leading-relaxed pt-1">
                    <p><?= e($bizAddress) ?></p>
                    <p><?= e($bizPhone) ?> &bull; <?= e($bizEmail) ?></p>
                </div>
            </div>

            <div class="text-right space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">INVOICE</span>
                <div class="text-xl sm:text-2xl font-black font-mono text-slate-900 tracking-tight">
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
        <div class="grid grid-cols-2 gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-200">
            <div>
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Tujuan Penagihan (Penyewa)</div>
                <div class="space-y-1">
                    <h3 class="text-base font-black text-slate-900"><?= e($invoice->tenant_name) ?></h3>
                    <div class="text-xs text-slate-600">
                        <strong>Kamar <?= e($invoice->room_number) ?></strong> &bull; <?= e($invoice->tenant_phone ?: '-') ?>
                    </div>
                    <div class="text-xs text-slate-500 pt-1">
                        Periode: <strong><?= date('d M Y', strtotime($invoice->period_start)) ?> s/d <?= date('d M Y', strtotime($invoice->period_end)) ?></strong>
                    </div>
                </div>
            </div>

            <div class="border-l border-slate-200 pl-6">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Rekening Pembayaran Resmi</div>
                <div class="space-y-1 text-xs text-slate-700">
                    <div class="font-bold text-slate-900"><?= e($bankName) ?></div>
                    <div class="font-mono text-sm font-black text-slate-900"><?= e($bankNumber) ?></div>
                    <div class="text-slate-500">A/N: <strong class="text-slate-800"><?= e($bankHolder) ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-3">
            <div class="text-xs font-black uppercase tracking-wider text-slate-400">Rincian Item Tagihan</div>
            <table class="w-full text-left text-xs sm:text-sm border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-[11px] font-black uppercase text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No.</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 w-32 text-center">Durasi</th>
                        <th class="py-3 px-4 text-right w-40">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($invoice->items as $idx => $item): ?>
                        <tr>
                            <td class="py-3 px-4 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="py-3 px-4 text-slate-900 font-semibold"><?= e($item->description) ?></td>
                            <td class="py-3 px-4 text-center text-slate-500">1 Bulan</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900"><?= $item->formattedAmount() ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs sm:text-sm">
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
                        <td class="py-3 px-4 text-right font-mono text-base <?= $invoice->balance > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                            <?= $invoice->formattedBalance() ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Terms & Signature -->
        <div class="grid grid-cols-2 gap-8 pt-6 border-t border-slate-200 text-xs text-slate-500">
            <div class="space-y-1">
                <div class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Ketentuan Pembayaran:</div>
                <p>Tagihan sewa bulanan wajib diselesaikan sebelum tanggal jatuh tempo. Kwitansi ini sah sebagai bukti tagihan resmi dari pengelola kost.</p>
            </div>
            <div class="text-right space-y-8 flex flex-col justify-between items-end">
                <p class="text-slate-400">Pengelola Resmi: <strong><?= e($bizName) ?></strong></p>
                <div class="pt-6 border-t border-slate-300 w-48 text-center">
                    <p class="font-bold text-slate-900">Manajemen Pengelola Kos</p>
                    <p class="text-[10px] text-slate-400">Authorized Signature & Stamp</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
