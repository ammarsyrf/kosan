<?php
$old = session('_flash.old') ?? [];
$errors = session('_flash.errors') ?? [];
?>

<!-- Quick Autofill Demo Credentials Bar -->
<div class="mb-6 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl">
    <div class="text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center justify-between">
        <span class="flex items-center gap-1.5">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-blue-600"></i> Pilih Cepat Akun Demo:
        </span>
        <span class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> 1-Klik Masuk
        </span>
    </div>
    <div class="grid grid-cols-3 gap-2">
        <button type="button" onclick="autoLogin('owner@kost.com', 'password123')" class="px-2 py-2 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 hover:border-amber-300 rounded-xl text-xs font-bold text-center transition flex flex-col items-center justify-center gap-0.5 active:scale-95 shadow-sm">
            <span class="flex items-center gap-1">👑 Owner</span>
            <span class="text-[9px] text-amber-700 font-normal">Masuk Eksekutif</span>
        </button>
        <button type="button" onclick="autoLogin('admin@kost.com', 'password123')" class="px-2 py-2 bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 hover:border-blue-300 rounded-xl text-xs font-bold text-center transition flex flex-col items-center justify-center gap-0.5 active:scale-95 shadow-sm">
            <span class="flex items-center gap-1">🛡️ Admin</span>
            <span class="text-[9px] text-blue-700 font-normal">Masuk Operasional</span>
        </button>
        <button type="button" onclick="autoLogin('budi@gmail.com', 'password123')" class="px-2 py-2 bg-teal-50 hover:bg-teal-100 text-teal-900 border border-teal-200 hover:border-teal-300 rounded-xl text-xs font-bold text-center transition flex flex-col items-center justify-center gap-0.5 active:scale-95 shadow-sm">
            <span class="flex items-center gap-1">🏠 Penghuni</span>
            <span class="text-[9px] text-teal-700 font-normal">Masuk Penyewa</span>
        </button>
    </div>
</div>

<form method="POST" action="<?= url('login') ?>" class="space-y-4">
    <?= csrf_field() ?>

    <div>
        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Alamat Email
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="mail" class="w-4 h-4"></i>
            </div>
            <input id="email" name="email" type="email" autocomplete="email" required
                value="<?= e($old['email'] ?? '') ?>"
                placeholder="nama@email.com"
                class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition <?= isset($errors['email']) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' ?>">
        </div>
        <?php if (isset($errors['email'])): ?>
            <p class="mt-1 text-xs text-rose-600"><?= e($errors['email'][0] ?? '') ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Kata Sandi
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="lock" class="w-4 h-4"></i>
            </div>
            <input id="password" name="password" type="password" autocomplete="current-password" required
                placeholder="••••••••"
                class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition <?= isset($errors['password']) ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' ?>">
            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" title="Lihat/Sembunyikan Sandi">
                <i data-lucide="eye" id="eye-icon" class="w-4 h-4"></i>
            </button>
        </div>
        <?php if (isset($errors['password'])): ?>
            <p class="mt-1 text-xs text-rose-600"><?= e($errors['password'][0] ?? '') ?></p>
        <?php endif; ?>
    </div>

    <div class="pt-2">
        <button id="submit-btn" type="submit"
            class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl shadow-md shadow-blue-500/25 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition transform active:scale-98">
            <i data-lucide="log-in" class="w-4 h-4"></i> Masuk ke Sistem
        </button>
    </div>
</form>

<div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-400">
    <p>Akun penghuni baru diaktifkan oleh Pengelola Kost.</p>
</div>

<script>
function autoLogin(email, password) {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submit-btn');
    if (emailInput && passwordInput) {
        emailInput.value = email;
        passwordInput.value = password;
        if (submitBtn) {
            submitBtn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memproses Masuk...</span>';
            submitBtn.classList.add('opacity-90', 'cursor-wait');
        }
        const form = emailInput.closest('form') || document.querySelector('form');
        if (form) {
            form.submit();
        }
    }
}

function fillCreds(email, password) {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    if (emailInput && passwordInput) {
        emailInput.value = email;
        passwordInput.value = password;
        emailInput.focus();
    }
}

function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (pwd) {
        if (pwd.type === 'password') {
            pwd.type = 'text';
            if (icon) icon.setAttribute('data-lucide', 'eye-off');
        } else {
            pwd.type = 'password';
            if (icon) icon.setAttribute('data-lucide', 'eye');
        }
        if (window.lucide) lucide.createIcons();
    }
}
</script>
</script>
