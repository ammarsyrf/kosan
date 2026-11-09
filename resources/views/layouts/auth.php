<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Autentikasi — Kost Management System') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="flex flex-col justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8 text-slate-800 antialiased">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center mb-2">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                <i data-lucide="home" class="w-7 h-7"></i>
            </div>
        </div>
        <h2 class="text-center text-2xl font-bold tracking-tight text-slate-900">
            <?= e(config('app.name', 'Kost Management')) ?>
        </h2>
        <p class="mt-1 text-center text-sm text-slate-500">
            Sistem Manajemen Kos Personal &bullet; Internal Access Only
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200 rounded-2xl sm:px-10">
            <?php if ($success = session('_flash.success')): ?>
                <div class="mb-4 p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <span><?= e($success) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error = session('_flash.error')): ?>
                <div class="mb-4 p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center space-x-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

