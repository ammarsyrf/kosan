<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perjanjian Sewa #<?= e($contract->contract_number) ?> - <?= e(setting('business_name', 'Kost Zenerie')) ?></title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        @media print {
            html, body {
                background: #ffffff !important;
                color: #0f172a !important;
                font-size: 11px !important;
                line-height: 1.4 !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print { display: none !important; }
            .print-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen py-6 px-4">

    <!-- Top Action Bar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="<?= url("admin/contracts/{$contract->id}") ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Detail Kontrak</span>
        </a>
        <div class="flex items-center gap-3">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-md shadow-blue-500/20 transition cursor-pointer">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Paper Sheet (A4 Proportion) -->
    <div class="print-sheet max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-200/80 p-8 sm:p-12 space-y-6 text-slate-800">
        <!-- Letterhead Kop Surat -->
        <div class="flex items-center justify-between pb-6 border-b-2 border-slate-800 gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-xl">
                    <i data-lucide="home" class="w-6 h-6 text-teal-400"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-950 uppercase tracking-tight"><?= e(setting('business_name', 'Kost Zenerie')) ?></h1>
                    <p class="text-xs text-slate-600 font-medium"><?= e(setting('business_address', 'Jl. Kost Nyaman No. 10')) ?></p>
                    <p class="text-[11px] text-slate-500">WhatsApp: <?= e(setting('business_phone', '081234567890')) ?> &bull; Sistem Manajemen Kost Modern</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-800 border border-slate-300">
                    DOKUMEN RESMI
                </span>
                <div class="text-xs font-mono font-bold text-slate-900 mt-1">#<?= e($contract->contract_number) ?></div>
                <div class="text-[10px] text-slate-400">Dibuat: <?= date('d M Y', strtotime($contract->created_at)) ?></div>
            </div>
        </div>

        <!-- Title Header -->
        <div class="text-center space-y-1 pt-2">
            <h2 class="text-base sm:text-lg font-black uppercase tracking-wide text-slate-900 underline decoration-2 underline-offset-4">
                SURAT PERJANJIAN SEWA KAMAR KOST
            </h2>
            <p class="text-xs text-slate-500">Nomor Registrasi: SPK/<?= date('Ym', strtotime($contract->start_date)) ?>/<?= e($contract->contract_number) ?></p>
        </div>

        <!-- Pembuka -->
        <p class="text-xs text-slate-700 leading-relaxed text-justify">
            Pada hari ini, tanggal <strong><?= date('d F Y', strtotime($contract->start_date)) ?></strong>, telah dibuat dan disepakati perjanjian sewa kamar kost oleh dan antara pihak-pihak di bawah ini:
        </p>

        <!-- Identitas Kedua Belah Pihak -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                <div class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-200 pb-1 flex items-center justify-between">
                    <span>PIHAK PERTAMA (PENGELOLA)</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-slate-500">Nama Usaha</span>
                    <span class="col-span-2 font-semibold text-slate-800">: <?= e(setting('business_name', 'Kost Zenerie')) ?></span>
                    <span class="text-slate-500">Alamat</span>
                    <span class="col-span-2 text-slate-800">: <?= e(setting('business_address', 'Jl. Kost Nyaman No. 10')) ?></span>
                    <span class="text-slate-500">No. WhatsApp</span>
                    <span class="col-span-2 text-slate-800">: <?= e(setting('business_phone', '081234567890')) ?></span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                <div class="font-bold text-slate-900 uppercase text-[11px] border-b border-slate-200 pb-1 flex items-center justify-between">
                    <span>PIHAK KEDUA (PENYEWA)</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-slate-500">Nama Lengkap</span>
                    <span class="col-span-2 font-bold text-slate-900">: <?= e($contract->tenant_name) ?></span>
                    <span class="text-slate-500">No. KTP/NIK</span>
                    <span class="col-span-2 font-mono text-slate-800">: <?= e($tenant->id_card_number ?? '-') ?></span>
                    <span class="text-slate-500">No. Telepon</span>
                    <span class="col-span-2 text-slate-800">: <?= e($contract->tenant_phone ?: '-') ?></span>
                    <span class="text-slate-500">Pekerjaan</span>
                    <span class="col-span-2 text-slate-800">: <?= e($tenant->occupation ?? '-') ?></span>
                </div>
            </div>
        </div>

        <!-- Ketentuan Pasal-Pasal -->
        <div class="space-y-3.5 text-xs text-slate-700 leading-relaxed text-justify">
            <div>
                <h4 class="font-bold text-slate-900 mb-0.5">Pasal 1 — Objek Sewa</h4>
                <p>PIHAK PERTAMA menyewakan kepada PIHAK KEDUA satu unit kamar kost yaitu <strong>Kamar <?= e($contract->room_number) ?> (<?= e($contract->room_name) ?>)</strong> bertempat di <?= e(setting('business_address', 'Kost Zenerie')) ?>, beserta seluruh inventaris dan fasilitas kamar yang telah diserahterimakan dalam keadaan baik dan berfungsi normal.</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 mb-0.5">Pasal 2 — Masa Sewa</h4>
                <p>Perjanjian sewa ini berlaku terhitung mulai tanggal <strong><?= date('d F Y', strtotime($contract->start_date)) ?></strong> sampai dengan tanggal <strong><?= date('d F Y', strtotime($contract->end_date)) ?></strong>. Perpanjangan masa sewa wajib dikonfirmasikan paling lambat 7 (tujuh) hari sebelum masa sewa berakhir.</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 mb-0.5">Pasal 3 — Tarif Sewa & Cara Pembayaran</h4>
                <p>Tarif sewa kamar disepakati sebesar <strong><?= $contract->formattedPrice() ?> per bulan</strong>. Pembayaran dilakukan di awal periode sewa melalui transfer ke rekening resmi pengelola kost atau tunai dan dibuktikan dengan penerbitan invoice resmi bernomor valid.</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 mb-0.5">Pasal 4 — Tata Tertib & Tanggung Jawab</h4>
                <ol class="list-decimal pl-4 space-y-1">
                    <li>PIHAK KEDUA wajib menjaga ketenangan, kebersihan kamar, serta merawat inventaris yang disediakan.</li>
                    <li>Jam tenang diberlakukan pukul 22.00 - 06.00 WIB. Tamu berkunjung diterima di ruang tamu bersama dan meninggalkan area kos sebelum pukul 22.00 WIB.</li>
                    <li>Dilarang keras membawa, menyimpan, atau mengonsumsi narkotika, obat terlarang, miras, senjata tajam, serta melakukan tindak asusila atau perjudian. Pelanggaran mengakibatkan pemutusan kontrak sewa sepihak tanpa pengembalian dana.</li>
                    <li>Seluruh kamar ber-AC adalah <em>Non-Smoking Area</em> (bebas asap rokok).</li>
                </ol>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 mb-0.5">Pasal 5 — Pengembalian Kamar (Checkout)</h4>
                <p>Saat masa sewa berakhir, PIHAK KEDUA wajib menyerahkan kembali kunci kamar kepada PIHAK PERTAMA dalam keadaan bersih, rapi, dan fasilitas lengkap seperti sedia kala.</p>
            </div>
        </div>

        <!-- Penutup & Kolom Tanda Tangan -->
        <div class="pt-6 border-t border-slate-200">
            <p class="text-xs text-slate-600 mb-6 text-center">
                Demikian surat perjanjian sewa ini dibuat secara sadar, tanpa paksaan dari pihak manapun, dan ditandatangani untuk dipatuhi bersama.
            </p>

            <div class="grid grid-cols-2 gap-8 text-center text-xs">
                <div class="space-y-16">
                    <div>
                        <div class="font-bold text-slate-900 uppercase">PIHAK PERTAMA</div>
                        <div class="text-[11px] text-slate-500">Pengelola <?= e(setting('business_name', 'Kost Zenerie')) ?></div>
                    </div>
                    <div class="border-t border-slate-400 pt-1.5 w-48 mx-auto font-bold text-slate-900">
                        ( Pengelola Kost )
                    </div>
                </div>

                <div class="space-y-16">
                    <div>
                        <div class="font-bold text-slate-900 uppercase">PIHAK KEDUA</div>
                        <div class="text-[11px] text-slate-500">Penyewa Kamar <?= e($contract->room_number) ?></div>
                    </div>
                    <div class="border-t border-slate-400 pt-1.5 w-48 mx-auto font-bold text-slate-900">
                        ( <?= e($contract->tenant_name) ?> )
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
