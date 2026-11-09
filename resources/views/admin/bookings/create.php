<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('admin/bookings') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Booking
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Tambah Booking Baru</h1>
        <p class="text-slate-500 text-sm">Pemesanan kamar kos bersifat gratis tanpa biaya booking.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="<?= url('admin/bookings') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Kamar <span class="text-rose-500">*</span></label>
                <select name="room_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">-- Pilih Kamar Tersedia --</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= (int)$room->id ?>">
                            <?= e($room->room_number) ?> - <?= e($room->name) ?> (Rp <?= number_format((float)$room->current_price, 0, ',', '.') ?>/bln)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Calon Penyewa <span class="text-rose-500">*</span></label>
                <select name="tenant_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">-- Pilih Penyewa --</option>
                    <?php foreach ($tenants as $tenant): ?>
                        <option value="<?= (int)$tenant->id ?>">
                            <?= e($tenant->name) ?> (<?= e($tenant->phone ?: '-') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai Sewa <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" required min="<?= date('Y-m-d') ?>" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berakhir Sewa <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" required min="<?= date('Y-m-d') ?>" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Contoh: Rencana masuk tanggal 1"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="<?= url('admin/bookings') ?>" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">Simpan Booking</button>
            </div>
        </form>
    </div>
</div>
