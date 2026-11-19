<!-- Interactive Room Detail Modal Partial -->
<div id="room-detail-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" onclick="closeRoomDetailModal()"></div>

    <!-- Modal Content -->
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-2xl w-full overflow-hidden z-10 my-auto transform transition-all duration-300 scale-95 opacity-0" id="room-detail-modal-box">
        <!-- Close Button (Absolute Top Right) -->
        <button type="button" onclick="closeRoomDetailModal()" aria-label="Tutup Dialog" class="absolute top-3.5 right-3.5 z-20 w-9 h-9 rounded-full bg-slate-950/70 hover:bg-slate-950 text-white flex items-center justify-center transition shadow-lg backdrop-blur-md">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <!-- Modal Top Photo Showcase -->
        <div class="relative aspect-[16/10] sm:aspect-[16/9] w-full overflow-hidden bg-slate-900">
            <img id="modal-room-photo" src="" alt="Room Photo" class="w-full h-full object-cover">
            <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-slate-950/70 to-transparent pointer-events-none"></div>
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950/85 via-slate-950/30 to-transparent pointer-events-none"></div>

            <!-- Top Badges -->
            <div class="absolute top-3.5 left-3.5 flex items-center gap-2">
                <span id="modal-room-badge" class="bg-slate-950/80 backdrop-blur-md border border-white/20 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5">
                    <i data-lucide="key" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span id="modal-room-number-badge">KAMAR 101</span>
                </span>
                <span id="modal-room-status-badge"></span>
            </div>

            <!-- Bottom Badges inside photo -->
            <div class="absolute bottom-3 left-3.5 right-3.5 flex items-center justify-between text-white text-xs">
                <div class="flex items-center gap-2">
                    <span id="modal-room-floor" class="bg-slate-900/80 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10 font-medium">Lantai 1</span>
                    <span id="modal-room-size" class="bg-slate-900/80 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10 font-medium">3x4 m²</span>
                </div>
                <span class="text-[11px] text-slate-300">Foto Asli Unit Siap Huni</span>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-7 space-y-5 max-h-[60vh] overflow-y-auto">
            <!-- Header Row -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                <div>
                    <h3 id="modal-room-title" class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kamar 101</h3>
                    <p class="text-xs text-slate-500 mt-0.5"><?= e(setting('business_name', 'Kost Management')) ?> &bull; Unit Bersih & Terawat</p>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tarif Sewa</div>
                    <div class="text-xl sm:text-2xl font-black text-blue-600 font-mono tracking-tight">
                        <span id="modal-room-price">Rp 1.500.000</span><span class="text-xs font-semibold text-slate-400">/bln</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="align-left" class="w-3.5 h-3.5 text-blue-600"></i> Deskripsi Kamar
                </h4>
                <p id="modal-room-desc" class="text-xs sm:text-sm text-slate-600 leading-relaxed"></p>
            </div>

            <!-- Full Facilities -->
            <div class="space-y-2 pt-2">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i> Fasilitas Lengkap Kamar
                </h4>
                <div id="modal-room-facilities" class="grid grid-cols-2 sm:grid-cols-3 gap-2"></div>
            </div>

            <!-- Quick Rules & Notes -->
            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 text-[11px] text-slate-600 space-y-1">
                <div class="font-bold text-slate-800 flex items-center gap-1 mb-1">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-blue-600"></i> Informasi Sewa:
                </div>
                <div>&bull; Sudah termasuk fasilitas kamar lengkap, WiFi fiber, dan air bersih.</div>
                <div>&bull; Akses masuk gerbang 24 jam dengan sistem keamanan & CCTV.</div>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="p-4 sm:p-5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <a id="modal-room-fullpage-link" href="#" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:text-blue-600 hover:bg-white text-xs font-bold transition">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Buka Halaman Lengkap</span>
            </a>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button type="button" onclick="closeRoomDetailModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-bold transition">
                    Tutup
                </button>
                <a id="modal-room-book-btn" href="#" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-teal-500 hover:opacity-95 text-white text-xs font-black shadow-md shadow-blue-500/20 transition">
                    <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    <span id="modal-room-book-label">Pesan Unit</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openRoomDetailModal(id) {
    const data = window.roomsData && window.roomsData[id];
    if (!data) return;

    const photoEl = document.getElementById('modal-room-photo');
    if (photoEl) photoEl.src = data.photo_url;
    
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
            item.className = 'flex items-center gap-2 p-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800';
            item.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i><span>' + fac + '</span>';
            facContainer.appendChild(item);
        });
    }

    // Book button
    const bookBtn = document.getElementById('modal-room-book-btn');
    const bookLabel = document.getElementById('modal-room-book-label');
    if (bookBtn && bookLabel) {
        if (data.is_available) {
            bookBtn.href = data.booking_url;
            bookBtn.className = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-teal-500 hover:opacity-95 text-white text-xs font-black shadow-md shadow-blue-500/20 transition cursor-pointer';
            bookBtn.removeAttribute('disabled');
            bookLabel.textContent = data.is_admin ? 'Buat Booking (Admin)' : 'Pesan Unit via WA';
            if (!data.is_admin) bookBtn.target = '_blank';
            else bookBtn.removeAttribute('target');
        } else {
            bookBtn.href = 'javascript:void(0)';
            bookBtn.className = 'w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 text-slate-400 text-xs font-bold cursor-not-allowed';
            bookLabel.textContent = 'Unit Sedang Terisi';
            bookBtn.removeAttribute('target');
        }
    }

    const modal = document.getElementById('room-detail-modal');
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
    if (e.key === 'Escape') closeRoomDetailModal();
});
</script>
