<div class="text-center py-4 space-y-4">
    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center mx-auto">
        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
    </div>
    <h3 class="text-lg font-bold text-slate-900">Aktivasi Tidak Dapat Diproses</h3>
    <p class="text-xs text-slate-600 leading-relaxed max-w-sm mx-auto">
        <?= e($error ?? 'Tautan aktivasi tidak valid, telah kedaluwarsa, atau akun sudah pernah diaktifkan sebelumnya.') ?>
    </p>
    <div class="pt-2">
        <a href="<?= url('login') ?>" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-semibold hover:bg-slate-800 transition">
            Ke Halaman Masuk
        </a>
    </div>
</div>

