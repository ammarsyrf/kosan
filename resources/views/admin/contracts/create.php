<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="<?= url('admin/contracts') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Kontrak
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Buat Kontrak Sewa Langsung</h1>
        <p class="text-slate-500 text-sm">Terbitkan kontrak sewa langsung tanpa melalui tahapan booking terlebih dahulu.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="<?= url('admin/contracts') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Kamar <span class="text-rose-500">*</span></label>
                    <select name="room_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Kamar Tersedia --</option>
                        <?php
                        $selectedRoomId = (int)($_GET['room_id'] ?? 0);
                        foreach ($rooms as $room):
                            $rId = (int)(is_object($room) ? $room->id : $room['id']);
                            $rNum = is_object($room) ? $room->room_number : $room['room_number'];
                            $rName = is_object($room) ? $room->name : $room['name'];
                            $rPrice = (float)(is_object($room) ? ($room->current_price ?? 0) : ($room['current_price'] ?? 0));
                        ?>
                            <option value="<?= $rId ?>" <?= $selectedRoomId === $rId ? 'selected' : '' ?>>
                                <?= e($rNum) ?> - <?= e($rName) ?> (Rp <?= number_format($rPrice, 0, ',', '.') ?>/bln)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Penyewa <span class="text-rose-500">*</span></label>
                    <select name="tenant_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Penyewa --</option>
                        <?php foreach ($tenants as $tenant):
                            $tId = (int)(is_object($tenant) ? $tenant->id : $tenant['id']);
                            $tName = is_object($tenant) ? $tenant->name : $tenant['name'];
                            $tPhone = is_object($tenant) ? $tenant->phone : ($tenant['phone'] ?? '');
                        ?>
                            <option value="<?= $tId ?>">
                                <?= e($tName) ?> (<?= e($tPhone ?: '-') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai Sewa <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-slate-500 mt-1">Hari dari tanggal mulai ini otomatis menjadi <strong>billing anchor day</strong>.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berakhir Sewa <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tarif Sewa Khusus (Opsional)</label>
                    <input type="number" step="0.01" name="rent_price_snapshot" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Kosongkan jika sesuai tarif kamar">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Billing Anchor Day Khusus (Opsional)</label>
                    <input type="number" min="1" max="31" name="billing_anchor_day" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Default mengikuti tanggal mulai">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Catatan kesepakatan sewa"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="<?= url('admin/contracts') ?>" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">Terbitkan Kontrak</button>
            </div>
        </form>
    </div>
</div>
