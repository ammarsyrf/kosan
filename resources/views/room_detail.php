<?php
$rNum = (string)$room->room_number;
$floor = (int)($room->floor ?? (str_starts_with($rNum, '2') ? 2 : 1));
$isAvailable = ($room->status ?? 'available') === 'available';
$price = (float)$room->current_price;
$typeName = $room->name;
$size = !empty($room->size) ? $room->size : '3x4 m²';

// Local/uploaded photo with graceful fallback
$photoUrl = $room->photo_url 
    ? url($room->photo_url) 
    : (file_exists(BASE_PATH . "/public/images/rooms/room-{$rNum}.jpg") 
        ? url("images/rooms/room-{$rNum}.jpg") 
        : 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1200&q=80');

// Dynamic badge style based on tier / price
$badgeStyle = 'bg-blue-500/20 text-blue-200 border-blue-400/30';
if (stripos($room->name, 'Executive') !== false || $price >= 2000000) {
    $badgeStyle = 'bg-amber-500/20 text-amber-200 border-amber-400/30';
} elseif (stripos($room->name, 'VIP') !== false || $price >= 1800000) {
    $badgeStyle = 'bg-purple-500/20 text-purple-200 border-purple-400/30';
} elseif (stripos($room->name, 'Deluxe') !== false || $price >= 1650000) {
    $badgeStyle = 'bg-indigo-500/20 text-indigo-200 border-indigo-400/30';
}

// WhatsApp Booking Link
$bizPhone = preg_replace('/[^0-9]/', '', (string)setting('business_phone', '081234567890'));
if (str_starts_with($bizPhone, '0')) $bizPhone = '62' . substr($bizPhone, 1);
$bizName = setting('business_name', 'Kost Management');
$waMsg = "Halo Pengelola {$bizName}, saya tertarik untuk survei / booking Kamar {$rNum} ({$typeName}) seharga {$room->formattedPrice()}/bln. Apakah masih bisa survei?";
$bookingUrl = auth_check() && in_array(strtolower(auth_user()['role'] ?? ''), ['owner', 'admin'], true)
    ? url('admin/bookings/create?room_id=' . $room->id)
    : "https://wa.me/{$bizPhone}?text=" . rawurlencode($waMsg);
$targetAttr = auth_check() && in_array(strtolower(auth_user()['role'] ?? ''), ['owner', 'admin'], true) ? '' : 'target="_blank"';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8 animate-fade-in">
    <!-- Breadcrumbs & Quick Back Navigation -->
    <div class="flex items-center justify-between gap-4">
        <nav class="flex items-center space-x-2 text-xs sm:text-sm font-semibold text-slate-500">
            <a href="<?= url('/') ?>" class="hover:text-blue-600 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
            <a href="<?= url('katalog') ?>" class="hover:text-blue-600 transition">Katalog Kamar</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
            <span class="text-slate-900 font-bold">Kamar <?= e($rNum) ?></span>
        </nav>

        <a href="<?= url('katalog') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:bg-slate-50 text-xs font-bold transition shadow-xs">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Kembali ke Katalog</span>
        </a>
    </div>

    <!-- Main Room Showcase Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Image Showcase & Deep Details (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Big Hero Image with Badges -->
            <div class="relative rounded-3xl overflow-hidden bg-slate-900 shadow-xl aspect-[16/10] sm:aspect-[16/9] w-full border border-slate-200/80">
                <img src="<?= e($photoUrl) ?>" 
                     alt="Kamar <?= e($rNum) ?> - <?= e($typeName) ?>" 
                     class="w-full h-full object-cover object-center">

                <!-- Gradients -->
                <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-slate-950/80 to-transparent pointer-events-none"></div>
                <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent pointer-events-none"></div>

                <!-- Floating Top Badges -->
                <div class="absolute top-4 left-4 flex items-center gap-2">
                    <span class="bg-slate-950/80 backdrop-blur-md border border-white/20 text-white text-xs font-black px-3.5 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                        <i data-lucide="key" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>KAMAR <?= e($rNum) ?></span>
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider backdrop-blur-md border <?= $badgeStyle ?>">
                        <?= e($typeName) ?>
                    </span>
                </div>

                <div class="absolute top-4 right-4">
                    <?php if ($isAvailable): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-500/90 backdrop-blur-md text-white border border-emerald-300 shadow-lg">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            Tersedia
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-900/90 backdrop-blur-md text-slate-300 border border-white/20 shadow-lg">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            Sedang Disewa
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Bottom Floating Specs Tag -->
                <div class="absolute bottom-4 left-4 right-4 flex flex-wrap items-center justify-between gap-2 text-white">
                    <div class="flex items-center gap-3 text-xs sm:text-sm font-semibold">
                        <span class="bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-xl border border-white/10 flex items-center gap-1.5">
                            <i data-lucide="building" class="w-4 h-4 text-slate-300"></i>
                            Lantai <?= $floor ?>
                        </span>
                        <span class="bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-xl border border-white/10 flex items-center gap-1.5">
                            <i data-lucide="maximize-2" class="w-4 h-4 text-slate-300"></i>
                            Luas <?= e($size) ?>
                        </span>
                    </div>
                    <span class="text-xs text-slate-300 font-medium">Foto Asli Unit Siap Huni</span>
                </div>
            </div>

            <!-- Room Identity & Title Header -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Kamar <?= e($rNum) ?> — <?= e($typeName) ?>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-blue-600 shrink-0"></i>
                            <span><?= e(setting('business_address', 'Jl. Kost Nyaman No. 10')) ?> &bull; Lantai <?= $floor ?></span>
                        </p>
                    </div>
                    <div class="text-left sm:text-right shrink-0">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tarif Sewa Bulanan</div>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight text-blue-600">
                            <?= $room->formattedPrice() ?><span class="text-xs font-semibold text-slate-400">/bln</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                        <span>Deskripsi Kamar</span>
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        <?= e($room->description ?: 'Kamar eksklusif siap huni dengan ventilasi alami yang optimal, suasana tenang dan asri, furnitur lengkap serta keamanan terjamin. Cocok untuk mahasiswa maupun profesional muda yang mendambakan privasi dan kenyamanan tinggal.') ?>
                    </p>
                </div>
            </div>

            <!-- Complete Facilities Grid -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i>
                        <span>Fasilitas Lengkap Kamar & Kos</span>
                    </h3>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                        <?= !empty($room->facilities) ? count($room->facilities) : 8 ?> Fasilitas Tersedia
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <?php if (!empty($room->facilities)): ?>
                        <?php foreach ($room->facilities as $fac): ?>
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200/70 hover:bg-blue-50/50 hover:border-blue-200 transition">
                                <div class="w-9 h-9 rounded-xl bg-white text-blue-600 flex items-center justify-center shadow-xs border border-slate-200/60 shrink-0">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900"><?= e($fac['name']) ?></h4>
                                    <p class="text-[11px] text-slate-500 capitalize"><?= e($fac['category'] ?? 'Fasilitas Kamar') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Standard Fallback Facilities -->
                        <?php 
                            $defaults = [
                                ['name' => 'Air Conditioner (AC)', 'cat' => 'Pendingin Ruangan'],
                                ['name' => 'Kamar Mandi Dalam', 'cat' => 'Sanitasi & Mandi'],
                                ['name' => 'Kasur Springbed Berkualitas', 'cat' => 'Furnitur Kamar'],
                                ['name' => 'Lemari Pakaian & Cermin', 'cat' => 'Penyimpanan'],
                                ['name' => 'Meja & Kursi Kerja / Belajar', 'cat' => 'Area Produktif'],
                                ['name' => 'Internet Fiber 100 Mbps (WiFi)', 'cat' => 'Konektivitas Cepat'],
                                ['name' => 'Water Heater (Air Hangat)', 'cat' => 'Pemanas Air'],
                                ['name' => 'Akses Kunci & Gerbang 24 Jam', 'cat' => 'Keamanan Penghuni'],
                            ];
                            foreach ($defaults as $df):
                        ?>
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200/70">
                                <div class="w-9 h-9 rounded-xl bg-white text-emerald-500 flex items-center justify-center shadow-xs border border-slate-200/60 shrink-0">
                                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900"><?= e($df['name']) ?></h4>
                                    <p class="text-[11px] text-slate-500"><?= e($df['cat']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- House Rules & Notes -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-4">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i data-lucide="shield-check" class="w-5 h-5 text-indigo-600"></i>
                    <span>Ketentuan Sewa & Tata Tertib</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-600">
                    <div class="flex items-start gap-2.5">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-800 block mb-0.5">Akses Masuk & Jam Malam</strong>
                            Gerbang kos menggunakan akses kartu/kunci mandiri 24 jam. Tamu berkunjung maksimal pukul 22.00 WIB.
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i data-lucide="zap" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-800 block mb-0.5">Listrik & Utilitas</strong>
                            Air bersih dan WiFi fiber sudah gratis (termasuk tarif sewa). Pemakaian listrik sesuai ketentuan pengelola kos.
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i data-lucide="calendar" class="w-4 h-4 text-teal-600 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-800 block mb-0.5">Periode Sewa</strong>
                            Minimal kontrak sewa 1 bulan dengan sistem pembayaran otomatis diawal periode sewa.
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i data-lucide="shield" class="w-4 h-4 text-purple-600 shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="text-slate-800 block mb-0.5">Kebersihan & Kerapian</strong>
                            Dapur bersama dan fasilitas umum dibersihkan setiap hari oleh staf kebersihan internal kos.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Sticky Booking & Action Card (4 cols) -->
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
            <!-- Booking Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xl space-y-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tarif Sewa</span>
                        <?php if ($isAvailable): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Siap Huni
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                Terisi
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1 text-blue-600">
                        <?= $room->formattedPrice() ?>
                        <span class="text-xs font-semibold text-slate-400">/ bulan</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Sudah termasuk fasilitas kamar lengkap, WiFi fiber, dan air bersih.</p>
                </div>

                <!-- Fast Summary Specs -->
                <div class="space-y-2.5 py-3 border-y border-slate-100 text-xs text-slate-700">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 flex items-center gap-1.5">
                            <i data-lucide="door-closed" class="w-4 h-4 text-slate-400"></i> No. Kamar
                        </span>
                        <strong class="font-bold text-slate-900">Kamar <?= e($rNum) ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-4 h-4 text-slate-400"></i> Posisi Lantai
                        </span>
                        <strong class="font-bold text-slate-900">Lantai <?= $floor ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 flex items-center gap-1.5">
                            <i data-lucide="maximize" class="w-4 h-4 text-slate-400"></i> Dimensi Luas
                        </span>
                        <strong class="font-bold text-slate-900"><?= e($size) ?></strong>
                    </div>
                </div>

                <!-- Call to Action Buttons -->
                <div class="space-y-3">
                    <?php if ($isAvailable): ?>
                        <a href="<?= e($bookingUrl) ?>" <?= $targetAttr ?>
                           class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white font-black text-sm shadow-lg shadow-blue-500/25 hover:shadow-xl transition transform active:scale-98">
                            <i data-lucide="calendar-check" class="w-4 h-4"></i>
                            <span><?= auth_check() && in_array(strtolower(auth_user()['role'] ?? ''), ['owner', 'admin'], true) ? 'Buat Booking Resmi (Admin)' : 'Booking / Tanya via WhatsApp' ?></span>
                        </a>
                    <?php else: ?>
                        <button type="button" disabled
                                class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-slate-100 text-slate-400 font-bold text-xs border border-slate-200 cursor-not-allowed">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            <span>Unit Sedang Terisi Penuh</span>
                        </button>
                    <?php endif; ?>

                    <a href="https://wa.me/<?= e($bizPhone) ?>?text=Halo%20Pengelola%20Kost,%20saya%20ingin%20jadwal%20survei%20lokasi%20untuk%20Kamar%20<?= e($rNum) ?>." target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs border border-emerald-200 transition">
                        <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>Jadwalkan Survei Lokasi</span>
                    </a>
                </div>

                <!-- Management Security Assurance -->
                <div class="pt-2 text-center">
                    <p class="text-[11px] text-slate-400 flex items-center justify-center gap-1">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                        <span>Tanpa Biaya Admin &bull; Transparan &bull; Terverifikasi</span>
                    </p>
                </div>
            </div>

            <!-- Management Contact Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-3 text-xs">
                <h4 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="building-2" class="w-4 h-4 text-blue-600"></i>
                    <span>Informasi Pengelola Kos</span>
                </h4>
                <div class="space-y-2 text-slate-600">
                    <div class="flex items-center gap-2">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>WhatsApp: <?= e(setting('business_phone', '0812-3456-7890')) ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Layanan Konsultasi: 08.00 - 20.00 WIB</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations: Unit Kamar Lainnya -->
    <?php if (!empty($recommendations)): ?>
        <section class="pt-8 border-t border-slate-200/80 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Pilihan Kamar Lain yang Tersedia
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Unit alternatif siap huni dengan kenyamanan setara.</p>
                </div>
                <a href="<?= url('katalog') ?>" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <?php foreach ($recommendations as $rec): ?>
                    <?php
                        $recNum = (string)$rec->room_number;
                        $recFloor = (int)($rec->floor ?? (str_starts_with($recNum, '2') ? 2 : 1));
                        $recPhoto = $rec->photo_url 
                            ? url($rec->photo_url) 
                            : (file_exists(BASE_PATH . "/public/images/rooms/room-{$recNum}.jpg") 
                                ? url("images/rooms/room-{$recNum}.jpg") 
                                : 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=600&q=80');
                    ?>
                    <a href="<?= url('katalog/' . $rec->id) ?>" class="group bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-blue-400 transition-all duration-300 overflow-hidden flex flex-col justify-between">
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                            <img src="<?= e($recPhoto) ?>" alt="Kamar <?= e($recNum) ?>" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                            <div class="absolute top-2.5 left-2.5 bg-slate-950/75 backdrop-blur-md text-white text-[10px] font-black px-2.5 py-0.5 rounded-full">
                                KAMAR <?= e($recNum) ?>
                            </div>
                            <div class="absolute top-2.5 right-2.5 bg-emerald-500 text-white text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full">
                                Tersedia
                            </div>
                        </div>
                        <div class="p-4 space-y-2">
                            <h4 class="font-bold text-slate-900 text-xs sm:text-sm group-hover:text-blue-600 transition-colors">
                                <?= e($rec->name) ?>
                            </h4>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                                <span class="font-black text-slate-900 font-mono text-sm text-blue-600"><?= $rec->formattedPrice() ?></span>
                                <span class="text-[11px] font-bold text-slate-500">Lantai <?= $recFloor ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
