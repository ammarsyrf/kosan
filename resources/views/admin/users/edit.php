<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="user-cog" class="w-3.5 h-3.5"></i>
                    Edit Akun
                </span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Ubah Data Akun Pengguna</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui profil, hak akses role, status akun, dan kata sandi.</p>
        </div>
        <a href="<?= url('admin/users') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <form method="POST" action="<?= url("admin/users/{$targetUser->id}") ?>" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <?= csrf_field() ?>

        <div class="space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Informasi Profil</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        value="<?= e($targetUser->name) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                        value="<?= e($targetUser->email) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor WhatsApp / HP
                    </label>
                    <input type="text" id="phone" name="phone"
                        value="<?= e($targetUser->phone ?? '') ?>"
                        placeholder="081234567890"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Status Akun <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <option value="active" <?= $targetUser->status === 'active' ? 'selected' : '' ?>>Aktif (Bisa Masuk)</option>
                        <option value="pending" <?= $targetUser->status === 'pending' ? 'selected' : '' ?>>Menunggu Aktivasi</option>
                        <option value="inactive" <?= $targetUser->status === 'inactive' ? 'selected' : '' ?>>Nonaktif (Akses Diblokir)</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Tipe Hak Akses (Role) <span class="text-rose-500">*</span>
                </label>
                <select id="role" name="role" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <option value="tenant" <?= strtolower($targetUser->role) === 'tenant' ? 'selected' : '' ?>>Penghuni (Tenant)</option>
                    <option value="admin" <?= strtolower($targetUser->role) === 'admin' ? 'selected' : '' ?>>Admin Operasional</option>
                    <option value="owner" <?= strtolower($targetUser->role) === 'owner' ? 'selected' : '' ?>>Owner (Pemilik Kos)</option>
                </select>
            </div>
        </div>

        <!-- Section Ganti Kata Sandi -->
        <div class="space-y-3 p-4 rounded-2xl bg-amber-50/70 border border-amber-200">
            <div class="flex items-center gap-2 text-amber-800">
                <i data-lucide="key" class="w-4 h-4 text-amber-600"></i>
                <h2 class="text-xs font-bold uppercase tracking-wider text-amber-900">Ubah / Reset Kata Sandi</h2>
            </div>
            <p class="text-xs text-amber-700 leading-relaxed">
                Kosongkan kolom di bawah jika tidak ingin mengubah kata sandi pengguna ini. Masukkan kata sandi baru jika anak kost atau admin lupa password.
            </p>
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Kata Sandi Baru (Opsional, min. 6 karakter)
                </label>
                <input type="text" id="password" name="password" minlength="6"
                    placeholder="Ketik password baru jika ingin mengubah..."
                    class="w-full px-3.5 py-2.5 bg-white border border-amber-300 rounded-xl text-sm font-mono text-slate-900 focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="<?= url('admin/users') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold shadow-md shadow-blue-500/25 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

