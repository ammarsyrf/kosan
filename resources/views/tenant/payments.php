<div class="max-w-2xl mx-auto px-4 sm:px-0 py-4 animate-in fade-in duration-300">
    <!-- Breadcrumb & Navigation -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="<?= url('tenant/dashboard') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors bg-white hover:bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200/80 shadow-xs mb-3 group">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5"></i>
                <span>Kembali ke Dashboard</span>
            </a>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    Portal Mandiri Penghuni
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kirim Bukti Pembayaran</h1>
            <p class="text-slate-500 text-sm mt-1">Unggah konfirmasi transfer untuk diverifikasi cepat oleh tim pengelola kos.</p>
        </div>
    </div>

    <!-- Virtual Destination Bank Card (Luxury UI) -->
    <div class="mb-8 relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-7 shadow-xl shadow-indigo-950/20 border border-slate-800">
        <!-- Glow blobbies -->
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col justify-between h-full gap-6">
            <!-- Card Top: Chip & Bank Brand -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <!-- Metallic Chip -->
                    <div class="w-11 h-8 rounded-md bg-gradient-to-tr from-amber-400 via-amber-200 to-yellow-500 border border-yellow-300/60 shadow-sm relative overflow-hidden flex items-center justify-center">
                        <div class="w-full h-px bg-amber-600/40 absolute top-2.5"></div>
                        <div class="w-full h-px bg-amber-600/40 absolute bottom-2.5"></div>
                        <div class="w-px h-full bg-amber-600/40 absolute left-3"></div>
                        <div class="w-px h-full bg-amber-600/40 absolute right-3"></div>
                    </div>
                    <!-- Contactless Icon -->
                    <i data-lucide="wifi" class="w-5 h-5 text-slate-400/80 rotate-90"></i>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-extrabold tracking-widest bg-white/10 text-white backdrop-blur-md border border-white/15">
                        <?= e(config('payment.bank_name', 'BCA')) ?>
                    </span>
                    <span class="text-[10px] uppercase font-semibold tracking-wider text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Card Middle: Account Number & Copy Button -->
            <div>
                <p class="text-[11px] font-medium tracking-wider uppercase text-slate-400 mb-1.5">Nomor Rekening Tujuan</p>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <?php 
                        $rawAccNo = (string)config('payment.bank_account_number', '1234567890');
                        $formattedAccNo = chunk_split($rawAccNo, 4, ' ');
                    ?>
                    <div class="text-2xl sm:text-3xl font-mono font-bold tracking-widest text-slate-100 selection:bg-blue-500 selection:text-white" id="bank-acc-text">
                        <?= e($formattedAccNo) ?>
                    </div>
                    <button type="button" 
                            id="btn-copy-acc" 
                            data-account="<?= e($rawAccNo) ?>"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/10 hover:bg-white/20 active:scale-95 text-white backdrop-blur-md border border-white/20 transition-all cursor-pointer">
                        <i data-lucide="copy" class="w-3.5 h-3.5" id="copy-icon"></i>
                        <span id="copy-label">Salin No. Rekening</span>
                    </button>
                </div>
            </div>

            <!-- Card Bottom: Cardholder info & Guarantee -->
            <div class="flex items-end justify-between pt-2 border-t border-white/10 text-xs">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Atas Nama</span>
                    <span class="font-bold tracking-wide text-white uppercase text-sm sm:text-base">
                        <?= e(config('payment.bank_account_holder', 'Owner Kos')) ?>
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Metode</span>
                    <span class="text-xs font-medium text-slate-300">Transfer Manual / Real-time</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Payment Form Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/80 overflow-hidden">
        <div class="p-6 sm:p-8">
            <form action="<?= url('tenant/payments') ?>" method="POST" enctype="multipart/form-data" class="space-y-6" id="form-tenant-payment">
                <?= csrf_field() ?>

                <!-- Section: Pilih Tagihan -->
                <div>
                    <label for="invoice_id" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Pilih Tagihan yang Dibayar <span class="text-rose-500">*</span>
                    </label>

                    <?php if (empty($invoices)): ?>
                        <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-900 flex items-start gap-3">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-amber-600 mt-0.5 shrink-0"></i>
                            <div>
                                <h4 class="font-bold text-sm">Tidak Ada Tagihan Tertunggak</h4>
                                <p class="text-xs text-amber-700 mt-0.5">Semua invoice sewa Anda tercatat telah lunas. Jika ingin melakukan pembayaran di muka, hubungi admin pengelola kos.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="relative">
                            <select name="invoice_id" id="invoice_id" required class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 py-3 pr-10 text-sm font-medium text-slate-800 shadow-xs focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition">
                                <option value="" data-balance="" data-number="" data-period="">-- Pilih Tagihan Belum Lunas --</option>
                                <?php foreach ($invoices as $inv): ?>
                                    <option value="<?= (int)$inv->id ?>" 
                                            data-balance="<?= (float)$inv->balance ?>"
                                            data-balance-formatted="<?= $inv->formattedBalance() ?>"
                                            data-number="<?= e($inv->invoice_number) ?>"
                                            data-period="<?= e($inv->period_start) ?>">
                                        #<?= e($inv->invoice_number) ?> - Periode <?= e($inv->period_start) ?> &bull; Sisa Tagihan: <?= $inv->formattedBalance() ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>

                        <!-- Dynamic Invoice Callout on Selection -->
                        <div id="invoice-callout" class="hidden mt-3 p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/70 flex items-center justify-between gap-3 animate-in fade-in duration-200">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                                    <i data-lucide="receipt" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-blue-700 font-medium block">Nominal yang harus dibayarkan:</span>
                                    <span class="text-sm font-bold text-blue-950 font-mono" id="invoice-callout-balance">Rp 0</span>
                                </div>
                            </div>
                            <button type="button" id="btn-autofill-amount" class="text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition active:scale-95 cursor-pointer">
                                Isi Otomatis
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Section: Nominal & Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nominal Transfer -->
                    <div>
                        <label for="amount" class="block text-sm font-semibold text-slate-800 mb-1.5">
                            Nominal Transfer <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md border border-slate-200">Rp</span>
                            </div>
                            <input type="number" 
                                   step="0.01" 
                                   name="amount" 
                                   id="amount" 
                                   required 
                                   class="w-full rounded-xl border border-slate-300 py-2.5 pl-14 pr-3.5 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition" 
                                   placeholder="Contoh: 1500000">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1" id="amount-helper">
                            <i data-lucide="info" class="w-3 h-3 text-slate-400"></i>
                            <span>Masukkan angka tanpa titik atau koma.</span>
                        </p>
                    </div>

                    <!-- Tanggal Transfer -->
                    <div>
                        <label for="payment_date" class="block text-sm font-semibold text-slate-800 mb-1.5">
                            Tanggal Transfer <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl shadow-xs">
                            <input type="date" 
                                   name="payment_date" 
                                   id="payment_date" 
                                   value="<?= date('Y-m-d') ?>" 
                                   required 
                                   class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm font-medium text-slate-900 focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1.5">Sesuaikan dengan tanggal bukti transfer Anda.</p>
                    </div>
                </div>

                <!-- Section: Drag & Drop File Upload Bukti Transfer -->
                <div>
                    <label class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Bukti Transfer (Struk / Tangkapan Layar) <span class="text-rose-500">*</span>
                    </label>

                    <div id="dropzone" class="border-2 border-dashed border-slate-300 hover:border-blue-500 hover:bg-blue-50/20 bg-slate-50/50 rounded-2xl p-6 text-center cursor-pointer transition-all duration-200 relative group">
                        <input type="file" 
                               name="proof" 
                               id="proof" 
                               required 
                               accept="image/jpeg,image/png,image/webp,application/pdf" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <!-- Default Prompt State -->
                        <div id="dropzone-prompt" class="space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto transition-transform group-hover:scale-110">
                                <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">
                                    <span class="text-blue-600 hover:underline">Klik untuk pilih file</span> atau tarik file ke sini
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    Format: JPG, PNG, WEBP, atau PDF (Maks. 5MB)
                                </p>
                            </div>
                        </div>

                        <!-- Active File Selected Preview State -->
                        <div id="dropzone-preview" class="hidden flex items-center justify-between gap-3 p-3 bg-white rounded-xl border border-slate-200/80 shadow-xs relative z-20">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <!-- Image thumbnail or PDF icon -->
                                <div id="preview-thumb-container" class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">
                                    <img id="preview-img" src="" alt="Bukti Transfer" class="w-full h-full object-cover hidden">
                                    <i data-lucide="file-text" id="preview-file-icon" class="w-6 h-6 text-slate-500"></i>
                                </div>
                                <div class="text-left overflow-hidden">
                                    <p class="text-xs font-bold text-slate-800 truncate max-w-[220px] sm:max-w-xs" id="preview-filename">bukti-bayar.jpg</p>
                                    <p class="text-[11px] text-slate-500" id="preview-filesize">0 KB</p>
                                </div>
                            </div>
                            <button type="button" id="btn-remove-file" class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition border border-rose-200/60 shrink-0">
                                Ganti
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section: Catatan Tambahan -->
                <div>
                    <label for="notes" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Catatan Tambahan <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                    </label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="2" 
                              class="w-full rounded-xl border border-slate-300 p-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:outline-hidden focus:ring-3 focus:ring-blue-500/15 transition" 
                              placeholder="Contoh: Transfer via m-BCA a.n. Budi Gunawan untuk sewa bulan November."></textarea>
                </div>

                <!-- Form Footer Actions -->
                <div class="pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                        <span>Bukti transfer tersimpan aman dan terenkripsi.</span>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="<?= url('tenant/dashboard') ?>" class="px-5 py-2.5 text-sm font-semibold rounded-xl text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" 
                                id="btn-submit-payment" 
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/35 active:scale-95 transition-all cursor-pointer">
                            <i data-lucide="send" class="w-4 h-4" id="submit-icon"></i>
                            <span id="submit-label">Kirim Bukti Pembayaran</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Interactive Client-side Script for /ui-ux-pro-max Polish -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Copy Account Number to Clipboard with Feedback
    const btnCopy = document.getElementById('btn-copy-acc');
    if (btnCopy) {
        btnCopy.addEventListener('click', function() {
            const acc = this.getAttribute('data-account') || '1234567890';
            navigator.clipboard.writeText(acc).then(() => {
                const label = document.getElementById('copy-label');
                const icon = document.getElementById('copy-icon');
                
                label.textContent = 'Tersalin!';
                btnCopy.classList.add('bg-emerald-500/20', 'text-emerald-300', 'border-emerald-500/40');
                
                setTimeout(() => {
                    label.textContent = 'Salin No. Rekening';
                    btnCopy.classList.remove('bg-emerald-500/20', 'text-emerald-300', 'border-emerald-500/40');
                }, 2200);
            }).catch(err => {
                console.warn('Clipboard write failed:', err);
            });
        });
    }

    // 2. Invoice Selector & Auto-fill Balance
    const invoiceSelect = document.getElementById('invoice_id');
    const invoiceCallout = document.getElementById('invoice-callout');
    const invoiceCalloutBalance = document.getElementById('invoice-callout-balance');
    const btnAutofill = document.getElementById('btn-autofill-amount');
    const amountInput = document.getElementById('amount');

    if (invoiceSelect) {
        invoiceSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const balance = selected.getAttribute('data-balance');
            const formatted = selected.getAttribute('data-balance-formatted');

            if (balance && parseFloat(balance) > 0) {
                invoiceCallout.classList.remove('hidden');
                invoiceCalloutBalance.textContent = formatted;
            } else {
                invoiceCallout.classList.add('hidden');
            }
        });

        if (btnAutofill) {
            btnAutofill.addEventListener('click', function() {
                const selected = invoiceSelect.options[invoiceSelect.selectedIndex];
                const balance = selected.getAttribute('data-balance');
                if (balance) {
                    amountInput.value = balance;
                    amountInput.focus();
                    amountInput.classList.add('ring-3', 'ring-blue-500/30');
                    setTimeout(() => amountInput.classList.remove('ring-3', 'ring-blue-500/30'), 800);
                }
            });
        }
    }

    // 3. Drag & Drop File Upload with Real-time Preview
    const fileInput = document.getElementById('proof');
    const dropzone = document.getElementById('dropzone');
    const dropzonePrompt = document.getElementById('dropzone-prompt');
    const dropzonePreview = document.getElementById('dropzone-preview');
    const previewFilename = document.getElementById('preview-filename');
    const previewFilesize = document.getElementById('preview-filesize');
    const previewImg = document.getElementById('preview-img');
    const previewFileIcon = document.getElementById('preview-file-icon');
    const btnRemoveFile = document.getElementById('btn-remove-file');

    function formatBytes(bytes) {
        if (!+bytes) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(1))} ${sizes[i]}`;
    }

    function showFile(file) {
        if (!file) return;
        previewFilename.textContent = file.name;
        previewFilesize.textContent = formatBytes(file.size);

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                previewFileIcon.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.classList.add('hidden');
            previewFileIcon.classList.remove('hidden');
        }

        dropzonePrompt.classList.add('hidden');
        dropzonePreview.classList.remove('hidden');
        dropzone.classList.add('border-blue-500', 'bg-blue-50/20');
    }

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                showFile(this.files[0]);
            }
        });

        if (btnRemoveFile) {
            btnRemoveFile.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = '';
                previewImg.src = '';
                dropzonePrompt.classList.remove('hidden');
                dropzonePreview.classList.add('hidden');
                dropzone.classList.remove('border-blue-500', 'bg-blue-50/20');
            });
        }

        // Drag & drop highlight
        ['dragenter', 'dragover'].forEach(event => {
            dropzone.addEventListener(event, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-blue-600', 'bg-blue-50/40');
            });
        });
        ['dragleave', 'drop'].forEach(event => {
            dropzone.addEventListener(event, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-blue-600', 'bg-blue-50/40');
            });
        });
    }

    // 4. Form Submit Loading Feedback
    const paymentForm = document.getElementById('form-tenant-payment');
    const btnSubmit = document.getElementById('btn-submit-payment');
    const submitLabel = document.getElementById('submit-label');
    const submitIcon = document.getElementById('submit-icon');

    if (paymentForm && btnSubmit) {
        paymentForm.addEventListener('submit', function() {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-80', 'cursor-wait');
            submitLabel.textContent = 'Mengirim Bukti...';
            if (submitIcon) {
                submitIcon.classList.add('animate-spin');
            }
        });
    }

    // Re-initialize Lucide Icons
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
});
</script>
