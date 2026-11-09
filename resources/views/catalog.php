<?php
/** @var array $rooms */
/** @var array $facilities */
/** @var array $stats */
/** @var array|null $user */
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-2">
                <a href="<?= url() ?>" class="hover:text-blue-600 transition flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    <span>Beranda</span>
                </a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-400"></i>
                <span class="text-slate-800 font-bold">Katalog Kamar</span>
            </nav>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="door-closed" class="w-3.5 h-3.5"></i>
                    Katalog Lengkap
                </span>
                <span class="text-xs text-slate-400">Real-Time Inventory</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1.5">
                Pilihan Unit Kamar Siap Huni
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                Jelajahi seluruh kamar kost eksklusif dengan pencahayaan alami, interior berperabot lengkap, dan transparansi tarif tanpa biaya tersembunyi.
            </p>
        </div>

        <!-- Quick Stats Pills -->
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 bg-white p-2.5 rounded-2xl border border-slate-200/90 shadow-xs text-xs">
            <div class="px-3 py-1.5 bg-slate-50 rounded-xl text-center">
                <div class="text-[10px] uppercase font-bold text-slate-400 leading-none">Total Unit</div>
                <div class="text-base font-black text-slate-900 mt-0.5"><?= $stats['total_rooms'] ?></div>
            </div>
            <div class="w-px h-8 bg-slate-200 hidden sm:block"></div>
            <div class="px-3 py-1.5 bg-emerald-50 rounded-xl text-center">
                <div class="text-[10px] uppercase font-bold text-emerald-600 leading-none">Tersedia</div>
                <div class="text-base font-black text-emerald-700 mt-0.5"><?= $stats['available'] ?></div>
            </div>
            <div class="w-px h-8 bg-slate-200 hidden sm:block"></div>
            <div class="px-3 py-1.5 bg-blue-50 rounded-xl text-center">
                <div class="text-[10px] uppercase font-bold text-blue-600 leading-none">Tarif Mulai</div>
                <div class="text-xs sm:text-sm font-black text-blue-800 mt-0.5 font-mono">
                    Rp <?= number_format($stats['min_price'], 0, ',', '.') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH CONTROL BAR -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 sm:p-6 space-y-4">
        <!-- Row 1: Search, Floor, Status, Price, Sort -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- Search Text -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" id="filter-search" placeholder="Cari No / Nama Kamar..." 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition shadow-xs">
            </div>

            <!-- Floor Filter -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="building" class="w-4 h-4"></i>
                </div>
                <select id="filter-floor" class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-8 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                    <option value="all">Semua Lantai</option>
                    <option value="1">Lantai 1</option>
                    <option value="2">Lantai 2</option>
                    <option value="3">Lantai 3</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
                <select id="filter-status" class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-8 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="available" selected>Hanya Tersedia (Available)</option>
                    <option value="occupied">Sedang Disewa (Occupied)</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>

            <!-- Price Tier Filter -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="tag" class="w-4 h-4"></i>
                </div>
                <select id="filter-price" class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-8 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                    <option value="all">Semua Rentang Harga</option>
                    <option value="under-1500">< Rp 1.500.000 / bln</option>
                    <option value="1500-1700">Rp 1.500.000 - Rp 1.700.000</option>
                    <option value="above-1700">> Rp 1.700.000 / bln</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>

            <!-- Sort By -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="arrow-up-down" class="w-4 h-4"></i>
                </div>
                <select id="filter-sort" class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-8 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition shadow-xs cursor-pointer">
                    <option value="number-asc">Nomor Kamar (101 - 204)</option>
                    <option value="price-asc">Harga: Termurah &rarr; Termahal</option>
                    <option value="price-desc">Harga: Termahal &rarr; Termurah</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>
        </div>

        <!-- Row 2: Facility Filter Tags & Reset Action -->
        <div class="pt-3 border-t border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider shrink-0 flex items-center gap-1">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i> Fasilitas:
                </span>
                <?php 
                    $popularFacilities = ['AC', 'KM Dalam', 'WiFi', 'Kasur Springbed', 'Water Heater', 'Smart TV', 'Balkon'];
                    foreach ($popularFacilities as $fac): 
                ?>
                    <button type="button" 
                            data-facility="<?= e(strtolower($fac)) ?>" 
                            onclick="toggleFacilityFilter(this)"
                            class="facility-filter-pill px-3 py-1 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/80 hover:bg-slate-200 transition cursor-pointer whitespace-nowrap shrink-0">
                        <?= e($fac) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center justify-between md:justify-end gap-3 shrink-0">
                <span id="result-counter" class="text-xs font-bold text-slate-600">
                    Menampilkan <strong class="text-slate-900"><?= count($rooms) ?></strong> unit
                </span>
                <button type="button" onclick="resetAllFilters()" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Reset Filter</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ROOM INVENTORY GRID -->
    <div id="catalog-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($rooms as $index => $room): ?>
            <?php
                $rNum = (string)$room->room_number;
                $floor = (int)($room->floor ?? (str_starts_with($rNum, '2') ? 2 : 1));
                $isAvailable = ($room->status ?? 'available') === 'available';
                $price = (float)$room->current_price;
                $typeName = $room->name;
                $size = !empty($room->size) ? $room->size : '3x4 m²';

                $facList = !empty($room->facilities) 
                    ? array_column($room->facilities, 'name') 
                    : ['AC 1/2 PK', 'KM Dalam', 'WiFi Fiber', 'Kasur Springbed'];
                $facSearchString = strtolower(implode(' ', $facList));

                // Badge style
                $badgeStyle = 'bg-blue-500/20 text-blue-200 border-blue-400/30';
                if (stripos($room->name, 'Executive') !== false || $price >= 2000000) {
                    $badgeStyle = 'bg-amber-500/20 text-amber-200 border-amber-400/30';
                } elseif (stripos($room->name, 'VIP') !== false || $price >= 1800000) {
                    $badgeStyle = 'bg-purple-500/20 text-purple-200 border-purple-400/30';
                } elseif (stripos($room->name, 'Deluxe') !== false || $price >= 1650000) {
                    $badgeStyle = 'bg-indigo-500/20 text-indigo-200 border-indigo-400/30';
                }

                // Photo URL
                $photoUrl = $room->photo_url 
                    ? url($room->photo_url) 
                    : (file_exists(BASE_PATH . "/public/images/rooms/room-{$rNum}.jpg") 
                        ? url("images/rooms/room-{$rNum}.jpg") 
                        : 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80');

                // WhatsApp Booking Link
                $bizPhone = preg_replace('/[^0-9]/', '', (string)setting('business_phone', '081234567890'));
                if (str_starts_with($bizPhone, '0')) $bizPhone = '62' . substr($bizPhone, 1);
                $bizName = setting('business_name', 'Kost Management');
                $waMsg = "Halo Pengelola {$bizName}, saya tertarik untuk booking / survei Kamar {$rNum} ({$typeName}) seharga {$room->formattedPrice()}/bln. Apakah unit masih tersedia?";
                $waBookingUrl = "https://wa.me/{$bizPhone}?text=" . rawurlencode($waMsg);
            ?>
            <div class="catalog-card bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-2xl hover:border-blue-400/70 transition-all duration-300 overflow-hidden flex flex-col justify-between group h-full"
                 data-room-number="<?= e($rNum) ?>"
                 data-room-name="<?= e(strtolower($typeName)) ?>"
                 data-floor="<?= $floor ?>"
                 data-status="<?= $isAvailable ? 'available' : 'occupied' ?>"
                 data-price="<?= $price ?>"
                 data-facilities="<?= e($facSearchString) ?>">

                <!-- Image Showcase Header (Strict Uniform 3:2 Aspect Ratio) -->
                <div class="relative aspect-[3/2] w-full overflow-hidden bg-slate-100 skeleton shrink-0">
                    <img src="<?= e($photoUrl) ?>" 
                         alt="Kamar <?= e($rNum) ?> - <?= e($typeName) ?>" 
                         loading="lazy"
                         onload="this.classList.remove('opacity-0'); this.closest('.skeleton')?.classList.remove('skeleton')"
                         class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-108 opacity-0">

                    <!-- Top & Bottom Gradients -->
                    <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-slate-950/70 to-transparent pointer-events-none"></div>
                    <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-transparent pointer-events-none"></div>

                    <!-- Room Badge -->
                    <div class="absolute top-3 left-3 bg-slate-950/75 backdrop-blur-md border border-white/20 text-white text-[11px] font-black px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5">
                        <i data-lucide="key" class="w-3 h-3 text-amber-400"></i>
                        <span>KAMAR <?= e($rNum) ?></span>
                    </div>

                    <!-- Availability Pill -->
                    <div class="absolute top-3 right-3">
                        <?php if ($isAvailable): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500/90 backdrop-blur-md text-white border border-emerald-300 shadow-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                Tersedia
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-900/80 backdrop-blur-md text-slate-300 border border-white/10 shadow-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                Disewa
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Room Type & Floor Tag -->
                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider backdrop-blur-md border <?= $badgeStyle ?>">
                            <?= e($typeName) ?>
                        </span>
                        <span class="text-[11px] font-medium text-slate-200 flex items-center gap-1">
                            <i data-lucide="building" class="w-3 h-3 text-slate-300"></i>
                            Lantai <?= $floor ?>
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-bold text-slate-900 text-sm group-hover:text-blue-600 transition-colors">
                                <?= e($room->name) ?>
                            </h3>
                            <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0 flex items-center gap-1">
                                <i data-lucide="maximize-2" class="w-3 h-3 text-slate-400"></i>
                                <?= e($size) ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed">
                            <?= e($room->description ?: 'Kamar siap huni dengan ventilasi alami, lingkungan tenang, dan fasilitas lengkap.') ?>
                        </p>
                    </div>

                    <!-- Facilities Chips -->
                    <div class="pt-3 border-t border-slate-100">
                        <div class="flex flex-wrap gap-1.5">
                            <?php foreach ($facList as $fac): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-50 text-slate-600 border border-slate-200/80">
                                    <i data-lucide="check" class="w-2.5 h-2.5 text-emerald-500"></i>
                                    <?= e($fac) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Price & Action Footer -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none">Tarif Sewa</div>
                            <div class="text-base sm:text-lg font-black text-slate-900 tracking-tight font-mono mt-0.5">
                                <?= $room->formattedPrice() ?><span class="text-[11px] font-semibold text-slate-400">/bln</span>
                            </div>
                        </div>

                        <div class="shrink-0">
                            <?php if ($isAvailable): ?>
                                <?php if (auth_check() && in_array(strtolower(auth_user()['role'] ?? ''), ['owner', 'admin'], true)): ?>
                                    <a href="<?= url('admin/bookings/create?room_id=' . $room->id) ?>" 
                                       class="inline-flex items-center gap-1.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow-md shadow-blue-500/20 hover:shadow-lg transition transform active:scale-95">
                                        <span>Booking Unit</span>
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= e($waBookingUrl) ?>" target="_blank"
                                       class="inline-flex items-center gap-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow-md shadow-emerald-500/20 hover:shadow-lg transition transform active:scale-95">
                                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                        <span>Pesan via WA</span>
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed">
                                    <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>Terisi</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Empty State (When Filter Matches 0 Rooms) -->
    <div id="catalog-empty-state" class="hidden text-center py-16 bg-white rounded-3xl border border-dashed border-slate-300 p-8 space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto shadow-xs">
            <i data-lucide="search-x" class="w-8 h-8"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900">Tidak Ada Kamar yang Sesuai</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Kriteria filter atau pencarian Anda saat ini tidak menemukan unit kamar yang cocok.
            </p>
        </div>
        <button type="button" onclick="resetAllFilters()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md shadow-blue-500/20 hover:bg-blue-700 transition">
            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
            <span>Reset Semua Filter</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }

    const searchInput = document.getElementById('filter-search');
    const floorSelect = document.getElementById('filter-floor');
    const statusSelect = document.getElementById('filter-status');
    const priceSelect = document.getElementById('filter-price');
    const sortSelect = document.getElementById('filter-sort');
    const counter = document.getElementById('result-counter');
    const emptyState = document.getElementById('catalog-empty-state');
    const catalogGrid = document.getElementById('catalog-grid');

    let activeFacilities = new Set();

    window.toggleFacilityFilter = function(btn) {
        const fac = btn.getAttribute('data-facility');
        if (activeFacilities.has(fac)) {
            activeFacilities.delete(fac);
            btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
            btn.classList.add('bg-slate-100', 'text-slate-600', 'border-slate-200/80');
        } else {
            activeFacilities.add(fac);
            btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
            btn.classList.remove('bg-slate-100', 'text-slate-600', 'border-slate-200/80');
        }
        applyCatalogFilters();
    };

    window.resetAllFilters = function() {
        searchInput.value = '';
        floorSelect.value = 'all';
        statusSelect.value = 'all';
        priceSelect.value = 'all';
        sortSelect.value = 'number-asc';
        activeFacilities.clear();

        document.querySelectorAll('.facility-filter-pill').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
            btn.classList.add('bg-slate-100', 'text-slate-600', 'border-slate-200/80');
        });

        applyCatalogFilters();
    };

    function applyCatalogFilters() {
        const query = searchInput.value.trim().toLowerCase();
        const selectedFloor = floorSelect.value;
        const selectedStatus = statusSelect.value;
        const selectedPrice = priceSelect.value;

        const cards = Array.from(document.querySelectorAll('.catalog-card'));
        let visibleCount = 0;

        cards.forEach(card => {
            const rNum = card.getAttribute('data-room-number') || '';
            const rName = card.getAttribute('data-room-name') || '';
            const floor = card.getAttribute('data-floor') || '';
            const status = card.getAttribute('data-status') || '';
            const price = parseFloat(card.getAttribute('data-price')) || 0;
            const facs = card.getAttribute('data-facilities') || '';

            // Match text search
            let matchSearch = true;
            if (query) {
                matchSearch = rNum.toLowerCase().includes(query) || rName.toLowerCase().includes(query);
            }

            // Match Floor
            let matchFloor = (selectedFloor === 'all' || floor === selectedFloor);

            // Match Status
            let matchStatus = (selectedStatus === 'all' || status === selectedStatus);

            // Match Price
            let matchPrice = true;
            if (selectedPrice === 'under-1500') {
                matchPrice = price < 1500000;
            } else if (selectedPrice === '1500-1700') {
                matchPrice = price >= 1500000 && price <= 1700000;
            } else if (selectedPrice === 'above-1700') {
                matchPrice = price > 1700000;
            }

            // Match Facilities
            let matchFac = true;
            if (activeFacilities.size > 0) {
                for (let f of activeFacilities) {
                    if (!facs.includes(f)) {
                        matchFac = false;
                        break;
                    }
                }
            }

            if (matchSearch && matchFloor && matchStatus && matchPrice && matchFac) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Sorting
        const sortVal = sortSelect.value;
        cards.sort((a, b) => {
            if (sortVal === 'price-asc') {
                return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
            } else if (sortVal === 'price-desc') {
                return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
            } else {
                return (a.getAttribute('data-room-number') || '').localeCompare(b.getAttribute('data-room-number') || '', undefined, { numeric: true });
            }
        });
        cards.forEach(card => catalogGrid.appendChild(card));

        // Update counter & empty state
        counter.innerHTML = `Menampilkan <strong class="text-slate-900">${visibleCount}</strong> unit`;
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    searchInput.addEventListener('input', applyCatalogFilters);
    floorSelect.addEventListener('change', applyCatalogFilters);
    statusSelect.addEventListener('change', applyCatalogFilters);
    priceSelect.addEventListener('change', applyCatalogFilters);
    sortSelect.addEventListener('change', applyCatalogFilters);

    // Initial run
    applyCatalogFilters();
});
</script>

