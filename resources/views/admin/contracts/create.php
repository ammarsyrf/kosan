<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-1">
        <div>
            <a href="<?= url('admin/contracts') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 mb-2 transition">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Daftar Kontrak</span>
            </a>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="file-signature" class="w-3.5 h-3.5"></i>
                    Penerbitan Kontrak
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">Buat Kontrak Sewa Langsung</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Terbitkan kontrak sewa resmi secara langsung tanpa melalui tahapan booking terlebih dahulu.</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-6 sm:p-8 space-y-6">
        <form action="<?= url('admin/contracts') ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>

            <!-- Kamar & Penyewa -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="room_id" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i data-lucide="door-closed" class="w-3.5 h-3.5 text-blue-600"></i>
                        <span>Pilih Kamar Kost</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="bed-double" class="w-4 h-4"></i>
                        </div>
                        <select name="room_id" id="room_id" required 
                                class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-10 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                            <option value="">-- Pilih Kamar Tersedia --</option>
                            <?php
                            $selectedRoomId = (int)($_GET['room_id'] ?? 0);
                            foreach ($rooms as $room):
                                $rId = (int)(is_object($room) ? $room->id : $room['id']);
                                $rNum = is_object($room) ? $room->room_number : $room['room_number'];
                                $rName = is_object($room) ? $room->name : $room['name'];
                                $rPrice = (float)(is_object($room) ? ($room->current_price ?? 0) : ($room['current_price'] ?? 0));
                                $rFloor = is_object($room) ? ($room->floor ?? 1) : ($room['floor'] ?? 1);
                            ?>
                                <option value="<?= $rId ?>" <?= $selectedRoomId === $rId ? 'selected' : '' ?>>
                                    Kamar <?= e($rNum) ?> &bull; <?= e($rName) ?> &bull; Lt <?= $rFloor ?> (Rp <?= number_format($rPrice, 0, ',', '.') ?>/bln)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="tenant_id" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-indigo-600"></i>
                        <span>Pilih Calon Penyewa</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </div>
                        <select name="tenant_id" id="tenant_id" required 
                                class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-10 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                            <option value="">-- Pilih Penyewa Terdaftar --</option>
                            <?php foreach ($tenants as $tenant):
                                $tId = (int)(is_object($tenant) ? $tenant->id : $tenant['id']);
                                $tName = is_object($tenant) ? $tenant->name : $tenant['name'];
                                $tPhone = is_object($tenant) ? $tenant->phone : ($tenant['phone'] ?? '');
                            ?>
                                <option value="<?= $tId ?>">
                                    <?= e($tName) ?> &bull; WA: <?= e($tPhone ?: '-') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Model Kontrak Sewa (Standar Kos Indonesia) -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Model Durasi Sewa <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-start p-3.5 rounded-xl border-2 border-blue-500 bg-white shadow-xs cursor-pointer hover:border-blue-600 transition" id="label-rolling">
                        <input type="radio" name="duration_type" value="rolling" checked class="mt-0.5 text-blue-600 focus:ring-blue-500" onchange="toggleDurationType('rolling')">
                        <div class="ml-2.5">
                            <span class="block text-xs font-bold text-slate-900">Bulanan Bergulir (Open-Ended)</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5 leading-relaxed">Sewa fleksibel berlanjut otomatis tiap bulan sampai penyewa checkout. Standar kos di Indonesia.</span>
                        </div>
                    </label>

                    <label class="relative flex items-start p-3.5 rounded-xl border border-slate-200 bg-white shadow-xs cursor-pointer hover:border-slate-300 transition" id="label-fixed">
                        <input type="radio" name="duration_type" value="fixed" class="mt-0.5 text-blue-600 focus:ring-blue-500" onchange="toggleDurationType('fixed')">
                        <div class="ml-2.5">
                            <span class="block text-xs font-bold text-slate-900">Jangka Waktu Tetap (Fixed Term)</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5 leading-relaxed">Kontrak dengan tanggal selesai pasti (misal 3 bulan, 6 bulan, atau 1 tahun).</span>
                        </div>
                    </label>
                </div>

                <div id="rolling-info" class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200/60 text-xs text-blue-900 flex items-start gap-2.5">
                    <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                    <span class="leading-relaxed">Tagihan sewa bulanan dan pengingat WhatsApp Fonnte akan terbit otomatis setiap bulan berdasarkan tanggal billing anchor, tanpa perlu membuat kontrak ulang.</span>
                </div>
            </div>

            <!-- Tanggal Sewa -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i data-lucide="calendar-plus" class="w-3.5 h-3.5 text-teal-600"></i>
                        <span>Tanggal Mulai Sewa</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <input type="date" name="start_date" id="start_date" required value="<?= date('Y-m-d') ?>" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-3 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition shadow-xs cursor-pointer" 
                               onchange="handleStartDateChange()">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Hari dari tanggal mulai ini otomatis menjadi <strong>billing anchor day</strong>.</p>
                </div>

                <div>
                    <label for="end_date" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Tanggal Berakhir Sewa</span>
                        <span id="end-date-star" class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <input type="date" name="end_date" id="end_date" required 
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-3 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                    </div>
                    <div id="quick-duration-chips" class="hidden mt-2 flex flex-wrap gap-1.5">
                        <button type="button" onclick="setFixedDuration(1)" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 border border-slate-200 transition">1 Bln</button>
                        <button type="button" onclick="setFixedDuration(3)" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 border border-slate-200 transition">3 Bln</button>
                        <button type="button" onclick="setFixedDuration(6)" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 border border-slate-200 transition">6 Bln</button>
                        <button type="button" onclick="setFixedDuration(12)" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 border border-slate-200 transition">1 Thn</button>
                    </div>
                </div>
            </div>

            <!-- Snapshot & Anchor -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="rent_price_snapshot" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i data-lucide="tag" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Tarif Sewa Khusus (Opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" step="0.01" id="rent_price_snapshot" name="rent_price_snapshot" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-3.5 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition shadow-xs" 
                               placeholder="Kosongkan jika sesuai tarif kamar">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika mengikuti harga default kamar.</p>
                </div>

                <div>
                    <label for="billing_anchor_day" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-indigo-600"></i>
                        <span>Billing Anchor Day Khusus (Opsional)</span>
                    </label>
                    <input type="number" min="1" max="31" id="billing_anchor_day" name="billing_anchor_day" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition shadow-xs" 
                           placeholder="Default: mengikuti tanggal mulai sewa (1-31)">
                    <p class="text-[11px] text-slate-400 mt-1">Tanggal tagihan rutin bulanan diterbitkan.</p>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="notes" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Catatan Tambahan (Opsional)</span>
                </label>
                <textarea id="notes" name="notes" rows="2" 
                          placeholder="Catatan kesepakatan sewa khusus dengan penghuni..."
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3.5 text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition shadow-xs"></textarea>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="<?= url('admin/contracts') ?>" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/25 hover:shadow-lg transition transform active:scale-95">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>Terbitkan Kontrak</span>
                </button>
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
        labelRolling.classList.remove('border-slate-200');
        labelFixed.classList.remove('border-blue-500', 'bg-blue-50/20');
        labelFixed.classList.add('border-slate-200');
        if (startDateInput.value) {
            endDateInput.value = computeRollingEndDate(startDateInput.value);
        }
    } else {
        rollingInfo.classList.add('hidden');
        chips.classList.remove('hidden');
        labelFixed.classList.add('border-blue-500', 'bg-blue-50/20');
        labelFixed.classList.remove('border-slate-200');
        labelRolling.classList.remove('border-blue-500', 'bg-blue-50/20');
        labelRolling.classList.add('border-slate-200');
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
    if (window.lucide) {
        window.lucide.createIcons();
    }
    handleStartDateChange();
});
</script>
