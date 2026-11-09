<?php
$old = session('_flash.old') ?? [];
$errors = session('_flash.errors') ?? [];
?>

<form method="POST" action="<?= url('login') ?>" class="space-y-5">
    <?= csrf_field() ?>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
            Alamat Email
        </label>
        <div class="relative">
            <input id="email" name="email" type="email" autocomplete="email" required
                value="<?= e($old['email'] ?? '') ?>"
                placeholder="nama@email.com"
                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition <?= isset($errors['email']) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' ?>">
        </div>
        <?php if (isset($errors['email'])): ?>
            <p class="mt-1 text-xs text-rose-600"><?= e($errors['email'][0] ?? '') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
            Kata Sandi
        </label>
        <div class="relative">
            <input id="password" name="password" type="password" autocomplete="current-password" required
                placeholder="••••••••"
                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition <?= isset($errors['password']) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' ?>">
        </div>
        <?php if (isset($errors['password'])): ?>
            <p class="mt-1 text-xs text-rose-600"><?= e($errors['password'][0] ?? '') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <button type="submit"
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
            Masuk ke Sistem
        </button>
    </div>
</form>

<div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-400">
    <p>Pengguna baru diaktifkan oleh Admin via tautan khusus.</p>
</div>

