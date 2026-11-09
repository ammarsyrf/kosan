<div class="py-12 text-center max-w-lg mx-auto">
    <div class="w-16 h-16 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i data-lucide="compass" class="w-8 h-8"></i>
    </div>
    <h1 class="text-3xl font-bold text-slate-900 mb-2">404 — Halaman Tidak Ditemukan</h1>
    <p class="text-sm text-slate-600 mb-6">
        <?= e($message ?: 'Halaman yang Anda tuju tidak ditemukan atau URL mungkin telah dipindahkan.') ?>
    </p>
    <a href="<?= url() ?>" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-white bg-slate-900 hover:bg-slate-800 transition">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i> Kembali ke Beranda
    </a>
</div>

