@php
    $userActive = Auth::user();
    $uRole = $userActive ? trim(strtolower($userActive->role)) : '';
    $uName = $userActive ? trim(strtoupper($userActive->name)) : '';

    $isSuperAdmin = ($uRole === 'superadmin' || $uName === 'ADMIN SUPER');
    $isAdminStationery = ($uRole === 'admin_stationery');
    $isAdminAbsensi = ($uRole === 'admin_absensi');
    $isStandar = ($uRole === 'user' || $uRole === '');

    $dataStationery = \App\Models\Stationery::all();
    $transaksis = \App\Models\StationeryTransaction::with('stationery')
        ->orderBy('tanggal', 'desc')
        ->get();

    $defaultSatuan = ['Rim', 'Pcs', 'Pack', 'Box', 'Pc', 'Ktk', 'Duz', 'Pac', 'Set', 'EA', 'Ikat', 'BTNG', 'Rol', 'AE', 'PSG', 'KG'];
    $dbSatuan = $dataStationery->pluck('satuan')->filter()->map(function($s) { return trim($s); })->unique()->toArray();
    $allSatuan = array_unique(array_merge($defaultSatuan, $dbSatuan));
    sort($allSatuan);

    $defaultKategori = ['Stationary', 'Office Suplai', 'Kebersihan'];
    $dbKategori = $dataStationery->pluck('kategori')->filter()->map(function($k) { return trim($k); })->unique()->toArray();
    $allKategori = array_unique(array_merge($defaultKategori, $dbKategori));
    sort($allKategori);

    $barangMenipis = $dataStationery->filter(function($item) {
        return $item->total_stok <= $item->min_stok;
    });

    $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(30);
    $bulanIni = date('m');
    $tahunIni = date('Y');
    $hariBerjalan = (int) date('d');

    $orderBarang = $dataStationery->map(function($item) use ($transaksis, $thirtyDaysAgo, $bulanIni, $tahunIni, $hariBerjalan) {
        $trxBarangIni = $transaksis->filter(fn($q) => $q->stationery_id === $item->id);
        $trxKeluar = $trxBarangIni->filter(fn($q) => strtolower($q->type) === 'keluar');

        $total30Hari = $trxKeluar->filter(fn($q) => $q->tanggal >= $thirtyDaysAgo)->sum('jumlah');
        $rataPenggunaanHarian = (int) ceil($total30Hari / 30);

        $totalKeluarBulanIni = $trxKeluar->filter(function($q) use ($bulanIni, $tahunIni) {
            $tgl = \Carbon\Carbon::parse($q->tanggal);
            return $tgl->format('m') == $bulanIni && $tgl->format('Y') == $tahunIni;
        })->sum('jumlah');
        $rataHarianBulanIni = $hariBerjalan > 0 ? (int) ceil($totalKeluarBulanIni / $hariBerjalan) : 0;

        $penggunaanPerDept = [];
        foreach ($trxKeluar as $out) {
            $d = $out->dept ?: 'Tanpa Dept';
            if (!isset($penggunaanPerDept[$d])) $penggunaanPerDept[$d] = 0;
            $penggunaanPerDept[$d] += $out->jumlah;
        }
        arsort($penggunaanPerDept);
        $maxDept = key($penggunaanPerDept);
        $maxJumlah = current($penggunaanPerDept);

        $pemakaianTerbanyak = $maxJumlah > 0 ? "$maxJumlah {$item->satuan} ($maxDept)" : "0 {$item->satuan}";

        $penggunaUnik = array_filter(array_unique($trxKeluar->pluck('dept')->toArray()));
        $penggunaStr = count($penggunaUnik) > 0 ? implode(', ', $penggunaUnik) : 'BELUM ADA DATA';

        $terakhirOrder = $trxBarangIni->filter(fn($q) => strtolower($q->type) === 'masuk')->max('tanggal');

        $item->rata_penggunaan = $rataPenggunaanHarian;
        $item->terakhir_order = $terakhirOrder;
        $item->status_reorder = ($item->total_stok <= $item->min_stok) ? 'Perlu order' : 'Aman';
        $item->siapa_pengguna = $penggunaStr;

        $item->rata_pakai_otomatis = $rataHarianBulanIni;
        $item->pemakaian_terbanyak = $pemakaianTerbanyak;

        return $item;
    });

    $pemakaianBarang = $transaksis->filter(fn($q) => strtolower($q->type) === 'keluar')->values();

    $dataPengadaan = \App\Models\Pengadaan::with('stationery')
        ->orderBy('created_at', 'desc')
        ->get();

    $pengadaanMenunggu = \App\Models\Pengadaan::with('stationery')
        ->where('status', 'Menunggu')
        ->get();
@endphp

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); margin: 0; overflow: hidden; height: 100vh; }
    .bg-indofood-blue { background: #004a99; }
    .text-indofood-blue { color: #004a99; }
    .glass-card { background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 1.25rem; box-shadow: 0 10px 25px -5px rgba(0, 74, 153, 0.05); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .glass-card:hover { transform: translateY(-4px); box-shadow: 0 20px 30px -10px rgba(0, 74, 153, 0.12); background: rgba(255, 255, 255, 0.9); border-color: rgba(0, 74, 153, 0.2); }
    header.bg-white { background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(8px); border-bottom: 1px solid rgba(226, 232, 240, 0.8); }
    .side-nav-btn { display: flex; align-items: center; width: 100%; padding: 0.85rem 1.25rem; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 600; color: rgba(255, 255, 255, 0.85); border-radius: 0.75rem; transition: 0.3s; text-transform: capitalize; letter-spacing: 0.02em; position: relative; overflow: hidden; }
    .side-nav-btn i { width: 22px; margin-right: 12px; font-size: 1.1rem; opacity: 1; color: inherit; }
    .side-nav-btn:hover { background: rgba(255, 255, 255, 0.1); color: white; }
    .side-nav-btn.active { background: white; color: #004a99; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); font-weight: 800; }
    .side-nav-btn.active::before { content: ''; position: absolute; left: 0; top: 15%; height: 70%; width: 4px; background-color: #004a99; border-radius: 0 4px 4px 0; }
    #sub-menu-container { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; }
    .menu-collapsed { max-height: 0; opacity: 0; transform: translateY(-10px); pointer-events: none; }
    .menu-expanded { max-height: 1500px; opacity: 1; transform: translateY(0); }

    .form-input-modern { width: 100%; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; padding: 0.6rem 0.8rem; font-size: 0.85rem; color:#1e293b; transition: 0.2s; font-weight: 600; }
    .form-input-modern:focus { border-color: #004a99; outline: none; box-shadow: 0 0 0 3px rgba(0, 74, 153, 0.1); }

    .pl-icon-modern { padding-left: 2.25rem !important; }

    /* ========================================================== */
    /* CUSTOM SCROLLBAR KHUSUS TABEL AGAR SELALU MENGAMBANG       */
    /* ========================================================== */
    .table-scroll-container {
        /* KUNCI TINGGI ABSOLUT: 100% Layar dikurangi 320px (untuk ruang Header & Filter) */
        height: calc(100vh - 320px) !important;
        min-height: 350px !important;
        overflow-y: auto !important;
        overflow-x: auto !important;
    }
    .table-scroll-container::-webkit-scrollbar {
        height: 16px !important;
        width: 12px !important;
    }
    .table-scroll-container::-webkit-scrollbar-track {
        background: #f1f5f9 !important;
        border-top: 1px solid #e2e8f0;
        border-left: 1px solid #e2e8f0;
        border-radius: 0 0 10px 10px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb {
        background-color: #94a3b8 !important;
        border-radius: 10px !important;
        border: 3px solid #f1f5f9 !important;
    }
    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background-color: #64748b !important;
    }
    .table-scroll-container::-webkit-scrollbar-corner {
        background: #f1f5f9;
    }

    body::-webkit-scrollbar, .custom-scrollbar::-webkit-scrollbar { width: 10px; height: 10px; }
    body::-webkit-scrollbar-track, .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    body::-webkit-scrollbar-thumb, .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    body::-webkit-scrollbar-thumb:hover, .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .table-bordered th, .table-bordered td { border: 1px solid #e2e8f0; }
    .table-bordered thead th { border-bottom: 2px solid #94a3b8; }
    #addEmployeeModal, #editEmployeeModal { left: 18rem !important; background: #f8fafc !important; }
    .modal-full-content { position: fixed; top: 0; right: 0; bottom: 0; left: 18rem; z-index: 100; background: #f8fafc; display: flex; flex-direction: column; }
    @media print {
        aside, header, .side-nav-btn, form, button, .print-hide { display: none !important; }
        body, .flex-1, main, #tab-lembur { height: auto !important; overflow: visible !important; }
        .glass-card { border: none !important; box-shadow: none !important; }
    }

    header, [id*="header"], .header-container { position: relative !important; z-index: 9999 !important; }
    #btnNotifikasi { position: relative !important; z-index: 99999 !important; pointer-events: auto !important; cursor: pointer !important; }
    #wita-header-display, .system-time-text { font-weight: 800 !important; color: #004a99 !important; letter-spacing: 0.05em; }
    #dropdownNotifikasi { z-index: 999999 !important; width: 340px !important; background: #ffffff !important; border: 1px solid #e2e8f0 !important; border-radius: 0.75rem !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important; overflow: hidden !important; }
    #dropdownNotifikasi * { font-family: 'Plus Jakarta Sans', sans-serif !important; letter-spacing: normal !important; }

    /* ========================================================== */
    /* CUSTOM CSS UNTUK SELECT2 GLOBAL                            */
    /* ========================================================== */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        font-weight: 700;
        color: #1f2937;
        outline: none;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container {
        z-index: 99999 !important;
    }

    /* ========================================================== */
    /* STYLING KHUSUS UNTUK SATUAN (BISA KETIK & PILIH)           */
    /* ========================================================== */
    .select2-satuan + .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .select2-satuan + .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 0.75rem !important; /* text-xs */
        font-weight: 700 !important; /* font-bold */
        color: #1f2937 !important; /* text-gray-800 */
        line-height: 36px !important;
    }
    .select2-satuan + .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    /* PADDING KIRI KETIKA ADA ICON DI TAMBAH BARANG */
    .select2-with-icon + .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 3rem !important; /* pl-12 untuk menampung icon absolute */
    }
    /* PADDING KIRI KETIKA TIDAK ADA ICON DI EDIT BARANG */
    .select2-no-icon + .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0.75rem !important; /* px-3 normal */
    }

    /* CUSTOM STYLING DROPDOWN KATEGORI */
    .custom-select-arrow {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none' stroke='%236b7280' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 8 10 12 14 8'/%3E%3C/svg%3E");
        background-position: right 0.6rem center;
        background-repeat: no-repeat;
        background-size: 1.2em 1.2em;
        padding-right: 2.2rem !important;
    }

    /* Styling Custom Checkbox Bulk Delete */
    .checkbox-custom {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 2px solid #cbd5e1;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .checkbox-custom:checked {
        background-color: #ef4444;
        border-color: #ef4444;
    }
    .selection-col {
        transition: all 0.3s ease;
    }
</style>

{{-- HTML5 DATALIST UNTUK FITUR KETIK & PILIH OTOMATIS (SANGAT RAPI) --}}
<datalist id="datalist_kategori">
    @foreach($allKategori as $kat)
        <option value="{{ $kat }}"></option>
    @endforeach
</datalist>

<datalist id="datalist_satuan">
    @foreach($allSatuan as $sat)
        <option value="{{ $sat }}"></option>
    @endforeach
</datalist>

<main class="flex-1 overflow-y-auto p-4 md:p-5 relative flex flex-col min-h-0">
    <div class="max-w-full mx-auto w-full flex-1 flex flex-col min-h-0">

        @if(!$isAdminStationery)
            @include('dashboard.ringkasan')
        @endif

        @if($isSuperAdmin || $isStandar || $isAdminAbsensi)
            @include('dashboard.karyawan')
            @include('dashboard.input')
            @include('dashboard.input_lembur')
            @include('dashboard.rekap')
            @include('dashboard.lembur')
        @endif

        @if($isSuperAdmin || $isAdminStationery)

        {{-- TAB ANALITIK STATIONERY --}}
        <div id="tab-analitik-stationery" class="tab-content hidden flex-1 flex-col gap-4 overflow-hidden min-h-0">

            {{-- WIDGET STATISTIK ATAS --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 shrink-0">
                <div class="glass-card p-3.5 border-l-4 border-blue-500 bg-blue-50/30">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Jenis Barang</p>
                    <h3 class="text-lg font-extrabold text-slate-700" id="stat-total-barang">0</h3>
                </div>
                <div class="glass-card p-3.5 border-l-4 border-indigo-500 bg-indigo-50/30">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Barang Masuk</p>
                    <h3 class="text-lg font-extrabold text-indigo-600" id="stat-total-in">0</h3>
                </div>
                <div class="glass-card p-3.5 border-l-4 border-red-500 bg-red-50/30">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Barang Keluar</p>
                    <h3 class="text-lg font-extrabold text-red-600" id="stat-total-out">0</h3>
                </div>
                <div class="glass-card p-3.5 border-l-4 border-green-500 bg-green-50/30">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5" id="title-aset">Nilai Aset Stok</p>
                    <h3 class="text-lg font-extrabold text-green-600 truncate" id="stat-total-aset">Rp 0</h3>
                </div>
                <div class="glass-card p-3.5 border-l-4 border-yellow-500 bg-yellow-50/50 shadow-sm">
                    <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-0.5">Stok Habis (0)</p>
                    <h3 class="text-lg font-extrabold text-yellow-600" id="stat-stok-habis">0</h3>
                </div>
            </div>

            <div class="glass-card p-5 flex-1 flex flex-col min-h-0 overflow-y-auto custom-scrollbar">

                {{-- HEADER FILTER & TOMBOL DOWNLOAD --}}
                <div class="flex flex-wrap justify-between items-center mb-5 gap-3 border-b-2 border-slate-100 pb-4 shrink-0">
                    <div>
                        <h3 class="text-base font-black text-slate-800 uppercase tracking-widest"><i class="fas fa-chart-pie text-indofood-blue mr-2"></i>Dashboard Analitik</h3>
                        <p class="text-[11px] font-bold text-slate-500 mt-1 uppercase">Visualisasi Top 5 Kuantitas & Frekuensi</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200 shadow-sm">
                        <div class="flex flex-col">
                            <label class="text-[9px] font-black text-slate-500 uppercase mb-1">Filter Hari</label>
                            <input type="date" id="chartFilterHari" onchange="updateStationeryChart('hari')" class="form-input-modern text-xs py-1.5 px-3 w-36 shadow-sm cursor-pointer border-gray-300">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[9px] font-black text-slate-500 uppercase mb-1">Filter Bulan</label>
                            <select id="chartFilterBulan" onchange="updateStationeryChart('bulan')" class="form-input-modern text-xs py-1.5 px-3 w-36 shadow-sm cursor-pointer border-gray-300 custom-select-arrow">
                                <option value="">Semua Bulan</option>
                                <option value="01">Januari</option><option value="02">Februari</option><option value="03">Maret</option>
                                <option value="04">April</option><option value="05">Mei</option><option value="06">Juni</option>
                                <option value="07">Juli</option><option value="08">Agustus</option><option value="09">September</option>
                                <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[9px] font-black text-slate-500 uppercase mb-1">Filter Barang</label>
                            <select id="chartFilterBarang" onchange="updateStationeryChart('barang')" class="form-input-modern text-xs py-1.5 px-3 w-48 shadow-sm cursor-pointer border-gray-300 custom-select-arrow">
                                <option value="top5">Top 5 Paling Sering/Banyak</option>
                                <option value="bottom5">Top 5 Paling Dikit/Jarang</option>
                                <option value="all">Semua Barang</option>
                                <optgroup label="Barang Spesifik:">
                                    @foreach($dataStationery->sortBy('nama_barang') as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama_barang }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="flex flex-col justify-end h-full">
                            <label class="text-[9px] font-black text-slate-500 uppercase mb-1">Download PNG</label>
                            <div class="flex gap-2 mt-auto">
                                <button type="button" onclick="downloadChartPNG('stationeryOutChart', 'Quantity')" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-md flex items-center border border-blue-700">
                                    <i class="fas fa-download mr-1"></i> QTY
                                </button>
                                <button type="button" onclick="downloadChartPNG('stationeryFreqChart', 'Frekuensi')" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-md flex items-center border border-indigo-700">
                                    <i class="fas fa-download mr-1"></i> FREQ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BUNGKUS KOTAK GRAFIK (SIDE-BY-SIDE) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full shrink-0 mb-4">

                    {{-- KOTAK 1: KUANTITAS --}}
                    <div id="boxChartKuantitas" class="w-full bg-white rounded-2xl border-2 border-blue-100 shadow-lg p-6 flex flex-col transition-all duration-300">
                        <h4 class="text-sm font-medium text-blue-800 tracking-wide mb-4 text-center">
                            1. Grafik Kuantitas (Quantity)
                        </h4>
                        <div id="wrapperQty" class="relative w-full flex items-center justify-center" style="height: 350px;">
                            <canvas id="stationeryOutChart" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    {{-- KOTAK 2: FREKUENSI --}}
                    <div id="boxChartFrekuensi" class="w-full bg-white rounded-2xl border-2 border-indigo-100 shadow-lg p-6 flex flex-col transition-all duration-300">
                        <h4 class="text-sm font-medium text-indigo-800 tracking-wide mb-4 text-center">
                            2. Grafik Frekuensi (Frequency)
                        </h4>
                        <div id="wrapperFreq" class="relative w-full flex items-center justify-center" style="height: 350px;">
                            <canvas id="stationeryFreqChart" class="w-full h-full"></canvas>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- TAB 1: KELOLA MASTER STOK --}}
        <div id="tab-stationery" class="tab-content hidden flex-1 flex-col gap-6 min-h-0">
            <div class="flex flex-wrap justify-between items-center bg-white p-5 rounded-xl border border-gray-100 shadow-sm gap-4 shrink-0">
                <div>
                    <h3 class="text-xs font-black text-gray-600 uppercase tracking-widest">Master Data Stationery (Ending Stock)</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">Sistem Cek Stok Otomatis: (Masuk - Keluar = Sisa)</p>
                </div>
            </div>

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl shadow-sm flex items-center justify-between gap-4 shrink-0">
                    <div class="flex items-center gap-3"><i class="fas fa-check-circle text-xl text-green-500"></i><p class="text-xs font-bold">{{ session('success') }}</p></div>
                    <button onclick="this.parentElement.style.display='none'" class="text-green-500 hover:text-green-700"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if (session('warning'))
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-xl shadow-sm flex items-center justify-between gap-4 shrink-0">
                    <div class="flex items-center gap-3"><i class="fas fa-exclamation-circle text-xl text-yellow-500"></i><p class="text-xs font-bold">{{ session('warning') }}</p></div>
                    <button onclick="this.parentElement.style.display='none'" class="text-yellow-500 hover:text-yellow-700"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl shadow-sm flex items-center justify-between gap-4 shrink-0">
                    <div class="flex items-center gap-3"><i class="fas fa-times-circle text-xl text-red-500"></i><p class="text-xs font-bold">{{ session('error') }}</p></div>
                    <button onclick="this.parentElement.style.display='none'" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- NOTIFIKASI RESTOCK (STOK MENIPIS) --}}
            @if($barangMenipis->count() > 0)
            <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-xl shadow-sm flex items-start gap-4 shrink-0">
                <div class="bg-red-100 p-2 rounded-lg text-red-600 animate-pulse"><i class="fas fa-exclamation-triangle text-xl"></i></div>
                <div class="flex-1">
                    <h4 class="text-xs font-black text-red-700 uppercase tracking-widest">Peringatan Restock Segera!</h4>
                    <p class="text-[11px] text-red-600 mt-1 font-semibold">Terdapat <b class="text-lg">{{ $barangMenipis->count() }}</b> barang yang stoknya menyentuh indikator merah.</p>
                </div>
            </div>
            @endif

            {{-- NOTIFIKASI STATUS MENUNGGU BARANG --}}
            @if($pengadaanMenunggu->count() > 0)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-xl shadow-sm flex items-start gap-4 mt-2 shrink-0">
                <div class="bg-blue-100 p-2 rounded-lg text-blue-600 animate-pulse"><i class="fas fa-truck-loading text-xl"></i></div>
                <div class="flex-1">
                    <h4 class="text-xs font-black text-blue-800 uppercase tracking-widest">Informasi: Menunggu Barang Datang</h4>
                    <p class="text-[11px] text-blue-700 mt-1 font-semibold">Terdapat <b class="text-lg">{{ $pengadaanMenunggu->count() }}</b> pesanan barang (PO/PR) yang berstatus sedang menunggu kedatangan dari vendor.</p>
                </div>
            </div>
            @endif

            <div class="glass-card flex-1 overflow-hidden flex flex-col min-h-0">
                <div class="px-6 py-4 border-b border-gray-100 bg-white/80 flex flex-wrap justify-between items-center gap-4 shrink-0">

                    <div class="flex flex-wrap items-center gap-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1"><i class="fas fa-filter text-blue-500 mr-1"></i>Filter:</p>

                        <div class="relative w-36 mt-2 md:mt-0">
                            <label class="absolute -top-2 left-3 bg-white px-1 text-[9px] font-black text-blue-500 uppercase tracking-wider rounded-md">Mulai Tgl</label>
                            <input type="date" id="statFilterMulai" onchange="window.filterStationery()" class="form-input-modern py-1.5 px-3 text-xs font-bold text-gray-700 border-gray-300 shadow-sm rounded-xl cursor-pointer w-full">
                        </div>

                        <div class="relative w-36 mt-2 md:mt-0">
                            <label class="absolute -top-2 left-3 bg-white px-1 text-[9px] font-black text-blue-500 uppercase tracking-wider rounded-md">Sampai Tgl</label>
                            <input type="date" id="statFilterAkhir" onchange="window.filterStationery()" class="form-input-modern py-1.5 px-3 text-xs font-bold text-gray-700 border-gray-300 shadow-sm rounded-xl cursor-pointer w-full">
                        </div>

<div class="relative w-40">
<select id="statKategori" onchange="window.filterStationery()" class="form-input-modern py-1.5 px-3 text-xs font-bold text-gray-700 shadow-sm rounded-xl border-gray-300 cursor-pointer w-full custom-select-arrow">
                                <option value="">Semua Kategori</option>
                                @foreach($allKategori as $kat)
                                    <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative w-36 mt-2 md:mt-0">
                            <select id="statStatus" onchange="window.filterStationery()" class="form-input-modern py-1.5 px-3 text-xs font-bold text-gray-700 shadow-sm rounded-xl border-gray-300 cursor-pointer w-full custom-select-arrow">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

<div class="relative w-52">
                            <input type="text" id="statSearch" oninput="window.filterStationery()" placeholder="Cari Kode / Nama..." class="form-input-modern py-1.5 pr-3 pl-icon-modern text-xs border-gray-300 shadow-sm rounded-xl w-full">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-3 md:mt-0 items-center">
{{-- FORM HIDDEN UNTUK BULK DELETE --}}
                        <form id="formBulkDelete" action="{{ route('stationery.bulk-destroy') }}" method="POST" class="m-0 hidden">
                            @csrf
                            @method('DELETE')
                            </form>

                        {{-- FORM HIDDEN UNTUK UBAH STATUS BULK --}}
                        <form id="formBulkStatus" action="{{ route('stationery.bulk-status') }}" method="POST" class="m-0 hidden">
                            @csrf
                            @method('PUT')
                        </form>

                        {{-- FORM HIDDEN UNTUK HAPUS SEMUA (TRUNCATE) --}}
                        <form id="formTruncate" action="{{ route('stationery.truncate') }}" method="POST" class="m-0 hidden">
                            @csrf
                            @method('DELETE')
                        </form>

                        {{-- TOMBOL MODE PILIH (Untuk memunculkan checkbox) --}}
                        <button type="button" id="btnTogglePilih" onclick="window.toggleModePilih()" class="bg-gray-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-700 transition shadow-md flex items-center border-none">
                            <i class="fas fa-tasks mr-2 text-sm" id="ikonTogglePilih"></i> <span id="teksTogglePilih">Pilih Data</span>
                        </button>

{{-- TOMBOL HAPUS BULK (Dinamis: Awalnya Mati/Abu-abu Gelap agar terlihat seperti tombol) --}}
                        <button type="button" id="btnHapus" onclick="window.prosesHapus()" disabled class="bg-gray-500 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition flex items-center shadow-md cursor-not-allowed border-none">
                            <i class="fas fa-trash-alt mr-2 text-sm"></i> <span id="teksHapus">Hapus Terpilih</span>
                        </button>

                        {{-- TOMBOL UBAH STATUS BULK --}}
                        <button type="button" id="btnBulkStatus" onclick="window.prosesBulkStatus()" disabled class="bg-gray-500 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition flex items-center shadow-md cursor-not-allowed border-none">
                            <i class="fas fa-toggle-on mr-2 text-sm"></i> <span id="teksBulkStatus">Ubah Status</span>
                        </button>

                        <button onclick="downloadExcelLaporan()" class="bg-green-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-700 transition shadow-md flex items-center border-none">
                            <i class="fas fa-file-excel mr-2 text-sm"></i> Excel
                        </button>

                        <button onclick="downloadPDFMaster()" class="bg-red-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition shadow-md flex items-center border-none">
                            <i class="fas fa-file-pdf mr-2 text-sm"></i> PDF
                        </button>
                    </div>
                </div>

                <div class="table-scroll-container flex-1 mx-6 mb-6 mt-4 border border-gray-200 rounded-xl bg-white shadow-sm min-h-0">
                    <table id="stationeryTable" class="w-full text-left border-collapse table-bordered whitespace-nowrap min-w-max">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-[10px] uppercase tracking-wider">
                                {{-- KOTAK CENTANG MASTER (Dihilangkan class sticky-nya agar tak bertabrakan warna putih) --}}
                                <th class="p-3 font-black text-center w-8 selection-col hidden">
                                    <input type="checkbox" id="checkAllMaster" onclick="window.toggleSemuaCheckbox(this)" class="checkbox-custom">
                                </th>
                                <th class="p-3 font-black text-center w-10">No</th>
                                <th class="p-3 font-black text-center">Tanggal</th>
                                <th class="p-3 font-black text-blue-600">Kode</th>
                                <th class="p-3 font-black">Material Name</th>
                                <th class="p-3 font-black">Kategori</th>
                                <th class="p-3 font-black text-center">Satuan</th>
                                <th class="p-3 font-black text-center text-blue-600 bg-blue-50">Stok Awal</th>
                                <th class="p-3 font-black text-center text-green-600 bg-green-50">Masuk</th>
                                <th class="p-3 font-black text-center text-red-600 bg-red-50">Keluar</th>

                                <th class="p-3 font-black text-center">Rata2 Pakai</th>
                                <th class="p-3 font-black text-center text-purple-700 bg-purple-50">Paling Banyak</th>

                                <th class="p-3 font-black text-center">Lead Time</th>
                                <th class="p-3 font-black text-center">Safety Stok</th>

                                <th class="p-3 font-black text-center text-gray-800 bg-gray-200">Ending Stock</th>
                                <th class="p-3 font-black text-center">Status Barang</th>
                                <th class="p-3 font-black text-center">Indikator</th>
                                <th class="p-3 font-black text-right">Harga Unit</th>
                                <th class="p-3 font-black text-center action-col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="stationeryTableBody">
                            @forelse($orderBarang as $index => $item)
                            @php
                                $statusColor = 'bg-green-100 text-green-800';
                                $statusBadge = '<span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[9px] uppercase tracking-widest shadow-sm font-black"><i class="fas fa-check-circle mr-1"></i> AMAN</span>';
                                if ($item->total_stok <= 0) { $statusColor = 'bg-red-200 text-red-900 animate-pulse'; $statusBadge = '<span class="bg-red-600 text-white px-2 py-1 rounded text-[9px] uppercase tracking-widest font-black shadow-sm"><i class="fas fa-times-circle mr-1"></i> KOSONG</span>'; }
                                elseif ($item->total_stok <= $item->min_stok) { $statusColor = 'bg-red-100 text-red-800 font-bold'; $statusBadge = '<span class="bg-red-100 text-red-700 px-2 py-1 rounded text-[9px] uppercase tracking-widest font-black shadow-sm shadow-red-200 border border-red-300"><i class="fas fa-exclamation-triangle mr-1"></i> ORDER</span>'; }
                            @endphp
<tr class="stat-row text-xs font-bold text-gray-700 hover:bg-gray-50 transition border-b border-gray-100 bg-white"
    data-kategori="{{ strtolower($item->kategori) }}"
    data-tanggal="{{ $item->updated_at->format('Y-m-d') }}"
    data-nama="{{ strtolower($item->kode . ' ' . $item->nama_barang) }}"
    data-status="{{ (isset($item->is_active) && ($item->is_active == '0' || $item->is_active === false || $item->is_active === 'Tidak Aktif')) ? '0' : '1' }}">
                                {{-- KOTAK CENTANG INDIVIDU --}}
                                <td class="p-3 text-center selection-col hidden">
                                    <input type="checkbox" class="checkbox-custom checkbox-item" value="{{ $item->id }}" onclick="window.perbaruiStatusCentang()">
                                </td>
                                <td class="p-3 text-center row-number">{{ $index + 1 }}</td>
                                <td class="p-3 text-center text-gray-500">{{ $item->updated_at->format('Y-m-d') }}</td>
                                <td class="p-3 text-blue-600">{{ $item->kode }}</td>
                                <td class="p-3">{{ $item->nama_barang }}</td>
                                <td class="p-3 text-gray-500">{{ $item->kategori }}</td>
                                <td class="p-3 text-center">{{ $item->satuan }}</td>
                                <td class="p-3 text-center bg-blue-50/30">{{ $item->stok_awal }}</td>
                                <td class="p-3 text-center bg-green-50/30">{{ $item->stok_masuk }}</td>
                                <td class="p-3 text-center bg-red-50/30">{{ $item->stok_keluar }}</td>

                                <td class="p-3 text-center font-bold text-blue-700">{{ $item->rata_penggunaan }} <span class="text-[10px] text-gray-500 font-normal uppercase">{{ $item->satuan }}/Hari</span></td>
                                <td class="p-3 text-center font-bold text-purple-600">{{ $item->pemakaian_terbanyak }}</td>

                                <td class="p-3 text-center">{{ $item->lead_time ?? 0 }} Hr</td>
                                <td class="p-3 text-center">{{ $item->safety_stock ?? 0 }}</td>

                                <td class="p-3 text-center font-black text-sm {{ $statusColor }}">{{ $item->total_stok }}</td>

                                {{-- KOLOM BARU: STATUS AKTIF / TIDAK AKTIF --}}
                                <td class="p-3 text-center">
                                    @if(!isset($item->is_active) || $item->is_active == 1 || $item->is_active === true || $item->is_active === 'Aktif')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[9px] font-black uppercase shadow-sm border border-green-200">Aktif</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-[9px] font-black uppercase shadow-sm border border-red-200">Tidak Aktif</span>
                                    @endif
                                </td>

                                <td class="p-3 text-center">{!! $statusBadge !!}</td>
                                <td class="p-3 text-right text-gray-500">Rp {{ number_format($item->harga_unit, 0, ',', '.') }}</td>
                                <td class="p-3 text-center flex justify-center gap-2 action-col">
                                    <button onclick="openEditStationeryModal('{{ $item->id }}', '{{ addslashes($item->kode) }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->kategori }}', '{{ $item->satuan }}', '{{ $item->stok_awal }}', '{{ $item->harga_unit }}', '{{ $item->min_stok }}', '{{ $item->avg_penggunaan }}', '{{ $item->lead_time }}', '{{ $item->safety_stock }}', '{{ $item->is_active ?? 1 }}')" class="text-blue-400 hover:text-blue-600 px-1" title="Edit"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('stationery.destroy', $item->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus master barang ini secara permanen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 px-1" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow"><td colspan="19" class="p-6 text-center text-gray-400 text-xs italic">Data masih kosong.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 2: TRANSAKSI (IN/OUT) --}}
        <div id="tab-transaksi-stationery" class="tab-content hidden flex-1 flex-col gap-6 min-h-0">
            <div class="flex flex-wrap justify-between items-center glass-card p-5 gap-4 shrink-0">
                <div>
                    <h3 class="text-xs font-black text-slate-600 uppercase tracking-widest">Riwayat Transaksi (In/Out)</h3>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Pusat pencatatan barang masuk ke gudang dan pemakaian keluar</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                    <div class="flex items-center gap-2 mr-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200">
                        <div class="relative w-32">
                            <label class="absolute -top-2 left-3 bg-white px-1 text-[9px] font-black text-blue-500 uppercase tracking-wider rounded-md">Mulai Tgl</label>
                            <input type="date" id="trxFilterMulai" onchange="filterTransaksiTable()" class="form-input-modern py-1.5 px-3 text-xs font-bold text-gray-700 border-gray-300 shadow-sm rounded-xl cursor-pointer w-full">
                        </div>
                        <div class="relative w-32">
                            <label class="absolute -top-2 left-3 bg-white px-1 text-[9px] font-black text-blue-500 uppercase tracking-wider rounded-md">Sampai Tgl</label>
                            <input type="date" id="trxFilterAkhir" onchange="filterTransaksiTable()" class="form-input-modern py-1.5 px-3 text-xs font-bold text-gray-700 border-gray-300 shadow-sm rounded-xl cursor-pointer w-full">
                        </div>
                    </div>

                    <button onclick="document.getElementById('modalImportExcel').classList.remove('hidden')" class="bg-indigo-600 text-white px-6 py-3 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-indigo-700 shadow-md transition transform hover:-translate-y-0.5 border border-indigo-700">
                        <i class="fas fa-file-import mr-2"></i> Import Global
                    </button>

                    <button onclick="document.getElementById('modalBarangMasukGabungan').classList.remove('hidden'); switchMasukTab('restock');" class="bg-green-600 text-white px-6 py-3 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5 border border-green-700">
                        <i class="fas fa-arrow-down mr-2"></i> Input Masuk
                    </button>
                    <button onclick="document.getElementById('modalBarangKeluar').classList.remove('hidden'); switchKeluarTab('manual'); checkStokOut();" class="bg-red-600 text-white px-6 py-3 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 shadow-md transition transform hover:-translate-y-0.5 border border-red-800">
                        <i class="fas fa-arrow-up mr-2"></i> Input Keluar
                    </button>
                </div>
            </div>

            <div class="glass-card flex-1 flex flex-col p-6 min-h-0">
                <div class="table-scroll-container flex-1 border border-gray-200 rounded-xl bg-white shadow-sm min-h-0">
                    <table class="w-full text-left border-collapse table-bordered whitespace-nowrap min-w-max">
                        <thead class="bg-slate-100 text-slate-600 text-[10px] uppercase tracking-wider sticky top-0 z-10 font-black">
                            <tr>
                                <th class="p-4 text-center w-12">No</th>
                                <th class="p-4 text-center">Tgl Input</th>
                                <th class="p-4 text-center">Jenis</th>
                                <th class="p-4">Kode & Nama Barang</th>
                                <th class="p-4 text-center">Jumlah</th>
                                <th class="p-4">Detail (Dept/Vendor/Ket)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $index => $trx)
                            <tr class="trx-row text-xs font-bold text-slate-700 border-b border-gray-100 hover:bg-blue-50/30 transition" data-tanggal="{{ \Carbon\Carbon::parse($trx->tanggal)->format('Y-m-d') }}">
                                <td class="p-4 text-center text-gray-400 trx-row-number">{{ $index + 1 }}</td>
                                <td class="p-4 text-center">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}</td>
                                <td class="p-4 text-center">
                                    @if(strtolower($trx->type) == 'masuk')
                                        <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-[9px] uppercase font-black shadow-sm border border-green-200"><i class="fas fa-arrow-down mr-1"></i> Masuk</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-[9px] uppercase font-black shadow-sm border border-red-200"><i class="fas fa-arrow-up mr-1"></i> Keluar</span>
                                    @endif
                                </td>

                                <td class="p-4">
                                    <span class="text-blue-700 font-black bg-blue-50 px-2 py-1 rounded-md">{{ $trx->stationery->kode ?? 'N/A' }}</span> <span class="ml-2">{{ $trx->stationery->nama_barang ?? 'Barang Dihapus' }}</span>
                                    @if(strtolower($trx->type) == 'keluar')
                                        <span class="block text-[10px] text-slate-500 font-normal mt-1.5">
                                            <i class="fas fa-user-tag text-red-500 mr-1"></i> Pemakai: <span class="font-bold text-slate-700">{{ !empty($trx->dept) ? $trx->dept : '-' }}</span>
                                        </span>
                                    @endif
                                </td>

                                <td class="p-4 text-center text-sm font-black {{ strtolower($trx->type) == 'masuk' ? 'text-green-600' : 'text-red-600' }}">{{ $trx->jumlah }}</td>
                                <td class="p-4 text-[10px] text-gray-500 whitespace-normal min-w-[250px] leading-relaxed">
                                    @if(strtolower($trx->type) == 'masuk')
                                        <span class="font-bold text-gray-600">Vendor:</span> {{ !empty($trx->vendor) ? $trx->vendor : '-' }} <br> <span class="font-bold text-gray-600">Dok:</span> {{ !empty($trx->no_dokumen) ? $trx->no_dokumen : '-' }}
                                    @else
                                        <span class="font-bold text-gray-600">Dept:</span> {{ !empty($trx->dept) ? $trx->dept : '-' }} | <span class="font-bold text-gray-600">FPB:</span> {{ !empty($trx->no_fpb) ? $trx->no_fpb : '-' }} <br> <span class="font-bold text-gray-600">Ket:</span> {{ !empty($trx->keterangan) ? $trx->keterangan : '-' }}
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="p-8 text-center text-gray-400 italic text-xs">Belum ada riwayat transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 3: LAPORAN EXPORT (SUB-TAB + FITUR PENCARIAN) --}}
        <div id="tab-laporan-stationery" class="tab-content hidden flex-1 flex-col gap-6 min-h-0">

            {{-- HEADER LAPORAN (SUB-TAB & PENCARIAN) --}}
            <div class="glass-card flex-wrap justify-between items-center p-5 flex shrink-0 gap-4">
                <div>
                    <h3 class="text-xs font-black text-slate-600 uppercase tracking-widest">Laporan & Rekap Stok</h3>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Pusat data pengadaan barang dan riwayat pemakaian</p>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">

                    <div class="relative w-36">
                        <label class="absolute -top-2 left-3 bg-white px-1 text-[9px] font-black text-blue-500 uppercase tracking-wider rounded-md">Mulai Tgl</label>
                        <input type="date" id="lapFilterMulai" onchange="filterLaporanTables()" class="form-input-modern py-1.5 px-3 text-xs font-bold text-gray-700 border-gray-300 shadow-sm rounded-xl cursor-pointer w-full">
                    </div>

                    <div class="relative w-36">
                        <label class="absolute -top-2 left-3 bg-white px-1 text-[9px] font-black text-blue-500 uppercase tracking-wider rounded-md">Sampai Tgl</label>
                        <input type="date" id="lapFilterAkhir" onchange="filterLaporanTables()" class="form-input-modern py-1.5 px-3 text-xs font-bold text-gray-700 border-gray-300 shadow-sm rounded-xl cursor-pointer w-full">
                    </div>

                    {{-- Pencarian Cepat --}}
                    <div class="relative w-52">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="laporanGlobalSearch" oninput="filterLaporanTables()" class="w-full py-1.5 pr-3 pl-icon-modern bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-800 focus:border-blue-500 outline-none transition shadow-sm" placeholder="Cari data...">
                    </div>

                    {{-- Sub-Tab Toggle --}}
                    <div class="flex p-1.5 gap-1.5 bg-gray-100 rounded-xl border border-gray-200 shrink-0">
                        <button type="button" id="btnTabLaporanOrder" onclick="switchLaporan('order')" class="px-5 py-2 rounded-lg bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition">
                            <i class="fas fa-box-open mr-1.5"></i> Order & Stok
                        </button>
                        <button type="button" id="btnTabLaporanPemakaian" onclick="switchLaporan('pemakaian')" class="px-5 py-2 rounded-lg bg-transparent text-gray-500 hover:text-gray-700 text-[10px] font-black uppercase tracking-widest transition">
                            <i class="fas fa-dolly mr-1.5"></i> Riwayat Pemakaian
                        </button>
                    </div>
                </div>
            </div>

            {{-- BUNGKUS KEDUA TABEL AGAR BISA DI-TOGGLE --}}
            <div class="glass-card flex-1 overflow-hidden flex flex-col shadow-md rounded-xl">

                {{-- SUB-MODUL 1: ORDER BARANG (Default Tampil) --}}
                <div id="container-laporan-order" class="flex-1 flex flex-col overflow-hidden min-h-0">
                    <div class="px-6 py-4 border-b border-gray-100 bg-blue-50/50 flex flex-wrap justify-between items-center gap-4 shrink-0">
                        <div>
                            <h3 class="text-sm font-black text-blue-800 uppercase tracking-widest">Tabel Order Barang</h3>
                            <p class="text-[10px] font-bold text-gray-500 mt-1 tracking-wide">Pantauan sisa stok dan saran re-order</p>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <select id="lapFilterStatus" onchange="filterLaporanTables()" class="py-2 px-3 text-xs font-bold text-blue-800 border border-blue-300 shadow-sm rounded-lg cursor-pointer bg-white outline-none focus:ring-2 focus:ring-blue-500 transition h-full custom-select-arrow">
                                <option value="">-- Semua Status --</option>
                                <option value="perlu order">⚠️ Perlu Order</option>
                                <option value="aman">✅ Aman</option>
                            </select>
                            <button type="button" onclick="exportLaporanData('order')" class="bg-green-600 text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-green-700 transition shadow-md flex items-center border-none cursor-pointer h-full">
                                <i class="fas fa-file-excel mr-2 text-sm"></i> Excel
                            </button>
                            <button type="button" onclick="exportLaporanPDF('order')" class="bg-red-600 text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-red-700 transition shadow-md flex items-center border-none cursor-pointer h-full ml-2">
                                <i class="fas fa-file-pdf mr-2 text-sm"></i> PDF
                            </button>
                        </div>
                    </div>
                    <div class="table-scroll-container flex-1 m-4 border border-gray-200 rounded-xl bg-white shadow-sm">
                        <table class="w-full text-left border-collapse table-bordered whitespace-nowrap min-w-max">
                            <thead class="bg-blue-50 text-blue-800 text-[10px] uppercase tracking-wider font-black border-b-2 border-blue-200 sticky top-0 z-10">
                                <tr>
                                    <th class="p-3 text-center">Barang</th>
                                    <th class="p-3 text-center">Stock Now</th>
                                    <th class="p-3">Rata-rata penggunaan / hari & siapa penggunanya</th>
                                    <th class="p-3 text-center">Kapan terakhir order</th>
<th class="p-3 text-center">Kebutuhan / Reorder</th>
<th class="p-3 text-center">Aksi</th> <!-- TAMBAHAN BARU -->
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($orderBarang as $item)
<tr class="row-lap-order text-xs font-bold text-gray-700 hover:bg-blue-50/50 transition border-b border-gray-100" data-tanggal="{{ $item->terakhir_order ? \Carbon\Carbon::parse($item->terakhir_order)->format('Y-m-d') : '' }}" data-status="{{ strtolower($item->status_reorder) }}" data-pencarian="{{ strtolower($item->kode . ' ' . $item->nama_barang) }}">
                                    <td class="p-3 text-blue-700">{{ $item->kode }} - {{ $item->nama_barang }}</td>
                                    <td class="p-3 text-center text-lg {{ $item->total_stok <= $item->min_stok ? 'text-red-600' : 'text-green-600' }}">{{ $item->total_stok }}</td>

                                    <td class="p-3 text-gray-600">
                                        <span class="text-purple-600 font-black">{{ $item->rata_penggunaan }} {{ $item->satuan }}</span> / Hari
                                        <span class="text-[10px] text-gray-400 ml-1">(Berdasarkan data 30 hari terakhir)</span><br>
                                        <span class="inline-flex items-center mt-1.5 text-[9px] font-black text-blue-700 bg-blue-50 px-2 py-1 rounded-md border border-blue-200 tracking-wide uppercase shadow-sm">
                                            <i class="fas fa-user-tag mr-1"></i> Pemakai: {{ $item->siapa_pengguna }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center text-gray-500">
                                        {{ $item->terakhir_order ? \Carbon\Carbon::parse($item->terakhir_order)->format('d M Y') : 'Belum pernah order' }}
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($item->status_reorder == 'Perlu order')
                                            <span class="bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-[9px] uppercase font-black shadow-sm border border-red-200 animate-pulse"><i class="fas fa-exclamation-triangle mr-1"></i> Perlu Order</span>
                                        @else
                                            <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-[9px] uppercase font-black shadow-sm border border-green-200"><i class="fas fa-check mr-1"></i> Aman</span>
                                        @endif
                                    </td>

                                    {{-- TAMBAHAN BARU: KOLOM AKSI --}}
                                    <td class="p-3 text-center flex justify-center gap-2 items-center">
                                        {{-- Tombol Edit (Menggunakan Modal Master Barang) --}}
                                        <button onclick="openEditStationeryModal('{{ $item->id }}', '{{ addslashes($item->kode) }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->kategori }}', '{{ $item->satuan }}', '{{ $item->stok_awal }}', '{{ $item->harga_unit }}', '{{ $item->min_stok }}', '{{ $item->avg_penggunaan }}', '{{ $item->lead_time }}', '{{ $item->safety_stock }}', '{{ $item->is_active ?? 1 }}')" class="text-blue-500 hover:text-blue-700 px-1.5 transition-colors" title="Edit">
                                            <i class="fas fa-edit text-[14px]"></i>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('stationery.destroy', $item->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus master barang ini secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 px-1.5 transition-colors" title="Hapus">
                                                <i class="fas fa-trash-alt text-[14px]"></i>
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SUB-MODUL 2: PEMAKAIAN BARANG (Default Sembunyi) --}}
                <div id="container-laporan-pemakaian" class="hidden flex-1 flex-col overflow-hidden min-h-0">
                    <div class="px-6 py-4 border-b border-gray-100 bg-red-50/50 flex flex-wrap justify-between items-center gap-4 shrink-0">
                        <div>
                            <h3 class="text-sm font-black text-red-800 uppercase tracking-widest">Tabel Pemakaian Barang Keluar</h3>
                            <p class="text-[10px] font-bold text-gray-500 mt-1 tracking-wide">Catatan riwayat pengambilan material oleh departemen</p>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <select id="lapFilterKategori" onchange="filterLaporanTables()" class="py-2 px-3 text-xs font-bold text-red-800 border border-red-300 shadow-sm rounded-lg cursor-pointer bg-white outline-none focus:ring-2 focus:ring-red-500 transition h-full custom-select-arrow">
                                <option value="">-- Semua Kategori --</option>
                                @foreach($allKategori as $kat)
                                    <option value="{{ strtolower($kat) }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                            <select id="lapFilterDept" onchange="filterLaporanTables()" class="py-2 px-3 text-xs font-bold text-red-800 border border-red-300 shadow-sm rounded-lg cursor-pointer bg-white outline-none focus:ring-2 focus:ring-red-500 transition h-full custom-select-arrow">
                                <option value="">-- Semua Dept --</option>
                                @php
                                    $listDeptLaporan = array_filter(array_unique($pemakaianBarang->pluck('dept')->toArray()));
                                    sort($listDeptLaporan);
                                @endphp
                                @foreach($listDeptLaporan as $d)
                                    <option value="{{ strtolower($d) }}">{{ strtoupper($d) }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="exportLaporanData('pemakaian')" class="bg-green-600 text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-green-700 transition shadow-md flex items-center border-none cursor-pointer h-full">
                                <i class="fas fa-file-excel mr-2 text-sm"></i> Excel
                            </button>
                            <button type="button" onclick="exportLaporanPDF('pemakaian')" class="bg-red-600 text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-red-700 transition shadow-md flex items-center border-none cursor-pointer h-full ml-2">
                                <i class="fas fa-file-pdf mr-2 text-sm"></i> PDF
                            </button>
                        </div>
                    </div>
                    <div class="table-scroll-container flex-1 m-4 border border-gray-200 rounded-xl bg-white shadow-sm">
                        <table class="w-full text-left border-collapse table-bordered whitespace-nowrap min-w-max">
                            <thead class="bg-red-50 text-red-800 text-[10px] uppercase tracking-wider font-black border-b-2 border-red-200 sticky top-0 z-10">
                                <tr>
                                    <th class="p-3 text-center">No PR (FPB)</th>
                                    <th class="p-3 text-center">Tanggal PR</th>
                                    <th class="p-3 text-center">Kode Barang</th>
                                    <th class="p-3">Nama Material & Pemakai</th>
                                    <th class="p-3 text-center">Jumlah</th>
                                    <th class="p-3 text-center">Aksi</th> <!-- TAMBAHAN -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pemakaianBarang as $trx)
<tr class="row-lap-pemakaian text-xs font-bold text-gray-700 hover:bg-red-50/50 transition border-b border-gray-100" data-tanggal="{{ \Carbon\Carbon::parse($trx->tanggal)->format('Y-m-d') }}" data-dept="{{ strtolower($trx->dept) }}" data-kategori="{{ strtolower($trx->stationery->kategori ?? '') }}" data-pencarian="{{ strtolower(($trx->stationery->kode ?? '') . ' ' . ($trx->stationery->nama_barang ?? '')) }}">
                                    <td class="p-3 text-center font-black text-gray-600">{{ !empty($trx->no_fpb) ? $trx->no_fpb : 'PR-' . $trx->id }}</td>
                                    <td class="p-3 text-center text-gray-500">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d-m-Y') }}</td>
                                    <td class="p-3 text-center text-blue-600">{{ $trx->stationery->kode ?? '-' }}</td>
                                    <td class="p-3">
                                        {{ $trx->stationery->nama_barang ?? '-' }}
                                        <span class="block text-[10px] text-slate-500 font-normal mt-1">
                                            <i class="fas fa-user-tag text-red-400 mr-1"></i> Pemakai: <span class="font-bold text-gray-700">{{ !empty($trx->dept) ? $trx->dept : '-' }}</span>
                                        </span>
                                    </td>
                                    <td class="p-3 text-center font-black text-red-600 text-sm">{{ $trx->jumlah }} <span class="text-xs text-gray-500">{{ $trx->stationery->satuan ?? '' }}</span></td>
                                    <td class="p-3 text-center flex justify-center items-center gap-2">
                                        {{-- Tombol Edit --}}
                                        <button type="button" onclick="openEditPemakaianModal('{{ $trx->id }}', '{{ $trx->stationery_id }}', '{{ \Carbon\Carbon::parse($trx->tanggal)->format('Y-m-d') }}', '{{ $trx->jumlah }}', '{{ addslashes($trx->no_fpb) }}', '{{ addslashes($trx->dept) }}', '{{ addslashes($trx->keterangan) }}')" class="text-blue-500 hover:text-blue-700 px-1.5 transition-colors" title="Edit Pemakaian">
                                            <i class="fas fa-edit text-[14px]"></i>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('stationery.transaction.destroy', $trx->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus riwayat pemakaian ini? Stok barang akan otomatis dikembalikan ke gudang.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 px-1.5 transition-colors" title="Hapus Pemakaian">
                                                <i class="fas fa-trash-alt text-[14px]"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- TAB 4: DATA PENGADAAN (PO/PR) --}}
        <div id="tab-pengadaan-stationery" class="tab-content hidden flex-1 flex-col gap-6 min-h-0">

            {{-- NOTIFIKASI STATUS MENUNGGU BARANG (Dipindah ke paling atas) --}}
            @if($pengadaanMenunggu->count() > 0)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-xl shadow-sm flex items-start gap-4 shrink-0">
                <div class="bg-blue-100 p-2 rounded-lg text-blue-600 animate-pulse"><i class="fas fa-truck-loading text-xl"></i></div>
                <div class="flex-1">
                    <h4 class="text-xs font-black text-blue-800 uppercase tracking-widest">Informasi: Menunggu Barang Datang</h4>
                    <p class="text-[11px] text-blue-700 mt-1 font-semibold">Terdapat <b class="text-lg">{{ $pengadaanMenunggu->count() }}</b> pesanan barang (PO/PR) yang berstatus sedang menunggu kedatangan dari vendor.</p>
                </div>
            </div>
            @endif

            <div class="flex flex-wrap justify-between items-center glass-card p-5 gap-4 shrink-0">
                <div>
                    <h3 class="text-xs font-black text-slate-600 uppercase tracking-widest">Data Pengadaan (PO/PR)</h3>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Buku catatan pemesanan barang ke vendor</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1"><i class="fas fa-filter text-blue-500 mr-1"></i>Filter:</p>

                    <div class="relative w-36">
                        <label class="absolute -top-2 left-3 bg-white px-1 text-[9px] font-black text-blue-500 uppercase tracking-wider rounded-md">Mulai Tgl</label>
                        <input type="date" id="pengadaanFilterMulai" onchange="filterPengadaanTable()" class="form-input-modern text-xs py-1.5 px-3 font-bold text-gray-700 shadow-sm cursor-pointer border border-gray-300 rounded-xl w-full">
                    </div>

                    <div class="relative w-36">
                        <label class="absolute -top-2 left-3 bg-white px-1 text-[9px] font-black text-blue-500 uppercase tracking-wider rounded-md">Sampai Tgl</label>
                        <input type="date" id="pengadaanFilterAkhir" onchange="filterPengadaanTable()" class="form-input-modern text-xs py-1.5 px-3 font-bold text-gray-700 shadow-sm cursor-pointer border border-gray-300 rounded-xl w-full">
                    </div>

                    {{-- Pencarian Cepat Pengadaan --}}
                    <div class="relative w-48">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchPengadaan" oninput="filterPengadaanTable()" class="w-full py-1.5 pr-3 pl-icon-modern bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-800 focus:border-blue-500 outline-none transition shadow-sm" placeholder="Cari Kode/Nama...">
                    </div>

<div class="flex gap-2">
    <button onclick="downloadExcelPengadaan()" class="bg-green-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5 border border-green-800 flex items-center">
        <i class="fas fa-file-excel mr-2"></i> Export Excel
    </button>
    <button onclick="downloadPDFPengadaan()" class="bg-red-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-red-700 shadow-md transition transform hover:-translate-y-0.5 border border-red-800 flex items-center">
        <i class="fas fa-file-pdf mr-2"></i> Export PDF
    </button>
</div>
                </div>
            </div>

            <div class="glass-card flex-1 flex flex-col p-6 min-h-0">
                <div class="table-scroll-container flex-1 border border-gray-200 rounded-xl bg-white shadow-sm min-h-0">
                    <table class="w-full text-left border-collapse table-bordered whitespace-nowrap min-w-max">
                        <thead class="bg-slate-100 text-slate-600 text-[10px] uppercase tracking-wider sticky top-0 z-10 font-black">
                            <tr>
                                <th class="p-4 text-center w-12">No</th>
                                <th class="p-4 text-center">Tgl PR</th>
                                <th class="p-4 text-center">No. PR</th>
                                <th class="p-4">Kode & Nama Barang</th>
                                <th class="p-4 text-center">Qty</th>
                                <th class="p-4 text-center">No. PO</th>
                                <th class="p-4 text-center">Status</th>

                                {{-- KOLOM BARU: ESTIMASI TIBA --}}
                                <th class="p-4 text-center">Estimasi Tiba</th>

                                {{-- KOLOM BARU: AKSI --}}
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengadaanTableBody">
                            @forelse($dataPengadaan as $index => $p)
                            <tr class="pengadaan-row text-xs font-bold text-slate-700 border-b border-gray-100 hover:bg-blue-50/30 transition" data-tanggal="{{ $p->tgl_pr }}">
                                <td class="p-row-number p-4 text-center text-gray-400">{{ $index + 1 }}</td>
                                <td class="p-4 text-center">{{ \Carbon\Carbon::parse($p->tgl_pr)->format('d/m/Y') }}</td>
                                <td class="p-4 text-center text-blue-700 font-black">{{ $p->no_pr }}</td>
                                <td class="p-4">
                                    <span class="text-blue-700 font-black bg-blue-50 px-2 py-1 rounded-md">{{ $p->stationery->kode ?? 'N/A' }}</span>
                                    <span class="ml-2">{{ $p->stationery->nama_barang ?? 'Barang Dihapus' }}</span>
                                </td>
                                <td class="p-4 text-center text-sm font-black text-purple-600">{{ $p->qty_item }}</td>
                                <td class="p-4 text-center font-black">{{ $p->no_po ?? '-' }}</td>
                                <td class="p-4 text-center">
                                    @if($p->status == 'Menunggu')
                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg text-[9px] uppercase font-black shadow-sm border border-yellow-200 animate-pulse"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                                    @else
                                        <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-[9px] uppercase font-black shadow-sm border border-green-200"><i class="fas fa-check mr-1"></i> Selesai</span>
                                    @endif
                                </td>

                                {{-- KOLOM BARU: INDIKATOR ESTIMASI TIBA REAL-TIME --}}
                                @php
                                    $tglEstimasi = $p->estimasi_tiba ? \Carbon\Carbon::parse($p->estimasi_tiba)->startOfDay() : null;
                                    $hariIni = \Carbon\Carbon::now()->startOfDay();
                                    $badgeEstimasi = '-';

                                    if ($tglEstimasi && $p->status == 'Menunggu') {
                                        if ($hariIni->gt($tglEstimasi)) {
                                            $badgeEstimasi = '<span class="bg-red-100 text-red-700 px-2 py-1 rounded-md text-[9px] uppercase font-black border border-red-200 animate-pulse"><i class="fas fa-exclamation-triangle mr-1"></i> Terlambat</span>';
                                        } elseif ($hariIni->eq($tglEstimasi)) {
                                            $badgeEstimasi = '<span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-md text-[9px] uppercase font-black border border-yellow-200"><i class="fas fa-bell mr-1"></i> Hari Ini</span>';
                                        } else {
                                            $badgeEstimasi = '<span class="bg-green-100 text-green-700 px-2 py-1 rounded-md text-[9px] uppercase font-black border border-green-200"><i class="fas fa-truck mr-1"></i> On Track</span>';
                                        }
                                        $badgeEstimasi .= '<br><span class="text-[10px] text-gray-500 mt-1 block font-bold">'. $tglEstimasi->format('d/m/Y') .'</span>';
                                    } elseif ($p->status == 'Selesai') {
                                        $badgeEstimasi = '<span class="text-gray-400 text-[10px] font-bold"><i class="fas fa-check-double"></i> Tiba di Gudang</span>';
                                    }
                                @endphp
                                <td class="p-4 text-center">{!! $badgeEstimasi !!}</td>

                                {{-- KOLOM BARU: TOMBOL AKSI CRUD --}}
                                <td class="p-4 text-center flex justify-center items-center gap-2">
                                    @if($p->status == 'Menunggu')
                                        <form action="{{ route('pengadaan.status', $p->id) }}" method="POST" onsubmit="return confirm('✅ Validasi Barang Tiba?\n\nSistem akan otomatis menghitung Lead Time (dari Tanggal PR hingga hari ini) dan menyimpannya ke Master Barang.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="bg-green-600 text-white px-3 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5 border border-green-800" title="Validasi & Update Lead Time">
                                                <i class="fas fa-check-double mr-1"></i> Tiba
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-[10px] italic font-bold mr-2"><i class="fas fa-check-circle text-green-500"></i> TUNTAS</span>
                                    @endif

                                    <button onclick="openEditPengadaanModal('{{ $p->id }}', '{{ $p->stationery_id }}', '{{ addslashes($p->no_pr) }}', '{{ \Carbon\Carbon::parse($p->tgl_pr)->format('Y-m-d') }}', '{{ $p->estimasi_tiba ? \Carbon\Carbon::parse($p->estimasi_tiba)->format('Y-m-d') : '' }}', '{{ $p->qty_item }}', '{{ addslashes($p->no_po) }}')" class="text-blue-500 hover:text-blue-700 p-1 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('pengadaan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengadaan ini secara permanen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1 transition" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyPengadaan"><td colspan="9" class="p-8 text-center text-gray-400 italic text-xs">Belum ada data pengadaan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @endif

        @if($isSuperAdmin)
            @include('dashboard.users')
        @endif

    </div>
</main>

@include('dashboard.modals')

{{-- Include Modal Edit Pemakaian --}}
@include('Dashboard.modalEditPemakaian')

{{-- MODAL BARANG MASUK GABUNGAN DENGAN GRID SEMPURNA AGAR TIDAK MOTONG --}}
<div id="modalBarangMasukGabungan" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center transition-all duration-300">
    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden transform scale-100 border-t-4 border-green-600 flex flex-col max-h-[95vh]">

        {{-- HEADER MODAL --}}
        <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 text-green-600 p-2.5 rounded-xl shadow-inner"><i class="fas fa-box text-xl"></i></div>
                <div>
                    <h2 class="font-black text-base text-gray-800 uppercase tracking-widest">Input Barang Masuk</h2>
                    <p class="text-[10px] text-gray-500 mt-0.5 font-bold tracking-wide">Pilih salah satu metode pengisian di bawah</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalBarangMasukGabungan').classList.add('hidden')" class="text-gray-400 hover:text-red-600 bg-gray-100 hover:bg-red-50 border border-gray-200 rounded-xl p-2 transition-colors shadow-sm"><i class="fas fa-times"></i></button>
        </div>

        {{-- TAB TOGGLE MASUK --}}
        <div class="flex p-3 gap-2 bg-gray-100 border-b border-gray-200 shrink-0 overflow-x-auto">
            <button type="button" id="btnTabRestock" onclick="switchMasukTab('restock')" class="whitespace-nowrap flex-1 py-2 px-3 rounded-xl bg-green-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition border border-green-700">
                <i class="fas fa-boxes mr-1"></i> Stok Lama
            </button>
            <button type="button" id="btnTabBaru" onclick="switchMasukTab('baru')" class="whitespace-nowrap flex-1 py-2 px-3 rounded-xl bg-white border-2 border-gray-300 text-gray-600 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                <i class="fas fa-plus-circle mr-1"></i> Item Baru
            </button>
            <button type="button" id="btnTabImportMasuk" onclick="switchMasukTab('import')" class="whitespace-nowrap flex-1 py-2 px-3 rounded-xl bg-white border-2 border-gray-300 text-gray-600 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                <i class="fas fa-file-excel mr-1 text-green-600"></i> Import Excel
            </button>
        </div>

        {{-- BODY SCROLL --}}
        <div class="p-5 overflow-y-auto bg-gray-50 flex-1 custom-scrollbar">

            {{-- ========================================================================= --}}
            {{-- FORM 1: TAMBAH STOK BARANG LAMA --}}
            {{-- ========================================================================= --}}
            <form id="formRestockMasuk" action="{{ route('pengadaan.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    <div class="col-span-1 md:col-span-3">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Pilih Master Barang <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="stationery_id" id="select_restock_id" required class="w-full bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                                <option value=""></option>
                                @foreach($dataStationery->sortBy('nama_barang') as $item)
                                    @if(!isset($item->is_active) || $item->is_active == 1 || $item->is_active === true || $item->is_active === 'Aktif')
                                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama_barang }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Tanggal Masuk <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-200 border-r border-gray-300 rounded-l-lg text-gray-600"><i class="fas fa-calendar-day"></i></div>
                            <input type="date" name="tgl_pr" required value="{{ date('Y-m-d') }}" class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Estimasi Tiba <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-200 border-r border-gray-300 rounded-l-lg text-gray-600"><i class="fas fa-calendar-check"></i></div>
                            <input type="date" name="estimasi_tiba" required class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Jumlah Masuk <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-blue-100 border-r border-blue-300 rounded-l-lg text-blue-600"><i class="fas fa-layer-group"></i></div>
                            <input type="number" name="qty_item" required min="1" placeholder="0" class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-black text-blue-600 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Vendor / Supplier <span class="text-[9px] font-normal text-gray-400">(Opsional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-200 border-r border-gray-300 rounded-l-lg text-gray-600"><i class="fas fa-truck"></i></div>
                            <input type="text" name="vendor" placeholder="Nama Toko..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Nomor PO <span class="text-[9px] font-normal text-gray-400">(Opsional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-200 border-r border-gray-300 rounded-l-lg text-gray-600"><i class="fas fa-file-signature"></i></div>
                            <input type="text" name="no_po" placeholder="Nomor Surat Pesanan..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">No. Dokumen / SJ <span class="text-[9px] font-normal text-gray-400">(Opsional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-200 border-r border-gray-300 rounded-l-lg text-gray-600"><i class="fas fa-file-invoice"></i></div>
                            <input type="text" name="no_dokumen" placeholder="Nota / Invoice..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Nomor PR <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-200 border-r border-gray-300 rounded-l-lg text-gray-600"><i class="fas fa-file-alt"></i></div>
                            <input type="text" name="no_pr" required placeholder="Wajib Diisi..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>

                    {{-- KOTAK BIRU: AUTO HITUNG MINIMUM STOK --}}
                    <div class="col-span-1 md:col-span-3 bg-blue-50 border border-blue-200 rounded-xl p-3.5 mt-1 shadow-sm">
                        <h4 class="text-[10px] font-black text-blue-800 uppercase tracking-widest mb-2.5 border-b border-blue-200 pb-1.5 flex items-center">
                            <i class="fas fa-calculator mr-2 text-sm"></i> UPDATE AUTO-HITUNG STOK <span class="text-[9px] ml-2 font-normal italic text-blue-600">(Opsional)</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                            <div>
                                <label class="block text-[9px] font-extrabold text-blue-700 uppercase mb-1">Rata2 Pemakaian</label>
                                <input type="number" step="0.01" name="avg_penggunaan" placeholder="0.00" class="w-full py-1.5 px-3 bg-white border border-blue-300 rounded-md text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[9px] font-extrabold text-blue-700 uppercase mb-1">Lead Time (Hari)</label>
                                <input type="number" name="lead_time" placeholder="0" class="w-full py-1.5 px-3 bg-white border border-blue-300 rounded-md text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[9px] font-extrabold text-blue-700 uppercase mb-1">Safety Stock</label>
                                <input type="number" name="safety_stock" placeholder="0" class="w-full py-1.5 px-3 bg-white border border-blue-300 rounded-md text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-3 pt-2">
                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-900/30 hover:bg-blue-700 transition transform hover:-translate-y-0.5"><i class="fas fa-clock mr-2"></i> SIMPAN & TAHAN DI PENGADAAN</button>
                    </div>
                </div>
            </form>

            {{-- ========================================================================= --}}
            {{-- FORM 2: DAFTARKAN ITEM BARU --}}
            {{-- ========================================================================= --}}
            <form id="formMasterBaru" action="{{ route('stationery.store') }}" method="POST" class="hidden">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">

                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Kode <span class="text-red-500">*</span></label>
                        <div class="relative"><div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none"><i class="fas fa-barcode"></i></div><input type="text" name="kode" required placeholder="A01001" class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm"></div>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Nama Barang Baru <span class="text-red-500">*</span></label>
                        <div class="relative"><div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none"><i class="fas fa-tag"></i></div><input type="text" name="nama_barang" required placeholder="Nama lengkap barang..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm"></div>
                    </div>

                    {{-- HYBRID KATEGORI --}}
                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Kategori <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none z-10"><i class="fas fa-layer-group"></i></div>
                            <input type="text" name="kategori" list="datalist_kategori" required autocomplete="off" placeholder="Pilih / Ketik Kategori..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm h-[38px] custom-select-arrow">
                        </div>
                    </div>

                    {{-- HYBRID SATUAN --}}
                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Satuan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none z-10"><i class="fas fa-weight-hanging"></i></div>
                            <input type="text" name="satuan" list="datalist_satuan" required placeholder="Pilih / Ketik Satuan..." autocomplete="off" class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm h-[38px] custom-select-arrow">
                        </div>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Harga Satuan <span class="text-red-500">*</span></label>
                        <div class="relative"><div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 font-black text-[9px] pointer-events-none">Rp</div><input type="number" name="harga_unit" required min="0" value="0" class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm"></div>
                    </div>

                    {{-- BLOK JUMLAH AWAL (STOK) --}}
                    <div class="col-span-1 border border-blue-200 bg-blue-50 rounded-lg p-2.5 shadow-sm">
                        <label class="block text-[9px] font-extrabold text-blue-800 uppercase mb-1 tracking-wider">Jumlah Awal (Stok) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-8 flex items-center justify-center bg-blue-100 border-r border-blue-300 rounded-l-md text-blue-600 pointer-events-none"><i class="fas fa-box-open text-xs"></i></div>
                            <input type="number" name="stok_awal" required min="0" value="0" class="w-full pl-10 pr-2 py-1.5 bg-white border border-blue-300 rounded-md text-sm font-black text-blue-700 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Estimasi Tiba <span class="text-[9px] font-normal text-gray-400">(Awal)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none"><i class="fas fa-calendar-check"></i></div>
                            <input type="date" name="estimasi_tiba" class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                        </div>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Batas Min (Manual)</label>
                        <div class="relative"><div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-red-50 border-r border-red-300 rounded-l-lg text-red-500 pointer-events-none"><i class="fas fa-bell"></i></div><input type="number" name="min_stok" placeholder="Opsional" min="0" class="w-full pl-12 pr-3 py-2 bg-white border border-red-300 rounded-lg text-xs font-bold text-red-700 focus:ring-2 focus:ring-red-500 outline-none shadow-sm"></div>
                    </div>

                    {{-- KOTAK BIRU AUTO HITUNG --}}
                    <div class="col-span-1 md:col-span-3 bg-blue-50 border border-blue-200 rounded-xl p-3.5 mt-1 shadow-sm">
                        <h4 class="text-[10px] font-black text-blue-800 uppercase tracking-widest mb-2.5 border-b border-blue-200 pb-1.5 flex items-center">
                            <i class="fas fa-calculator mr-2 text-sm"></i> AUTO-HITUNG MINIMUM STOK (SAFETY STOCK)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                            <div>
                                <label class="block text-[9px] font-extrabold text-blue-700 uppercase mb-1">Rata2 Pemakaian</label>
                                <input type="number" step="0.01" name="avg_penggunaan" placeholder="Cth: 2.5" class="w-full py-1.5 px-3 bg-white border border-blue-300 rounded-md text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[9px] font-extrabold text-blue-700 uppercase mb-1">Lead Time (Hari)</label>
                                <input type="number" name="lead_time" placeholder="Cth: 5" class="w-full py-1.5 px-3 bg-white border border-blue-300 rounded-md text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[9px] font-extrabold text-blue-700 uppercase mb-1">Safety Stock</label>
                                <input type="number" name="safety_stock" placeholder="Cth: 10" class="w-full py-1.5 px-3 bg-white border border-blue-300 rounded-md text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                            </div>
                        </div>
                        <p class="text-[9px] text-blue-600 mt-2 font-medium italic">*Abaikan form ini jika ingin isi Min Stok manual di atas. Rumus: (Rata2 x Lead Time) + Safety Stock.</p>
                    </div>

                    {{-- INFORMASI PENGADAAN (OPSIONAL) --}}
                    <div class="col-span-1 md:col-span-3 border-t border-gray-200 border-dashed mt-1 pt-3">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2.5">Informasi Pengadaan (Opsional)</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1">Vendor / Supplier</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none"><i class="fas fa-truck"></i></div>
                                    <input type="text" name="vendor" placeholder="Nama Toko..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                                </div>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1">Nomor PO</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none"><i class="fas fa-file-signature"></i></div>
                                    <input type="text" name="no_po" placeholder="Nomor SP..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                                </div>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1">No. Dokumen / SJ</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none"><i class="fas fa-file-invoice"></i></div>
                                    <input type="text" name="no_dokumen" placeholder="Nota / Invoice..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                                </div>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1">Nomor PR <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none"><i class="fas fa-file-alt"></i></div>
                                    <input type="text" name="no_pr" required placeholder="Wajib Diisi..." class="w-full pl-12 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-3 pt-2">
                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-900/30 hover:bg-blue-700 transition transform hover:-translate-y-0.5"><i class="fas fa-clock mr-2"></i> SIMPAN & TAHAN DI PENGADAAN</button>
                    </div>

                    {{-- STATUS BARANG UNTUK TAMBAH BARU (DISAMBUNG DI SINI AGAR RAPI) --}}
                    <div class="col-span-1 mt-2">
                        <label class="block text-[10px] font-extrabold text-gray-700 uppercase mb-1 tracking-wide">Status Barang</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 w-10 flex items-center justify-center bg-gray-100 border-r border-gray-300 rounded-l-lg text-gray-500 pointer-events-none"><i class="fas fa-toggle-on"></i></div>
                            <select name="is_active" class="w-full pl-12 pr-8 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm h-[38px] custom-select-arrow">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                </div>
            </form>

            {{-- ========================================================================= --}}
            {{-- FORM 3: IMPORT KHUSUS MASUK --}}
            {{-- ========================================================================= --}}
            <div id="formImportMasuk" class="hidden flex flex-col gap-4">
                <div class="bg-green-50 text-green-700 p-3.5 rounded-xl text-[11px] font-bold border border-green-200 leading-relaxed shadow-sm">
                    <i class="fas fa-info-circle mr-1"></i> Upload file Excel khusus data Barang Masuk. Sistem otomatis mendeteksi baris judul.
                </div>

                <label class="block text-xs font-extrabold text-gray-700 uppercase mt-2">Upload File Excel (.xlsx)</label>
                <div class="bg-white border-2 border-dashed border-green-300 rounded-xl p-6 text-center hover:border-green-500 transition-colors">
                    <input type="file" id="fileExcelMasuk" accept=".xlsx, .xls, .csv" class="w-full text-sm text-gray-600 cursor-pointer outline-none">
                </div>

                <button type="button" onclick="prosesImportMasukExcel()" id="btnProsesMasuk" class="w-full mt-4 py-3.5 rounded-xl bg-green-600 text-white text-xs font-black uppercase tracking-widest shadow-lg hover:bg-green-700 transition transform hover:-translate-y-0.5 border-none">
                    <i class="fas fa-upload mr-2"></i> Proses Import Barang Masuk
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BARANG KELUAR --}}
<div id="modalBarangKeluar" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center transition-all duration-300">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden transform scale-100 border-t-4 border-red-600 flex flex-col max-h-[90vh]">

        <div class="bg-white px-8 pt-5 pb-4 border-b border-gray-200 flex flex-col shrink-0 gap-4">
            <div class="flex justify-between items-center w-full">
                <div class="flex items-center gap-4">
                    <div class="bg-red-100 text-red-600 p-3 rounded-xl shadow-inner"><i class="fas fa-dolly text-2xl"></i></div>
                    <div><h2 class="font-black text-lg text-gray-800 uppercase tracking-widest">Barang Keluar (OUT)</h2><p class="text-[11px] text-gray-500 mt-1 font-bold tracking-wide">Input pemakaian material</p></div>
                </div>
                <button type="button" onclick="document.getElementById('modalBarangKeluar').classList.add('hidden')" class="text-gray-400 hover:text-red-600 bg-gray-100 hover:bg-red-50 border border-gray-200 rounded-xl p-2.5 transition-colors shadow-sm"><i class="fas fa-times text-lg"></i></button>
            </div>

            {{-- TAB TOGGLE KELUAR --}}
            <div class="flex gap-2 bg-gray-100 p-1.5 rounded-xl border border-gray-200 w-full">
                <button type="button" id="btnTabKeluarManual" onclick="switchKeluarTab('manual')" class="flex-1 py-2 rounded-lg bg-red-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition">
                    <i class="fas fa-keyboard mr-1"></i> Input Manual
                </button>
                <button type="button" id="btnTabKeluarImport" onclick="switchKeluarTab('import')" class="flex-1 py-2 rounded-lg bg-transparent text-gray-600 hover:text-gray-800 text-[10px] font-black uppercase tracking-widest transition">
                    <i class="fas fa-file-excel mr-1 text-red-600"></i> Import Excel
                </button>
            </div>
        </div>

        {{-- FORM MANUAL --}}
        <form id="formKeluarManual" action="{{ route('stationery.transaction.store') }}" method="POST" class="p-8 space-y-6 overflow-y-auto bg-gray-50 flex-1">
            @csrf <input type="hidden" name="type" value="keluar">
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Pilih Barang <span class="text-red-500">*</span></label>
                <select name="stationery_id" id="out_stationery_id" onchange="checkStokOut()" required class="w-full bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                    <option value=""></option>
                    @foreach($dataStationery->sortBy('nama_barang') as $item)
                        @if(!isset($item->is_active) || $item->is_active == 1 || $item->is_active === true || $item->is_active === 'Aktif')
                            <option value="{{ $item->id }}" data-stok="{{ $item->total_stok }}" data-min="{{ $item->min_stok }}">{{ $item->kode }} - {{ $item->nama_barang }} (Sisa: {{ $item->total_stok }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="flex gap-6">
                <div class="w-1/2 relative pb-4"><label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Tanggal</label><input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800"></div>
                <div class="w-1/2 relative pb-4">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Jumlah</label>
                    <input type="number" name="jumlah" id="out_jumlah" oninput="checkStokOut()" required min="1" class="w-full py-3 px-4 bg-white border-2 border-red-300 rounded-xl text-lg font-black text-red-600">
                    <div id="out_warning" class="text-[10px] font-bold text-gray-500 mt-1.5 absolute">Pilih barang untuk melihat limit stok.</div>
                </div>
            </div>
            <div class="flex gap-6">
                <div class="w-1/2"><label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">No FPB</label><input type="text" name="no_fpb" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800"></div>
                <div class="w-1/2"><label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Departemen</label><input type="text" name="dept" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800"></div>
            </div>
            <div><label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Keperluan</label><input type="text" name="keterangan" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800"></div>
            <button type="submit" id="btnSubmitOut" class="w-full py-4 rounded-xl bg-red-600 text-white text-xs font-black uppercase tracking-widest shadow-lg hover:bg-red-700 transition">SIMPAN KELUAR</button>
        </form>

        {{-- FORM IMPORT KELUAR --}}
        <div id="formImportKeluar" class="hidden p-8 flex-col space-y-6 overflow-y-auto bg-gray-50 flex-1">
            <div class="bg-red-50 text-red-700 p-3.5 rounded-xl text-[11px] font-bold border border-red-200 leading-relaxed shadow-sm">
                <i class="fas fa-info-circle mr-1"></i> Upload file Excel khusus data Barang Keluar. Sistem otomatis mendeteksi baris judul.
            </div>

            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Upload File Excel (.xlsx)</label>
                <div class="bg-white border-2 border-dashed border-red-300 rounded-xl p-6 text-center hover:border-red-500 transition-colors">
                    <input type="file" id="fileExcelKeluar" accept=".xlsx, .xls, .csv" class="w-full text-sm text-gray-600 cursor-pointer outline-none">
                </div>
            </div>

            <button type="button" onclick="prosesImportKeluarExcel()" id="btnProsesKeluar" class="w-full mt-auto py-4 rounded-xl bg-red-600 text-white text-xs font-black uppercase tracking-widest shadow-lg hover:bg-red-700 transition transform hover:-translate-y-0.5 border-none">
                <i class="fas fa-upload mr-2"></i> Proses Import Barang Keluar
            </button>
        </div>

    </div>
</div>

{{-- MODAL EDIT STATIONERY DENGAN GRID SEMPURNA AGAR TIDAK MOTONG --}}
<div id="editStationeryModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center transition-all duration-300">
    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden transform scale-100">
        <div class="bg-green-600 px-6 py-4 flex justify-between items-center text-white font-black text-sm uppercase tracking-widest">
            <span>Edit Master Barang</span><button type="button" onclick="document.getElementById('editStationeryModal').classList.add('hidden')"><i class="fas fa-times"></i></button>
        </div>
        <form id="editStationeryForm" method="POST" class="p-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1.5">Kode</label>
                    <input type="text" name="kode" id="e_stat_kode" required class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:border-green-500 outline-none">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1.5">Nama</label>
                    <input type="text" name="nama_barang" id="e_stat_nama" required class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:border-green-500 outline-none">
                </div>

                {{-- HYBRID KATEGORI (EDIT) --}}
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1.5">Kategori</label>
                    <input type="text" name="kategori" id="e_stat_kategori" list="datalist_kategori" required autocomplete="off" placeholder="Pilih / Ketik Kategori..." class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:border-green-500 outline-none custom-select-arrow">
                </div>

                {{-- HYBRID SATUAN (EDIT) --}}
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1.5">Satuan</label>
                    <input type="text" name="satuan" id="e_stat_satuan" list="datalist_satuan" required autocomplete="off" placeholder="Pilih / Ketik Satuan..." class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:border-green-500 outline-none custom-select-arrow">
                </div>

                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1.5">Harga Unit</label>
                    <input type="number" name="harga_unit" id="e_stat_harga" required min="0" class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:border-green-500 outline-none">
                </div>

                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1.5 border-b border-gray-200 pb-0.5">Jml Awal</label>
                    <input type="number" name="stok_awal" id="e_stat_awal" required min="0" class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:border-green-500 outline-none bg-gray-50">
                </div>

                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-red-500 uppercase mb-1.5 border-b border-red-200 pb-0.5">Batas Min Stok (Manual)</label>
                    <input type="number" name="min_stok" id="e_stat_min" min="0" placeholder="Kosongkan jika pakai Auto-Hitung" class="w-full py-2.5 px-3 bg-white border border-red-300 rounded-lg text-xs font-bold text-gray-800 focus:border-red-500 outline-none">
                </div>

                {{-- STATUS BARANG DI EDIT --}}
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1.5 border-b border-gray-200 pb-0.5">Status Barang</label>
                    <select name="is_active" id="e_stat_status" class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:border-green-500 outline-none custom-select-arrow">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                {{-- DESAIN KOTAK BIRU AUTO HITUNG --}}
                <div class="col-span-1 md:col-span-3 bg-blue-50 border border-blue-200 rounded-xl p-4 mt-2">
                    <h4 class="text-[11px] font-black text-blue-800 uppercase tracking-widest mb-3 border-b border-blue-200 pb-2 flex items-center">
                        <i class="fas fa-calculator mr-2 text-sm"></i> AUTO-HITUNG MINIMUM STOK (SAFETY STOCK)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-extrabold text-blue-700 uppercase mb-1.5">Rata2 Pemakaian</label>
                            <input type="number" step="0.01" name="avg_penggunaan" id="e_stat_avg" placeholder="0.00" class="w-full py-2 px-3 bg-white border border-blue-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-blue-700 uppercase mb-1.5">Lead Time (Hari)</label>
                            <input type="number" name="lead_time" id="e_stat_lead" placeholder="0" class="w-full py-2 px-3 bg-white border border-blue-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-blue-700 uppercase mb-1.5">Safety Stock</label>
                            <input type="number" name="safety_stock" id="e_stat_safety" placeholder="0" class="w-full py-2 px-3 bg-white border border-blue-300 rounded-lg text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none transition-all shadow-sm">
                        </div>
                    </div>
                    <p class="text-[9px] text-blue-600 mt-2.5 font-medium italic">*Abaikan form ini jika ingin isi Min Stok manual di atas. Rumus: (Rata2 x Lead Time) + Safety Stock.</p>
                </div>

                <div class="col-span-1 md:col-span-3 pt-2">
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-green-600 text-white text-xs font-bold shadow-lg hover:bg-green-700 transition transform hover:-translate-y-0.5">UPDATE DATA BARANG</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PENGADAAN --}}
<div id="modalEditPengadaan" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center transition-all duration-300">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden transform scale-100 border-t-4 border-blue-600 flex flex-col max-h-[90vh]">
        <div class="bg-white px-8 py-5 border-b border-gray-200 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-xl shadow-inner"><i class="fas fa-edit text-2xl"></i></div>
                <div><h2 class="font-black text-lg text-gray-800 uppercase tracking-widest">Edit Pengadaan</h2><p class="text-[11px] text-gray-500 mt-1 font-bold tracking-wide">Perbarui data PR / PO</p></div>
            </div>
            <button type="button" onclick="document.getElementById('modalEditPengadaan').classList.add('hidden')" class="text-gray-400 hover:text-red-600 bg-gray-100 hover:bg-red-50 border border-gray-200 rounded-xl p-2.5 transition-colors shadow-sm"><i class="fas fa-times text-lg"></i></button>
        </div>

        <form id="formEditPengadaan" method="POST" class="p-8 space-y-6 overflow-y-auto bg-gray-50">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Pilih Barang <span class="text-red-500">*</span></label>
                <select name="stationery_id" id="edit_p_barang" required class="w-full bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800 focus:border-blue-500 outline-none">
                    <option value=""></option>
                    @foreach($dataStationery->sortBy('nama_barang') as $item)
                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama_barang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-6">
                <div class="w-1/3">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Tanggal PR <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_pr" id="edit_p_tgl_pr" required class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
                <div class="w-1/3">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Estimasi Tiba <span class="text-red-500">*</span></label>
                    <input type="date" name="estimasi_tiba" id="edit_p_estimasi" required class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
                <div class="w-1/3">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Jumlah Diminta (Qty) <span class="text-red-500">*</span></label>
                    <input type="number" name="qty_item" id="edit_p_qty" required min="1" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-lg font-black text-blue-600">
                </div>
            </div>

            <div class="flex gap-6">
                <div class="w-1/2">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Vendor / Supplier <span class="text-[10px] font-normal text-gray-400">(Opsional)</span></label>
                    <input type="text" name="vendor" id="edit_p_vendor" placeholder="Nama Toko..." class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
                <div class="w-1/2">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Nomor PO <span class="text-[10px] font-normal text-gray-400">(Opsional)</span></label>
                    <input type="text" name="no_po" id="edit_p_no_po" placeholder="Nomor Surat Pesanan..." class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
            </div>

            <div class="flex gap-6 pb-4 border-b border-gray-300 border-dashed">
                <div class="w-1/2">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">No. Dokumen (Surat Jalan) <span class="text-[10px] font-normal text-gray-400">(Opsional)</span></label>
                    <input type="text" name="no_dokumen" id="edit_p_no_dokumen" placeholder="Nota / Invoice..." class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
                <div class="w-1/2">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Nomor PR <span class="text-red-500">*</span></label>
                    <input type="text" name="no_pr" id="edit_p_no_pr" required placeholder="Wajib Diisi (Cth: PR-001)" class="w-full py-3 px-4 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-800">
                </div>
            </div>

            <button type="submit" class="w-full py-4 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest shadow-lg hover:bg-blue-700 transition">UPDATE PENGADAAN</button>
        </form>
    </div>
</div>

{{-- MODAL IMPORT EXCEL (GLOBAL) --}}
<div id="modalImportExcel" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center transition-all duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform scale-100 border-t-4 border-indigo-600 flex flex-col">
        <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="font-black text-lg text-gray-800 uppercase tracking-widest"><i class="fas fa-file-excel text-indigo-600 mr-2"></i> Import Global Data</h2>
            <button type="button" onclick="document.getElementById('modalImportExcel').classList.add('hidden')" class="text-gray-400 hover:text-red-600 transition-colors"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-6 bg-gray-50">
            <div class="bg-blue-50 text-blue-700 p-3 rounded-lg text-[10px] font-bold mb-4 border border-blue-200 leading-relaxed">
                <i class="fas fa-info-circle mr-1"></i> Sistem akan memproses otomatis ke-3 sheet sekaligus. Baris judul dan format tanggal akan dideteksi secara otomatis.
            </div>

            <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Upload File Excel (.xlsx)</label>
            <div class="bg-white border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-indigo-500 transition-colors">
                <input type="file" id="fileExcelImport" accept=".xlsx, .xls, .csv" class="w-full text-sm text-gray-600 cursor-pointer outline-none">
            </div>

            <button onclick="window.prosesImportExcel()" id="btnProsesImport" class="w-full mt-6 py-3.5 rounded-xl bg-indigo-600 text-white text-xs font-black uppercase tracking-widest shadow-lg hover:bg-indigo-700 transition transform hover:-translate-y-0.5 border-none">
                <i class="fas fa-upload mr-2"></i> Proses Import Global
            </button>
        </div>
    </div>
</div>

<script>
    function applyFullBorder(worksheet) {
        const MAX_CELLS = 6000;
        let applied = 0;

        worksheet.eachRow({ includeEmpty: false }, function(row) {
            row.eachCell({ includeEmpty: false }, function(cell) {
                if (applied >= MAX_CELLS) return;

                cell.border = {
                    top: { style: 'thin', color: { argb: 'FF000000' } },
                    left: { style: 'thin', color: { argb: 'FF000000' } },
                    bottom: { style: 'thin', color: { argb: 'FF000000' } },
                    right: { style: 'thin', color: { argb: 'FF000000' } }
                };

                applied++;
            });
        });
    }

    window.modePilihAktif = false;

    window.toggleModePilih = function() {
        window.modePilihAktif = !window.modePilihAktif;

        let btnToggle = document.getElementById('btnTogglePilih');
        let teksToggle = document.getElementById('teksTogglePilih');
        let ikonToggle = document.getElementById('ikonTogglePilih');
        let selectionCols = document.querySelectorAll('.selection-col');

        if (window.modePilihAktif) {
            btnToggle.classList.replace('bg-slate-600', 'bg-gray-400');
            btnToggle.classList.replace('hover:bg-slate-700', 'hover:bg-gray-500');
            teksToggle.innerText = 'Batal Pilih';
            ikonToggle.classList.replace('fa-tasks', 'fa-times');

            selectionCols.forEach(col => col.classList.remove('hidden'));
        } else {
            btnToggle.classList.replace('bg-gray-400', 'bg-slate-600');
            btnToggle.classList.replace('hover:bg-gray-500', 'hover:bg-slate-700');
            teksToggle.innerText = 'Pilih Data';
            ikonToggle.classList.replace('fa-times', 'fa-tasks');

            selectionCols.forEach(col => col.classList.add('hidden'));

            document.getElementById('checkAllMaster').checked = false;
            document.querySelectorAll('.checkbox-item').forEach(cb => cb.checked = false);
        }

        window.perbaruiStatusCentang();
    };

    window.toggleSemuaCheckbox = function(masterCheckbox) {
        let checkboxes = document.querySelectorAll('.checkbox-item');

        checkboxes.forEach(function(cb) {
            let row = cb.closest('tr');
            if (row.style.display !== 'none') {
                cb.checked = masterCheckbox.checked;
            }
        });

        window.perbaruiStatusCentang();
    };

    window.perbaruiStatusCentang = function() {
        let checkboxes = document.querySelectorAll('.checkbox-item:checked');
        let allVisibleCheckboxes = document.querySelectorAll('.checkbox-item:not([style*="display: none"])');

        let btnHapus = document.getElementById('btnHapus');
        let teksHapus = document.getElementById('teksHapus');
        let btnBulkStatus = document.getElementById('btnBulkStatus');
        let teksBulkStatus = document.getElementById('teksBulkStatus');
        let masterCheckbox = document.getElementById('checkAllMaster');

        if (allVisibleCheckboxes.length > 0 && checkboxes.length === allVisibleCheckboxes.length) {
            masterCheckbox.checked = true;
        } else {
            masterCheckbox.checked = false;
        }

        if (checkboxes.length > 0) {
            btnHapus.disabled = false;
            btnHapus.className = 'bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg transition transform border-none flex items-center shadow-md';
            teksHapus.innerText = `HAPUS TERPILIH (${checkboxes.length})`;

            btnBulkStatus.disabled = false;
            btnBulkStatus.className = 'bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg transition transform border-none flex items-center shadow-md ml-2';
            teksBulkStatus.innerText = `UBAH STATUS (${checkboxes.length})`;
        } else {
            btnHapus.disabled = true;
            btnHapus.className = 'bg-gray-400 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition flex items-center shadow-sm cursor-not-allowed border-none';
            teksHapus.innerText = 'HAPUS TERPILIH';

            btnBulkStatus.disabled = true;
            btnBulkStatus.className = 'bg-gray-400 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition flex items-center shadow-sm cursor-not-allowed border-none ml-2';
            teksBulkStatus.innerText = 'UBAH STATUS';
        }
    };

    window.prosesHapus = function() {
        let checkboxes = document.querySelectorAll('.checkbox-item:checked');
        let formBulkDelete = document.getElementById('formBulkDelete');

        if (checkboxes.length === 0) return;

        Swal.fire({
            title: 'Hapus ' + checkboxes.length + ' Data Terpilih?',
            text: "Data barang yang dipilih beserta riwayat transaksinya akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                formBulkDelete.innerHTML = '@csrf @method("DELETE")';
                checkboxes.forEach(function(cb) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    formBulkDelete.appendChild(input);
                });

formBulkDelete.submit();
            }
        });
    };

    window.prosesBulkStatus = function() {
        let checkboxes = document.querySelectorAll('.checkbox-item:checked');
        let formBulkStatus = document.getElementById('formBulkStatus');

        if (checkboxes.length === 0) return;

        Swal.fire({
            title: 'Ubah Status ' + checkboxes.length + ' Barang?',
            text: "Pilih status baru untuk barang-barang yang dicentang:",
            icon: 'question',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            denyButtonColor: '#dc2626',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="fas fa-check-circle mr-1"></i> Set AKTIF',
            denyButtonText: '<i class="fas fa-times-circle mr-1"></i> Set TIDAK AKTIF',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed || result.isDenied) {
                let statusValue = result.isConfirmed ? 1 : 0;

                formBulkStatus.innerHTML = '@csrf @method("PUT")';

                let inputStatus = document.createElement('input');
                inputStatus.type = 'hidden';
                inputStatus.name = 'is_active';
                inputStatus.value = statusValue;
                formBulkStatus.appendChild(inputStatus);

                checkboxes.forEach(function(cb) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    formBulkStatus.appendChild(input);
                });

                formBulkStatus.submit();
            }
        });
    };

    function parseExcelDate(val) {
        if (val == null || val === '') return null;
        if (!isNaN(val)) { let date = new Date(Math.round((val - 25569) * 86400 * 1000)); return date.toISOString().split('T')[0]; }
        let str = String(val).trim();
        if (str.includes('.')) { let p = str.split('.'); if (p.length === 3) return `${p[2].length <= 2 ? '20'+p[2] : p[2]}-${p[1].padStart(2, '0')}-${p[0].padStart(2, '0')}`; }
        if (str.includes('/')) { let p = str.split('/'); if (p.length === 3) return p[0].length === 4 ? `${p[0]}-${p[1].padStart(2, '0')}-${p[2].padStart(2, '0')}` : `${p[2].length <= 2 ? '20'+p[2] : p[2]}-${p[1].padStart(2, '0')}-${p[0].padStart(2, '0')}`; }
        if (str.includes('-')) { let p = str.split('-'); if (p.length === 3) return p[0].length === 4 ? `${p[0]}-${p[1].padStart(2, '0')}-${p[2].padStart(2, '0')}` : `${p[2].length <= 2 ? '20'+p[2] : p[2]}-${p[1].padStart(2, '0')}-${p[0].padStart(2, '0')}`; }
        let d = new Date(str); if (!isNaN(d.getTime())) return d.toISOString().split('T')[0]; return str;
    }

    function extractAndMap(sheet, type) {
        if (!sheet) return [];
        const rawArray = XLSX.utils.sheet_to_json(sheet, { header: 1, blankrows: false });
        let headerRowIdx = 0;

        for (let i = 0; i < Math.min(20, rawArray.length); i++) {
            let rowStr = (rawArray[i] || []).join('').toLowerCase();
            let score = 0;
            if (rowStr.includes('kode') || rowStr.includes('material') || rowStr.includes('item') || rowStr.includes('number')) score++;
            if (rowStr.includes('nama') || rowStr.includes('name') || rowStr.includes('deskripsi')) score++;
            if (rowStr.includes('jumlah') || rowStr.includes('qty') || rowStr.includes('quantity') || rowStr.includes('keluar')) score++;
            if (rowStr.includes('tgl') || rowStr.includes('tanggal') || rowStr.includes('date')) score++;
            if (score >= 2) { headerRowIdx = i; break; }
        }

        const rawData = XLSX.utils.sheet_to_json(sheet, { range: headerRowIdx });
        let result = [];
        let emptyCount = 0;

        rawData.forEach(row => {
            let cleanRow = {}; let normalizedRow = {};
            let keys = Object.keys(row); let firstKey = keys[0] || '';

            if (keys.length === 1 && (firstKey.includes(',') || firstKey.includes(';'))) {
                let giantKey = firstKey; let giantVal = row[giantKey] || ''; let delimiter = giantKey.includes(';') ? ';' : ',';
                let splitKeys = giantKey.split(delimiter); let splitVals = typeof giantVal === 'string' ? giantVal.split(delimiter) : [giantVal];
                splitKeys.forEach((k, idx) => {
                    let safeKey = k ? k.replace(/[^a-zA-Z0-9]/g, '').toLowerCase() : 'empty' + (emptyCount++);
                    normalizedRow[safeKey] = splitVals[idx];
                });
            } else {
                for(let k in row) {
                    if (k && typeof k === 'string') {
                        let safeKey = k.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
                        if(!safeKey) safeKey = 'empty' + (emptyCount++);
                        normalizedRow[safeKey] = row[k];
                    }
                }
            }

            cleanRow = { ...normalizedRow };

            if (type === 'master') {
                cleanRow['kode'] = normalizedRow['materialnumber'] || normalizedRow['kode'] || normalizedRow['kodebarang'] || normalizedRow['materialno'] || normalizedRow['material'] || normalizedRow['item'] || null;
                cleanRow['nama'] = normalizedRow['nama'] || normalizedRow['materialname'] || normalizedRow['matname'] || normalizedRow['deskripsi'] || normalizedRow['namabarang'] || null;
                cleanRow['kategori'] = normalizedRow['kategori'] || null;
                cleanRow['satuan'] = normalizedRow['satuan'] || normalizedRow['uom'] || normalizedRow['unit'] || null;
                cleanRow['jumlah'] = normalizedRow['jumlah'] || normalizedRow['qty'] || normalizedRow['stokawal'] || normalizedRow['quantity'] || null;
                cleanRow['harga'] = normalizedRow['hargaunit'] || normalizedRow['harga'] || 0;

                if (!cleanRow['kode']) {
                    let vals = Object.values(row);
                    let foundKode = vals.find(v => typeof v === 'string' && /^[A-Za-z0-9_-]{3,15}$/.test(v.trim()) && !v.toLowerCase().includes('tgl'));
                    if (foundKode) cleanRow['kode'] = foundKode.trim();
                }
                if (cleanRow['kode'] || cleanRow['nama']) result.push(cleanRow);
            }
            else if (type === 'masuk') {
                cleanRow['tanggal'] = parseExcelDate(normalizedRow['tanggal'] || normalizedRow['tgl'] || normalizedRow['date']);
                let dokVal = normalizedRow['materialdokument'] || normalizedRow['nodokumen'] || normalizedRow['dokumen'] || normalizedRow['dok'] || null;
                cleanRow['dokumen'] = dokVal; cleanRow['no_dokumen'] = dokVal;
                cleanRow['kode'] = normalizedRow['materialnumber'] || normalizedRow['kode'] || normalizedRow['kodebarang'] || normalizedRow['materialno'] || normalizedRow['material'] || null;
                cleanRow['nama'] = normalizedRow['nama'] || normalizedRow['materialname'] || normalizedRow['matname'] || normalizedRow['deskripsi'] || null;
                cleanRow['satuan'] = normalizedRow['satuan'] || normalizedRow['uom'] || normalizedRow['unit'] || null;
                cleanRow['jumlah'] = normalizedRow['jumlah'] || normalizedRow['qty'] || normalizedRow['quantity'] || normalizedRow['in'] || normalizedRow['masuk'] || null;
                cleanRow['vendor'] = normalizedRow['vendor'] || normalizedRow['supplier'] || null;

                if (!cleanRow['kode']) {
                    let vals = Object.values(row);
                    let foundKode = vals.find(v => typeof v === 'string' && /^[A-Za-z0-9_-]{3,15}$/.test(v.trim()) && !v.toLowerCase().includes('tgl'));
                    if (foundKode) cleanRow['kode'] = foundKode.trim();
                }
                if (cleanRow['kode'] || cleanRow['nama']) result.push(cleanRow);
            }
            else if (type === 'keluar') {
                cleanRow['tanggal'] = parseExcelDate(normalizedRow['tanggal'] || normalizedRow['tgl'] || normalizedRow['date']);

                cleanRow['kode'] = normalizedRow['materialnumber'] || normalizedRow['kode'] || normalizedRow['kodebarang'] || normalizedRow['materialno'] || normalizedRow['material'] || normalizedRow['item'] || null;
                cleanRow['nama'] = normalizedRow['nama'] || normalizedRow['materialname'] || normalizedRow['matname'] || normalizedRow['deskripsi'] || normalizedRow['namabarang'] || null;

                cleanRow['satuan'] = normalizedRow['satuan'] || normalizedRow['uom'] || null;

                cleanRow['jumlah'] = normalizedRow['jumlah'] || normalizedRow['qty'] || normalizedRow['quantity'] || normalizedRow['keluar'] || normalizedRow['pemakaian'] || normalizedRow['out'] || null;

                let fpbVal = normalizedRow['fpb'] || normalizedRow['no_fpb'] || null;
                if (!fpbVal) {
                    if (normalizedRow['nofpb'] && normalizedRow['empty']) {
                        fpbVal = normalizedRow['empty'];
                    } else {
                        fpbVal = normalizedRow['nofpb'] || normalizedRow['empty'] || normalizedRow['empty1'];
                    }
                }
                if (!fpbVal) {
                    let vals = Object.values(row);
                    let foundFpb = vals.find(v => typeof v === 'string' && (v.includes('PR-') || v.includes('FPB')));
                    if(foundFpb) fpbVal = foundFpb;
                }
                cleanRow['fpb'] = fpbVal; cleanRow['no_fpb'] = fpbVal;

                let deptVal = normalizedRow['dept'] || normalizedRow['departemen'] || normalizedRow['costcenter'] || normalizedRow['divisi'] || normalizedRow['pemakai'] || null;
                if (!deptVal) {
                    let fallbackDeptKey = Object.keys(normalizedRow).find(k => k.includes('dept') || k.includes('divisi') || k.includes('pemakai'));
                    if (fallbackDeptKey) deptVal = normalizedRow[fallbackDeptKey];
                }
                cleanRow['dept'] = deptVal;

                let ketVal = normalizedRow['ket'] || normalizedRow['keterangan'] || normalizedRow['keperluan'] || null;
                cleanRow['ket'] = ketVal; cleanRow['keterangan'] = ketVal;

                if (!cleanRow['kode']) {
                    let vals = Object.values(row);
                    let foundKode = vals.find(v => typeof v === 'string' && /^[A-Za-z0-9_-]{3,20}$/.test(v.trim()) && !v.toLowerCase().includes('tgl'));
                    if (foundKode) cleanRow['kode'] = foundKode.trim();
                }

                if (cleanRow['kode'] || cleanRow['nama']) result.push(cleanRow);
            }
        });

        return result;
    }

    window.prosesImportExcel = function() {
        const fileInput = document.getElementById('fileExcelImport');
        const file = fileInput.files[0];
        if (!file) { Swal.fire('Peringatan', 'Pilih file Excel terlebih dahulu!', 'warning'); return; }

        const btn = document.getElementById('btnProsesImport');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Membaca File... Mohon Tunggu'; btn.disabled = true;

        setTimeout(() => {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    let sheetNames = workbook.SheetNames;

                    let sMasterName = sheetNames.find(n => { let lower = n.toLowerCase(); return lower.includes('ending') || lower.includes('master') || lower.includes('awal'); });
                    let sMasukName = sheetNames.find(n => { if (n === sMasterName) return false; let lower = n.toLowerCase(); return lower.includes('masuk') || lower.match(/\bin\b/); });
                    let sKeluarName = sheetNames.find(n => { if (n === sMasterName || n === sMasukName) return false; let lower = n.toLowerCase(); return lower.includes('keluar') || lower.match(/\bout\b/) || lower.includes('pakai'); });

                    if (!sMasterName && sheetNames.length > 0) sMasterName = sheetNames[0];
                    if (!sMasukName && sheetNames.length > 1) sMasukName = sheetNames.find(n => n !== sMasterName) || sheetNames[1];
                    if (!sKeluarName && sheetNames.length > 2) sKeluarName = sheetNames.find(n => n !== sMasterName && n !== sMasukName) || sheetNames[2];

                    const dataMaster = sMasterName ? extractAndMap(workbook.Sheets[sMasterName], 'master') : [];
                    const dataMasuk = sMasukName ? extractAndMap(workbook.Sheets[sMasukName], 'masuk') : [];
                    const dataKeluar = sKeluarName ? extractAndMap(workbook.Sheets[sKeluarName], 'keluar') : [];

                    btn.innerHTML = '<i class="fas fa-paper-plane fa-bounce mr-2"></i> Mengirim Ribuan Data...';

                    fetch("{{ route('stationery.import') }}", {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ data_excel: dataMaster, data_masuk: dataMasuk, data_keluar: dataKeluar })
                    })
                    .then(async res => { if (!res.ok) throw new Error(await res.text()); return res.json(); })
                    .then(response => {
                        if(response.success) Swal.fire('BERHASIL!', response.message, 'success').then(() => location.reload());
                        else Swal.fire('GAGAL!', response.message, 'error');
                    })
                    .catch(error => {
                        Swal.fire('Error Server', 'Terjadi kesalahan saat memproses data.', 'error');
                        console.error(error);
                    })
                    .finally(() => {
                        btn.innerHTML = '<i class="fas fa-upload mr-2"></i> Proses Import Global';
                        btn.disabled = false;
                    });

                } catch (err) {
                    Swal.fire('Error File', 'Gagal membaca format Excel. Pastikan format tabel benar.', 'error');
                    console.error(err);
                    btn.innerHTML = '<i class="fas fa-upload mr-2"></i> Proses Import Global';
                    btn.disabled = false;
                }
            };
            reader.readAsArrayBuffer(file);
        }, 150);
    };

    window.prosesImportMasukExcel = function() {
        const fileInput = document.getElementById('fileExcelMasuk');
        const file = fileInput.files[0];
        if (!file) { Swal.fire('Peringatan', 'Pilih file Excel terlebih dahulu!', 'warning'); return; }

        const btn = document.getElementById('btnProsesMasuk');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Membaca File...'; btn.disabled = true;

        setTimeout(() => {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    let sMasukName = workbook.SheetNames.find(n => n.toLowerCase().includes('masuk') || n.toLowerCase().includes('in')) || workbook.SheetNames[0];
                    const dataMasuk = extractAndMap(workbook.Sheets[sMasukName], 'masuk');

                    btn.innerHTML = '<i class="fas fa-paper-plane fa-bounce mr-2"></i> Memproses Import...';

                    fetch("{{ route('stationery.import') }}", {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ data_excel: [], data_masuk: dataMasuk, data_keluar: [] })
                    })
                    .then(async res => { if (!res.ok) throw new Error(await res.text()); return res.json(); })
                    .then(response => {
                        if(response.success) Swal.fire('BERHASIL!', response.message, 'success').then(() => location.reload());
                        else Swal.fire('GAGAL!', response.message, 'error');
                    })
                    .catch(error => { Swal.fire('Error Server', 'Gagal memproses di server.', 'error'); })
                    .finally(() => { btn.innerHTML = '<i class="fas fa-upload mr-2"></i> Proses Import Barang Masuk'; btn.disabled = false; });
                } catch (err) {
                    Swal.fire('Error File', 'Gagal membaca isi file Excel.', 'error');
                    btn.innerHTML = '<i class="fas fa-upload mr-2"></i> Proses Import Barang Masuk'; btn.disabled = false;
                }
            };
            reader.readAsArrayBuffer(file);
        }, 150);
    };

    window.prosesImportKeluarExcel = function() {
        const fileInput = document.getElementById('fileExcelKeluar');
        const file = fileInput.files[0];
        if (!file) { Swal.fire('Peringatan', 'Pilih file Excel terlebih dahulu!', 'warning'); return; }

        const btn = document.getElementById('btnProsesKeluar');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Membaca File...'; btn.disabled = true;

        setTimeout(() => {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    let sKeluarName = workbook.SheetNames.find(n => n.toLowerCase().includes('keluar') || n.toLowerCase().includes('out') || n.toLowerCase().includes('pakai')) || workbook.SheetNames[0];
                    const dataKeluar = extractAndMap(workbook.Sheets[sKeluarName], 'keluar');

                    btn.innerHTML = '<i class="fas fa-paper-plane fa-bounce mr-2"></i> Memproses Import...';

                    fetch("{{ route('stationery.import') }}", {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ data_excel: [], data_masuk: [], data_keluar: dataKeluar })
                    })
                    .then(async res => { if (!res.ok) throw new Error(await res.text()); return res.json(); })
                    .then(response => {
                        if(response.success) Swal.fire('BERHASIL!', response.message, 'success').then(() => location.reload());
                        else Swal.fire('GAGAL!', response.message, 'error');
                    })
                    .catch(error => { Swal.fire('Error Server', 'Gagal memproses di server.', 'error'); })
                    .finally(() => { btn.innerHTML = '<i class="fas fa-upload mr-2"></i> Proses Import Barang Keluar'; btn.disabled = false; });
                } catch (err) {
                    Swal.fire('Error File', 'Gagal membaca isi file Excel.', 'error');
                    btn.innerHTML = '<i class="fas fa-upload mr-2"></i> Proses Import Barang Keluar'; btn.disabled = false;
                }
            };
            reader.readAsArrayBuffer(file);
        }, 150);
    };

    let timerStationery = null;
    let timerLaporan = null;
    let timerPengadaan = null;
    let timerTransaksi = null;

    function customSelect2Matcher(params, data) {
        if ($.trim(params.term) === '') { return data; }
        if (typeof data.text === 'undefined') { return null; }
        var searchTerms = params.term.toLowerCase().split(' ');
        var itemText = data.text.toLowerCase();
        var matches = true;
        for (var i = 0; i < searchTerms.length; i++) {
            if (itemText.indexOf(searchTerms[i]) === -1) { matches = false; break; }
        }
        if (matches) { return data; }
        return null;
    }

    $(document).ready(function() {
        $('#select_restock_id').select2({ placeholder: "-- Ketik / Pilih Barang --", width: '100%', dropdownParent: $('#modalBarangMasukGabungan'), matcher: customSelect2Matcher });
        $('#out_stationery_id').select2({ placeholder: "-- Ketik / Pilih Barang --", width: '100%', dropdownParent: $('#modalBarangKeluar'), matcher: customSelect2Matcher });
        $('#out_stationery_id').on('select2:select', function (e) { checkStokOut(); });
        $('#edit_p_barang').select2({ placeholder: "-- Ketik / Pilih Barang --", width: '100%', dropdownParent: $('#modalEditPengadaan'), matcher: customSelect2Matcher });

        $('#edit_out_stationery_id').select2({ placeholder: "-- Ketik / Pilih Barang --", width: '100%', dropdownParent: $('#modalEditPemakaian'), matcher: customSelect2Matcher });
    });

    const masterData = @json($orderBarang);
    const rawTrxData = @json($transaksis);

    let statChartInstance = null;
    let statFreqChartInstance = null;

    window.toggleChartMode = function(mode) {
        const btnKuantitas = document.getElementById('btnTampilKuantitas');
        const btnFrekuensi = document.getElementById('btnTampilFrekuensi');
        const boxKuantitas = document.getElementById('boxChartKuantitas');
        const boxFrekuensi = document.getElementById('boxChartFrekuensi');

        if (mode === 'kuantitas') {
            btnKuantitas.className = 'px-8 py-2.5 rounded-lg bg-blue-600 text-white text-[11px] font-black uppercase tracking-widest shadow-md transition transform hover:scale-105 border border-blue-700';
            btnFrekuensi.className = 'px-8 py-2.5 rounded-lg bg-transparent text-gray-500 hover:text-gray-700 text-[11px] font-black uppercase tracking-widest transition';

            boxKuantitas.classList.remove('hidden'); boxKuantitas.classList.add('flex');
            boxFrekuensi.classList.add('hidden'); boxFrekuensi.classList.remove('flex');
            if (statChartInstance) { setTimeout(() => statChartInstance.resize(), 50); }
        } else {
            btnFrekuensi.className = 'px-8 py-2.5 rounded-lg bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest shadow-md transition transform hover:scale-105 border border-indigo-700';
            btnKuantitas.className = 'px-8 py-2.5 rounded-lg bg-transparent text-gray-500 hover:text-gray-700 text-[11px] font-black uppercase tracking-widest transition';

            boxFrekuensi.classList.remove('hidden'); boxFrekuensi.classList.add('flex');
            boxKuantitas.classList.add('hidden'); boxKuantitas.classList.remove('flex');
            if (statFreqChartInstance) { setTimeout(() => statFreqChartInstance.resize(), 50); }
        }
    };

    window.downloadChartPNG = function(canvasId = 'stationeryOutChart', labelName = 'Quantity') {
        const canvas = document.getElementById(canvasId);
        if (!canvas) { Swal.fire('Gagal!', 'Grafik belum selesai dimuat.', 'error'); return; }
        try {
            const imageURL = canvas.toDataURL('image/png', 1.0);
            const link = document.createElement('a'); link.download = `Grafik_Stationery_${labelName}.png`; link.href = imageURL;
            document.body.appendChild(link); link.click(); document.body.removeChild(link);
        } catch (error) { Swal.fire('Error', 'Gagal memproses gambar: ' + error.message, 'error'); }
    };
    window.downloadChart = window.downloadChartPNG;

    function checkStokOut() {
        let select = document.getElementById('out_stationery_id'); let inputQty = document.getElementById('out_jumlah');
        let btnSubmit = document.getElementById('btnSubmitOut'); let warningText = document.getElementById('out_warning');
        if(!select || !inputQty || !btnSubmit || !warningText) return;
        if(!select.value) { warningText.innerHTML = "Pilih barang untuk melihat limit stok."; warningText.className = "text-[10px] font-bold text-gray-500 mt-1.5 absolute"; btnSubmit.disabled = false; return; }
        let selectedOption = select.options[select.selectedIndex];
        let maxStok = parseInt(selectedOption.getAttribute('data-stok')) || 0; let minStok = parseInt(selectedOption.getAttribute('data-min')) || 0;
        let reqQty = parseInt(inputQty.value) || 0;
        let maxBolehDiambil = maxStok - minStok; if (maxBolehDiambil < 0) maxBolehDiambil = 0;
        if (reqQty <= 0) { warningText.innerHTML = `Bisa diambil: <b>${maxBolehDiambil} Unit</b> (Sisa Safety: ${minStok})`; warningText.className = "text-[10px] font-bold text-blue-600 mt-1.5 absolute"; btnSubmit.disabled = true; btnSubmit.classList.add('opacity-50', 'cursor-not-allowed'); }
        else if (reqQty > maxBolehDiambil) { warningText.innerHTML = `<i class="fas fa-times-circle"></i> GAGAL! Stok batas minimum. Maks ambil: ${maxBolehDiambil}`; warningText.className = "text-[10px] font-bold text-red-600 mt-1.5 absolute"; btnSubmit.disabled = true; btnSubmit.classList.add('opacity-50', 'cursor-not-allowed'); }
        else { warningText.innerHTML = `<i class="fas fa-check-circle"></i> Aman! Sisa stok gudang nanti: ${maxStok - reqQty}`; warningText.className = "text-[10px] font-bold text-green-600 mt-1.5 absolute"; btnSubmit.disabled = false; btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed'); }
    }

    function switchMasukTab(tab) {
        const btnRestock = document.getElementById('btnTabRestock'); const btnBaru = document.getElementById('btnTabBaru'); const btnImport = document.getElementById('btnTabImportMasuk');
        const formRestock = document.getElementById('formRestockMasuk'); const formMaster = document.getElementById('formMasterBaru'); const formImport = document.getElementById('formImportMasuk');

        $('#select_restock_id').val(null).trigger('change');
        let clsActiveGreen = 'whitespace-nowrap flex-1 py-2 px-3 rounded-xl bg-green-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition border border-green-700';
        let clsActiveBlue = 'whitespace-nowrap flex-1 py-2 px-3 rounded-xl bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition border border-blue-700';
        let clsInactive = 'whitespace-nowrap flex-1 py-2 px-3 rounded-xl bg-white border-2 border-gray-300 text-gray-600 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition shadow-sm';

        btnRestock.className = clsInactive; btnBaru.className = clsInactive; btnImport.className = clsInactive;
        formRestock.classList.add('hidden'); formMaster.classList.add('hidden'); formImport.classList.add('hidden');

        if(tab === 'restock') { btnRestock.className = clsActiveGreen; formRestock.classList.remove('hidden'); }
        else if(tab === 'baru') { btnBaru.className = clsActiveBlue; formMaster.classList.remove('hidden'); }
        else if(tab === 'import') { btnImport.className = clsActiveGreen; formImport.classList.remove('hidden'); formImport.classList.add('flex'); }
    }

    window.switchKeluarTab = function(tab) {
        const btnManual = document.getElementById('btnTabKeluarManual'); const btnImport = document.getElementById('btnTabKeluarImport');
        const formManual = document.getElementById('formKeluarManual'); const formImport = document.getElementById('formImportKeluar');

        if (tab === 'manual') {
            btnManual.className = 'flex-1 py-2 rounded-lg bg-red-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition';
            btnImport.className = 'flex-1 py-2 rounded-lg bg-transparent text-gray-600 hover:text-gray-800 text-[10px] font-black uppercase tracking-widest transition';
            formManual.classList.remove('hidden'); formImport.classList.add('hidden'); formImport.classList.remove('flex');
        } else {
            btnImport.className = 'flex-1 py-2 rounded-lg bg-red-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition';
            btnManual.className = 'flex-1 py-2 rounded-lg bg-transparent text-gray-600 hover:text-gray-800 text-[10px] font-black uppercase tracking-widest transition';
            formImport.classList.remove('hidden'); formImport.classList.add('flex'); formManual.classList.add('hidden');
        }
    };

    window.openEditPemakaianModal = function(id, stationery_id, tanggal, jumlah, no_fpb, dept, keterangan) {
        document.getElementById('formEditPemakaian').action = '/stationery/transaction/' + id;

        $('#edit_out_stationery_id').val(stationery_id).trigger('change');

        document.getElementById('edit_out_tanggal').value = tanggal;
        document.getElementById('edit_out_jumlah').value = jumlah;
        document.getElementById('edit_out_no_fpb').value = no_fpb;
        document.getElementById('edit_out_dept').value = dept;
        document.getElementById('edit_out_keterangan').value = keterangan;

        document.getElementById('modalEditPemakaian').classList.remove('hidden');
    };

    window.openEditStationeryModal = function(id, kode, nama, kategori, satuan, awal, harga, min, avg, lead, safety, is_active) {
        document.getElementById('editStationeryForm').action = '/stationery/' + id;

        document.getElementById('e_stat_kode').value = kode;
        document.getElementById('e_stat_nama').value = nama;

        document.getElementById('e_stat_kategori').value = kategori;
        document.getElementById('e_stat_satuan').value = satuan;

        document.getElementById('e_stat_awal').value = awal;
        document.getElementById('e_stat_harga').value = harga;
        document.getElementById('e_stat_min').value = min;
        document.getElementById('e_stat_avg').value = avg;
        document.getElementById('e_stat_lead').value = lead;
        document.getElementById('e_stat_safety').value = safety;

        document.getElementById('e_stat_status').value = is_active == '0' || is_active == false || is_active === 'Tidak Aktif' ? '0' : '1';

        document.getElementById('editStationeryModal').classList.remove('hidden');
    }

    window.filterStationery = function() {
        clearTimeout(timerStationery);
        timerStationery = setTimeout(() => {
            let search = document.getElementById('statSearch').value.toLowerCase();
            let kategori = document.getElementById('statKategori').value.toLowerCase();
            let status = document.getElementById('statStatus').value;
            let valMulai = document.getElementById('statFilterMulai').value;
            let valAkhir = document.getElementById('statFilterAkhir').value;
            let rows = document.querySelectorAll('.stat-row');
            let count = 1;

            rows.forEach(row => {
                let rNama = row.dataset.nama;
                let rKategori = row.dataset.kategori;
                let rTanggal = row.dataset.tanggal;
                let rStatus = row.dataset.status;

                let matchSearch = rNama.includes(search);
                let matchKategori = kategori === "" || rKategori === kategori;
                let matchStatus = status === "" || rStatus === status;

                let matchTgl = true;
                if (valMulai !== "" && rTanggal < valMulai) matchTgl = false;
                if (valAkhir !== "" && rTanggal > valAkhir) matchTgl = false;

                if (matchSearch && matchKategori && matchStatus && matchTgl) {
                    row.style.display = '';
                    let rowNumber = row.querySelector('.row-number');
                    if (rowNumber) rowNumber.textContent = count++;
                }
                else {
                    row.style.display = 'none';
                    let cb = row.querySelector('.checkbox-item');
                    if(cb) cb.checked = false;
                }
            });

            window.perbaruiStatusCentang();

        }, 300);
    }

    window.filterTransaksiTable = function() {
        clearTimeout(timerTransaksi);
        timerTransaksi = setTimeout(() => {
            let valMulai = document.getElementById('trxFilterMulai').value;
            let valAkhir = document.getElementById('trxFilterAkhir').value;
            let rows = document.querySelectorAll('.trx-row');
            let count = 1;

            rows.forEach(row => {
                let dateStr = row.getAttribute('data-tanggal');
                let matchTgl = true;

                if (dateStr) {
                    if (valMulai !== "" && dateStr < valMulai) matchTgl = false;
                    if (valAkhir !== "" && dateStr > valAkhir) matchTgl = false;
                } else if (valMulai !== "" || valAkhir !== "") { matchTgl = false; }

                if (matchTgl) {
                    row.style.display = ''; let numCol = row.querySelector('.trx-row-number');
                    if (numCol) numCol.textContent = count++;
                } else { row.style.display = 'none'; }
            });
        }, 300);
    };

    window.updateStationeryChart = function(trigger) {
        let monthFilter = document.getElementById('chartFilterBulan');
        let dayFilter = document.getElementById('chartFilterHari');
        let itemFilter = document.getElementById('chartFilterBarang');

        if (trigger === 'bulan' && monthFilter.value !== '') dayFilter.value = '';
        else if (trigger === 'hari' && dayFilter.value !== '') monthFilter.value = '';

        let month = monthFilter ? monthFilter.value : '';
        let day = dayFilter ? dayFilter.value : '';
        let rawItemValue = itemFilter ? itemFilter.value : 'top5';
        let isSpecificItem = !isNaN(rawItemValue) && rawItemValue.trim() !== '';

        let sortMode = 'top5'; let itemText = String(rawItemValue).toLowerCase();
        if (itemText.includes('bottom') || itemText.includes('dikit')) { sortMode = 'bottom5'; }
        else if (itemText === 'all' || itemText.includes('semua')) { sortMode = 'all'; }

        let priceMap = {};
        masterData.forEach(m => {
            let rawHarga = String(m.harga_unit || m.harga_satuan || m.harga || '0');
            priceMap[m.id] = parseFloat(rawHarga.replace(/[^0-9]/g, '')) || 0;
        });

        let totalIn = 0, totalOut = 0, nominalPemakaian = 0;
        let aggregatedOutQty = {}; let aggregatedOutFreq = {}; let activeItems = new Set();

        if (!isSpecificItem) {
            masterData.forEach(m => { aggregatedOutQty[m.nama_barang] = 0; aggregatedOutFreq[m.nama_barang] = 0; });
        }

        let filteredTrx = rawTrxData.filter(trx => {
            if (!trx.tanggal) return false;
            let dateOnly = String(trx.tanggal).split(' ')[0] || '';
            let trxMonth = dateOnly.includes('-') ? dateOnly.split('-')[1] : '';
            return (month === '' || trxMonth === month) && (day === '' || dateOnly === day);
        });

        filteredTrx.forEach(trx => {
            activeItems.add(trx.stationery_id);
            let qty = parseFloat(trx.jumlah) || 0;

            if(trx.type && trx.type.toLowerCase() === 'masuk') {
                totalIn += qty;
            } else if (trx.type && trx.type.toLowerCase() === 'keluar') {
                totalOut += qty; nominalPemakaian += (qty * (priceMap[trx.stationery_id] || 0));

                if(!isSpecificItem || trx.stationery_id == rawItemValue) {
                    let name = trx.stationery ? trx.stationery.nama_barang : 'Barang Dihapus';
                    if(aggregatedOutQty[name] === undefined) { aggregatedOutQty[name] = 0; aggregatedOutFreq[name] = 0; }
                    aggregatedOutQty[name] += qty; aggregatedOutFreq[name] += 1;
                }
            }
        });

        let totalAsetGudang = 0, stokHabis = 0;
        masterData.forEach(m => {
            let stok = parseFloat(m.total_stok || m.stok || 0); totalAsetGudang += (stok * (priceMap[m.id] || 0));
            if(stok <= 0) stokHabis++;
        });

        let displayBarangCount = (month === '' && day === '') ? masterData.length : activeItems.size;

        if(document.getElementById('stat-total-barang')) document.getElementById('stat-total-barang').innerText = displayBarangCount;
        if(document.getElementById('stat-total-in')) document.getElementById('stat-total-in').innerText = totalIn;
        if(document.getElementById('stat-total-out')) document.getElementById('stat-total-out').innerText = totalOut;
        if(document.getElementById('stat-stok-habis')) document.getElementById('stat-stok-habis').innerText = stokHabis;
        if (month !== '' || day !== '') {
            if(document.getElementById('title-aset')) document.getElementById('title-aset').innerText = "NILAI PEMAKAIAN (KELUAR)";
            if(document.getElementById('stat-total-aset')) document.getElementById('stat-total-aset').innerText = 'Rp ' + nominalPemakaian.toLocaleString('id-ID');
        } else {
            if(document.getElementById('title-aset')) document.getElementById('title-aset').innerText = "NILAI ASET STOK TOTAL";
            if(document.getElementById('stat-total-aset')) document.getElementById('stat-total-aset').innerText = 'Rp ' + totalAsetGudang.toLocaleString('id-ID');
        }

        let sortedDataQty = Object.keys(aggregatedOutQty).map(key => ({ name: key, qty: aggregatedOutQty[key] }));
        if (isSpecificItem) sortedDataQty.sort((a, b) => b.qty - a.qty);
        else if (sortMode === 'top5') sortedDataQty = sortedDataQty.filter(d => d.qty > 0).sort((a, b) => b.qty - a.qty).slice(0, 5);
        else if (sortMode === 'bottom5') sortedDataQty = sortedDataQty.filter(d => d.qty > 0).sort((a, b) => a.qty - b.qty).slice(0, 5);
        else sortedDataQty.sort((a, b) => b.qty - a.qty);

        let sortedDataFreq = Object.keys(aggregatedOutFreq).map(key => ({ name: key, freq: aggregatedOutFreq[key] }));
        if (isSpecificItem) sortedDataFreq.sort((a, b) => b.freq - a.freq);
        else if (sortMode === 'top5') sortedDataFreq = sortedDataFreq.filter(d => d.freq > 0).sort((a, b) => b.freq - a.freq).slice(0, 5);
        else if (sortMode === 'bottom5') sortedDataFreq = sortedDataFreq.filter(d => d.freq > 0).sort((a, b) => a.freq - b.freq).slice(0, 5);
        else sortedDataFreq.sort((a, b) => b.freq - a.freq);

        if (sortedDataQty.length === 0 || sortedDataQty.reduce((sum, d) => sum + d.qty, 0) === 0) sortedDataQty = [{ name: 'Belum ada data', qty: 1 }];
        if (sortedDataFreq.length === 0 || sortedDataFreq.reduce((sum, d) => sum + d.freq, 0) === 0) sortedDataFreq = [{ name: 'Belum ada data', freq: 1 }];

        const paletteQty = ['#38bdf8', '#fb7185', '#fbbf24', '#34d399', '#a78bfa', '#60a5fa', '#f472b6', '#fcd34d'];
        const paletteFreq = ['#818cf8', '#34d399', '#f87171', '#fbbf24', '#c084fc', '#38bdf8', '#fb7185', '#a3e635'];

        if(statChartInstance) { statChartInstance.destroy(); statChartInstance = null; }
        if(statFreqChartInstance) { statFreqChartInstance.destroy(); statFreqChartInstance = null; }

        const wrapper1 = document.getElementById('wrapperQty'); const wrapper2 = document.getElementById('wrapperFreq');
        if(!wrapper1 || !wrapper2) return;

        wrapper1.innerHTML = '<canvas id="stationeryOutChart" class="w-full h-full"></canvas>';
        wrapper2.innerHTML = '<canvas id="stationeryFreqChart" class="w-full h-full"></canvas>';

        try {
            statChartInstance = new Chart(document.getElementById('stationeryOutChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: sortedDataQty.map(d => d.name),
                    datasets: [{ data: sortedDataQty.map(d => d.qty), backgroundColor: sortedDataQty.map((_, i) => sortedDataQty[0].name === 'Belum ada data' ? '#e2e8f0' : paletteQty[i % paletteQty.length]), borderWidth: 2, borderColor: '#ffffff', hoverOffset: 10 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, animation: { duration: 800 }, layout: { padding: 40 },
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.96)', titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12, weight: '500' }, padding: 10, cornerRadius: 8, displayColors: true, boxPadding: 5, callbacks: { label: (context) => context.label === 'Belum ada data' ? ' 0 Unit' : ` ${context.raw} Unit Terpakai` } },
                        datalabels: { labels: { name: { align: 'end', anchor: 'end', color: '#1f2937', font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }, formatter: (v, ctx) => ctx.chart.data.labels[ctx.dataIndex] === 'Belum ada data' ? '' : ctx.chart.data.labels[ctx.dataIndex] }, value: { align: 'center', anchor: 'center', color: '#1f2937', font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'normal' }, formatter: (value, context) => { if (context.chart.data.labels[context.dataIndex] === 'Belum ada data') return ''; let dataArr = context.chart.data.datasets[0].data; let sum = dataArr.reduce((a, b) => a + b, 0); return (value * 100 / sum).toFixed(1) + "%"; } } } }
                    }
                }
            });
        } catch(e) { console.error(e); }

        try {
            statFreqChartInstance = new Chart(document.getElementById('stationeryFreqChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: sortedDataFreq.map(d => d.name),
                    datasets: [{ data: sortedDataFreq.map(d => d.freq), backgroundColor: sortedDataFreq.map((_, i) => sortedDataFreq[0].name === 'Belum ada data' ? '#e2e8f0' : paletteFreq[i % paletteFreq.length]), borderWidth: 2, borderColor: '#ffffff', hoverOffset: 10 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, animation: { duration: 800 }, layout: { padding: 40 },
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.96)', titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12, weight: '500' }, padding: 10, cornerRadius: 8, displayColors: true, boxPadding: 5, callbacks: { label: (context) => context.label === 'Belum ada data' ? ' 0 Kali' : ` ${context.raw} Kali Pengambilan` } },
                        datalabels: { labels: { name: { align: 'end', anchor: 'end', color: '#1f2937', font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }, formatter: (v, ctx) => ctx.chart.data.labels[ctx.dataIndex] === 'Belum ada data' ? '' : ctx.chart.data.labels[ctx.dataIndex] }, value: { align: 'center', anchor: 'center', color: '#1f2937', font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'normal' }, formatter: (value, context) => { if (context.chart.data.labels[context.dataIndex] === 'Belum ada data') return ''; let dataArr = context.chart.data.datasets[0].data; let sum = dataArr.reduce((a, b) => a + b, 0); return (value * 100 / sum).toFixed(1) + "%"; } } } }
                    }
                }
            });
        } catch(e) { console.error(e); }
    };

    async function downloadExcelLaporan() {
        if (typeof ExcelJS === 'undefined') { Swal.fire('Error', 'Library ExcelJS gagal dimuat.', 'error'); return; }

        let search = document.getElementById('statSearch') ? document.getElementById('statSearch').value.toLowerCase() : '';
        let kategori = document.getElementById('statKategori') ? document.getElementById('statKategori').value.toLowerCase() : '';

        let isTabLaporan = !document.getElementById('tab-laporan-stationery').classList.contains('hidden');
        let valMulai = isTabLaporan ? (document.getElementById('lapFilterMulai') ? document.getElementById('lapFilterMulai').value : "") : (document.getElementById('statFilterMulai') ? document.getElementById('statFilterMulai').value : "");
        let valAkhir = isTabLaporan ? (document.getElementById('lapFilterAkhir') ? document.getElementById('lapFilterAkhir').value : "") : (document.getElementById('statFilterAkhir') ? document.getElementById('statFilterAkhir').value : "");

        const wb = new ExcelJS.Workbook();
        const styleHeader = (worksheet) => { const headerRow = worksheet.getRow(1); headerRow.font = { bold: true, color: { argb: 'FFFFFFFF' } }; headerRow.fill = { type: 'pattern', pattern:'solid', fgColor: { argb:'FF004A99' } }; headerRow.alignment = { vertical: 'middle', horizontal: 'center' }; };

        const ws1 = wb.addWorksheet('Ending Stock');
        ws1.columns = [
            { header: 'Tanggal', key: 'tgl', width: 15 }, { header: 'Kode', key: 'kode', width: 15 }, { header: 'Material Name', key: 'nama', width: 40 },
            { header: 'Kategori', key: 'kategori', width: 25 }, { header: 'Satuan', key: 'satuan', width: 12 }, { header: 'Stok Awal', key: 'awal', width: 12 },
            { header: 'HargaUnit', key: 'harga', width: 18 }, { header: 'Rata2 Pakai', key: 'rata', width: 15 }, { header: 'Paling Banyak', key: 'paling_banyak', width: 20 },
            { header: 'Lead Time', key: 'lead', width: 15 }, { header: 'Safety Stock', key: 'safety', width: 15 }, { header: 'Batas Minimum', key: 'min', width: 15 },
            { header: 'Keluar', key: 'keluar', width: 12 }, { header: 'Masuk', key: 'masuk', width: 12 }, { header: 'Total Stok', key: 'total', width: 15 },
            { header: 'Status Order', key: 'cek', width: 18 }
        ];

        let dataMasterFiltered = masterData.filter(item => {
            let isActive = item.is_active !== undefined ? (item.is_active == 1 || item.is_active == true || item.is_active === '1' || item.is_active === 'Aktif') : true;
            if (!isActive) return false;

            let rNama = (item.kode + ' ' + item.nama_barang).toLowerCase(); let rKategori = (item.kategori || '').toLowerCase();
            let matchSearch = search === "" || rNama.includes(search); let matchKategori = kategori === "" || rKategori === kategori;

            let matchTgl = true;
            if(item.updated_at) {
                let cleanDate = String(item.updated_at).split('T')[0].split(' ')[0];
                if (valMulai !== "" && cleanDate < valMulai) matchTgl = false;
                if (valAkhir !== "" && cleanDate > valAkhir) matchTgl = false;
            } else if (valMulai !== "" || valAkhir !== "") { matchTgl = false; }

            return matchSearch && matchKategori && matchTgl;
        });

        dataMasterFiltered.forEach(item => {
            let hrgStr = String(item.harga_unit || item.harga_satuan || '0').replace(/[^0-9]/g, ''); let hrg = parseFloat(hrgStr) || 0;
            let awal = parseFloat(item.stok_awal || 0); let masuk = parseFloat(item.stok_masuk || 0); let keluar = parseFloat(item.stok_keluar || 0); let sisa = parseFloat(item.total_stok || item.stok || 0);
            let batas = parseFloat(item.min_stok || item.batas_minimum || 5);
            let status = sisa <= batas ? "Order" : "Aman";
            let cleanDate = String(item.updated_at).split('T')[0].split(' ')[0]; let [y, m, d] = cleanDate.split('-'); let dateFormatted = new Date(y, m-1, d, 12, 0, 0);

            let row = ws1.addRow({
                tgl: dateFormatted, kode: item.kode || '-', nama: item.nama_barang || '-', kategori: item.kategori || '-', satuan: item.satuan || '-', awal: awal, harga: hrg,
                rata: (Math.round(parseFloat(item.rata_pakai_otomatis)) || Math.round(parseFloat(item.avg_penggunaan)) || 0) + ' ' + (item.satuan || ''),
                paling_banyak: item.pemakaian_terbanyak || '-', lead: item.lead_time || 0, safety: item.safety_stock || 0, min: batas, keluar: keluar, masuk: masuk, total: sisa, cek: status
            });

            if (status === "Order") { row.getCell('cek').font = { color: { argb: 'FFDC2626' }, bold: true }; } else { row.getCell('cek').font = { color: { argb: 'FF16A34A' }, bold: true }; }
        });
        styleHeader(ws1); ws1.getColumn('tgl').numFmt = 'yyyy-mm-dd'; ws1.autoFilter = 'A1:P1';

        const ws2 = wb.addWorksheet('Barang Masuk');
        ws2.columns = [ { header: 'Tanggal', key: 'tgl', width: 15 }, { header: 'Material Dokument', key: 'dok', width: 20 }, { header: 'Material Number', key: 'kode', width: 20 }, { header: 'Material Name', key: 'nama', width: 40 }, { header: 'Satuan', key: 'satuan', width: 12 }, { header: 'Jumlah', key: 'jumlah', width: 12 }, { header: 'Total Harga', key: 'total_harga', width: 18 }, { header: 'Harga Unit', key: 'harga', width: 18 }, { header: 'Vendor', key: 'vendor', width: 30 } ];

        let dataMasuk = rawTrxData.filter(t => (t.type || '').toLowerCase() === 'masuk');
        dataMasuk = dataMasuk.filter(t => {
            if(!t.tanggal) return false;
            let cleanDate = String(t.tanggal).split('T')[0].split(' ')[0]; let matchTgl = true;
            if (valMulai !== "" && cleanDate < valMulai) matchTgl = false;
            if (valAkhir !== "" && cleanDate > valAkhir) matchTgl = false;

            let rNama = (t.stationery ? t.stationery.kode + ' ' + t.stationery.nama_barang : '').toLowerCase();
            let matchSearch = search === "" || rNama.includes(search);
            return matchTgl && matchSearch;
        });
        dataMasuk.forEach(t => {
            let item = t.stationery || {}; let hrgStr = String(item.harga_unit || item.harga_satuan || '0').replace(/[^0-9]/g, ''); let hrg = parseFloat(hrgStr) || 0; let qty = parseFloat(t.jumlah || 0);
            let cleanDate = String(t.tanggal).split('T')[0].split(' ')[0]; let [y, m, d] = cleanDate.split('-'); let dateObj = new Date(y, m-1, d, 12, 0, 0);
            ws2.addRow({ tgl: dateObj, dok: t.no_dokumen || '-', kode: item.kode || '-', nama: item.nama_barang || '-', satuan: item.satuan || '-', jumlah: qty, total_harga: qty * hrg, harga: hrg, vendor: t.vendor || '-' });
        });
        styleHeader(ws2); ws2.getColumn('tgl').numFmt = 'yyyy-mm-dd'; ws2.autoFilter = 'A1:I1';

        const ws3 = wb.addWorksheet('Barang Keluar');
        ws3.columns = [ { header: 'NO FPB', key: 'fpb', width: 15 }, { header: 'Tanggal', key: 'tgl', width: 15 }, { header: 'Material Number', key: 'kode', width: 20 }, { header: 'MatName', key: 'nama', width: 40 }, { header: 'Satuan', key: 'satuan', width: 12 }, { header: 'Jumlah', key: 'jumlah', width: 12 }, { header: 'Dept', key: 'dept', width: 15 }, { header: 'Ket', key: 'ket', width: 25 }, { header: 'Kategori', key: 'kategori', width: 20 }, { header: 'Harga', key: 'harga', width: 18 }, { header: 'Harga Total', key: 'total_harga', width: 18 } ];
        let noFpbFallback = 1;

        let dataKeluar = rawTrxData.filter(t => (t.type || '').toLowerCase() === 'keluar');
        dataKeluar = dataKeluar.filter(t => {
            if(!t.tanggal) return false;
            let cleanDate = String(t.tanggal).split('T')[0].split(' ')[0]; let matchTgl = true;
            if (valMulai !== "" && cleanDate < valMulai) matchTgl = false;
            if (valAkhir !== "" && cleanDate > valAkhir) matchTgl = false;

            let rNama = (t.stationery ? t.stationery.kode + ' ' + t.stationery.nama_barang : '').toLowerCase();
            let matchSearch = search === "" || rNama.includes(search);
            return matchTgl && matchSearch;
        });
        dataKeluar.forEach(t => {
            let item = t.stationery || {}; let hrgStr = String(item.harga_unit || item.harga_satuan || '0').replace(/[^0-9]/g, ''); let hrg = parseFloat(hrgStr) || 0; let qty = parseFloat(t.jumlah || 0);
            let cleanDate = String(t.tanggal).split('T')[0].split(' ')[0]; let [y, m, d] = cleanDate.split('-'); let dateObj = new Date(y, m-1, d, 12, 0, 0);
            ws3.addRow({ fpb: t.no_fpb || noFpbFallback++, tgl: dateObj, kode: item.kode || '-', nama: item.nama_barang || '-', satuan: item.satuan || '-', jumlah: qty, dept: t.dept || '-', ket: t.keterangan || '-', kategori: item.kategori || '-', harga: hrg, total_harga: qty * hrg });
        });
        styleHeader(ws3); ws3.getColumn('tgl').numFmt = 'yyyy-mm-dd'; ws3.autoFilter = 'A1:K1';

let dateStr = "";
        if(valMulai && valAkhir) dateStr = valMulai + "_sd_" + valAkhir;
        else if (valMulai) dateStr = "Mulai_" + valMulai;
        else if (valAkhir) dateStr = "Sampai_" + valAkhir;
        else dateStr = new Date().toISOString().slice(0, 10);

        applyFullBorder(ws1);
        applyFullBorder(ws2);
        applyFullBorder(ws3);

const buffer = await wb.xlsx.writeBuffer(); const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" }); saveAs(blob, "Laporan_Indofood_Stationery_" + dateStr + ".xlsx");
    }

    window.downloadPDFPengadaan = function() {
        if (!window.jspdf) {
            Swal.fire('Error', 'Library jsPDF gagal dimuat. Pastikan koneksi internet aktif.', 'error');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');

        let valMulai = document.getElementById('pengadaanFilterMulai').value;
        let valAkhir = document.getElementById('pengadaanFilterAkhir').value;
        let searchInput = document.getElementById('searchPengadaan');
        let searchVal = searchInput ? searchInput.value.toLowerCase() : '';
        let dataPengadaanRaw = @json($dataPengadaan ?? []);

        let filteredData = dataPengadaanRaw.filter(p => {
            let dateStr = p.tgl_pr;
            let matchTgl = true;

            if (dateStr) {
                if (valMulai !== "" && dateStr < valMulai) matchTgl = false;
                if (valAkhir !== "" && dateStr > valAkhir) matchTgl = false;
            } else if (valMulai !== "" || valAkhir !== "") { matchTgl = false; }

            let statKode = p.stationery ? p.stationery.kode : '-';
            let statNama = p.stationery ? p.stationery.nama_barang : 'Barang Dihapus';

            let textContent = (p.no_pr + " " + statKode + " " + statNama + " " + (p.no_po||"") + " " + (p.status||"")).toLowerCase();
            let matchSearch = searchVal === "" || textContent.includes(searchVal);

            return matchTgl && matchSearch;
        });

        const columns = [
            { header: 'No', dataKey: 'no' },
            { header: 'Tanggal PR', dataKey: 'tgl_pr' },
            { header: 'No. PR', dataKey: 'no_pr' },
            { header: 'Kode Barang', dataKey: 'kode' },
            { header: 'Nama Barang', dataKey: 'nama' },
            { header: 'Qty', dataKey: 'qty' },
            { header: 'No. PO', dataKey: 'no_po' },
            { header: 'Status', dataKey: 'status' }
        ];

        const rows = filteredData.map((p, index) => {
            return {
                no: index + 1,
                tgl_pr: p.tgl_pr ? p.tgl_pr.split(' ')[0] : '-',
                no_pr: p.no_pr || '-',
                kode: p.stationery ? p.stationery.kode : '-',
                nama: p.stationery ? p.stationery.nama_barang : 'Barang Dihapus',
                qty: p.qty_item || 0,
                no_po: p.no_po || '-',
                status: p.status || '-'
            };
        });

        doc.setFontSize(16);
        doc.text("Laporan Data Pengadaan (PR/PO)", 40, 40);

        doc.setFontSize(10);
        doc.setTextColor(100);
        let subtitle = "Tanggal Cetak: " + new Date().toLocaleDateString('id-ID');
        if (valMulai || valAkhir) subtitle += `   |   Filter Tgl: ${valMulai || '...'} s/d ${valAkhir || '...'}`;
        if (searchVal) subtitle += `   |   Pencarian: "${searchVal}"`;
        doc.text(subtitle, 40, 55);

        doc.autoTable({
            startY: 65,
            columns: columns,
            body: rows,
            theme: 'grid',
            headStyles: {
                fillColor: [0, 74, 153],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center'
            },
            styles: { fontSize: 8, cellPadding: 4 },
            columnStyles: {
                no: { halign: 'center', cellWidth: 30 },
                tgl_pr: { halign: 'center', cellWidth: 70 },
                no_pr: { halign: 'center', cellWidth: 70 },
                kode: { halign: 'center', cellWidth: 70 },
                qty: { halign: 'center', cellWidth: 50 },
                status: { halign: 'center', fontStyle: 'bold', cellWidth: 60 }
            },
            didParseCell: function (data) {
                if (data.section === 'body' && data.column.dataKey === 'status') {
                    if (data.cell.raw === 'Menunggu') {
                        data.cell.styles.textColor = [217, 119, 6];
                    } else if (data.cell.raw === 'Selesai') {
                        data.cell.styles.textColor = [5, 150, 105];
                    }
                }
            }
        });

        let dateStrFile = new Date().toISOString().slice(0, 10);
        let labelStr = "";
        if(valMulai && valAkhir) labelStr = `_${valMulai}_sd_${valAkhir}`;
        else if (valMulai) labelStr = `_Mulai_${valMulai}`;
        else if (valAkhir) labelStr = `_Sampai_${valAkhir}`;

        doc.save(`Laporan_Pengadaan_PO${labelStr}_${dateStrFile}.pdf`);
    };

    window.downloadPDFMaster = function() {
        if (!window.jspdf) {
            Swal.fire('Error', 'Library jsPDF gagal dimuat. Pastikan koneksi internet aktif.', 'error');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');

        let search = document.getElementById('statSearch') ? document.getElementById('statSearch').value.toLowerCase() : '';
        let kategori = document.getElementById('statKategori') ? document.getElementById('statKategori').value.toLowerCase() : '';
        let valMulai = document.getElementById('statFilterMulai') ? document.getElementById('statFilterMulai').value : "";
        let valAkhir = document.getElementById('statFilterAkhir') ? document.getElementById('statFilterAkhir').value : "";

        let dataMasterFiltered = masterData.filter(item => {
            let isActive = item.is_active !== undefined ? (item.is_active == 1 || item.is_active == true || item.is_active === '1' || item.is_active === 'Aktif') : true;
            if (!isActive) return false;

            let rNama = (item.kode + ' ' + item.nama_barang).toLowerCase();
            let rKategori = (item.kategori || '').toLowerCase();
            let matchSearch = search === "" || rNama.includes(search);
            let matchKategori = kategori === "" || rKategori === kategori;

            let matchTgl = true;
            if(item.updated_at) {
                let cleanDate = String(item.updated_at).split('T')[0].split(' ')[0];
                if (valMulai !== "" && cleanDate < valMulai) matchTgl = false;
                if (valAkhir !== "" && cleanDate > valAkhir) matchTgl = false;
            } else if (valMulai !== "" || valAkhir !== "") { matchTgl = false; }

            return matchSearch && matchKategori && matchTgl;
        });

        const columns = [
            { header: 'No', dataKey: 'no' },
            { header: 'Kode', dataKey: 'kode' },
            { header: 'Nama Barang', dataKey: 'nama' },
            { header: 'Kategori', dataKey: 'kategori' },
            { header: 'Satuan', dataKey: 'satuan' },
            { header: 'Awal', dataKey: 'awal' },
            { header: 'Masuk', dataKey: 'masuk' },
            { header: 'Keluar', dataKey: 'keluar' },
            { header: 'Sisa Stok', dataKey: 'sisa' },
            { header: 'Status', dataKey: 'status' }
        ];

        const rows = dataMasterFiltered.map((item, index) => {
            let sisa = parseFloat(item.total_stok || item.stok || 0);
            let batas = parseFloat(item.min_stok || item.batas_minimum || 5);
            let status = sisa <= batas ? "ORDER" : "AMAN";

            return {
                no: index + 1,
                kode: item.kode || '-',
                nama: item.nama_barang || '-',
                kategori: item.kategori || '-',
                satuan: item.satuan || '-',
                awal: item.stok_awal || 0,
                masuk: item.stok_masuk || 0,
                keluar: item.stok_keluar || 0,
                sisa: sisa,
                status: status
            };
        });

        doc.setFontSize(16);
        doc.text("Laporan Master Data Stationery", 40, 40);

        doc.setFontSize(10);
        doc.setTextColor(100);
        let subtitle = "Tanggal Cetak: " + new Date().toLocaleDateString('id-ID');
        if (valMulai || valAkhir) subtitle += `   |   Filter Tgl: ${valMulai || '...'} s/d ${valAkhir || '...'}`;
        if (kategori) subtitle += `   |   Kategori: ${kategori}`;
        doc.text(subtitle, 40, 55);

        doc.autoTable({
            startY: 65,
            columns: columns,
            body: rows,
            theme: 'grid',
            headStyles: {
                fillColor: [0, 74, 153],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center'
            },
            styles: { fontSize: 8, cellPadding: 4 },
            columnStyles: {
                no: { halign: 'center', cellWidth: 30 },
                kode: { halign: 'center', cellWidth: 70 },
                satuan: { halign: 'center', cellWidth: 50 },
                awal: { halign: 'center', cellWidth: 40 },
                masuk: { halign: 'center', cellWidth: 40 },
                keluar: { halign: 'center', cellWidth: 40 },
                sisa: { halign: 'center', fontStyle: 'bold', cellWidth: 60 },
                status: { halign: 'center', fontStyle: 'bold', cellWidth: 60 }
            },
            didParseCell: function (data) {
                if (data.section === 'body' && data.column.dataKey === 'status') {
                    if (data.cell.raw === 'ORDER') {
                        data.cell.styles.textColor = [220, 38, 38];
                    } else {
                        data.cell.styles.textColor = [22, 163, 74];
                    }
                }
            }
        });

        let dateStr = new Date().toISOString().slice(0, 10);
        doc.save(`Laporan_Master_Stok_${dateStr}.pdf`);
    };

    window.switchLaporan = function(tabName) {
        const btnOrder = document.getElementById('btnTabLaporanOrder');
        const btnPemakaian = document.getElementById('btnTabLaporanPemakaian');
        const contOrder = document.getElementById('container-laporan-order');
        const contPemakaian = document.getElementById('container-laporan-pemakaian');
        const searchInput = document.getElementById('laporanGlobalSearch');
        const filterStatusContainer = document.getElementById('filterStatusOrderContainer');

        if (searchInput) { searchInput.value = ''; filterLaporanTables(); }

        if (tabName === 'order') {
            btnOrder.className = 'px-5 py-2 rounded-lg bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition';
            btnPemakaian.className = 'px-5 py-2 rounded-lg bg-transparent text-gray-500 hover:text-gray-700 text-[10px] font-black uppercase tracking-widest transition';
            contOrder.classList.remove('hidden'); contOrder.classList.add('flex');
            contPemakaian.classList.add('hidden'); contPemakaian.classList.remove('flex');
            if (filterStatusContainer) filterStatusContainer.classList.remove('hidden');
        } else {
            btnPemakaian.className = 'px-5 py-2 rounded-lg bg-red-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md transition';
            btnOrder.className = 'px-5 py-2 rounded-lg bg-transparent text-gray-500 hover:text-gray-700 text-[10px] font-black uppercase tracking-widest transition';
            contPemakaian.classList.remove('hidden'); contPemakaian.classList.add('flex');
            contOrder.classList.add('hidden'); contOrder.classList.remove('flex');
            if (filterStatusContainer) filterStatusContainer.classList.add('hidden');
        }
    };

    window.filterLaporanTables = function() {
        clearTimeout(timerLaporan);
        timerLaporan = setTimeout(() => {
            const term = document.getElementById('laporanGlobalSearch').value.toLowerCase();
            const tglMulai = document.getElementById('lapFilterMulai') ? document.getElementById('lapFilterMulai').value : '';
            const tglAkhir = document.getElementById('lapFilterAkhir') ? document.getElementById('lapFilterAkhir').value : '';
            const statusFilter = document.getElementById('lapFilterStatus') ? document.getElementById('lapFilterStatus').value.toLowerCase() : '';
            const kategoriFilter = document.getElementById('lapFilterKategori') ? document.getElementById('lapFilterKategori').value.toLowerCase() : '';
            const deptFilter = document.getElementById('lapFilterDept') ? document.getElementById('lapFilterDept').value.toLowerCase() : '';

document.querySelectorAll('.row-lap-order').forEach(row => {
                const text = row.getAttribute('data-pencarian') || row.textContent.toLowerCase();
                const dateStr = row.getAttribute('data-tanggal');
                const statusStr = row.getAttribute('data-status') || '';

                let matchSearch = text.includes(term);
                let matchDate = true;
                let matchStatus = statusFilter === "" || statusStr.includes(statusFilter);

                if (dateStr) {
                    if (tglMulai !== "" && dateStr < tglMulai) matchDate = false;
                    if (tglAkhir !== "" && dateStr > tglAkhir) matchDate = false;
                } else if (tglMulai !== "" || tglAkhir !== "") { matchDate = false; }

                row.style.display = (matchSearch && matchDate && matchStatus) ? '' : 'none';
            });

document.querySelectorAll('.row-lap-pemakaian').forEach(row => {
                const text = row.getAttribute('data-pencarian') || row.textContent.toLowerCase();
                const dateStr = row.getAttribute('data-tanggal');
                const deptStr = row.getAttribute('data-dept') || '';
                const katStr = row.getAttribute('data-kategori') || '';

                let matchSearch = text.includes(term);
                let matchDate = true;
                let matchDept = deptFilter === "" || deptStr === deptFilter;
                let matchKat = kategoriFilter === "" || katStr === kategoriFilter;

                if (dateStr) {
                    if (tglMulai !== "" && dateStr < tglMulai) matchDate = false;
                    if (tglAkhir !== "" && dateStr > tglAkhir) matchDate = false;
                } else if (tglMulai !== "" || tglAkhir !== "") { matchDate = false; }

                row.style.display = (matchSearch && matchDate && matchDept && matchKat) ? '' : 'none';
            });
        }, 300);
    };

    window.exportLaporanData = async function(tipe) {
        if (typeof ExcelJS === 'undefined') { Swal.fire('Error', 'Library ExcelJS gagal dimuat.', 'error'); return; }

        const wb = new ExcelJS.Workbook();
        const ws = wb.addWorksheet(tipe === 'order' ? 'Laporan Order' : 'Laporan Pemakaian');

        const styleHeader = (worksheet) => {
            const headerRow = worksheet.getRow(1);
            headerRow.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            headerRow.fill = { type: 'pattern', pattern:'solid', fgColor: { argb:'FF004A99' } };
            headerRow.alignment = { vertical: 'middle', horizontal: 'center' };
        };

        let tglMulai = document.getElementById('lapFilterMulai') ? document.getElementById('lapFilterMulai').value : '';
        let tglAkhir = document.getElementById('lapFilterAkhir') ? document.getElementById('lapFilterAkhir').value : '';
        let search = document.getElementById('laporanGlobalSearch') ? document.getElementById('laporanGlobalSearch').value.toLowerCase() : '';

        if (tipe === 'order') {
            ws.columns = [
                { header: 'Kode Barang', key: 'kode', width: 15 },
                { header: 'Nama Barang', key: 'nama', width: 40 },
                { header: 'Stock Now', key: 'stok', width: 15 },
                { header: 'Rata-rata Penggunaan/Hari', key: 'rata', width: 30 },
                { header: 'Pemakai', key: 'pemakai', width: 30 },
                { header: 'Terakhir Order', key: 'last_order', width: 20 },
                { header: 'Status', key: 'status', width: 15 }
            ];

            let statusFilter = document.getElementById('lapFilterStatus') ? document.getElementById('lapFilterStatus').value.toLowerCase() : '';

            let filtered = masterData.filter(item => {
                let isActive = item.is_active !== undefined ? (item.is_active == 1 || item.is_active == true || item.is_active === '1' || item.is_active === 'Aktif') : true;
                if (!isActive) return false;

                let rNama = (item.kode + ' ' + item.nama_barang).toLowerCase();
                let matchSearch = search === "" || rNama.includes(search);
                let dateStr = item.terakhir_order ? item.terakhir_order.split(' ')[0] : '';

                let itemStatus = (item.status_reorder || '').toLowerCase();
                let matchStatus = statusFilter === "" || itemStatus.includes(statusFilter);

                let matchDate = true;
                if (dateStr) {
                    if (tglMulai !== "" && dateStr < tglMulai) matchDate = false;
                    if (tglAkhir !== "" && dateStr > tglAkhir) matchDate = false;
                } else if (tglMulai !== "" || tglAkhir !== "") {
                    matchDate = false;
                }
                return matchSearch && matchDate && matchStatus;
            });

            filtered.forEach(item => {
                ws.addRow({
                    kode: item.kode,
                    nama: item.nama_barang,
                    stok: item.total_stok,
                    rata: item.rata_penggunaan + ' ' + item.satuan,
                    pemakai: item.siapa_pengguna,
                    last_order: item.terakhir_order ? item.terakhir_order.split(' ')[0] : 'Belum pernah order',
                    status: item.status_reorder
                });
            });
        } else {
            ws.columns = [
                { header: 'No PR (FPB)', key: 'pr', width: 15 },
                { header: 'Tanggal PR', key: 'tgl', width: 15 },
                { header: 'Kode Barang', key: 'kode', width: 15 },
                { header: 'Nama Material', key: 'nama', width: 40 },
                { header: 'Pemakai / Dept', key: 'pemakai', width: 20 },
                { header: 'Jumlah Terpakai', key: 'jumlah', width: 15 }
            ];

            let kategoriFilter = document.getElementById('lapFilterKategori') ? document.getElementById('lapFilterKategori').value.toLowerCase() : '';
            let deptFilter = document.getElementById('lapFilterDept') ? document.getElementById('lapFilterDept').value.toLowerCase() : '';

            let dataKeluar = rawTrxData.filter(t => (t.type || '').toLowerCase() === 'keluar');
            let filtered = dataKeluar.filter(t => {
                let rNama = (t.stationery ? t.stationery.kode + ' ' + t.stationery.nama_barang : '').toLowerCase();
                let matchSearch = search === "" || rNama.includes(search);
                let dateStr = t.tanggal ? String(t.tanggal).split('T')[0].split(' ')[0] : '';

                let rDept = (t.dept || '').toLowerCase();
                let rKat = (t.stationery && t.stationery.kategori ? t.stationery.kategori : '').toLowerCase();

                let matchDept = deptFilter === "" || rDept === deptFilter;
                let matchKat = kategoriFilter === "" || rKat === kategoriFilter;

                let matchDate = true;
                if (dateStr) {
                    if (tglMulai !== "" && dateStr < tglMulai) matchDate = false;
                    if (tglAkhir !== "" && dateStr > tglAkhir) matchDate = false;
                } else if (tglMulai !== "" || tglAkhir !== "") {
                    matchDate = false;
                }
                return matchSearch && matchDate && matchDept && matchKat;
            });

            filtered.forEach(t => {
                let item = t.stationery || {};
                let dateStr = t.tanggal ? String(t.tanggal).split('T')[0].split(' ')[0] : '-';
                ws.addRow({
                    pr: t.no_fpb || ('PR-' + t.id),
                    tgl: dateStr,
                    kode: item.kode || '-',
                    nama: item.nama_barang || '-',
                    pemakai: t.dept || '-',
                    jumlah: t.jumlah + ' ' + (item.satuan || '')
                });
            });
        }

styleHeader(ws);

        applyFullBorder(ws);

        const buffer = await wb.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        saveAs(blob, `Laporan_${tipe}_Filtered_${new Date().toISOString().slice(0, 10)}.xlsx`);
    };

    window.exportLaporanPDF = function(tipe) {
        if (!window.jspdf) {
            Swal.fire('Error', 'Library jsPDF gagal dimuat. Pastikan koneksi internet aktif.', 'error');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');

        let tglMulai = document.getElementById('lapFilterMulai') ? document.getElementById('lapFilterMulai').value : '';
        let tglAkhir = document.getElementById('lapFilterAkhir') ? document.getElementById('lapFilterAkhir').value : '';
        let search = document.getElementById('laporanGlobalSearch') ? document.getElementById('laporanGlobalSearch').value.toLowerCase() : '';

        let title = "";
        let columns = [];
        let rows = [];
        let headerColor = [];

        if (tipe === 'order') {
            title = "Laporan Order & Stok Stationery";
            headerColor = [0, 74, 153];
            let statusFilter = document.getElementById('lapFilterStatus') ? document.getElementById('lapFilterStatus').value.toLowerCase() : '';

            let filtered = masterData.filter(item => {
                let isActive = item.is_active !== undefined ? (item.is_active == 1 || item.is_active == true || item.is_active === '1' || item.is_active === 'Aktif') : true;
                if (!isActive) return false;

                let rNama = (item.kode + ' ' + item.nama_barang).toLowerCase();
                let matchSearch = search === "" || rNama.includes(search);
                let dateStr = item.terakhir_order ? item.terakhir_order.split(' ')[0] : '';
                let itemStatus = (item.status_reorder || '').toLowerCase();
                let matchStatus = statusFilter === "" || itemStatus.includes(statusFilter);

                let matchDate = true;
                if (dateStr) {
                    if (tglMulai !== "" && dateStr < tglMulai) matchDate = false;
                    if (tglAkhir !== "" && dateStr > tglAkhir) matchDate = false;
                } else if (tglMulai !== "" || tglAkhir !== "") { matchDate = false; }

                return matchSearch && matchDate && matchStatus;
            });

            columns = [
                { header: 'No', dataKey: 'no' },
                { header: 'Kode', dataKey: 'kode' },
                { header: 'Nama Barang', dataKey: 'nama' },
                { header: 'Stock Now', dataKey: 'stok' },
                { header: 'Rata2 Pakai/Hari', dataKey: 'rata' },
                { header: 'Pemakai', dataKey: 'pemakai' },
                { header: 'Terakhir Order', dataKey: 'last_order' },
                { header: 'Status', dataKey: 'status' }
            ];

            rows = filtered.map((item, index) => ({
                no: index + 1,
                kode: item.kode,
                nama: item.nama_barang,
                stok: item.total_stok,
                rata: item.rata_penggunaan + ' ' + item.satuan,
                pemakai: item.siapa_pengguna,
                last_order: item.terakhir_order ? item.terakhir_order.split(' ')[0] : 'Belum pernah order',
                status: (item.status_reorder || '').toUpperCase()
            }));

        } else {
            title = "Laporan Riwayat Pemakaian Stationery";
            headerColor = [185, 28, 28];
            let kategoriFilter = document.getElementById('lapFilterKategori') ? document.getElementById('lapFilterKategori').value.toLowerCase() : '';
            let deptFilter = document.getElementById('lapFilterDept') ? document.getElementById('lapFilterDept').value.toLowerCase() : '';

            let dataKeluar = rawTrxData.filter(t => (t.type || '').toLowerCase() === 'keluar');
            let filtered = dataKeluar.filter(t => {
                let rNama = (t.stationery ? t.stationery.kode + ' ' + t.stationery.nama_barang : '').toLowerCase();
                let matchSearch = search === "" || rNama.includes(search);
                let dateStr = t.tanggal ? String(t.tanggal).split('T')[0].split(' ')[0] : '';

                let rDept = (t.dept || '').toLowerCase();
                let rKat = (t.stationery && t.stationery.kategori ? t.stationery.kategori : '').toLowerCase();

                let matchDept = deptFilter === "" || rDept === deptFilter;
                let matchKat = kategoriFilter === "" || rKat === kategoriFilter;

                let matchDate = true;
                if (dateStr) {
                    if (tglMulai !== "" && dateStr < tglMulai) matchDate = false;
                    if (tglAkhir !== "" && dateStr > tglAkhir) matchDate = false;
                } else if (tglMulai !== "" || tglAkhir !== "") { matchDate = false; }

                return matchSearch && matchDate && matchDept && matchKat;
            });

            columns = [
                { header: 'No', dataKey: 'no' },
                { header: 'No PR (FPB)', dataKey: 'pr' },
                { header: 'Tanggal PR', dataKey: 'tgl' },
                { header: 'Kode Barang', dataKey: 'kode' },
                { header: 'Nama Material', dataKey: 'nama' },
                { header: 'Pemakai / Dept', dataKey: 'pemakai' },
                { header: 'Jumlah Terpakai', dataKey: 'jumlah' }
            ];

            rows = filtered.map((t, index) => {
                let item = t.stationery || {};
                let dateStr = t.tanggal ? String(t.tanggal).split('T')[0].split(' ')[0] : '-';
                return {
                    no: index + 1,
                    pr: t.no_fpb || ('PR-' + t.id),
                    tgl: dateStr,
                    kode: item.kode || '-',
                    nama: item.nama_barang || '-',
                    pemakai: t.dept || '-',
                    jumlah: t.jumlah + ' ' + (item.satuan || '')
                };
            });
        }

        doc.setFontSize(16);
        doc.text(title, 40, 40);

        doc.setFontSize(10);
        doc.setTextColor(100);
        let subtitle = "Tanggal Cetak: " + new Date().toLocaleDateString('id-ID');
        if (tglMulai || tglAkhir) subtitle += `   |   Filter Tgl: ${tglMulai || '...'} s/d ${tglAkhir || '...'}`;
        doc.text(subtitle, 40, 55);

        doc.autoTable({
            startY: 65,
            columns: columns,
            body: rows,
            theme: 'grid',
            headStyles: {
                fillColor: headerColor,
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center'
            },
            styles: { fontSize: 8, cellPadding: 4 },
            columnStyles: {
                no: { halign: 'center', cellWidth: 30 }
            },
            didParseCell: function (data) {
                if (tipe === 'order' && data.section === 'body' && data.column.dataKey === 'status') {
                    if (data.cell.raw === 'PERLU ORDER') {
                        data.cell.styles.textColor = [220, 38, 38];
                        data.cell.styles.fontStyle = 'bold';
                    } else if (data.cell.raw === 'AMAN') {
                        data.cell.styles.textColor = [22, 163, 74];
                        data.cell.styles.fontStyle = 'bold';
                    }
                }
            }
        });

        let dateStrFile = new Date().toISOString().slice(0, 10);
        doc.save(`Laporan_${tipe}_${dateStrFile}.pdf`);
    };

    window.openEditPengadaanModal = function(id, item_id, no_pr, tgl_pr, estimasi_tiba, qty, no_po, vendor, no_dokumen) {
        document.getElementById('formEditPengadaan').action = '/pengadaan/' + id; $('#edit_p_barang').val(item_id).trigger('change');
        document.getElementById('edit_p_no_pr').value = no_pr;
        let formattedDate = tgl_pr; if(tgl_pr && tgl_pr.includes(' ')) { formattedDate = tgl_pr.split(' ')[0]; }
        document.getElementById('edit_p_tgl_pr').value = formattedDate;
        if(document.getElementById('edit_p_estimasi')) { document.getElementById('edit_p_estimasi').value = estimasi_tiba; }
        document.getElementById('edit_p_qty').value = qty;
        let rawPo = no_po || ''; let parsedPo = rawPo; let parsedVendor = ''; let parsedDokumen = '';

        if(rawPo.includes(' | Vendor: ')) {
            let parts = rawPo.split(' | Vendor: '); parsedPo = parts[0]; let rest = parts[1];
            if(rest.includes(' | Dok: ')) { let subParts = rest.split(' | Dok: '); parsedVendor = subParts[0]; parsedDokumen = subParts[1]; } else { parsedVendor = rest; }
        } else if (rawPo.includes(' | Dok: ')) { let parts = rawPo.split(' | Dok: '); parsedPo = parts[0]; parsedDokumen = parts[1]; }

        if(parsedPo === '-') parsedPo = '';
        document.getElementById('edit_p_no_po').value = parsedPo;
        if (document.getElementById('edit_p_vendor')) document.getElementById('edit_p_vendor').value = parsedVendor;
        if (document.getElementById('edit_p_no_dokumen')) document.getElementById('edit_p_no_dokumen').value = parsedDokumen;

        document.getElementById('modalEditPengadaan').classList.remove('hidden');
    };

    window.filterPengadaanTable = function() {
        clearTimeout(timerPengadaan);
        timerPengadaan = setTimeout(() => {
            let valMulai = document.getElementById('pengadaanFilterMulai').value;
            let valAkhir = document.getElementById('pengadaanFilterAkhir').value;
            let searchVal = document.getElementById('searchPengadaan') ? document.getElementById('searchPengadaan').value.toLowerCase() : '';
            let rows = document.querySelectorAll('.pengadaan-row');
            let count = 1;

            rows.forEach(row => {
                let dateStr = row.getAttribute('data-tanggal');
                let textContent = row.textContent.toLowerCase();
                let matchTgl = true;

                if (dateStr) {
                    if (valMulai !== "" && dateStr < valMulai) matchTgl = false;
                    if (valAkhir !== "" && dateStr > valAkhir) matchTgl = false;
                } else if (valMulai !== "" || valAkhir !== "") { matchTgl = false; }

                let matchSearch = searchVal === "" || textContent.includes(searchVal);

                if (matchTgl && matchSearch) {
                    row.style.display = '';
                    row.querySelector('.p-row-number').textContent = count++;
                } else {
                    row.style.display = 'none';
                }
            });
        }, 300);
    };

    window.downloadExcelPengadaan = async function() {
        if (typeof ExcelJS === 'undefined') { Swal.fire('Error', 'Library ExcelJS gagal dimuat.', 'error'); return; }

        let valMulai = document.getElementById('pengadaanFilterMulai').value;
        let valAkhir = document.getElementById('pengadaanFilterAkhir').value;
        let dataPengadaanRaw = @json($dataPengadaan ?? []);

        const wb = new ExcelJS.Workbook();
        const ws = wb.addWorksheet('Data Pengadaan PO');

        const headerRow = ws.addRow(['No', 'Tanggal PR', 'No. PR', 'Kode Barang', 'Nama Barang', 'Qty Permintaan', 'No. PO', 'Status']);
        headerRow.font = { bold: true, color: { argb: 'FFFFFFFF' } }; headerRow.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF004A99' } }; headerRow.alignment = { vertical: 'middle', horizontal: 'center' };
        ws.columns = [ { width: 5 }, { width: 15 }, { width: 20 }, { width: 15 }, { width: 40 }, { width: 15 }, { width: 20 }, { width: 15 } ];

        let no = 1;
        dataPengadaanRaw.forEach(p => {
            let dateStr = p.tgl_pr; let matchTgl = true;

            if (dateStr) {
                if (valMulai !== "" && dateStr < valMulai) matchTgl = false;
                if (valAkhir !== "" && dateStr > valAkhir) matchTgl = false;
            } else if (valMulai !== "" || valAkhir !== "") { matchTgl = false; }

            if (matchTgl) {
                let statKode = p.stationery ? p.stationery.kode : '-'; let statNama = p.stationery ? p.stationery.nama_barang : 'Barang Dihapus';
                let row = ws.addRow([ no++, dateStr, p.no_pr || '-', statKode, statNama, p.qty_item || 0, p.no_po || '-', p.status || '-' ]);
                if (p.status === 'Menunggu') row.getCell(8).font = { color: { argb: 'FFD97706' }, bold: true };
                else row.getCell(8).font = { color: { argb: 'FF059669' }, bold: true };
            }
        });

        const buffer = await wb.xlsx.writeBuffer(); const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        let labelStr = "";
        if(valMulai && valAkhir) labelStr = `_${valMulai}_sd_${valAkhir}`;
        else if (valMulai) labelStr = `_Mulai_${valMulai}`;
        else if (valAkhir) labelStr = `_Sampai_${valAkhir}`;
        else labelStr = "_Semua_Data";

        saveAs(blob, `Laporan_Pengadaan_PO${labelStr}.xlsx`);
    };

    function updateWitaClock() {
        const now = new Date();
        const timeStr = new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Makassar', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).format(now);
        const label = document.getElementById('wita-header-display');
        if(label) label.innerText = "System Time: " + timeStr + " WITA";
    }
    setInterval(updateWitaClock, 1000);

    function toggleMenu() {
        const container = document.getElementById('sub-menu-container'); const chevron = document.getElementById('chevron-icon');
        if (container.classList.contains('menu-expanded')) {
            container.classList.remove('menu-expanded'); container.classList.add('menu-collapsed'); chevron.style.transform = 'rotate(0deg)';
        } else {
            container.classList.remove('menu-collapsed'); container.classList.add('menu-expanded'); chevron.style.transform = 'rotate(90deg)';
        }
    }

    function switchTab(id) {
        const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }}; const isAdminStationery = {{ $isAdminStationery ? 'true' : 'false' }};
        if(id === 'kelola-user' && !isSuperAdmin) { Swal.fire('Akses Ditolak', 'Hanya Super Admin yang bisa mengakses menu ini.', 'error'); return; }
        if((id === 'stationery' || id === 'analitik-stationery' || id === 'transaksi-stationery' || id === 'laporan-stationery' || id === 'pengadaan-stationery') && !isSuperAdmin && !isAdminStationery) {
            Swal.fire('Akses Ditolak', 'Hanya Admin Stationery yang bisa mengakses menu ini.', 'error'); return;
        }

        const pageTitles = {
            'ringkasan': 'Ringkasan Statistik', 'karyawan': 'Data Karyawan', 'input': 'Rekap Input Kehadiran', 'input-lembur': 'Input Lembur Karyawan',
            'rekap': 'Laporan Rekap Absensi', 'lembur': 'Sistem Payroll Lembur', 'analitik-stationery': 'Analitik Stationery',
            'stationery': 'Kelola Master Stok', 'transaksi-stationery': 'Riwayat Transaksi (In/Out)', 'kelola-user': 'Kelola Pengguna', 'laporan-stationery': 'Laporan & Rekap Stok', 'pengadaan-stationery': 'Data Pengadaan (PR / PO)'
        };
        const headerTitleEl = document.getElementById('header-title'); if (headerTitleEl && pageTitles[id]) { headerTitleEl.innerText = pageTitles[id]; }
        const bellNotif = document.getElementById('bell-notif-container');
        if (bellNotif) {
            if (id === 'stationery' || id === 'analitik-stationery' || id === 'transaksi-stationery' || id === 'laporan-stationery' || id === 'pengadaan-stationery') bellNotif.classList.remove('hidden');
            else bellNotif.classList.add('hidden');
        }

        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden')); document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('flex'));
        document.querySelectorAll('.side-nav-btn').forEach(el => el.classList.remove('active'));

        const targetTab = document.getElementById('tab-' + id);
        if (targetTab) { targetTab.classList.remove('hidden'); targetTab.classList.add('flex'); }
        if (document.getElementById('btn-' + id)) { document.getElementById('btn-' + id).classList.add('active'); }
        window.location.hash = id;

        if(id === 'analitik-stationery') {
            setTimeout(() => { if (typeof updateStationeryChart === 'function') { updateStationeryChart('init'); } }, 100);
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart !== 'undefined' && typeof ChartDataLabels !== 'undefined') { Chart.register(ChartDataLabels); }

        const h = window.location.hash.substring(1);
        const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }}; const isAdminStationery = {{ $isAdminStationery ? 'true' : 'false' }};
        const pengadaanMenungguData = {!! json_encode($pengadaanMenunggu) !!}; const barangMenipisCount = {{ $barangMenipis->count() }};
        const pengadaanMenungguCount = pengadaanMenungguData.length; const totalNotifikasi = barangMenipisCount + pengadaanMenungguCount;

        const btnNotif = document.getElementById("btnNotifikasi"); const dropdownNotif = document.getElementById("dropdownNotifikasi");

        if (btnNotif && totalNotifikasi > 0) {
            btnNotif.classList.add('relative');
            const existingSpans = btnNotif.querySelectorAll('span.absolute'); existingSpans.forEach(span => span.remove());
            btnNotif.insertAdjacentHTML('beforeend', `<span style="pointer-events: none;" class="absolute -top-1.5 -right-1.5 flex items-center justify-center min-w-[20px] h-[20px] text-[12px] font-black text-white bg-red-600 rounded-full border-2 border-white shadow-md z-50">${totalNotifikasi}</span>`);
        }

        if (pengadaanMenungguCount > 0 && dropdownNotif && !document.getElementById("notif-item-pengadaan")) {
            let notifHTML = `<div id="notif-item-pengadaan" class="w-full bg-white flex flex-col"><div class="px-4 py-3 bg-blue-50 flex justify-between items-center border-b border-blue-200"><span class="text-xs font-extrabold text-blue-800 uppercase tracking-widest"><i class="fas fa-clock mr-1"></i> Sedang Menunggu Barang</span><span class="text-[10px] font-black bg-blue-200 text-blue-800 px-2 py-0.5 rounded-md shadow-sm">${pengadaanMenungguCount} PESANAN</span></div><div class="max-h-[300px] overflow-y-auto">`;
            pengadaanMenungguData.forEach(item => {
                let namaBarang = item.stationery ? item.stationery.nama_barang : 'Barang'; let kodeBarang = item.stationery ? item.stationery.kode : '';
                let tglParts = item.tgl_pr.split('-'); let formattedTgl = tglParts.length === 3 ? `${tglParts[2]}/${tglParts[1]}/${tglParts[0]}` : item.tgl_pr;
                notifHTML += `<div class="p-3.5 border-b border-gray-100 hover:bg-slate-50 cursor-pointer transition-colors duration-200 flex items-start gap-3" onclick="switchTab('pengadaan-stationery'); document.getElementById('dropdownNotifikasi').classList.add('hidden');"><div class="bg-blue-50 text-blue-500 p-2.5 rounded-xl shrink-0 border border-blue-100"><i class="fas fa-clock text-sm"></i></div><div class="flex-1 min-w-0"><div class="text-xs font-bold text-gray-800 truncate">${kodeBarang} - ${namaBarang}</div><div class="text-[11px] text-blue-600 font-semibold mt-1"><i class="far fa-calendar-alt mr-1"></i> Tgl PR: ${formattedTgl}</div><div class="text-[10px] text-slate-500 mt-1 flex items-center justify-between"><span>No PR: <b class="text-gray-700">${item.no_pr}</b></span><span class="bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded font-bold">Qty: ${item.qty_item}</span></div></div></div>`;
            });
            notifHTML += `</div></div>`; dropdownNotif.insertAdjacentHTML('beforeend', notifHTML);
        }

        const dropNotif = document.getElementById("dropdownNotifikasi"); if (dropNotif && dropNotif.parentNode !== document.body) { document.body.appendChild(dropNotif); }

        document.addEventListener("click", function(event) {
            const tombolNotif = event.target.closest("#btnNotifikasi"); const menuDrop = document.getElementById("dropdownNotifikasi");
            if (tombolNotif) {
                event.preventDefault(); event.stopPropagation();
                if (menuDrop) {
                    const rect = tombolNotif.getBoundingClientRect(); menuDrop.style.position = "absolute"; menuDrop.style.top = (rect.bottom + window.scrollY + 12) + "px"; menuDrop.style.right = (window.innerWidth - rect.right - 10) + "px"; menuDrop.style.left = "auto"; menuDrop.classList.toggle("hidden");
                }
            } else if (menuDrop && !menuDrop.contains(event.target)) { if (!event.target.closest('.select2-container')) { menuDrop.classList.add("hidden"); } }
        }, true);

        if (h && ['ringkasan', 'karyawan', 'input', 'input-lembur', 'rekap', 'lembur', 'kelola-user', 'stationery', 'analitik-stationery', 'transaksi-stationery', 'laporan-stationery', 'pengadaan-stationery'].includes(h)) { switchTab(h); }
        else { if (isAdminStationery) switchTab('analitik-stationery'); else switchTab('ringkasan'); }
    });
</script>
