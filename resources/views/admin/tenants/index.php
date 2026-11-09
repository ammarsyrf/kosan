<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Penghuni (Tenants)</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar akun penghuni kos dan status aktivasi.</p>
        </div>
        <div>
            <a href="<?= url('admin/tenants/create') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                <i data-lucide="user-plus" class="w-4 h-4 mr-1.5"></i> Daftarkan Penghuni Baru
            </a>
        </div>
    </div>

    <!-- Activation Link Notification Box if generated in previous request -->
    <?php if ($actUrl = session('_flash.activation_url')): ?>
        <div class="p-5 rounded-2xl bg-blue-50 border border-blue-200 shadow-sm space-y-2">
            <div class="flex items-center space-x-2 text-blue-900 font-bold text-sm">
                <i data-lucide="link" class="w-5 h-5 text-blue-600"></i>
                <span>Tautan Aktivasi Akun Berhasil Dibuat!</span>
            </div>
            <p class="text-xs text-blue-800 leading-relaxed">
                Salin tautan aman ini dan kirimkan langsung kepada calon penghuni (melalui WhatsApp/Email) untuk aktivasi mandiri dan pembuatan kata sandi:
            </p>
            <div class="flex items-center space-x-2 pt-1">
                <input type="text" readonly value="<?= e($actUrl) ?>" id="actLinkInput"
                    class="w-full px-3.5 py-2 text-xs font-mono bg-white border border-blue-300 rounded-lg text-slate-800 select-all focus:outline-none">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('actLinkInput').value); alert('Tautan aktivasi berhasil disalin ke clipboard!');"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg flex-shrink-0 transition">
                    Salin Tautan
                </button>
            </div>
            <p class="text-[11px] text-blue-600 italic">* Tautan hanya berlaku 1 kali pakai dan kedaluwarsa dalam 48 jam.</p>
        </div>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Nama Penghuni</th>
                        <th class="px-6 py-3.5">Kontak</th>
                        <th class="px-6 py-3.5">Kamar Saat Ini</th>
                        <th class="px-6 py-3.5">Status Akun</th>
                        <th class="px-6 py-3.5">Pekerjaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($tenants)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Belum ada data penghuni. Klik "Daftarkan Penghuni Baru" untuk memulai.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tenants as $t): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900"><?= e($t->name) ?></div>
                                    <div class="text-xs text-slate-400 mt-0.5">NIK: <?= e($t->id_card_number ?: '-') ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-slate-800"><?= e($t->email) ?></div>
                                    <div class="text-xs text-slate-500 mt-0.5"><?= e($t->phone ?: '-') ?></div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    <?php if ($t->room_number): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Kamar <?= e($t->room_number) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic">Belum ada kontrak</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($t->status === 'active'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    <?php elseif ($t->status === 'pending'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Aktivasi
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    <?= e($t->occupation ?: '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

