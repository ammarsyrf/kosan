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

            <!-- Model Kontrak Sewa (Standar Kos Indonesia) -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Model Durasi Sewa <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-start p-3 rounded-xl border border-blue-200 bg-white shadow-sm cursor-pointer hover:border-blue-400 transition" id="label-rolling">
                        <input type="radio" name="duration_type" value="rolling" checked class="mt-0.5 text-blue-600 focus:ring-blue-500" onchange="toggleDurationType('rolling')">
                        <div class="ml-2.5">
                            <span class="block text-xs font-bold text-slate-900">Bulanan Bergulir (Open-Ended)</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Sewa fleksibel berlanjut otomatis tiap bulan sampai penyewa checkout. Standar kos di Indonesia.</span>
                        </div>
                    </label>

                    <label class="relative flex items-start p-3 rounded-xl border border-slate-200 bg-white shadow-sm cursor-pointer hover:border-slate-300 transition" id="label-fixed">
                        <input type="radio" name="duration_type" value="fixed" class="mt-0.5 text-blue-600 focus:ring-blue-500" onchange="toggleDurationType('fixed')">
                        <div class="ml-2.5">
                            <span class="block text-xs font-bold text-slate-900">Jangka Waktu Tetap (Fixed Term)</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Kontrak dengan tanggal selesai pasti (misal 3 bulan, 6 bulan, atau 1 tahun).</span>
                        </div>
                    </label>
                </div>

                <div id="rolling-info" class="p-3 rounded-lg bg-blue-50/80 border border-blue-200/60 text-xs text-blue-900 flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                    <span>Tagihan sewa bulanan dan pesan reminder WhatsApp Fonnte akan terbit secara otomatis setiap bulan berdasarkan tanggal billing anchor, tanpa perlu admin membuat kontrak ulang.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai Sewa <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" required value="<?= date('Y-m-d') ?>" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="handleStartDateChange()">
                    <p class="text-xs text-slate-500 mt-1">Hari dari tanggal mulai ini otomatis menjadi <strong>billing anchor day</strong>.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Tanggal Berakhir Sewa <span id="end-date-star" class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <div id="quick-duration-chips" class="hidden mt-2 flex flex-wrap gap-1.5">
                        <button type="button" onclick="setFixedDuration(1)" class="px-2 py-0.5 text-[11px] font-semibold rounded bg-slate-100 hover:bg-slate-200 text-slate-700">1 Bln</button>
                        <button type="button" onclick="setFixedDuration(3)" class="px-2 py-0.5 text-[11px] font-semibold rounded bg-slate-100 hover:bg-slate-200 text-slate-700">3 Bln</button>
                        <button type="button" onclick="setFixedDuration(6)" class="px-2 py-0.5 text-[11px] font-semibold rounded bg-slate-100 hover:bg-slate-200 text-slate-700">6 Bln</button>
                        <button type="button" onclick="setFixedDuration(12)" class="px-2 py-0.5 text-[11px] font-semibold rounded bg-slate-100 hover:bg-slate-200 text-slate-700">1 Thn</button>
                    </div>
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

<script>
function computeRollingEndDate(startStr) {
    if (!startStr) return '';
    const d = new Date(startStr);
    d.setMonth(d.getMonth() + 1);
    d.setDate(d.getDate() - 1);
    return d.toISOString().split('T')[0];
}

function toggleDurationType(type) {
    const rollingInfo = document.getElementById('rolling-info');
    const chips = document.getElementById('quick-duration-chips');
    const labelRolling = document.getElementById('label-rolling');
    const labelFixed = document.getElementById('label-fixed');
    const endDateInput = document.getElementById('end_date');
    const startDateInput = document.getElementById('start_date');

    if (type === 'rolling') {
        rollingInfo.classList.remove('hidden');
        chips.classList.add('hidden');
        labelRolling.classList.add('border-blue-500', 'bg-blue-50/20');
        labelFixed.classList.remove('border-blue-500', 'bg-blue-50/20');
        if (startDateInput.value) {
            endDateInput.value = computeRollingEndDate(startDateInput.value);
        }
    } else {
        rollingInfo.classList.add('hidden');
        chips.classList.remove('hidden');
        labelFixed.classList.add('border-blue-500', 'bg-blue-50/20');
        labelRolling.classList.remove('border-blue-500', 'bg-blue-50/20');
        if (startDateInput.value) {
            setFixedDuration(3);
        }
    }
}

function handleStartDateChange() {
    const isRolling = document.querySelector('input[name="duration_type"]:checked')?.value === 'rolling';
    if (isRolling) {
        const startVal = document.getElementById('start_date').value;
        document.getElementById('end_date').value = computeRollingEndDate(startVal);
    }
}

function setFixedDuration(months) {
    const startVal = document.getElementById('start_date').value;
    if (!startVal) return;
    const d = new Date(startVal);
    d.setMonth(d.getMonth() + months);
    d.setDate(d.getDate() - 1);
    document.getElementById('end_date').value = d.toISOString().split('T')[0];
}

document.addEventListener('DOMContentLoaded', () => {
    handleStartDateChange();
});
</script>
