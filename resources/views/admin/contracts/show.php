<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="<?= url('admin/contracts') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-800 inline-flex items-center gap-1 mb-2">
                &larr; Kembali ke Daftar Kontrak
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Kontrak #<?= e($contract->contract_number) ?></h1>
            <p class="text-slate-500 text-sm">Status: <span class="font-semibold uppercase text-blue-600"><?= e($contract->status) ?></span></p>
        </div>

        <?php if ($contract->isActive()): ?>
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('modal-transfer-room').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 shadow-sm transition active:scale-95 cursor-pointer">
                    <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                    <span>Mutasi / Pindah Kamar</span>
                </button>
                <button type="button" onclick="document.getElementById('modal-terminate-contract').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 shadow-sm transition active:scale-95 cursor-pointer">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Selesai Sewa (Checkout)</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contract Overview Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Informasi Kamar & Penyewa</h2>
            <div class="space-y-2 text-sm text-slate-700">
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Kamar:</span>
                    <span class="font-semibold text-slate-900"><?= e($contract->room_number) ?> (<?= e($contract->room_name) ?>)</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Nama Penyewa:</span>
                    <span class="font-semibold text-slate-900"><?= e($contract->tenant_name) ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">No. Telepon:</span>
                    <span class="text-slate-900"><?= e($contract->tenant_phone ?: '-') ?></span>
                </div>
                <div class="flex justify-between pb-2">
                    <span class="text-slate-500">Catatan:</span>
                    <span class="text-slate-800"><?= e($contract->notes ?: '-') ?></span>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Ketentuan Sewa & Tanggal</h2>
            <div class="space-y-2 text-sm text-slate-700">
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Tarif Bulanan (Snapshot):</span>
                    <span class="font-bold text-blue-600"><?= $contract->formattedPrice() ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Billing Anchor Day:</span>
                    <span class="font-semibold text-slate-900">Setiap tanggal <?= (int)$contract->billing_anchor_day ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Periode Sewa:</span>
                    <span class="text-slate-900 font-medium"><?= e($contract->start_date) ?> s/d <?= e($contract->end_date) ?></span>
                </div>
                <div class="flex justify-between pb-2">
                    <span class="text-slate-500">Diterbitkan Pada:</span>
                    <span class="text-slate-600"><?= e($contract->created_at) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Audit History -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Histori Perubahan Kontrak</h3>
        </div>
        <div class="divide-y divide-slate-100">
            <?php if (empty($histories)): ?>
                <div class="p-6 text-center text-slate-400 text-sm">Belum ada catatan histori.</div>
            <?php else: ?>
                <?php foreach ($histories as $h): ?>
                    <div class="p-4 hover:bg-slate-50/50 transition">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                            <span class="font-semibold text-slate-800 uppercase px-2 py-0.5 rounded bg-slate-100">
                                <?= e($h->action) ?>
                            </span>
                            <span><?= e($h->created_at) ?></span>
                        </div>
                        <p class="text-sm text-slate-700 mt-1"><?= e($h->notes ?: '-') ?></p>
                        <?php if ($h->old_end_date || $h->new_end_date || $h->old_rent_price || $h->new_rent_price): ?>
                            <div class="mt-2 text-xs text-slate-500 flex flex-wrap gap-4">
                                <?php if ($h->old_end_date && $h->new_end_date): ?>
                                    <span>Periode: <?= e($h->old_end_date) ?> &rarr; <?= e($h->new_end_date) ?></span>
                                <?php endif; ?>
                                <?php if ($h->old_rent_price && $h->new_rent_price): ?>
                                    <span>Tarif: Rp <?= number_format((float)$h->old_rent_price, 0, ',', '.') ?> &rarr; Rp <?= number_format((float)$h->new_rent_price, 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($contract->isActive()): ?>
        <!-- Modal Mutasi / Pindah Kamar -->
        <div id="modal-transfer-room" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Mutasi / Pindah Kamar</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('modal-transfer-room').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="<?= url("admin/contracts/{$contract->id}/transfer-room") ?>" method="POST" class="p-6 space-y-4">
                    <?= csrf_field() ?>
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
                        Pindah kamar akan otomatis membebaskan <strong>Kamar <?= e($contract->room_number) ?></strong> menjadi <em>available</em> dan mengunci kamar baru menjadi <em>occupied</em>.
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Pilih Kamar Tujuan (Tersedia) <span class="text-rose-500">*</span>
                        </label>
                        <select name="new_room_id" required class="w-full text-xs sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Kamar Kosong --</option>
                            <?php foreach ($availableRooms as $r): ?>
                                <?php
                                $rId = is_object($r) ? $r->id : $r['id'];
                                $rNum = is_object($r) ? $r->room_number : $r['room_number'];
                                $rName = is_object($r) ? $r->name : $r['name'];
                                $rPrice = is_object($r) ? $r->current_price : $r['current_price'];
                                ?>
                                <option value="<?= (int)$rId ?>">
                                    Kamar <?= e($rNum) ?> - <?= e($rName) ?> (Rp <?= number_format((float)$rPrice, 0, ',', '.') ?>/bln)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Efektif <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="effective_date" required value="<?= date('Y-m-d') ?>" class="w-full text-xs sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Penyesuaian Tarif (Opsional)
                            </label>
                            <input type="number" step="0.01" name="new_price" placeholder="Default sesuai tarif kamar" class="w-full text-xs sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alasan Pindah Kamar (Opsional)
                        </label>
                        <input type="text" name="reason" placeholder="Contoh: Pindah ke lantai 1 / upgrade kamar mandi dalam" class="w-full text-xs sm:text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="document.getElementById('modal-transfer-room').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition">
                            Konfirmasi Pindah Kamar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Selesai Sewa (Checkout Langsung) -->
        <div id="modal-terminate-contract" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Pengakhiran Sewa / Checkout</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('modal-terminate-contract').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="<?= url("admin/contracts/{$contract->id}/terminate") ?>" method="POST" class="p-6 space-y-4">
                    <?= csrf_field() ?>
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-900">
                        Tindakan ini akan mengakhiri kontrak sewa aktif dan mengembalikan <strong>Kamar <?= e($contract->room_number) ?></strong> menjadi berstatus <em>tersedia (available)</em>.
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Tanggal Checkout Efektif <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="checkout_date" required value="<?= date('Y-m-d') ?>" class="w-full text-xs sm:text-sm rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alasan Selesai Sewa / Catatan
                        </label>
                        <input type="text" name="reason" value="Selesai masa tinggal / checkout normal" class="w-full text-xs sm:text-sm rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="document.getElementById('modal-terminate-contract').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition">
                            Proses Checkout Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
