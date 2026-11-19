<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="megaphone" class="w-6 h-6 text-indigo-600"></i>
                <span>Pengumuman & Broadcast Kost</span>
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
                Siarkan pengumuman atau informasi tata tertib yang langsung tampil di dashboard seluruh penghuni.
            </p>
        </div>
        <button type="button" onclick="document.getElementById('modal-create-announcement').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition cursor-pointer">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Buat Pengumuman Baru</span>
        </button>
    </div>

    <!-- Announcement List -->
    <div class="space-y-4">
        <?php if (empty($announcements)): ?>
            <div class="bg-white rounded-3xl border border-dashed border-slate-300 p-12 text-center text-slate-400 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="bell-off" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Belum Ada Pengumuman</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto">
                    Klik "Buat Pengumuman Baru" untuk mengirim pemberitahuan jadwal iuran, pemeliharaan air/listrik, atau himbauan ke seluruh anak kost.
                </p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($announcements as $a): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-3 flex flex-col justify-between hover:shadow-md transition">
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <?php if ($a['is_active']): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Tayang
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-500">
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-[11px] text-slate-400">
                                        <?= date('d M Y, H:i', strtotime($a['created_at'])) ?>
                                    </span>
                                </div>
                                <form method="POST" action="<?= url("admin/announcements/{$a['id']}/delete") ?>" onsubmit="return confirm('Hapus pengumuman ini?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 transition p-1" title="Hapus Pengumuman">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 leading-snug">
                                <?= e($a['title']) ?>
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                                <?= e($a['content']) ?>
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Oleh: <strong class="text-slate-700"><?= e($a['creator_name']) ?></strong></span>
                            <span class="flex items-center gap-1 text-slate-500">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>Dibaca <?= (int)$a['read_count'] ?> kali</span>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Create Announcement -->
<div id="modal-create-announcement" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" onclick="document.getElementById('modal-create-announcement').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 z-10 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="megaphone" class="w-4 h-4 text-blue-600"></i>
                <span>Tulis Pengumuman Baru</span>
            </h3>
            <button type="button" onclick="document.getElementById('modal-create-announcement').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="<?= url('admin/announcements') ?>" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="announcement_title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Judul Pengumuman <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="announcement_title" name="title" required
                    placeholder="Contoh: Pemeliharaan Tangki Air Bersih Sabtu Besok"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label for="announcement_content" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Isi Pesan Pengumuman <span class="text-rose-500">*</span>
                </label>
                <textarea id="announcement_content" name="content" rows="4" required
                    placeholder="Tuliskan detail pengumuman secara jelas untuk seluruh penghuni..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                <label for="is_active" class="text-xs font-medium text-slate-700">Langsung siarkan dan tayangkan sekarang</label>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-create-announcement').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20">
                    Kirim & Siarkan
                </button>
            </div>
        </form>
    </div>
</div>
