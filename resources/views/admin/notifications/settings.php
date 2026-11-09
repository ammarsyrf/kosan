<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Pengaturan WhatsApp & Jadwal Pengingat</h1>
        <p class="text-slate-500 text-sm">Konfigurasi integrasi Fonnte WhatsApp Gateway dan jadwal otomatisasi reminder tagihan.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/notifications') ?>" class="inline-flex items-center gap-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium px-4 py-2.5 rounded-lg text-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Log
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200">
                <h3 class="font-bold text-slate-800 text-sm">Jadwal Pengingat Pembayaran (Reminder Schedule)</h3>
                <p class="text-xs text-slate-500">Pilih interval pengingat tagihan yang aktif dijalankan oleh cron job harian.</p>
            </div>
            <form method="POST" action="<?= url('admin/notifications/settings') ?>" class="p-5 space-y-4">
                <?= csrf_field() ?>

                <div class="space-y-3">
                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                        <input type="checkbox" name="reminder_h7_active" value="1" <?= ($settings['reminder_h7_active'] ?? '1') === '1' ? 'checked' : '' ?> class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-semibold text-sm text-slate-800">Pengingat H-7 (7 Hari Sebelum Jatuh Tempo)</div>
                            <div class="text-xs text-slate-500">Kirim pemberitahuan awal tagihan periode sewa yang akan datang.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                        <input type="checkbox" name="reminder_h3_active" value="1" <?= ($settings['reminder_h3_active'] ?? '1') === '1' ? 'checked' : '' ?> class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-semibold text-sm text-slate-800">Pengingat H-3 (3 Hari Sebelum Jatuh Tempo)</div>
                            <div class="text-xs text-slate-500">Pengingat berkala untuk mempersiapkan pembayaran sewa.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                        <input type="checkbox" name="reminder_h1_active" value="1" <?= ($settings['reminder_h1_active'] ?? '1') === '1' ? 'checked' : '' ?> class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-semibold text-sm text-slate-800">Pengingat H-1 (1 Hari Sebelum Jatuh Tempo)</div>
                            <div class="text-xs text-slate-500">Peringatan jatuh tempo besok hari.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                        <input type="checkbox" name="reminder_due_active" value="1" <?= ($settings['reminder_due_active'] ?? '1') === '1' ? 'checked' : '' ?> class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-semibold text-sm text-slate-800">Pengingat Hari H (Tanggal Jatuh Tempo)</div>
                            <div class="text-xs text-slate-500">Pemberitahuan bahwa batas akhir pembayaran adalah hari ini.</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                        <input type="checkbox" name="reminder_overdue_active" value="1" <?= ($settings['reminder_overdue_active'] ?? '1') === '1' ? 'checked' : '' ?> class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-semibold text-sm text-slate-800">Peringatan H+1 (Keterlambatan / Overdue)</div>
                            <div class="text-xs text-slate-500">Peringatan tagihan menunggak setelah melewati tanggal jatuh tempo.</div>
                        </div>
                    </label>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Pengaturan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-800 text-sm mb-3">Status WhatsApp Gateway</h3>
            <div class="flex items-center gap-3 mb-4">
                <span class="w-3 h-3 rounded-full <?= $fonnteEnabled ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' ?>"></span>
                <span class="text-sm font-semibold <?= $fonnteEnabled ? 'text-emerald-700' : 'text-slate-500' ?>">
                    <?= $fonnteEnabled ? 'Gateway Aktif (Enabled)' : 'Gateway Non-Aktif' ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mb-3 leading-relaxed">
                Konektivitas dan kredensial Fonnte dikonfigurasikan secara aman melalui berkas lingkungan server (<code>.env</code>) guna mencegah kebocoran API token.
            </p>
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg font-mono text-xs text-slate-600 space-y-1">
                <div>Provider: Fonnte API</div>
                <div>Status: <?= $fonnteEnabled ? 'ENABLED' : 'DISABLED' ?></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-bold text-slate-800 text-sm mb-3">Jadwal Tugas Otomatis (Cron Jobs)</h3>
            <p class="text-xs text-slate-500 mb-3">Jalankan perintah berikut di background scheduler (crontab / Task Scheduler):</p>
            <div class="space-y-2">
                <div>
                    <span class="text-xs font-semibold text-slate-700">1. Evaluasi Pengingat Harian (08:00 WIB):</span>
                    <pre class="bg-slate-900 text-slate-200 p-2.5 rounded-lg text-xs font-mono overflow-x-auto mt-1">php cron/payment-reminders.php</pre>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-700">2. Auto Retry Pesan Gagal (Setiap 30 Menit):</span>
                    <pre class="bg-slate-900 text-slate-200 p-2.5 rounded-lg text-xs font-mono overflow-x-auto mt-1">php cron/notification-retry.php</pre>
                </div>
            </div>
        </div>
    </div>
</div>
