<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full uppercase tracking-wider mb-3">
                    Milestone M1 — MVC Foundation
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    Kost Management System
                </h1>
                <p class="text-slate-600 mt-2 text-sm leading-relaxed max-w-2xl">
                    Fondasi Modular Monolith dengan PHP Native Custom MVC berhasil terpasang dan berjalan secara optimal.
                </p>
            </div>
            <div class="hidden sm:flex w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl items-center justify-center">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
            </div>
        </div>

        <?php if (!empty($role_zone)): ?>
            <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-center space-x-3">
                <i data-lucide="shield-check" class="w-6 h-6 text-amber-600 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Otorisasi Role Berhasil:</p>
                    <p class="text-xs text-amber-800"><?= e($role_zone) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50">
                <div class="flex items-center space-x-3 text-slate-700 mb-2 font-semibold text-sm">
                    <i data-lucide="layers" class="w-4 h-4 text-blue-600"></i>
                    <span>Custom MVC Core</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Router, Request/Response, View Engine, ErrorHandler, dan Logger beroperasi secara independen tanpa framework backend.
                </p>
            </div>

            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50">
                <div class="flex items-center space-x-3 text-slate-700 mb-2 font-semibold text-sm">
                    <i data-lucide="shield" class="w-4 h-4 text-emerald-600"></i>
                    <span>Lapisan Keamanan</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    CSRF protection aktif, secure session cookies (HttpOnly, SameSite=Lax), password_hash, dan login rate limiter.
                </p>
            </div>

            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50">
                <div class="flex items-center space-x-3 text-slate-700 mb-2 font-semibold text-sm">
                    <i data-lucide="database" class="w-4 h-4 text-violet-600"></i>
                    <span>Database Foundation</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    PDO Singleton wrapper dengan prepared statements dan transaksi atomik siap untuk M2 Database Migration.
                </p>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap gap-3">
            <?php if (auth_check()): ?>
                <a href="<?= url('owner/dashboard') ?>" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition">
                    Uji Route Owner
                </a>
                <a href="<?= url('admin/dashboard') ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                    Uji Route Admin
                </a>
                <a href="<?= url('tenant/dashboard') ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                    Uji Route Tenant
                </a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition inline-flex items-center space-x-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Halaman Login</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

