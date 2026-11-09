<!-- Gooey Toast & Confirm Notification Engine -->
<style>
/* Gooey-Toast Spring & Liquid Keyframes */
@keyframes gooeySpringIn {
    0% {
        transform: translateY(-28px) scale(0.75);
        opacity: 0;
        filter: blur(4px);
    }
    55% {
        transform: translateY(6px) scale(1.05, 0.95);
        opacity: 1;
        filter: blur(0);
    }
    75% {
        transform: translateY(-2px) scale(0.98, 1.02);
    }
    100% {
        transform: translateY(0) scale(1, 1);
        opacity: 1;
        filter: blur(0);
    }
}

@keyframes gooeySpringOut {
    0% {
        transform: translateY(0) scale(1);
        opacity: 1;
        filter: blur(0);
    }
    35% {
        transform: translateY(4px) scale(1.04, 0.96);
        opacity: 0.9;
    }
    100% {
        transform: translateY(-32px) scale(0.7);
        opacity: 0;
        filter: blur(4px);
    }
}

@keyframes gooeyModalSpring {
    0% {
        transform: scale(0.82) translateY(24px);
        opacity: 0;
        filter: blur(6px);
    }
    60% {
        transform: scale(1.03) translateY(-4px);
        opacity: 1;
        filter: blur(0);
    }
    100% {
        transform: scale(1) translateY(0);
        opacity: 1;
        filter: blur(0);
    }
}

@keyframes gooeyProgressLine {
    0% { width: 100%; }
    100% { width: 0%; }
}

.gooey-toast-item {
    animation: gooeySpringIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    will-change: transform, opacity, filter;
}

.gooey-toast-item.is-leaving {
    animation: gooeySpringOut 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.gooey-modal-card {
    animation: gooeyModalSpring 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
</style>

<!-- Top Fixed Gooey Toast Container -->
<div id="gooey-toast-container" class="fixed top-5 left-1/2 -translate-x-1/2 z-[99999] flex flex-col items-center gap-2.5 pointer-events-none w-full max-w-md px-4"></div>

<!-- Universal Gooey Confirm Modal Dialog (Replacing browser window.confirm) -->
<div id="gooey-confirm-modal" class="hidden fixed inset-0 z-[100000] bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300">
    <div class="relative w-full max-w-md bg-slate-900/95 backdrop-blur-2xl text-white border border-slate-700/80 rounded-3xl p-6 sm:p-7 shadow-2xl shadow-black/80 overflow-hidden gooey-modal-card">
        <!-- Ambient Liquid Blob Background -->
        <div class="absolute -top-16 -right-16 w-40 h-40 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 text-center">
            <!-- Icon Blob -->
            <div id="gooey-confirm-icon-box" class="w-14 h-14 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i data-lucide="alert-triangle" class="w-7 h-7"></i>
            </div>

            <h3 class="text-lg font-bold text-white tracking-tight" id="gooey-confirm-title">
                Konfirmasi Tindakan
            </h3>
            
            <p class="text-xs sm:text-sm text-slate-300 mt-2.5 leading-relaxed" id="gooey-confirm-message">
                Apakah Anda yakin ingin melanjutkan tindakan ini?
            </p>

            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" 
                        id="gooey-confirm-cancel" 
                        class="flex-1 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-300 bg-slate-800/80 hover:bg-slate-800 hover:text-white border border-slate-700 transition active:scale-95 cursor-pointer">
                    Batal
                </button>
                <button type="button" 
                        id="gooey-confirm-ok" 
                        class="flex-1 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-rose-700 hover:from-rose-500 hover:to-rose-600 shadow-lg shadow-rose-600/30 active:scale-95 transition cursor-pointer">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // 1. Gooey Toast Implementation
    const toastContainer = document.getElementById('gooey-toast-container');

    window.gooeyToast = {
        show: function(options) {
            if (!toastContainer) return;

            const type = options.type || 'info';
            const title = options.title || (type === 'success' ? 'Berhasil' : (type === 'error' ? 'Peringatan' : 'Informasi'));
            const message = options.message || '';
            const duration = options.duration || 4000;

            // Config theme based on type
            const configs = {
                success: {
                    border: 'border-emerald-500/40',
                    glow: 'shadow-emerald-950/40',
                    iconBg: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40',
                    icon: 'check-circle-2',
                    progressColor: 'from-emerald-400 to-teal-400',
                    badgeColor: 'text-emerald-400'
                },
                error: {
                    border: 'border-rose-500/40',
                    glow: 'shadow-rose-950/40',
                    iconBg: 'bg-rose-500/20 text-rose-400 border-rose-500/40',
                    icon: 'alert-octagon',
                    progressColor: 'from-rose-500 to-amber-500',
                    badgeColor: 'text-rose-400'
                },
                warning: {
                    border: 'border-amber-500/40',
                    glow: 'shadow-amber-950/40',
                    iconBg: 'bg-amber-500/20 text-amber-400 border-amber-500/40',
                    icon: 'alert-triangle',
                    progressColor: 'from-amber-400 to-yellow-500',
                    badgeColor: 'text-amber-400'
                },
                info: {
                    border: 'border-blue-500/40',
                    glow: 'shadow-blue-950/40',
                    iconBg: 'bg-blue-500/20 text-blue-400 border-blue-500/40',
                    icon: 'info',
                    progressColor: 'from-blue-400 to-indigo-400',
                    badgeColor: 'text-blue-400'
                }
            };

            const cfg = configs[type] || configs.info;

            const toast = document.createElement('div');
            toast.className = `gooey-toast-item pointer-events-auto relative overflow-hidden bg-slate-900/95 backdrop-blur-xl text-white border ${cfg.border} shadow-2xl ${cfg.glow} rounded-full px-4 sm:px-5 py-2.5 sm:py-3 flex items-center gap-3 max-w-md w-full transition-all duration-300`;

            toast.innerHTML = `
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full ${cfg.iconBg} border flex items-center justify-center shrink-0 shadow-inner">
                    <i data-lucide="${cfg.icon}" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0 pr-1">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest ${cfg.badgeColor}">
                            ${title}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-slate-100 truncate leading-snug">
                        ${message}
                    </p>
                </div>
                <button type="button" class="btn-close-toast text-slate-400 hover:text-white p-1 rounded-full hover:bg-white/10 transition shrink-0 cursor-pointer">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
                <div class="absolute bottom-0 left-4 right-4 h-[2px] bg-gradient-to-r ${cfg.progressColor} rounded-full opacity-60" style="animation: gooeyProgressLine ${duration}ms linear forwards;"></div>
            `;

            toastContainer.appendChild(toast);

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }

            function dismiss() {
                if (toast.classList.contains('is-leaving')) return;
                toast.classList.add('is-leaving');
                setTimeout(() => toast.remove(), 350);
            }

            const closeBtn = toast.querySelector('.btn-close-toast');
            if (closeBtn) {
                closeBtn.addEventListener('click', dismiss);
            }

            const autoTimer = setTimeout(dismiss, duration);
            toast.addEventListener('mouseenter', () => clearTimeout(autoTimer));
        },
        success: function(msg, title = 'Berhasil') {
            this.show({ type: 'success', title: title, message: msg });
        },
        error: function(msg, title = 'Perhatian') {
            this.show({ type: 'error', title: title, message: msg });
        },
        warning: function(msg, title = 'Peringatan') {
            this.show({ type: 'warning', title: title, message: msg });
        },
        info: function(msg, title = 'Informasi') {
            this.show({ type: 'info', title: title, message: msg });
        }
    };

    // 2. Gooey Confirm Modal Implementation
    const confirmModal = document.getElementById('gooey-confirm-modal');
    const confirmMessage = document.getElementById('gooey-confirm-message');
    const confirmTitle = document.getElementById('gooey-confirm-title');
    const confirmOkBtn = document.getElementById('gooey-confirm-ok');
    const confirmCancelBtn = document.getElementById('gooey-confirm-cancel');
    let currentOnConfirm = null;
    let currentOnCancel = null;

    window.gooeyConfirm = function(message, onConfirm, onCancel, options = {}) {
        if (!confirmModal) {
            if (window.nativeConfirm(message)) {
                if (onConfirm) onConfirm();
            } else {
                if (onCancel) onCancel();
            }
            return;
        }

        confirmMessage.textContent = message;
        confirmTitle.textContent = options.title || 'Konfirmasi Tindakan';
        confirmOkBtn.textContent = options.okText || 'Ya, Lanjutkan';
        currentOnConfirm = onConfirm;
        currentOnCancel = onCancel;

        confirmModal.classList.remove('hidden');
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    };

    function closeConfirmModal() {
        if (confirmModal) confirmModal.classList.add('hidden');
        currentOnConfirm = null;
        currentOnCancel = null;
    }

    if (confirmCancelBtn) {
        confirmCancelBtn.addEventListener('click', function() {
            if (currentOnCancel) currentOnCancel();
            closeConfirmModal();
        });
    }

    if (confirmOkBtn) {
        confirmOkBtn.addEventListener('click', function() {
            const cb = currentOnConfirm;
            closeConfirmModal();
            if (cb) cb();
        });
    }

    if (confirmModal) {
        confirmModal.addEventListener('click', function(e) {
            if (e.target === confirmModal) {
                if (currentOnCancel) currentOnCancel();
                closeConfirmModal();
            }
        });
    }

    // 3. Universal Event Interception for inline confirm() in forms and buttons
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.hasAttribute('data-gooey-confirmed')) {
            form.removeAttribute('data-gooey-confirmed');
            return;
        }

        const onsubmitAttr = form.getAttribute('onsubmit');
        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/s);
            const msg = match ? match[1].replace(/\\'/g, "'").replace(/\\"/g, '"') : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

            window.gooeyConfirm(msg, function() {
                form.setAttribute('data-gooey-confirmed', 'true');
                form.submit();
            });
            return false;
        }
    }, true);

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('button, a');
        if (!btn) return;

        if (btn.hasAttribute('data-gooey-confirmed')) {
            btn.removeAttribute('data-gooey-confirmed');
            return;
        }

        const onclickAttr = btn.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('confirm(')) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const match = onclickAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/s);
            const msg = match ? match[1].replace(/\\'/g, "'").replace(/\\"/g, '"') : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

            window.gooeyConfirm(msg, function() {
                btn.setAttribute('data-gooey-confirmed', 'true');
                btn.click();
            });
            return false;
        }
    }, true);
})();
</script>

