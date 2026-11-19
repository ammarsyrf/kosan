<!-- Interactive Room Detail Modal Partial with Multi-Angle Photo Carousel -->
<div id="room-detail-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop with blur -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" onclick="closeRoomDetailModal()"></div>

    <!-- Modal Box: Fixed Max-Height Flex Container (Strictly Viewport Responsive & Centered) -->
    <div class="relative bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-100 max-w-2xl w-full max-h-[88vh] sm:max-h-[85vh] flex flex-col overflow-hidden z-10 m-auto transform transition-all duration-300 scale-95 opacity-0" id="room-detail-modal-box">
        
        <!-- Modal Top Photo Carousel (Responsive Multi-Angle Gallery) -->
        <div class="relative shrink-0 w-full h-40 sm:h-52 md:h-56 overflow-hidden bg-slate-950 select-none group" id="modal-carousel-container">
            <!-- Active Photo Image -->
            <img id="modal-room-photo" src="" alt="Room Photo Angle" class="w-full h-full object-cover transition-opacity duration-300">
            
            <!-- Vignette & Dark Gradients -->
            <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-slate-950/80 to-transparent pointer-events-none"></div>
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent pointer-events-none"></div>

            <!-- Top Left: Badges (Room Number & Status) -->
            <div class="absolute top-3 left-3 sm:top-3.5 sm:left-3.5 z-20 flex items-center gap-2">
                <span id="modal-room-badge" class="bg-slate-950/80 backdrop-blur-md border border-white/20 text-white text-[11px] sm:text-xs font-black px-2.5 sm:px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5">
                    <i data-lucide="key" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span id="modal-room-number-badge">KAMAR 101</span>
                </span>
                <span id="modal-room-status-badge"></span>
            </div>

            <!-- Top Right: Photo Counter & Close Button -->
            <div class="absolute top-3 right-3 sm:top-3.5 sm:right-3.5 z-30 flex items-center gap-2">
                <span id="modal-carousel-counter" class="bg-slate-950/80 backdrop-blur-md border border-white/20 text-white text-[10px] sm:text-[11px] font-bold px-2.5 py-1 rounded-full shadow-lg flex items-center gap-1.5">
                    <i data-lucide="camera" class="w-3 h-3 text-teal-400"></i>
                    <span id="modal-carousel-counter-text">1 / 4</span>
                </span>
                <button type="button" onclick="closeRoomDetailModal()" aria-label="Tutup Dialog" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-slate-950/70 hover:bg-slate-950 text-white flex items-center justify-center transition shadow-lg backdrop-blur-md border border-white/10 hover:scale-105 active:scale-95 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </button>
            </div>

            <!-- Carousel Navigation Chevrons (< and >) -->
            <button type="button" onclick="changeModalPhoto(-1)" aria-label="Foto Sebelumnya" class="absolute left-2.5 sm:left-3.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-slate-950/60 hover:bg-slate-950/90 text-white flex items-center justify-center transition border border-white/20 shadow-lg backdrop-blur-md cursor-pointer hover:scale-110 active:scale-95">
                <i data-lucide="chevron-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </button>
            <button type="button" onclick="changeModalPhoto(1)" aria-label="Foto Berikutnya" class="absolute right-2.5 sm:right-3.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-slate-950/60 hover:bg-slate-950/90 text-white flex items-center justify-center transition border border-white/20 shadow-lg backdrop-blur-md cursor-pointer hover:scale-110 active:scale-95">
                <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </button>

            <!-- Bottom Inside Photo: Angle Title & Dots Pagination -->
            <div class="absolute bottom-2.5 left-3 right-3 sm:bottom-3 sm:left-3.5 sm:right-3.5 z-20 flex items-center justify-between text-white text-xs">
                <div class="flex items-center gap-1.5 bg-slate-900/80 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/15 text-[11px] sm:text-xs font-semibold shadow-md max-w-[70%] truncate">
                    <i data-lucide="eye" class="w-3.5 h-3.5 text-teal-300 shrink-0"></i>
                    <span id="modal-carousel-angle-title" class="truncate">Tampak Utama & Kasur Springbed</span>
                </div>
                <!-- Interactive Dots -->
                <div id="modal-carousel-dots" class="flex items-center gap-1.5 pointer-events-auto"></div>
            </div>
        </div>

        <!-- Angle Selector Quick Pills (1-Click Switch between Angles) -->
        <div class="bg-slate-50 border-b border-slate-100 p-2 sm:px-4 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0" id="modal-carousel-pills"></div>

        <!-- Modal Body Content (Scrollable & Responsive) -->
        <div class="p-4 sm:p-5 space-y-3.5 overflow-y-auto flex-1 min-h-0 overscroll-contain">
            <!-- Header Row -->
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="min-w-0 flex-1">
                    <h3 id="modal-room-title" class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight truncate">Kamar 101</h3>
                    <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                        <span id="modal-room-floor" class="font-semibold text-slate-700">Lantai 1</span>
                        <span>&bull;</span>
                        <span id="modal-room-size" class="font-semibold text-slate-700">3x4 m²</span>
                        <span>&bull;</span>
                        <span class="truncate"><?= e(setting('business_name', 'Kost Zenerie')) ?></span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tarif Sewa</div>
                    <div class="text-lg sm:text-2xl font-black text-blue-600 font-mono tracking-tight whitespace-nowrap">
                        <span id="modal-room-price">Rp 1.500.000</span><span class="text-xs font-semibold text-slate-400">/bln</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="align-left" class="w-3.5 h-3.5 text-blue-600"></i> Deskripsi Kamar
                </h4>
                <p id="modal-room-desc" class="text-xs sm:text-sm text-slate-600 leading-relaxed"></p>
            </div>

            <!-- Full Facilities -->
            <div class="space-y-1.5 pt-1">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i> Fasilitas Lengkap Kamar
                </h4>
                <div id="modal-room-facilities" class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 sm:gap-2"></div>
            </div>

            <!-- Quick Rules & Notes -->
            <div class="bg-blue-50/60 rounded-2xl p-3 sm:p-3.5 border border-blue-100/80 text-[11px] text-slate-600 space-y-1">
                <div class="font-bold text-slate-800 flex items-center gap-1 mb-1">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-blue-600"></i> Informasi Sewa:
                </div>
                <div class="flex items-center gap-1.5 text-slate-600">
                    <i data-lucide="check-circle-2" class="w-3 h-3 text-emerald-600 shrink-0"></i>
                    <span>Sudah termasuk fasilitas kamar lengkap, WiFi fiber, dan air bersih.</span>
                </div>
                <div class="flex items-center gap-1.5 text-slate-600">
                    <i data-lucide="shield-check" class="w-3 h-3 text-blue-600 shrink-0"></i>
                    <span>Akses masuk gerbang 24 jam dengan sistem keamanan & CCTV.</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer Actions (Pinned at Bottom, Never Cut Off) -->
        <div class="shrink-0 p-3 sm:p-4 bg-slate-50 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
            <a id="modal-room-fullpage-link" href="#" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-blue-600 hover:border-blue-300 text-xs font-bold transition">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Detail Lengkap</span>
            </a>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="button" onclick="closeRoomDetailModal()" class="w-1/3 sm:w-auto px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-bold transition">
                    Tutup
                </button>
                <a id="modal-room-book-btn" href="#" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-teal-500 hover:opacity-95 text-white text-xs font-black shadow-md shadow-blue-500/20 transition cursor-pointer">
                    <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    <span id="modal-room-book-label">Pesan Unit</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// State for current modal multi-angle gallery
let currentModalPhotos = [];
let currentModalPhotoIndex = 0;

(function() {
    function ensureModalInBody() {
        const modal = document.getElementById('room-detail-modal');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureModalInBody);
    } else {
        ensureModalInBody();
    }

    // Touch Swipe Gesture on Carousel
    const container = document.getElementById('modal-carousel-container');
    if (container) {
        let touchStartX = 0;
        let touchEndX = 0;
        container.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        container.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchEndX < touchStartX - 40) {
                changeModalPhoto(1);
            } else if (touchEndX > touchStartX + 40) {
                changeModalPhoto(-1);
            }
        }, { passive: true });
    }
})();

function renderModalPhoto(index) {
    if (!currentModalPhotos || currentModalPhotos.length === 0) return;
    
    // Bounds wrap
    if (index < 0) index = currentModalPhotos.length - 1;
    if (index >= currentModalPhotos.length) index = 0;
    currentModalPhotoIndex = index;

    const cur = currentModalPhotos[currentModalPhotoIndex];
    const photoEl = document.getElementById('modal-room-photo');
    const angleTitleEl = document.getElementById('modal-carousel-angle-title');
    const counterTextEl = document.getElementById('modal-carousel-counter-text');

    if (photoEl && cur) {
        photoEl.style.opacity = '0.35';
        setTimeout(() => {
            photoEl.src = cur.url;
            photoEl.style.opacity = '1';
        }, 80);
    }

    if (angleTitleEl && cur) {
        angleTitleEl.textContent = cur.title || 'Sudut Kamar';
    }

    if (counterTextEl) {
        counterTextEl.textContent = `${currentModalPhotoIndex + 1} / ${currentModalPhotos.length}`;
    }

    // Update Dots
    const dotsContainer = document.getElementById('modal-carousel-dots');
    if (dotsContainer) {
        dotsContainer.innerHTML = '';
        currentModalPhotos.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('aria-label', `Lihat Sudut ${i + 1}`);
            dot.className = (i === currentModalPhotoIndex)
                ? 'w-6 h-2 rounded-full bg-teal-400 transition-all duration-300 shadow-sm cursor-pointer'
                : 'w-2 h-2 rounded-full bg-white/60 hover:bg-white transition-all duration-200 cursor-pointer';
            dot.onclick = () => renderModalPhoto(i);
            dotsContainer.appendChild(dot);
        });
    }

    // Update Pills
    const pillsContainer = document.getElementById('modal-carousel-pills');
    if (pillsContainer) {
        pillsContainer.innerHTML = '';
        currentModalPhotos.forEach((item, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            const isActive = (i === currentModalPhotoIndex);
            btn.className = isActive
                ? 'px-3 py-1.5 rounded-xl text-xs font-black bg-blue-600 text-white shadow-xs flex items-center gap-1.5 whitespace-nowrap transition cursor-pointer'
                : 'px-3 py-1.5 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-300 flex items-center gap-1.5 whitespace-nowrap transition cursor-pointer';
            
            const iconName = item.icon || (i === 0 ? 'bed' : (i === 1 ? 'laptop' : (i === 2 ? 'bath' : 'door-closed')));
            btn.innerHTML = `<i data-lucide="${iconName}" class="w-3.5 h-3.5"></i><span>${item.tag || item.title}</span>`;
            btn.onclick = () => renderModalPhoto(i);
            pillsContainer.appendChild(btn);
        });
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

function changeModalPhoto(direction) {
    renderModalPhoto(currentModalPhotoIndex + direction);
}

function openRoomDetailModal(id) {
    const modal = document.getElementById('room-detail-modal');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    const data = window.roomsData && window.roomsData[id];
    if (!data) return;

    // Load multi-angle photos or fallback
    if (Array.isArray(data.photos) && data.photos.length > 0) {
        currentModalPhotos = data.photos;
    } else {
        currentModalPhotos = [
            {
                url: data.photo_url,
                title: 'Tampak Utama & Kasur',
                tag: 'Kasur & Utama',
                icon: 'bed'
            }
        ];
    }

    // Preload gallery images for zero-lag switching
    currentModalPhotos.forEach(p => {
        if (p.url) {
            const img = new Image();
            img.src = p.url;
        }
    });

    renderModalPhoto(0);
    
    const numBadge = document.getElementById('modal-room-number-badge');
    if (numBadge) numBadge.textContent = 'KAMAR ' + data.room_number;
    
    const floorEl = document.getElementById('modal-room-floor');
    if (floorEl) floorEl.textContent = 'Lantai ' + data.floor;
    
    const sizeEl = document.getElementById('modal-room-size');
    if (sizeEl) sizeEl.textContent = data.size;
    
    const titleEl = document.getElementById('modal-room-title');
    if (titleEl) titleEl.textContent = data.name;
    
    const priceEl = document.getElementById('modal-room-price');
    if (priceEl) priceEl.textContent = data.price_formatted;
    
    const descEl = document.getElementById('modal-room-desc');
    if (descEl) descEl.textContent = data.description;
    
    const fullPageLink = document.getElementById('modal-room-fullpage-link');
    if (fullPageLink) fullPageLink.href = data.detail_url;

    // Status Badge
    const statusEl = document.getElementById('modal-room-status-badge');
    if (statusEl) {
        if (data.is_available) {
            statusEl.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/90 text-white backdrop-blur-md border border-emerald-300';
            statusEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Tersedia';
        } else {
            statusEl.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-900/80 text-slate-300 backdrop-blur-md border border-white/20';
            statusEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Disewa';
        }
    }

    // Facilities
    const facContainer = document.getElementById('modal-room-facilities');
    if (facContainer) {
        facContainer.innerHTML = '';
        (data.facilities || []).forEach(fac => {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-1.5 p-1.5 sm:p-2 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] sm:text-xs font-semibold text-slate-800';
            item.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i><span class="truncate">' + fac + '</span>';
            facContainer.appendChild(item);
        });
    }

    // Book button
    const bookBtn = document.getElementById('modal-room-book-btn');
    const bookLabel = document.getElementById('modal-room-book-label');
    if (bookBtn && bookLabel) {
        if (data.is_available) {
            bookBtn.href = data.booking_url;
            bookBtn.className = 'flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-teal-500 hover:opacity-95 text-white text-xs font-black shadow-md shadow-blue-500/20 transition cursor-pointer';
            bookBtn.removeAttribute('disabled');
            bookLabel.textContent = data.is_admin ? 'Buat Booking (Admin)' : 'Pesan Unit via WA';
            if (!data.is_admin) bookBtn.target = '_blank';
            else bookBtn.removeAttribute('target');
        } else {
            bookBtn.href = 'javascript:void(0)';
            bookBtn.className = 'flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 text-slate-400 text-xs font-bold cursor-not-allowed';
            bookLabel.textContent = 'Unit Sedang Terisi';
            bookBtn.removeAttribute('target');
        }
    }

    const box = document.getElementById('room-detail-modal-box');
    if (modal && box) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

function closeRoomDetailModal() {
    const modal = document.getElementById('room-detail-modal');
    const box = document.getElementById('room-detail-modal-box');
    if (!modal || !box) return;

    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

document.addEventListener('keydown', (e) => {
    const modal = document.getElementById('room-detail-modal');
    if (!modal || modal.classList.contains('hidden')) return;

    if (e.key === 'Escape') closeRoomDetailModal();
    if (e.key === 'ArrowLeft') changeModalPhoto(-1);
    if (e.key === 'ArrowRight') changeModalPhoto(1);
});
</script>
