<?php
$errors = session('_flash.errors') ?? [];
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ubah Data Penghuni</h1>
            <p class="text-sm text-slate-500 mt-1">Perbarui profil dan status akun penyewa.</p>
        </div>
        <a href="<?= url('admin/tenants') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <form method="POST" action="<?= url("admin/tenants/{$tenant->id}") ?>" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <?= csrf_field() ?>

        <div class="space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Informasi Akun</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        placeholder="Contoh: Budi Santoso"
                        value="<?= e($tenant->name) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition <?= isset($errors['name']) ? 'border-rose-400' : 'border-slate-300' ?>">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                        placeholder="budi@email.com"
                        value="<?= e($tenant->email) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition <?= isset($errors['email']) ? 'border-rose-400' : 'border-slate-300' ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="phone" name="phone" required
                        placeholder="081234567890"
                        value="<?= e($tenant->phone) ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition <?= isset($errors['phone']) ? 'border-rose-400' : 'border-slate-300' ?>">
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Status Akun <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <option value="active" <?= $tenant->status === 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="pending" <?= $tenant->status === 'pending' ? 'selected' : '' ?>>Menunggu Aktivasi</option>
                        <option value="inactive" <?= $tenant->status === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="id_card_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nomor KTP / NIK
                    </label>
                    <input type="text" id="id_card_number" name="id_card_number"
                        placeholder="3201xxxxxxxxxxxx"
                        value="<?= e($tenant->id_card_number ?? '') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="occupation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Pekerjaan / Instansi
                    </label>
                    <input type="text" id="occupation" name="occupation"
                        placeholder="Mahasiswa / Karyawan Swasta"
                        value="<?= e($tenant->occupation ?? '') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
            </div>
        </div>

        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Kontak Darurat & Catatan</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="emergency_contact_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Kontak Darurat
                    </label>
                    <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                        placeholder="Nama Orang Tua / Kerabat"
                        value="<?= e($tenant->emergency_contact_name ?? '') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="emergency_contact_phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        No. HP Kontak Darurat
                    </label>
                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                        placeholder="08xxxxxxxxxx"
                        value="<?= e($tenant->emergency_contact_phone ?? '') ?>"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Catatan Khusus
                </label>
                <textarea id="notes" name="notes" rows="2"
                    placeholder="Kendaraan bawaan (plat motor), catatan alergi, dsb."
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"><?= e($tenant->notes ?? '') ?></textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
            <a href="<?= url('admin/tenants') ?>" class="px-5 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
