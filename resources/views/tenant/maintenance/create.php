<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Ajukan Laporan Perbaikan</h1>
        <p class="text-slate-500 text-sm">Laporkan kendala kerusakan pada kamar atau fasilitas kos.</p>
    </div>
    <a href="<?= url('tenant/maintenance') ?>" class="inline-flex items-center gap-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-4 py-2.5 rounded-lg text-sm transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
    <form method="POST" action="<?= url('tenant/maintenance') ?>" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>

        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-900 flex items-center gap-2">
            <i data-lucide="home" class="w-4 h-4 text-blue-600"></i>
            <span>Laporan ini akan ditujukan untuk unit Anda: <strong>Kamar <?= e($contract->room_number) ?></strong></span>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Kategori Kerusakan *</label>
            <select name="category_id" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">-- Pilih Jenis Kerusakan --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>"><?= e($cat->name) ?> - <?= e($cat->description) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Judul Singkat Kerusakan *</label>
            <input type="text" name="title" required placeholder="Contoh: AC kamar tidak dingin / Kran air wastafel patah" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Deskripsi Detail Kerusakan *</label>
            <textarea name="description" rows="4" required placeholder="Jelaskan secara rinci kronologi, kondisi kerusakan, atau kapan masalah mulai terjadi..." class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Tingkat Urgensi / Prioritas</label>
            <select name="priority" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="low">Rendah (Low) — Kerusakan kecil / tidak mendesak</option>
                <option value="medium" selected>Sedang (Medium) — Memerlukan perbaikan standar</option>
                <option value="high">Tinggi (High) — Mengganggu kenyamanan utama</option>
                <option value="urgent">Mendesak (Urgent) — Kondisi darurat (pipa pecah, korsleting, dll)</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-slate-700 mb-1">Foto Bukti Kerusakan (Opsional)</label>
            <input type="file" name="attachments[]" multiple accept="image/jpeg,image/png,image/webp,application/pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-lg">
            <span class="text-[10px] text-slate-400 block mt-1">Dapat memilih lebih dari 1 file foto. Maksimal 5 MB per file (JPG, PNG, WEBP, PDF).</span>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                <i data-lucide="send" class="w-4 h-4"></i>
                Kirim Laporan
            </button>
        </div>
    </form>
</div>
