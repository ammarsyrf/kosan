<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Template Pesan WhatsApp</h1>
        <p class="text-slate-500 text-sm">Kelola redaksi pesan otomatis dan variabel dinamis yang digunakan Fonnte.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/notifications') ?>" class="inline-flex items-center gap-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-4 py-2.5 rounded-lg text-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Log
        </a>
    </div>
</div>

<div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-900">
    <div class="font-semibold flex items-center gap-2 mb-2">
        <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
        Daftar Variabel (Placeholder) yang Didukung:
    </div>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($placeholders as $ph): ?>
            <code class="px-2 py-1 bg-white border border-blue-200 rounded text-xs font-mono text-blue-700"><?= e($ph) ?></code>
        <?php endforeach; ?>
    </div>
    <p class="mt-2 text-xs text-blue-700">Setiap placeholder dalam pesan akan digantikan secara otomatis dengan data faktual tagihan/penyewa saat notifikasi dibuat.</p>
</div>

<div class="space-y-6">
    <?php foreach ($templates as $tmpl): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 font-mono text-sm"><?= e($tmpl->code) ?></h3>
                    <p class="text-xs text-slate-500"><?= e($tmpl->name) ?></p>
                </div>
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $tmpl->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' ?>">
                        <?= $tmpl->is_active ? 'Aktif' : 'Non-aktif' ?>
                    </span>
                </div>
            </div>
            <form method="POST" action="<?= url('admin/notifications/templates') ?>" class="p-5">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $tmpl->id ?>">

                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">Isi Redaksi Pesan</label>
                    <textarea name="content" rows="5" required class="w-full font-mono text-sm border border-slate-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= e($tmpl->content) ?></textarea>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?= $tmpl->is_active ? 'checked' : '' ?> class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-slate-700">Aktifkan template ini</span>
                    </label>

                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
</div>
