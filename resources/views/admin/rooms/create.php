<?php
$old = session('_flash.old') ?? [];
$errors = session('_flash.errors') ?? [];
?>

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Kamar Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Isi formulir untuk menambahkan unit kamar kos baru.</p>
        </div>
        <a href="<?= url('admin/rooms') ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <form method="POST" action="<?= url('admin/rooms') ?>" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="room_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Nomor / Kode Kamar <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="room_number" name="room_number" required
                    placeholder="Contoh: 101, A1"
                    value="<?= e($old['room_number'] ?? '') ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition <?= isset($errors['room_number']) ? 'border-rose-400' : 'border-slate-300' ?>">
                <?php if (isset($errors['room_number'])): ?>
                    <p class="mt-1 text-xs text-rose-600"><?= e($errors['room_number'][0]) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Nama / Tipe Kamar <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                    placeholder="Contoh: Kamar Standard, Deluxe Lantai 1"
                    value="<?= e($old['name'] ?? '') ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition <?= isset($errors['name']) ? 'border-rose-400' : 'border-slate-300' ?>">
                <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-xs text-rose-600"><?= e($errors['name'][0]) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="floor" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Posisi Lantai <span class="text-rose-500">*</span>
                </label>
                <select id="floor" name="floor" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <option value="1" <?= ((int)($old['floor'] ?? 1) === 1) ? 'selected' : '' ?>>Lantai 1</option>
                    <option value="2" <?= ((int)($old['floor'] ?? 1) === 2) ? 'selected' : '' ?>>Lantai 2</option>
                    <option value="3" <?= ((int)($old['floor'] ?? 1) === 3) ? 'selected' : '' ?>>Lantai 3</option>
                    <option value="4" <?= ((int)($old['floor'] ?? 1) === 4) ? 'selected' : '' ?>>Lantai 4</option>
                </select>
            </div>

            <div>
                <label for="size" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Luas / Dimensi Kamar <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="size" name="size" required
                    placeholder="Contoh: 3x4 m², 4x5 m²"
                    value="<?= e($old['size'] ?? '3x4 m²') ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="current_price" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Harga Sewa / Bulan (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="current_price" name="current_price" required min="100000" step="50000"
                    placeholder="1500000"
                    value="<?= e($old['current_price'] ?? '') ?>"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition <?= isset($errors['current_price']) ? 'border-rose-400' : 'border-slate-300' ?>">
                <p class="mt-1 text-[11px] text-slate-400">Harga ini akan menjadi patokan snapshot saat kontrak baru dibuat.</p>
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Status Operasional <span class="text-rose-500">*</span>
                </label>
                <select id="status" name="status" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <option value="available" <?= ($old['status'] ?? 'available') === 'available' ? 'selected' : '' ?>>Tersedia (Available)</option>
                    <option value="maintenance" <?= ($old['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Dalam Perbaikan (Maintenance)</option>
                    <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif (Inactive)</option>
                </select>
            </div>
        </div>

        <!-- Foto Kamar -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                Foto Kamar (Tampil di Landing Page / Katalog)
            </label>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="w-28 h-20 rounded-lg overflow-hidden border border-slate-300 bg-slate-200 shrink-0 relative">
                    <img src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=400&q=80" 
                         alt="Foto Preview" 
                         id="photo-preview"
                         class="w-full h-full object-cover">
                </div>
                <div class="flex-1 space-y-2 w-full">
                    <input type="file" name="photo" id="photo-input" accept="image/png,image/jpeg,image/webp"
                        onchange="const f = this.files[0]; if (f) { document.getElementById('photo-preview').src = URL.createObjectURL(f); }"
                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <div class="text-[11px] text-slate-400">
                        Format: JPG, PNG, WEBP. Maks 5MB. Rasio terbaik 3:2 landscape (misal 800x533 px).
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                Deskripsi Kamar
            </label>
            <textarea id="description" name="description" rows="2"
                placeholder="Spesifikasi ukuran kamar, sirkulasi udara, letak lantai, dsb."
                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"><?= e($old['description'] ?? '') ?></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                Fasilitas Kamar
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                <?php foreach ($facilities as $fac): ?>
                    <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer hover:text-slate-900">
                        <input type="checkbox" name="facilities[]" value="<?= $fac->id ?>"
                            <?= in_array($fac->id, (array)($old['facilities'] ?? [])) ? 'checked' : '' ?>
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span><?= e($fac->name) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
            <a href="<?= url('admin/rooms') ?>" class="px-5 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition shadow-sm">
                Simpan Kamar
            </button>
        </div>
    </form>
</div>

