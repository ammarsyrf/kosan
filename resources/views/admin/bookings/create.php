<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-1">
        <div>
            <a href="<?= url('admin/bookings') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 mb-2 transition">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Daftar Booking</span>
            </a>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                    Reservasi Unit
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">Tambah Booking Baru</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Pemesanan kamar kos bersifat gratis tanpa biaya booking, dengan masa tunggu konfirmasi otomatis.</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-6 sm:p-8 space-y-6">
        <form action="<?= url('admin/bookings') ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>

            <!-- Pilih Kamar -->
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

            <!-- Pilih Calon Penyewa -->
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
                <div class="mt-1.5 flex justify-end">
                    <a href="<?= url('admin/tenants/create') ?>" target="_blank" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <i data-lucide="user-plus" class="w-3 h-3"></i>
                        <span>+ Daftarkan Penyewa Baru</span>
                    </a>
                </div>
            </div>

            <!-- Periode Sewa -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        <input type="date" name="start_date" id="start_date" required min="<?= date('Y-m-d') ?>"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-3 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                    </div>
                </div>

                <div>
                    <label for="end_date" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Tanggal Berakhir Sewa</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <input type="date" name="end_date" id="end_date" required min="<?= date('Y-m-d') ?>"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-3 py-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Duration Indicator Pill (Dynamic via JS) -->
            <div id="duration-preview-pill" class="hidden p-3 rounded-xl bg-blue-50/80 border border-blue-200/80 flex items-center gap-2 text-xs text-blue-900 font-medium">
                <i data-lucide="clock" class="w-4 h-4 text-blue-600 shrink-0"></i>
                <span id="duration-text"></span>
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label for="notes" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Catatan Tambahan (Opsional)</span>
                </label>
                <textarea id="notes" name="notes" rows="2"
                          placeholder="Contoh: Calon penghuni berencana masuk akhir pekan, titip kunci ke security."
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3.5 text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition shadow-xs"></textarea>
            </div>

            <!-- Info Notice -->
            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex items-start gap-3 text-xs text-amber-900">
                <i data-lucide="shield-alert" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                <div class="leading-relaxed">
                    Sistem otomatis memvalidasi <strong>anti-bentrok tanggal</strong> dengan kontrak atau booking aktif lainnya. Booking memiliki masa tunggu kedaluwarsa 24 jam sebelum unit otomatis dibebaskan kembali.
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="<?= url('admin/bookings') ?>" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/25 hover:shadow-lg transition transform active:scale-95">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Simpan Booking</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }

    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const pill = document.getElementById('duration-preview-pill');
    const durationText = document.getElementById('duration-text');

    function updateDuration() {
        if (!startInput.value || !endInput.value) {
            pill.classList.add('hidden');
            return;
        }

        const start = new Date(startInput.value);
        const end = new Date(endInput.value);

        if (end <= start) {
            pill.classList.remove('hidden');
            pill.className = 'p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-2 text-xs text-rose-800 font-medium';
            durationText.innerHTML = '<strong>Peringatan:</strong> Tanggal berakhir sewa harus lebih besar dari tanggal mulai.';
            return;
        }

        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const months = (diffDays / 30).toFixed(1);

        pill.classList.remove('hidden');
        pill.className = 'p-3 rounded-xl bg-blue-50/80 border border-blue-200/80 flex items-center gap-2 text-xs text-blue-900 font-medium';
        durationText.innerHTML = `Estimasi Durasi Sewa: <strong>${diffDays} Hari</strong> (~${months} Bulan)`;
    }

    startInput.addEventListener('change', () => {
        if (startInput.value) {
            endInput.min = startInput.value;
            if (!endInput.value) {
                // Auto-suggest 1 month later
                const d = new Date(startInput.value);
                d.setMonth(d.getMonth() + 1);
                endInput.value = d.toISOString().split('T')[0];
            }
        }
        updateDuration();
    });

    endInput.addEventListener('change', updateDuration);
});
</script>
