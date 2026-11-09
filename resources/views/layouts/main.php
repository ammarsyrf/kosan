<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name', 'Kost Management')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="<?= url() ?>" class="flex items-center space-x-2 text-blue-600 font-bold text-lg tracking-tight">
                    <i data-lucide="home" class="w-6 h-6"></i>
                    <span><?= e(config('app.name', 'Kost Management')) ?></span>
                </a>

                <?php if (auth_check()): ?>
                    <?php $u = auth_user(); ?>
                    <?php if (in_array(strtolower($u['role'] ?? ''), ['owner', 'admin'], true)): ?>
                        <nav class="hidden md:flex items-center space-x-1">
                            <a href="<?= url('admin/rooms') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition">
                                Kamar
                            </a>
                            <a href="<?= url('admin/facilities') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition">
                                Fasilitas
                            </a>
                            <a href="<?= url('admin/tenants') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition">
                                Penghuni
                            </a>
                            <a href="<?= url('bookings') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition">
                                Booking
                            </a>
                            <a href="<?= url('contracts') ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition">
                                Kontrak Sewa
                            </a>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="flex items-center space-x-4">
                <?php if (auth_check()): ?>
                    <?php $user = auth_user(); ?>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-blue-50 text-blue-700 border border-blue-200 uppercase">
                            <?= e($user['role'] ?? 'User') ?>
                        </span>
                        <span class="text-sm font-semibold text-slate-700"><?= e($user['name'] ?? $user['email']) ?></span>
                        <form method="POST" action="<?= url('logout') ?>" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium ml-2 inline-flex items-center">
                                <i data-lucide="log-out" class="w-4 h-4 mr-1"></i> Keluar
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition">
                        <i data-lucide="log-in" class="w-4 h-4 mr-1.5"></i> Masuk
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if ($success = session('_flash.success')): ?>
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                <span><?= e($success) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error = session('_flash.error')): ?>
            <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center space-x-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-4 text-center text-xs text-slate-500">
        &copy; <?= date('Y') ?> <?= e(config('app.name', 'Kost Management System')) ?> — Personal Edition.
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
