<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    Staf & Admin Baru
                </span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Tambah Akun Pengelola</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Buat akun untuk admin operasional atau pemilik kos tambahan.</p>
        </div>
        <a href="<?= url('admin/users') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <form method="POST" action="<?= url('admin/users') ?>" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-5">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                    placeholder="Contoh: Siti Rahma"
                    value="<?= e(session('_flash.old.name') ?? '') ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Email (Login) <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="email" name="email" required
                    placeholder="admin2@kost.com"
                    value="<?= e(session('_flash.old.email') ?? '') ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nomor WhatsApp / HP
                </label>
                <input type="text" id="phone" name="phone"
                    placeholder="081234567890"
                    value="<?= e(session('_flash.old.phone') ?? '') ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>

            <div>
                <label for="role" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Tipe Hak Akses (Role) <span class="text-rose-500">*</span>
                </label>
                <select id="role" name="role" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <option value="admin" selected>Admin Operasional (Kelola Booking, Tagihan, Penghuni)</option>
                    <option value="owner">Owner / Pemilik (Akses Penuh + Keuangan)</option>
                </select>
            </div>
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                Kata Sandi Awal <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="password" name="password" required minlength="6"
                placeholder="Minimal 6 karakter"
                value="password123"
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            <p class="mt-1 text-[11px] text-slate-400">Pengelola dapat mengubah kata sandi ini kapan saja setelah masuk.</p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="<?= url('admin/users') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold shadow-md shadow-blue-500/25 transition">
                Simpan & Buat Akun
            </button>
        </div>
    </form>
</div>

