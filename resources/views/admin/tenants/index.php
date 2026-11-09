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
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($tenants)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
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
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <button type="button" onclick="openResetPasswordModal(<?= $t->id ?>, '<?= e(addslashes($t->name)) ?>', '<?= e(addslashes($t->email)) ?>')" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:text-amber-800 transition bg-amber-50 hover:bg-amber-100 px-2 py-1 rounded-lg border border-amber-200" title="Reset password jika anak kost lupa password">
                                        <i data-lucide="key" class="w-3.5 h-3.5"></i> Reset PW
                                    </button>
                                    <a href="<?= url("admin/tenants/{$t->id}/edit") ?>" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Ubah
                                    </a>
                                    <form method="POST" action="<?= url("admin/tenants/{$t->id}/delete") ?>" class="inline ml-2" onsubmit="return confirm('Hapus data penghuni <?= e(addslashes($t->name)) ?>? Data yang dihapus tidak dapat dipulihkan.')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-800 transition">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Reset Password Anak Kost -->
    <div id="reset-pw-modal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 sm:p-7 space-y-5 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <i data-lucide="key" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Reset Password Penghuni</h3>
                        <p class="text-xs text-slate-400">Ganti password akun jika anak kost lupa</p>
                    </div>
                </div>
                <button type="button" onclick="closeResetPasswordModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="reset-pw-form" method="POST" action="" class="space-y-4">
                <?= csrf_field() ?>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Penghuni Terpilih</div>
                    <div id="modal-tenant-name" class="font-bold text-slate-900 text-sm"></div>
                    <div id="modal-tenant-email" class="text-xs text-slate-500 font-mono"></div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="new_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="generateRandomPassword()" class="text-[11px] font-bold text-blue-600 hover:text-blue-800">
                            Acak Password
                        </button>
                    </div>
                    <input type="text" id="new_password" name="new_password" required minlength="6"
                        placeholder="Contoh: kost12345"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                    <p class="mt-1 text-[11px] text-slate-400">Minimal 6 karakter. Password langsung aktif seketika.</p>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs font-bold shadow-md shadow-amber-500/20 hover:shadow-lg transition">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openResetPasswordModal(tenantId, name, email) {
        const modal = document.getElementById('reset-pw-modal');
        const form = document.getElementById('reset-pw-form');
        const nameEl = document.getElementById('modal-tenant-name');
        const emailEl = document.getElementById('modal-tenant-email');
        const input = document.getElementById('new_password');

        form.action = '<?= url("admin/tenants") ?>/' + tenantId + '/reset-password';
        nameEl.textContent = name;
        emailEl.textContent = email;
        input.value = '';
        modal.classList.remove('hidden');
        input.focus();
    }

    function closeResetPasswordModal() {
        document.getElementById('reset-pw-modal').classList.add('hidden');
    }

    function generateRandomPassword() {
        const words = ['kosan', 'kost', 'kamar', 'hunian', 'sukses', 'nyaman'];
        const word = words[Math.floor(Math.random() * words.length)];
        const num = Math.floor(1000 + Math.random() * 9000);
        document.getElementById('new_password').value = word + num;
    }
    </script>
</div>

