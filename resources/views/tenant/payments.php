<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('tenant/dashboard') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
            &larr; Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Kirim Bukti Pembayaran Sewa</h1>
        <p class="text-slate-500 text-sm">Unggah bukti transfer untuk diverifikasi oleh pengelola kos.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <!-- Rekening Transfer Info -->
        <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-100 text-sm text-blue-950">
            <div class="font-bold mb-1">Rekening Tujuan Transfer:</div>
            <div>Bank: <strong><?= e(config('payment.bank_name', 'BCA')) ?></strong></div>
            <div>No. Rekening: <strong class="font-mono text-base"><?= e(config('payment.bank_account_number', '1234567890')) ?></strong></div>
            <div>Atas Nama: <strong><?= e(config('payment.bank_account_holder', 'Owner Kos')) ?></strong></div>
        </div>

        <form action="<?= url('tenant/payments') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Tagihan yang Dibayar <span class="text-rose-500">*</span></label>
                <select name="invoice_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">-- Pilih Tagihan Belum Lunas --</option>
                    <?php foreach ($invoices as $inv): ?>
                        <option value="<?= (int)$inv->id ?>">
                            #<?= e($inv->invoice_number) ?> - Periode <?= e($inv->period_start) ?> (Sisa: <?= $inv->formattedBalance() ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nominal Transfer (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Contoh: 1500000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Transfer <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Upload File Bukti Transfer <span class="text-rose-500">*</span></label>
                <input type="file" name="proof" required accept="image/jpeg,image/png,image/webp,application/pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <span class="text-xs text-slate-400 mt-1 block">Format diperbolehkan: JPG, PNG, WEBP, atau PDF (Maks. 5MB)</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Contoh: Transfer via m-BCA a.n. Budi"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="<?= url('tenant/dashboard') ?>" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">Kirim Pembayaran</button>
            </div>
        </form>
    </div>
</div>

