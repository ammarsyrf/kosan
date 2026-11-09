<?php
$errors = session('_flash.errors') ?? [];
?>

<div class="space-y-4">
    <div class="text-center pb-2">
        <h3 class="text-lg font-bold text-slate-900">Aktivasi Akun Penghuni</h3>
        <p class="text-xs text-slate-500 mt-1">Buat kata sandi baru untuk mengaktifkan akun dan mengakses portal sewa kos Anda.</p>
    </div>

    <form method="POST" action="<?= url('activate') ?>" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                Kata Sandi Baru <span class="text-rose-500">*</span>
            </label>
            <input type="password" id="password" name="password" required minlength="8"
                placeholder="Minimal 8 karakter"
                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition <?= isset($errors['password']) ? 'border-rose-400' : 'border-slate-300' ?>">
            <?php if (isset($errors['password'])): ?>
                <p class="mt-1 text-xs text-rose-600"><?= e($errors['password'][0]) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                Ulangi Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                placeholder="Ulangi kata sandi baru"
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
        </div>

        <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition shadow-sm">
            Aktifkan Akun & Selesai
        </button>
    </form>
</div>
