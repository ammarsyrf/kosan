<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                    Manajemen Akses & Keamanan
                </span>
                <span class="text-xs text-slate-400">Multi-Role Control</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Kelola Akun & Pengguna</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Atur akun admin operasional, staf, dan akun seluruh penghuni kos. Reset kata sandi langsung jika lupa password.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= url('admin/tenants/create') ?>" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-bold px-4 py-2.5 rounded-xl text-xs shadow-xs transition">
                <i data-lucide="user-plus" class="w-4 h-4 text-emerald-600"></i>
                <span>+ Daftar Penghuni</span>
            </a>
            <a href="<?= url('admin/users/create') ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md shadow-blue-500/25 transition">
                <i data-lucide="user-check" class="w-4 h-4"></i>
                <span>+ Tambah Admin Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <a href="<?= url('admin/users') ?>" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:border-blue-400 transition flex items-center justify-between group">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Akun</div>
                <div class="text-2xl font-black text-slate-900 mt-1"><?= $stats['total'] ?></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </a>

        <a href="<?= url('admin/users?role=owner') ?>" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:border-indigo-400 transition flex items-center justify-between group">
            <div>
                <div class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider">Owner (Pemilik)</div>
                <div class="text-2xl font-black text-indigo-900 mt-1"><?= $stats['owner'] ?></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                <i data-lucide="crown" class="w-5 h-5"></i>
            </div>
        </a>

        <a href="<?= url('admin/users?role=admin') ?>" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:border-blue-400 transition flex items-center justify-between group">
            <div>
                <div class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Admin Operasional</div>
                <div class="text-2xl font-black text-blue-900 mt-1"><?= $stats['admin'] ?></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                <i data-lucide="shield" class="w-5 h-5"></i>
            </div>
        </a>

        <a href="<?= url('admin/users?role=tenant') ?>" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:border-emerald-400 transition flex items-center justify-between group">
            <div>
                <div class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Penghuni Kos</div>
                <div class="text-2xl font-black text-emerald-900 mt-1"><?= $stats['tenant'] ?></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                <i data-lucide="home" class="w-5 h-5"></i>
            </div>
        </a>
    </div>

    <!-- Filter Tabs & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                <a href="<?= url('admin/users') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $currentFilter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    Semua Akun (<?= $stats['total'] ?>)
                </a>
                <a href="<?= url('admin/users?role=admin') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $currentFilter === 'admin' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    Admin (<?= $stats['admin'] ?>)
                </a>
                <a href="<?= url('admin/users?role=tenant') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $currentFilter === 'tenant' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    Penghuni (<?= $stats['tenant'] ?>)
                </a>
                <a href="<?= url('admin/users?role=owner') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $currentFilter === 'owner' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    Owner (<?= $stats['owner'] ?>)
                </a>
            </div>

            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" id="user-search-input" placeholder="Cari nama / email..."
                    class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
            </div>
        </div>

        <!-- Table Users -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="users-table">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Nama & Email</th>
                        <th class="py-3 px-4">Hak Akses (Role)</th>
                        <th class="py-3 px-4">Kontak HP</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                Belum ada data pengguna yang sesuai dengan filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <?php
                                $role = strtolower($u['role_name'] ?? 'tenant');
                                $roleBadge = 'bg-slate-100 text-slate-700 border-slate-200';
                                if ($role === 'owner') $roleBadge = 'bg-indigo-50 text-indigo-700 border-indigo-200 font-black';
                                elseif ($role === 'admin') $roleBadge = 'bg-blue-50 text-blue-700 border-blue-200 font-bold';
                                elseif ($role === 'tenant') $roleBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold';
                            ?>
                            <tr class="hover:bg-slate-50/80 transition user-row"
                                data-search="<?= e(strtolower($u['name'] . ' ' . $u['email'] . ' ' . ($u['phone'] ?? ''))) ?>">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr <?= $role === 'owner' ? 'from-indigo-600 to-purple-500' : ($role === 'admin' ? 'from-blue-600 to-teal-500' : 'from-emerald-600 to-teal-500') ?> text-white font-bold text-xs flex items-center justify-center shrink-0">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                                <span><?= e($u['name']) ?></span>
                                                <?php if (!empty($u['room_number'])): ?>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-600 border border-slate-200">
                                                        Kamar <?= e($u['room_number']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono"><?= e($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider border <?= $roleBadge ?>">
                                        <?php if ($role === 'owner'): ?>
                                            <i data-lucide="crown" class="w-3 h-3 text-indigo-600"></i> Owner
                                        <?php elseif ($role === 'admin'): ?>
                                            <i data-lucide="shield" class="w-3 h-3 text-blue-600"></i> Admin
                                        <?php else: ?>
                                            <i data-lucide="home" class="w-3 h-3 text-emerald-600"></i> Penghuni
                                        <?php endif; ?>
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-slate-600 font-mono">
                                    <?= e($u['phone'] ?: '-') ?>
                                </td>

                                <td class="py-3.5 px-4">
                                    <?php if ($u['status'] === 'active'): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    <?php elseif ($u['status'] === 'pending'): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Aktivasi
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    <!-- Tombol Reset Password Cepat -->
                                    <button type="button"
                                        onclick="openUserResetModal(<?= $u['id'] ?>, '<?= e(addslashes($u['name'])) ?>', '<?= e(addslashes($u['email'])) ?>', '<?= $role ?>')"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/80 px-2.5 py-1 rounded-lg transition"
                                        title="Ganti / Reset kata sandi pengguna">
                                        <i data-lucide="key" class="w-3.5 h-3.5 text-amber-600"></i>
                                        <span>Reset PW</span>
                                    </button>

                                    <!-- Tombol Edit Akun -->
                                    <a href="<?= url("admin/users/{$u['id']}/edit") ?>"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/80 px-2.5 py-1 rounded-lg transition">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5 text-blue-600"></i>
                                        <span>Ubah</span>
                                    </a>

                                    <!-- Tombol Hapus (Kecuali Akun Sendiri) -->
                                    <?php if ((int)$u['id'] !== (int)session('user.id')): ?>
                                        <form method="POST" action="<?= url("admin/users/{$u['id']}/delete") ?>" class="inline"
                                            onsubmit="return confirm('Hapus akun <?= e(addslashes($u['name'])) ?> (<?= e($u['email']) ?>)? Aksi ini tidak dapat dibatalkan.')">
                                            <?= csrf_field() ?>
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 px-2.5 py-1 rounded-lg transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Interactive Reset Password Modal -->
    <div id="user-reset-modal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 sm:p-7 space-y-5 animate-scale-up">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <i data-lucide="key" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Ganti / Reset Kata Sandi</h3>
                        <p class="text-xs text-slate-400">Atur password baru langsung untuk pengguna</p>
                    </div>
                </div>
                <button type="button" onclick="closeUserResetModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="user-reset-form" method="POST" action="" class="space-y-4">
                <?= csrf_field() ?>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Akun Terpilih</div>
                    <div class="flex items-center justify-between">
                        <div id="modal-user-name" class="font-bold text-slate-900 text-sm"></div>
                        <span id="modal-user-role" class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-200 text-slate-700"></span>
                    </div>
                    <div id="modal-user-email" class="text-xs text-slate-500 font-mono"></div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="modal_new_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="generateRandomUserPassword()" class="text-[11px] font-bold text-blue-600 hover:text-blue-800">
                            Acak Password
                        </button>
                    </div>
                    <input type="text" id="modal_new_password" name="new_password" required minlength="6"
                        placeholder="Contoh: kosan2026"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                    <p class="mt-1 text-[11px] text-slate-400">Minimal 6 karakter. Akun langsung bisa masuk dengan password ini.</p>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeUserResetModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs font-bold shadow-md shadow-amber-500/20 hover:shadow-lg transition">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Filter search client-side
const searchInput = document.getElementById('user-search-input');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.user-row').forEach(row => {
            const text = row.getAttribute('data-search') || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

function openUserResetModal(userId, name, email, role) {
    const modal = document.getElementById('user-reset-modal');
    const form = document.getElementById('user-reset-form');
    const nameEl = document.getElementById('modal-user-name');
    const emailEl = document.getElementById('modal-user-email');
    const roleEl = document.getElementById('modal-user-role');
    const input = document.getElementById('modal_new_password');

    form.action = '<?= url("admin/users") ?>/' + userId + '/reset-password';
    nameEl.textContent = name;
    emailEl.textContent = email;
    roleEl.textContent = role.toUpperCase();
    input.value = '';
    modal.classList.remove('hidden');
    input.focus();
}

function closeUserResetModal() {
    document.getElementById('user-reset-modal').classList.add('hidden');
}

function generateRandomUserPassword() {
    const words = ['kosan', 'admin', 'sukses', 'aman', 'lancar', 'berkah'];
    const word = words[Math.floor(Math.random() * words.length)];
    const num = Math.floor(1000 + Math.random() * 9000);
    document.getElementById('modal_new_password').value = word + num;
}
</script>
