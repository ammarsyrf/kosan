<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('tenant/dashboard') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
            &larr; Kembali ke Dashboard
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Ajukan Perpanjangan Sewa</h1>
        <p class="text-slate-500 text-sm">Perpanjang masa sewa kamar Anda sebelum kontrak berakhir.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-100 text-sm text-blue-900">
            <div class="font-bold">Kontrak Saat Ini: #<?= e($contract->contract_number) ?></div>
            <div>Kamar: <strong><?= e($contract->room_number) ?></strong></div>
            <div>Berakhir pada: <strong><?= e($contract->end_date) ?></strong></div>
        </div>

        <form action="<?= url('tenant/extension') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Durasi Perpanjangan (Bulan) <span class="text-rose-500">*</span></label>
                <select name="months" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="1">1 Bulan</option>
                    <option value="3">3 Bulan</option>
                    <option value="6">6 Bulan</option>
                    <option value="12">12 Bulan (1 Tahun)</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="<?= url('tenant/dashboard') ?>" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
