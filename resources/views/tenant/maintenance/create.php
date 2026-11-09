<div class="max-w-2xl mx-auto px-4 sm:px-0 py-4 animate-in fade-in duration-300">
    <!-- Breadcrumb & Top Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="<?= url('tenant/maintenance') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors bg-white hover:bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200/80 shadow-xs mb-3 group">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5"></i>
                <span>Kembali ke Daftar Laporan</span>
            </a>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Layanan Pemeliharaan & Tiket
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Ajukan Laporan Perbaikan</h1>
            <p class="text-slate-500 text-sm mt-1">Sampaikan keluhan fasilitas atau kerusakan kamar agar segera ditangani teknisi kos.</p>
        </div>
    </div>

    <!-- Active Room Badge Banner -->
    <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-blue-500/5 border border-blue-200/80 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm shadow-blue-500/20 shrink-0">
                <i data-lucide="door-closed" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider block">Unit Kamar Terhubung</span>
                <h3 class="text-base font-bold text-slate-900">Kamar <?= e($contract->room_number) ?></h3>
            </div>
        </div>
        <div class="text-right hidden sm:block">
            <span class="text-xs font-medium text-slate-500 block">Status Kontrak</span>
            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-0.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Aktif Terverifikasi
            </span>
        </div>
    </div>

    <!-- Main Maintenance Form Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/80 overflow-hidden">
        <div class="p-6 sm:p-8">
            <form method="POST" action="<?= url('tenant/maintenance') ?>" enctype="multipart/form-data" class="space-y-6" id="form-maintenance">
                <?= csrf_field() ?>

                <!-- Kategori Kerusakan -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Kategori Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="category_id" id="category_id" required class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 py-3 pr-10 text-sm font-medium text-slate-800 shadow-xs focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition">
                            <option value="">-- Pilih Jenis Kerusakan --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat->id ?>">
                                    <?= e($cat->name) ?> - <?= e($cat->description) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Judul Singkat -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Judul Singkat Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl shadow-xs">
                        <input type="text" 
                               name="title" 
                               id="title" 
                               required 
                               placeholder="Contoh: AC kamar tidak dingin / Kran air wastafel patah" 
                               class="w-full rounded-xl border border-slate-300 py-2.5 px-4 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition">
                    </div>
                </div>

                <!-- Deskripsi Detail -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Deskripsi Detail Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4" 
                              required 
                              placeholder="Jelaskan secara rinci kronologi, kondisi kerusakan, atau kapan kendala mulai terjadi agar teknisi dapat menyiapkan peralatan yang tepat..." 
                              class="w-full rounded-xl border border-slate-300 p-3.5 text-sm font-normal text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition"></textarea>
                </div>

                <!-- Tingkat Urgensi / Prioritas -->
                <div>
                    <label for="priority" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Tingkat Urgensi / Prioritas
                    </label>
                    <div class="relative">
                        <select name="priority" id="priority" class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 py-3 pr-10 text-sm font-medium text-slate-800 shadow-xs focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition">
                            <option value="low">🟢 Rendah (Low) — Kerusakan kecil / tidak mendesak</option>
                            <option value="medium" selected>🟡 Sedang (Medium) — Memerlukan perbaikan standar</option>
                            <option value="high">🟠 Tinggi (High) — Mengganggu kenyamanan utama</option>
                            <option value="urgent">🔴 Mendesak (Urgent) — Kondisi darurat (pipa pecah, korsleting, dll)</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Foto Bukti Kerusakan (Camera & File Dropzone) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-slate-800">
                            Foto Bukti Kerusakan <span class="text-xs font-normal text-slate-400">(Opsional tapi disarankan)</span>
                        </label>
                        <span class="text-[11px] text-slate-400 font-medium" id="photo-count-badge">0 Foto Terpilih</span>
                    </div>

                    <!-- Action Bar: Ambil dari Kamera & Pilih File -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <!-- Tombol Buka Kamera (Mobile Direct Camera / Desktop Modal) -->
                        <button type="button" 
                                id="btn-trigger-camera" 
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-dashed border-indigo-300 hover:border-indigo-500 bg-indigo-50/50 hover:bg-indigo-50 text-indigo-700 text-xs sm:text-sm font-bold transition active:scale-95 cursor-pointer">
                            <i data-lucide="camera" class="w-4 h-4 text-indigo-600"></i>
                            <span>Foto dari Kamera</span>
                        </button>

                        <!-- Tombol Pilih Berkas dari Galeri -->
                        <button type="button" 
                                id="btn-trigger-gallery" 
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-dashed border-slate-300 hover:border-blue-500 bg-slate-50/50 hover:bg-blue-50/30 text-slate-700 hover:text-blue-700 text-xs sm:text-sm font-bold transition active:scale-95 cursor-pointer">
                            <i data-lucide="image" class="w-4 h-4 text-slate-500"></i>
                            <span>Pilih dari Galeri</span>
                        </button>
                    </div>

                    <!-- Hidden Inputs for File Selection & Mobile Native Camera -->
                    <input type="file" 
                           id="native-camera-input" 
                           accept="image/*" 
                           capture="environment" 
                           class="hidden">
                    <input type="file" 
                           id="gallery-file-input" 
                           name="attachments[]" 
                           multiple 
                           accept="image/jpeg,image/png,image/webp,application/pdf" 
                           class="hidden">

                    <!-- Drag & Drop Zone Area -->
                    <div id="maint-dropzone" class="border-2 border-dashed border-slate-300 hover:border-blue-500 bg-slate-50/40 rounded-2xl p-5 text-center cursor-pointer transition-all duration-200 group">
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto transition-transform group-hover:scale-110">
                                <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                            </div>
                            <p class="text-xs text-slate-600">
                                Atau seret & lepaskan foto ke sini &bull; Maks. 5 MB per foto (JPG, PNG, WEBP)
                            </p>
                        </div>
                    </div>

                    <!-- Live Photo Gallery Preview Grid -->
                    <div id="preview-grid" class="hidden mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <!-- Dynamically populated previews -->
                    </div>
                </div>

                <!-- Form Footer Actions -->
                <div class="pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                        <span>Laporan Anda akan segera dinotifikasikan ke pengelola kos.</span>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="<?= url('tenant/maintenance') ?>" class="px-5 py-2.5 text-sm font-semibold rounded-xl text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" 
                                id="btn-submit-maint" 
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/35 active:scale-95 transition-all cursor-pointer">
                            <i data-lucide="send" class="w-4 h-4" id="maint-submit-icon"></i>
                            <span id="maint-submit-label">Kirim Laporan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- In-Browser Live Camera Viewfinder Modal -->
<div id="camera-modal" class="hidden fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 text-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-700 relative flex flex-col animate-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="camera" class="w-5 h-5 text-indigo-400"></i>
                <h3 class="text-sm font-bold tracking-wide">Ambil Foto dengan Kamera</h3>
            </div>
            <button type="button" id="btn-close-camera" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Video Viewfinder Frame -->
        <div class="relative bg-black aspect-4/3 flex items-center justify-center overflow-hidden">
            <video id="camera-stream" autoplay playsinline class="w-full h-full object-cover"></video>
            
            <!-- Viewfinder Overlay Reticle -->
            <div class="absolute inset-8 border border-white/25 rounded-xl pointer-events-none flex flex-col justify-between p-2">
                <div class="flex justify-between">
                    <div class="w-4 h-4 border-t-2 border-l-2 border-indigo-400"></div>
                    <div class="w-4 h-4 border-t-2 border-r-2 border-indigo-400"></div>
                </div>
                <div class="flex justify-between">
                    <div class="w-4 h-4 border-b-2 border-l-2 border-indigo-400"></div>
                    <div class="w-4 h-4 border-b-2 border-r-2 border-indigo-400"></div>
                </div>
            </div>

            <!-- Fallback Canvas (Hidden) -->
            <canvas id="camera-canvas" class="hidden"></canvas>
        </div>

        <!-- Camera Controls -->
        <div class="p-4 bg-slate-950/80 flex items-center justify-center gap-4">
            <button type="button" id="btn-snap-photo" class="w-16 h-16 rounded-full bg-white text-slate-900 border-4 border-indigo-500 hover:scale-105 active:scale-95 shadow-lg shadow-indigo-500/50 flex items-center justify-center transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-full bg-slate-900 flex items-center justify-center text-white">
                    <i data-lucide="camera" class="w-6 h-6"></i>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- Interactive Client-side Script for Center, Camera, & Live Gallery -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const galleryInput = document.getElementById('gallery-file-input');
    const nativeCameraInput = document.getElementById('native-camera-input');
    const btnTriggerGallery = document.getElementById('btn-trigger-gallery');
    const btnTriggerCamera = document.getElementById('btn-trigger-camera');
    const maintDropzone = document.getElementById('maint-dropzone');
    const previewGrid = document.getElementById('preview-grid');
    const photoCountBadge = document.getElementById('photo-count-badge');

    // Camera Modal Elements
    const cameraModal = document.getElementById('camera-modal');
    const cameraStream = document.getElementById('camera-stream');
    const cameraCanvas = document.getElementById('camera-canvas');
    const btnSnapPhoto = document.getElementById('btn-snap-photo');
    const btnCloseCamera = document.getElementById('btn-close-camera');

    // Accumulated Files State (using DataTransfer)
    const dt = new DataTransfer();
    let videoTrack = null;

    function updatePreviews() {
        previewGrid.innerHTML = '';
        const files = dt.files;

        if (files.length === 0) {
            previewGrid.classList.add('hidden');
            photoCountBadge.textContent = '0 Foto Terpilih';
            return;
        }

        previewGrid.classList.remove('hidden');
        photoCountBadge.textContent = `${files.length} Foto Terpilih`;

        Array.from(files).forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-50 shadow-xs aspect-4/3 flex items-center justify-center';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'w-full h-full object-cover';
                img.alt = file.name;
                const reader = new FileReader();
                reader.onload = e => img.src = e.target.result;
                reader.readAsDataURL(file);
                card.appendChild(img);
            } else {
                card.innerHTML = `
                    <div class="p-3 text-center">
                        <i data-lucide="file-text" class="w-8 h-8 text-slate-400 mx-auto mb-1"></i>
                        <p class="text-[10px] font-bold text-slate-700 truncate max-w-[120px]">${file.name}</p>
                    </div>
                `;
            }

            // Remove Button Overlay
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center opacity-90 hover:opacity-100 hover:scale-110 transition shadow-xs cursor-pointer';
            removeBtn.innerHTML = '&times;';
            removeBtn.title = 'Hapus foto ini';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                removeFileAtIndex(index);
            });

            card.appendChild(removeBtn);
            previewGrid.appendChild(card);
        });

        // Sync with form input
        galleryInput.files = dt.files;

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function addFiles(fileList) {
        if (!fileList) return;
        Array.from(fileList).forEach(file => {
            // Check max 5MB
            if (file.size <= 5 * 1024 * 1024) {
                dt.items.add(file);
            } else {
                alert(`File "${file.name}" melebihi batas ukuran 5MB.`);
            }
        });
        updatePreviews();
    }

    function removeFileAtIndex(idx) {
        const newDt = new DataTransfer();
        Array.from(dt.files).forEach((file, i) => {
            if (i !== idx) newDt.items.add(file);
        });
        // Clear dt and refill
        dt.items.clear();
        Array.from(newDt.files).forEach(f => dt.items.add(f));
        updatePreviews();
    }

    // Trigger Gallery / File Picker
    if (btnTriggerGallery) {
        btnTriggerGallery.addEventListener('click', () => galleryInput.click());
    }
    if (maintDropzone) {
        maintDropzone.addEventListener('click', () => galleryInput.click());
    }

    galleryInput.addEventListener('change', function() {
        addFiles(this.files);
    });

    // Native Camera input for mobile direct capture
    nativeCameraInput.addEventListener('change', function() {
        addFiles(this.files);
    });

    // Drag and Drop
    ['dragenter', 'dragover'].forEach(event => {
        maintDropzone.addEventListener(event, (e) => {
            e.preventDefault();
            maintDropzone.classList.add('border-blue-500', 'bg-blue-50/30');
        });
    });
    ['dragleave', 'drop'].forEach(event => {
        maintDropzone.addEventListener(event, (e) => {
            e.preventDefault();
            maintDropzone.classList.remove('border-blue-500', 'bg-blue-50/30');
        });
    });
    maintDropzone.addEventListener('drop', (e) => {
        if (e.dataTransfer && e.dataTransfer.files) {
            addFiles(e.dataTransfer.files);
        }
    });

    // Camera Trigger Handler:
    // On mobile devices, prefer native camera for maximum photo resolution;
    // on desktop / laptop, open the rich in-browser viewfinder modal!
    if (btnTriggerCamera) {
        btnTriggerCamera.addEventListener('click', function() {
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                nativeCameraInput.click();
            } else if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                // Open live in-browser camera modal
                cameraModal.classList.remove('hidden');
                navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } } 
                }).then(stream => {
                    cameraStream.srcObject = stream;
                    videoTrack = stream.getVideoTracks()[0];
                }).catch(err => {
                    console.warn('Camera stream error:', err);
                    // Fallback to native file input
                    cameraModal.classList.add('hidden');
                    nativeCameraInput.click();
                });
            } else {
                nativeCameraInput.click();
            }
        });
    }

    // Snap photo from live camera modal
    if (btnSnapPhoto) {
        btnSnapPhoto.addEventListener('click', function() {
            if (!cameraStream.videoWidth) return;
            cameraCanvas.width = cameraStream.videoWidth;
            cameraCanvas.height = cameraStream.videoHeight;
            const ctx = cameraCanvas.getContext('2d');
            ctx.drawImage(cameraStream, 0, 0);

            cameraCanvas.toBlob(blob => {
                if (blob) {
                    const snapFile = new File([blob], `kamera-bukti-${Date.now()}.jpg`, { type: 'image/jpeg' });
                    addFiles([snapFile]);
                    stopCameraModal();
                }
            }, 'image/jpeg', 0.88);
        });
    }

    function stopCameraModal() {
        if (videoTrack) {
            videoTrack.stop();
            videoTrack = null;
        }
        if (cameraStream.srcObject) {
            cameraStream.srcObject.getTracks().forEach(t => t.stop());
            cameraStream.srcObject = null;
        }
        cameraModal.classList.add('hidden');
    }

    if (btnCloseCamera) {
        btnCloseCamera.addEventListener('click', stopCameraModal);
    }

    // Form submission feedback
    const maintForm = document.getElementById('form-maintenance');
    const btnSubmitMaint = document.getElementById('btn-submit-maint');
    const maintSubmitLabel = document.getElementById('maint-submit-label');
    const maintSubmitIcon = document.getElementById('maint-submit-icon');

    if (maintForm && btnSubmitMaint) {
        maintForm.addEventListener('submit', function() {
            btnSubmitMaint.disabled = true;
            btnSubmitMaint.classList.add('opacity-80', 'cursor-wait');
            maintSubmitLabel.textContent = 'Mengirim Laporan...';
            if (maintSubmitIcon) {
                maintSubmitIcon.classList.add('animate-spin');
            }
        });
    }

    // Re-initialize Lucide Icons
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
});
</script>
