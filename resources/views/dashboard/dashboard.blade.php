<x-app-layout>
    @php
        $userActive = Auth::user();
        $uRole = $userActive ? trim(strtolower($userActive->role)) : '';
        $uName = $userActive ? trim(strtoupper($userActive->name)) : '';

        $isSuperAdmin = ($uRole === 'superadmin' || $uName === 'ADMIN SUPER');
        $isAdminStationery = ($uRole === 'admin_stationery' || str_contains($uRole, 'stationery'));
        $isAdminAbsensi = ($uRole === 'admin_absensi' || str_contains($uRole, 'absensi') || str_contains($uName, 'ABSENSI'));
        $isAdminPenilaian = ($uRole === 'admin_penilaian' || str_contains($uRole, 'penilaian'));
        $isAdminInternal = ($uRole === 'admin_internal' || str_contains($uRole, 'internal'));
        $isAdminOutsourcing = ($uRole === 'admin_outsourcing' || str_contains($uRole, 'outsourc'));
        $isStandar = (!$isSuperAdmin && !$isAdminStationery && !$isAdminAbsensi && !$isAdminPenilaian && !$isAdminInternal && !$isAdminOutsourcing);

        $orderBarang = \App\Models\Stationery::all()->map(function($item) {
            $rataPenggunaan = \App\Models\StationeryTransaction::where('stationery_id', $item->id)
                ->where(function($q) { $q->where('type', 'keluar')->orWhere('type', 'KELUAR'); })
                ->where('tanggal', '>=', \Carbon\Carbon::now()->subDays(30))
                ->sum('jumlah');

            $terakhirOrder = \App\Models\StationeryTransaction::where('stationery_id', $item->id)
                ->where(function($q) { $q->where('type', 'masuk')->orWhere('type', 'MASUK'); })
                ->max('tanggal');

            $trxKeluar = \App\Models\StationeryTransaction::where('stationery_id', $item->id)
                ->where(function($q) { $q->where('type', 'keluar')->orWhere('type', 'KELUAR'); })
                ->pluck('dept')
                ->toArray();

            $penggunaUnik = array_filter(array_unique($trxKeluar));
            $penggunaStr = count($penggunaUnik) > 0 ? implode(', ', $penggunaUnik) : 'Belum ada data';

            $item->rata_penggunaan = $rataPenggunaan;
            $item->terakhir_order = $terakhirOrder;
            $item->status_reorder = ($item->total_stok <= $item->min_stok) ? 'Perlu order' : 'Aman';
            $item->siapa_pengguna = $penggunaStr;

            return $item;
        });

        $pemakaianBarang = \App\Models\StationeryTransaction::with('stationery')
            ->where(function($q) { $q->where('type', 'keluar')->orWhere('type', 'KELUAR'); })
            ->orderBy('tanggal', 'desc')
            ->get();

        $dataStationery = \App\Models\Stationery::all();
        $barangMenipis = $dataStationery->filter(function($item) {
            return $item->total_stok <= $item->min_stok;
        });

        $notifAbsensi = collect([]);

        $totalNotif = 0;
        if ($isSuperAdmin) {
            $totalNotif = $barangMenipis->count() + $notifAbsensi->count();
        } elseif ($isAdminStationery) {
            $totalNotif = $barangMenipis->count();
        } elseif ($isAdminAbsensi || $isStandar || $isAdminInternal || $isAdminOutsourcing) {
            $totalNotif = $notifAbsensi->count();
        }

        $transaksis = \App\Models\StationeryTransaction::with('stationery')->orderBy('tanggal', 'desc')->get();

        $shifts = \App\Models\Shift::orderBy('wst_code', 'asc')->get();
        $schedules = \App\Models\EmployeeSchedule::with('shift')->orderBy('schedule_date', 'desc')->get();
        $employees = \App\Models\Employee::orderBy('nama')->get();
    @endphp

    {{-- SCRIPT INJEKSI VIEWPORT UNTUK SKALA HP (SANGAT PENTING!) --}}
    <script>
        if (!document.querySelector('meta[name="viewport"]')) {
            let meta = document.createElement('meta');
            meta.name = "viewport";
            meta.content = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no";
            document.getElementsByTagName('head')[0].appendChild(meta);
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        nav.bg-white.border-b.border-gray-100 { display: none !important; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f6fa;
            margin: 0;
            overflow: auto;
            min-height: 100vh;
        }

        .bg-indofood-blue { background: #004a99; }
        .text-indofood-blue { color: #004a99; }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideUpFadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        header.bg-white {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(16px);
            border-bottom: none !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03);
            animation: fadeInScale 0.6s ease-out 0.2s both;
        }

        .sub-menu-container { transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease-out, transform 0.4s ease-out; overflow: hidden; }
        .menu-collapsed { max-height: 0; opacity: 0; transform: translateY(-10px); pointer-events: none; margin-top: 0 !important; margin-bottom: 0 !important; }
        .menu-expanded { max-height: 1500px; opacity: 1; transform: translateY(0); margin-top: 0.5rem; margin-bottom: 1rem; }

        .side-nav-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.72rem 1.15rem;
            margin-bottom: 0.35rem;
            font-size: 0.825rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            border-radius: 0.85rem;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: capitalize;
            letter-spacing: 0.015em;
            position: relative;
            overflow: hidden;
        }
        .side-nav-btn i {
            width: 22px;
            min-width: 22px;
            text-align: center;
            margin-right: 12px;
            font-size: 0.95rem;
            opacity: 0.9;
            color: inherit;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .side-nav-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transform: translateX(4px);
        }

        .side-nav-btn.active {
            background: #ffffff !important;
            color: #004a99 !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            font-weight: 800;
            transform: translateX(0);
        }
        .side-nav-btn.active i {
            opacity: 1;
            color: #004a99 !important;
        }
        .side-nav-btn.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 15%;
            height: 70%;
            width: 4px;
            background-color: #004a99;
            border-radius: 0 4px 4px 0;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .glass-card:hover {
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
            border-color: rgba(203, 213, 225, 0.9);
        }

        .chart-container { position: relative; height: 420px; width: 100%; }

        table { border-collapse: separate; border-spacing: 0; }
        .table-bordered th, .table-bordered td { border: 1px solid #e2e8f0; }
        .table-bordered thead th { border-bottom: 3px solid #cbd5e1; background-color: #f1f5f9; color: #1e293b; }
        .table-bordered tbody tr:nth-child(even) { background-color: #f8fafc; }
        .table-bordered tbody tr:hover { background-color: #f1f5f9; transition: background-color 0.2s; }

        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        .form-input-modern { width: 100%; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 0.65rem; padding: 0.65rem 0.9rem; font-size: 0.875rem; color:#1e293b; transition: 0.2s; font-weight: 600; }
        .form-input-modern:focus { border-color: #004a99; outline: none; box-shadow: 0 0 0 4px rgba(0, 74, 153, 0.12); }

        .pl-icon-modern { padding-left: 2.25rem !important; }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 3px solid #f8fafc; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        #addEmployeeModal, #editEmployeeModal { left: 18rem !important; background: #f8fafc !important; transition: left 0.3s ease-in-out; }
        .modal-full-content { position: fixed; top: 0; right: 0; bottom: 0; left: 18rem; z-index: 100; background: #f8fafc; display: flex; flex-direction: column; transition: left 0.3s ease-in-out; }

        canvas#barChart, canvas#pieChart { min-height: 380px !important; max-height: 450px !important; width: 100% !important; }

        /* PERBAIKAN CSS MOBILE MENGGUNAKAN TAILWIND LOGIC PADA KELAS UTAMA */
        @media (max-width: 1024px) {
            #main-content { padding-left: 0.75rem; padding-right: 0.75rem; }
            header { padding-left: 0.75rem; padding-right: 0.75rem; }
            .glass-card { border-radius: 1rem; }
            .side-nav-btn { font-size: 0.82rem; padding: 0.75rem 1rem; }

            /* Penyesuaian Modal Mobile */
            #addEmployeeModal, #editEmployeeModal, .modal-full-content { left: 0 !important; }
        }

        @media (max-width: 768px) {
            canvas#barChart, canvas#pieChart { min-height: 240px !important; max-height: 290px !important; width: 100% !important; }
            .chart-container { min-height: 220px; }
            .table-bordered th, .table-bordered td { padding: 0.5rem 0.6rem !important; font-size: 0.75rem; }
            .glass-card { border-radius: 0.875rem; }
            .form-input-modern { font-size: 0.8rem; padding: 0.55rem 0.75rem; }
        }

        @media print {
            aside, header, .side-nav-btn, form, button, .print-hide { display: none !important; }
            body, .flex-1, main, #tab-lembur { height: auto !important; overflow: visible !important; }
            .glass-card { border: none !important; box-shadow: none !important; background: white !important; }
        }
    </style>

    <div class="flex h-screen w-full overflow-hidden relative">

        {{-- OVERLAY BACKGROUND GELAP UNTUK HP KETIKA SIDEBAR TERBUKA --}}
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity opacity-0 pointer-events-none"></div>

        {{-- SIDEBAR DIPERBARUI DENGAN TAILWIND RESPONSIVE (Fixed di Mobile, Relative di Desktop) --}}
        <aside id="main-sidebar" class="w-72 bg-indofood-blue text-white flex flex-col shadow-2xl z-50 shrink-0 print-hide transition-all duration-300 ease-in-out fixed inset-y-0 left-0 transform -translate-x-full lg:relative lg:translate-x-0 h-full">
            <div class="px-6 pt-8 pb-3 flex justify-center items-center">
                <img src="{{ asset('images/logo-baru.png') }}" alt="Indofood CBP GWS" class="w-40 h-auto object-contain drop-shadow-md bg-transparent filter mix-blend-screen brightness-110">
            </div>
            <div class="px-8 mb-2 mt-2"><div class="border-b border-white border-opacity-20"></div></div>

            <nav class="flex-grow px-4 overflow-y-auto pb-4 mt-2">

                {{-- KATEGORI: ABSENSI --}}
                @if(($isSuperAdmin || $isAdminAbsensi || $isStandar || $isAdminInternal || $isAdminOutsourcing) && !$isAdminPenilaian && !$isAdminStationery)
                    <button onclick="toggleMenu('menu-absensi', 'icon-absensi')" class="w-full flex justify-between items-center px-4 py-3 bg-white/5 hover:bg-white/10 rounded-xl text-[11px] font-extrabold text-white uppercase tracking-widest transition-all mb-1 shadow-sm">
                        <div class="flex items-center"><i class="fas fa-calendar-check mr-3 text-blue-300 text-base"></i>Absensi</div>
                        <i id="icon-absensi" class="fas fa-chevron-down text-[10px] transition-transform duration-300 transform rotate-0"></i>
                    </button>

                    <div id="menu-absensi" class="sub-menu-container menu-expanded space-y-1 pl-3 border-l border-white/20 ml-6">

                        {{-- HANYA ADMIN YANG BISA MELIHAT INI --}}
                        @if($isSuperAdmin || $isAdminAbsensi)
                            <button onclick="switchTab('ringkasan')" id="btn-ringkasan" class="side-nav-btn active"><i class="fas fa-th-large"></i> Dashboard Utama</button>
                            <button onclick="switchTab('karyawan')" id="btn-karyawan" class="side-nav-btn"><i class="fas fa-users"></i> Data Karyawan</button>

                            <button onclick="switchTab('master-shift')" id="btn-master-shift" class="side-nav-btn"><i class="fas fa-clock"></i> Master Shift</button>
                            <button onclick="switchTab('jadwal-karyawan')" id="btn-jadwal-karyawan" class="side-nav-btn"><i class="fas fa-calendar-alt"></i> Jadwal Karyawan</button>
                            <button onclick="switchTab('input')" id="btn-input" class="side-nav-btn"><i class="fas fa-file-signature"></i> Rekap Absensi</button>
                            <button onclick="switchTab('rekap')" id="btn-rekap" class="side-nav-btn"><i class="fas fa-database"></i> Laporan Absensi</button>
                            <button onclick="switchTab('pengajuan-spl')" id="btn-pengajuan-spl" class="side-nav-btn"><i class="fas fa-file-contract"></i> Pengajuan SPL</button>
                            <button onclick="switchTab('rekap-spl')" id="btn-rekap-spl" class="side-nav-btn"><i class="fas fa-list"></i> Rekap SPL</button>
                            <button onclick="switchTab('input-lembur')" id="btn-input-lembur" class="side-nav-btn"><i class="fas fa-clock"></i> Input Lembur</button>
                            <button onclick="switchTab('lembur')" id="btn-lembur" class="side-nav-btn"><i class="fas fa-money-check-alt"></i> Gaji Lembur</button>
                        @endif

                        {{-- HANYA ADMIN INTERNAL INDOFOOD (PENGELOLAAN ABSENSI & PENGAJUAN SPL) --}}
                        @if($isAdminInternal)
                            <button onclick="switchTab('input')" id="btn-input" class="side-nav-btn active"><i class="fas fa-file-signature text-blue-300"></i> Rekap Absensi</button>
                            <button onclick="switchTab('rekap')" id="btn-rekap" class="side-nav-btn"><i class="fas fa-database text-purple-300"></i> Laporan Absensi</button>
                            <button onclick="switchTab('pengajuan-spl')" id="btn-pengajuan-spl" class="side-nav-btn"><i class="fas fa-file-contract text-yellow-300"></i> Pengajuan SPL</button>
                            <button onclick="switchTab('rekap-spl')" id="btn-rekap-spl" class="side-nav-btn"><i class="fas fa-list text-amber-300"></i> Rekap SPL</button>
                        @endif

                        {{-- HANYA ADMIN OUTSOURCING (REKAP LEMBUR, BUKTI/REKAP SPL, & ABSENSI) --}}
                        @if($isAdminOutsourcing)
                            <button onclick="switchTab('rekap-spl')" id="btn-rekap-spl" class="side-nav-btn active"><i class="fas fa-list text-yellow-300"></i> Rekap SPL (Bukti SPL)</button>
                            <button onclick="switchTab('lembur')" id="btn-lembur" class="side-nav-btn"><i class="fas fa-money-check-alt text-emerald-300"></i> Rekap Gaji Lembur</button>
                            <button onclick="switchTab('input')" id="btn-input" class="side-nav-btn"><i class="fas fa-file-signature text-blue-300"></i> Rekap Absensi</button>
                            <button onclick="switchTab('rekap')" id="btn-rekap" class="side-nav-btn"><i class="fas fa-database text-purple-300"></i> Laporan Absensi</button>
                        @endif

                        {{-- HANYA KARYAWAN STANDAR YANG BISA MELIHAT INI --}}
                        @if($isStandar)
                            <button onclick="switchTab('form-absen')" id="btn-form-absen" class="side-nav-btn active"><i class="fas fa-fingerprint text-green-300"></i> Form Absen</button>
                        @endif

                    </div>
                @endif

                {{-- KATEGORI: STATIONERY --}}
                @if($isSuperAdmin || $isAdminStationery)
                    <button onclick="toggleMenu('menu-stationery', 'icon-stationery')" class="w-full flex justify-between items-center px-4 py-3 bg-white/5 hover:bg-white/10 rounded-xl text-[11px] font-extrabold text-white uppercase tracking-widest transition-all mb-1 mt-3 shadow-sm">
                        <div class="flex items-center"><i class="fas fa-box mr-3 text-cyan-300 text-base"></i>Stationery</div>
                        <i id="icon-stationery" class="fas fa-chevron-down text-[10px] transition-transform duration-300 transform rotate-0"></i>
                    </button>

                    <div id="menu-stationery" class="sub-menu-container menu-expanded space-y-1 pl-3 border-l border-white/20 ml-6">
                        <button onclick="switchTab('analitik-stationery')" id="btn-analitik-stationery" class="side-nav-btn text-purple-300"><i class="fas fa-chart-line"></i>Dashboard</button>
                        <button onclick="switchTab('stationery')" id="btn-stationery" class="side-nav-btn text-cyan-300"><i class="fas fa-box-open"></i> Master Stok</button>
                        <button onclick="switchTab('transaksi-stationery')" id="btn-transaksi-stationery" class="side-nav-btn text-green-300"><i class="fas fa-exchange-alt"></i> Transaksi Barang</button>
                        <button onclick="switchTab('laporan-stationery')" id="btn-laporan-stationery" class="side-nav-btn text-pink-300"><i class="fas fa-file-excel"></i> Laporan Barang</button>
                        <button onclick="switchTab('pengadaan-stationery')" id="btn-pengadaan-stationery" class="side-nav-btn text-yellow-200"><i class="fas fa-shopping-cart"></i> Pengadaan Barang</button>
                        <button onclick="switchTab('closing-stationery')" id="btn-closing-stationery" class="side-nav-btn text-red-200"><i class="fas fa-lock"></i> Closing Periode</button>

                    </div>
                @endif

                {{-- KATEGORI: PENILAIAN --}}
                @if($isSuperAdmin || $isAdminAbsensi || $isAdminPenilaian)
                    <button onclick="toggleMenu('menu-penilaian', 'icon-penilaian')" class="w-full flex justify-between items-center px-4 py-3 bg-white/5 hover:bg-white/10 rounded-xl text-[11px] font-extrabold text-white uppercase tracking-widest transition-all mb-1 mt-3 shadow-sm">
                        <div class="flex items-center"><i class="fas fa-award mr-3 text-amber-300 text-base"></i>Penilaian</div>
                        <i id="icon-penilaian" class="fas fa-chevron-down text-[10px] transition-transform duration-300 transform rotate-0"></i>
                    </button>

                    <div id="menu-penilaian" class="sub-menu-container menu-expanded space-y-1 pl-3 border-l border-white/20 ml-6">
                        <button onclick="switchTab('karyawan')" id="btn-karyawan-penilaian" class="side-nav-btn text-amber-200"><i class="fas fa-users"></i> Data Karyawan</button>
                        <button onclick="switchTab('aspek-penilaian')" id="btn-aspek-penilaian" class="side-nav-btn text-amber-200"><i class="fas fa-star"></i> Aspek Penilaian</button>
                        <button onclick="switchTab('laporan-penilaian')" id="btn-laporan-penilaian" class="side-nav-btn text-amber-200"><i class="fas fa-file-alt"></i> Laporan Penilaian</button>
                    </div>
                @endif

                {{-- KATEGORI: ADMINISTRATOR --}}
                @if($isSuperAdmin)
                    <button onclick="toggleMenu('menu-admin', 'icon-admin')" class="w-full flex justify-between items-center px-4 py-3 bg-white/5 hover:bg-white/10 rounded-xl text-[11px] font-extrabold text-white uppercase tracking-widest transition-all mb-1 mt-3 shadow-sm">
                        <div class="flex items-center"><i class="fas fa-shield-alt mr-3 text-blue-300 text-base"></i> Administrator</div>
                        <i id="icon-admin" class="fas fa-chevron-down text-[10px] transition-transform duration-300 transform rotate-0"></i>
                    </button>

                    <div id="menu-admin" class="sub-menu-container menu-expanded space-y-1 pl-3 border-l border-white/20 ml-6">
                        <button onclick="switchTab('kelola-user')" id="btn-kelola-user" class="side-nav-btn text-blue-200 hover:text-white"><i class="fas fa-users-cog"></i> Manajemen User</button>
                        <button onclick="switchTab('lokasi-absen')" id="btn-lokasi-absen" class="side-nav-btn text-blue-200 hover:text-white"><i class="fas fa-map-marked-alt"></i> Pengaturan Lokasi Absen</button>
                    </div>
                @endif

            </nav>

            <div class="p-6 border-t border-white border-opacity-30 shrink-0">
                <form method="POST" action="{{ route('logout') }}"> @csrf
                    <button class="w-full flex items-center justify-center py-3 bg-red-500/20 text-red-200 border border-red-500/30 rounded-xl hover:bg-red-600 hover:text-white hover:border-red-600 transition-all text-xs font-bold uppercase tracking-widest leading-none shadow-sm"><i class="fas fa-power-off mr-2"></i> Keluar</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 relative h-screen">

            <header class="bg-white/90 backdrop-blur-md px-3 sm:px-6 lg:px-8 py-2.5 sm:py-3.5 flex justify-between items-center z-20 shadow-2xs shrink-0 h-16 sm:h-[72px] print-hide">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="text-slate-500 hover:text-indofood-blue focus:outline-none transition-colors p-2.5 rounded-xl hover:bg-slate-100 flex items-center justify-center cursor-pointer" title="Toggle Sidebar">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
                    <h2 id="header-title" class="text-sm sm:text-base font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                        Dashboard / Overview
                    </h2>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    {{-- ============================== --}}
                    {{-- BAGIAN PROFIL DI HEADER (Donezo Style) --}}
                    {{-- ============================== --}}
                    <button onclick="openEditProfileModal()" class="flex items-center gap-3 bg-slate-50 hover:bg-slate-100/90 border border-slate-200/80 p-1.5 pr-3.5 rounded-full transition-all cursor-pointer outline-none focus:ring-2 focus:ring-blue-500/20 text-left shadow-2xs group">
                        <div class="relative shrink-0">
                            @if(Auth::user()->profile_pic)
                                <img src="{{ asset(Auth::user()->profile_pic) }}" alt="Profile" class="w-9 h-9 min-w-[36px] min-h-[36px] rounded-full object-cover shadow-2xs ring-2 ring-white">
                            @else
                                <div class="w-9 h-9 min-w-[36px] min-h-[36px] rounded-full bg-indofood-blue text-white flex items-center justify-center font-bold text-xs shadow-2xs uppercase ring-2 ring-white">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col overflow-hidden max-w-[90px] sm:max-w-[150px]">
                            <p class="text-[12px] sm:text-[13px] font-bold text-slate-800 truncate leading-tight group-hover:text-indofood-blue transition-colors mb-0.5" title="{{ Auth::user()->name }}">
                                {{ ucwords(strtolower(Auth::user()->name)) }}
                            </p>
                            <p class="text-[10px] font-medium text-slate-400 leading-none truncate">
                                @if($isSuperAdmin) Super Admin
                                @elseif($isAdminAbsensi) Admin Absensi
                                @elseif($isAdminStationery) Admin Stationery
                                @elseif($isAdminPenilaian) Admin Penilaian
                                @elseif($isAdminInternal) Admin Internal Indofood
                                @elseif($isAdminOutsourcing) Admin Outsourcing
                                @else User @endif
                            </p>
                        </div>

                        <i class="fas fa-chevron-down text-[10px] text-slate-400 ml-0.5 group-hover:text-slate-600 transition-colors"></i>
                    </button>
                </div>
            </header>

            <main id="main-content" class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 relative">
                <div class="max-w-full mx-auto h-full flex flex-col">

                    {{-- HANYA KARYAWAN STANDAR YANG MERENDER FORM ABSEN --}}
                    @if($isStandar)
                        <div id="tab-form-absen" class="tab-content flex-1 flex flex-col gap-6 flex">
                            @include('dashboard.absen_karyawan')
                        </div>
                    @endif

                    {{-- HALAMAN ADMIN ABSENSI / PENILAIAN / SUPER ADMIN --}}
                    @if($isSuperAdmin || $isAdminAbsensi || $isAdminPenilaian)

                        {{-- DASHBOARD ADMIN UTAMA YANG ASLI --}}
                        <div id="tab-ringkasan" class="tab-content flex-1 flex flex-col gap-6 flex">
                            <div class="space-y-6 animate-fade-in-up">

                                {{-- Header Dashboard Utama (Donezo Style) --}}
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-2 sm:pt-3 pb-2 mb-1">
                                    <div>
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Utama</h1>
                                            {{-- Realtime Live Status Badge --}}
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50/90 text-emerald-800 border border-emerald-200/90 text-xs font-bold shadow-2xs">
                                                <span class="relative flex h-2.5 w-2.5">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                                </span>
                                                <span class="font-extrabold text-emerald-900 tracking-tight">Realtime Live</span>
                                                <span class="text-emerald-300 font-bold">•</span>
                                                <span class="text-[11px] font-bold text-emerald-800 tracking-tight font-mono" id="realtime-last-sync">{{ \Carbon\Carbon::now('Asia/Makassar')->format('H:i:s') }} WITA</span>
                                            </div>
                                        </div>
                                        <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">Ringkasan statistik kehadiran, jam kerja, dan aktivitas presensi karyawan secara realtime.</p>
                                    </div>

                                    <div class="flex items-center gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                                        {{-- WIDGET TANGGAL SEKARANG (Donezo Pill Style) --}}
                                        <div class="w-full sm:w-auto bg-white border border-slate-200/90 hover:border-blue-400 px-4 py-2.5 rounded-2xl shadow-xs hover:shadow-sm flex items-center gap-3 transition-all cursor-pointer">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-indofood-blue flex items-center justify-center text-sm shrink-0 shadow-2xs">
                                                <i class="fas fa-calendar-alt text-xs"></i>
                                            </div>
                                            <div class="text-left flex-1 sm:flex-none">
                                                <label for="filter_tanggal_admin" class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider leading-none cursor-pointer block mb-0.5">Tanggal Presensi</label>
                                                <input type="date" id="filter_tanggal_admin" name="tanggal"
                                                       value="{{ request('tanggal', \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d')) }}"
                                                       onchange="handleDateFilterChange(this.value);"
                                                       class="text-xs sm:text-sm font-black text-slate-800 bg-transparent border-none p-0 m-0 focus:ring-0 cursor-pointer outline-none w-full font-mono">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ========================================== --}}
                                {{-- WIDGET KARTU STATISTIK (DONEZO STYLE - UNIFORM WHITE) --}}
                                {{-- ========================================== --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mt-2">
                                    {{-- Card 1: Total Hadir --}}
                                    <div id="card-hadir" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[145px] sm:min-h-[160px] group">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs sm:text-sm font-bold text-slate-500 tracking-wide">Total Hadir</span>
                                            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100/80 flex items-center justify-center text-xs font-bold group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-2xs">
                                                <i class="fas fa-user-check"></i>
                                            </div>
                                        </div>

                                        <div class="my-2">
                                            <h3 id="stat-count-hadir" class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-none transition-all duration-300">
                                                {{ $countHadir ?? 0 }}
                                            </h3>
                                        </div>

                                        <div>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100/80 text-[11px] font-bold">
                                                <i class="fas fa-check-circle text-[10px] text-emerald-500"></i>
                                                <span>Hadir hari ini</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card 2: Total Lembur --}}
                                    <div id="card-lembur" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[145px] sm:min-h-[160px] group">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs sm:text-sm font-bold text-slate-500 tracking-wide">Total Lembur</span>
                                            <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 border border-purple-100/80 flex items-center justify-center text-xs font-bold group-hover:bg-purple-600 group-hover:text-white transition-colors shadow-2xs">
                                                <i class="fas fa-business-time"></i>
                                            </div>
                                        </div>

                                        <div class="my-2">
                                            <h3 id="stat-count-lembur" class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-none transition-all duration-300">
                                                {{ $countLembur ?? 0 }}
                                            </h3>
                                        </div>

                                        <div>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100/80 text-[11px] font-bold">
                                                <i class="fas fa-clock text-[10px] text-purple-500"></i>
                                                <span>Jam lembur aktif</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card 3: Izin / Sakit --}}
                                    <div id="card-izin" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[145px] sm:min-h-[160px] group">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs sm:text-sm font-bold text-slate-500 tracking-wide">Izin / Sakit</span>
                                            <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 border border-amber-100/80 flex items-center justify-center text-xs font-bold group-hover:bg-amber-500 group-hover:text-white transition-colors shadow-2xs">
                                                <i class="fas fa-file-medical"></i>
                                            </div>
                                        </div>

                                        <div class="my-2">
                                            <h3 id="stat-count-izin" class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-none transition-all duration-300">
                                                {{ $countIzin ?? 0 }}
                                            </h3>
                                        </div>

                                        <div>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100/80 text-[11px] font-bold">
                                                <i class="fas fa-user-clock text-[10px] text-amber-500"></i>
                                                <span>Izin / sakit tercatat</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card 4: Tanpa Keterangan / Belum Clock-in --}}
                                    <div id="card-alpa" class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[145px] sm:min-h-[160px] group">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs sm:text-sm font-bold text-slate-500 tracking-wide" title="Karyawan yang belum melakukan clock-in atau tidak ada keterangan">Belum Clock-in / Tanpa Ket.</span>
                                            <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 border border-rose-100/80 flex items-center justify-center text-xs font-bold group-hover:bg-rose-600 group-hover:text-white transition-colors shadow-2xs">
                                                <i class="fas fa-user-times"></i>
                                            </div>
                                        </div>

                                        <div class="my-2">
                                            <h3 id="stat-count-alpa" class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-none transition-all duration-300">
                                                {{ $countAlpa ?? 0 }}
                                            </h3>
                                        </div>

                                        <div>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-100/80 text-[11px] font-bold">
                                                <i class="fas fa-exclamation-circle text-[10px] text-rose-500"></i>
                                                <span>Perlu konfirmasi</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Area Grafik / Chart (Donezo Style) --}}
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 mt-2">
                                    {{-- 1. Bar Chart: Tren Kehadiran Mingguan --}}
                                    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-100 flex flex-col justify-between hover:shadow-md transition-all duration-300">
                                        <div class="flex items-center justify-between pb-3">
                                            <div>
                                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Tren Kehadiran Mingguan</h3>
                                                <p class="text-xs text-slate-400 font-medium mt-0.5">Tren statistik presensi 7 hari terakhir</p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full bg-slate-50 text-[11px] font-bold text-slate-600 border border-slate-200/70">
                                                7 Hari
                                            </span>
                                        </div>
                                        <div class="relative w-full flex-1 flex flex-col items-center justify-center min-h-[300px] max-h-[330px]">
                                            <canvas id="barChart" class="w-full h-full"></canvas>
                                        </div>
                                        {{-- Empty State Note for Bar Chart --}}
                                        <div id="bar-empty-note" class="pt-2 text-center text-[11px] font-medium text-slate-400 flex items-center justify-center gap-1.5 {{ ($countHadir ?? 0) == 0 ? '' : 'hidden' }}">
                                            <i class="fas fa-info-circle text-blue-400"></i>
                                            <span>Menunggu data presensi masuk untuk 7 hari terakhir</span>
                                        </div>
                                    </div>

                                    {{-- 2. Semi-Circle Gauge: Rasio Kehadiran Hari Ini --}}
                                    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-100 flex flex-col justify-between hover:shadow-md transition-all duration-300">
                                        <div class="flex items-center justify-between pb-3">
                                            <div>
                                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Rasio Kehadiran Hari Ini</h3>
                                                <p class="text-xs text-slate-400 font-medium mt-0.5">Persentase karyawan yang sudah melakukan presensi</p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full bg-blue-50 text-[11px] font-bold text-indofood-blue border border-blue-100">
                                                Hari Ini
                                            </span>
                                        </div>
                                        <div class="relative w-full flex-1 flex flex-col items-center justify-center min-h-[210px] max-h-[240px]">
                                            <canvas id="pieChart" class="w-full h-full"></canvas>
                                        </div>

                                        {{-- Status / Empty state helper text --}}
                                        <div id="gauge-empty-note" class="text-center text-[11px] font-medium text-slate-400 pt-1 pb-1 flex items-center justify-center gap-1.5">
                                            @if(($countHadir ?? 0) == 0)
                                                <i class="fas fa-clock text-amber-500 text-xs"></i>
                                                <span>Menunggu data presensi masuk hari ini</span>
                                            @else
                                                <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                                                <span>{{ $countHadir }} dari {{ ($countHadir ?? 0) + ($countLembur ?? 0) + ($countIzin ?? 0) + ($countAlpa ?? 0) }} karyawan telah presensi</span>
                                            @endif
                                        </div>

                                        {{-- Custom Donezo Legend at the bottom --}}
                                        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 pt-3 mt-1 border-t border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-indofood-blue shadow-2xs"></span>
                                                <span class="text-xs font-bold text-slate-700">Hadir</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-purple-600 shadow-2xs"></span>
                                                <span class="text-xs font-bold text-slate-700">Lembur</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-2xs"></span>
                                                <span class="text-xs font-bold text-slate-700">Izin</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-slate-300 shadow-2xs"></span>
                                                <span class="text-xs font-bold text-slate-700">Tanpa Ket.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('dashboard.karyawan')

                        <div id="tab-master-shift" class="tab-content hidden flex-1 flex-col gap-6">
                            @include('dashboard.master_shift')
                        </div>

                        <div id="tab-jadwal-karyawan" class="tab-content hidden flex-1 flex-col gap-6">
                            @include('dashboard.jadwal_karyawan')
                        </div>
                        @include('dashboard.input_lembur')

                    @endif

                    {{-- MENU PENGAJUAN SPL (SUPERADMIN, ADMIN ABSENSI, ADMIN PENILAIAN, ADMIN INTERNAL) --}}
                    @if($isSuperAdmin || $isAdminAbsensi || $isAdminPenilaian || $isAdminInternal)
                        @include('dashboard.pengajuan_spl')
                    @endif

                    {{-- MENU REKAP ABSENSI & SPL (SUPERADMIN, ADMIN ABSENSI, ADMIN PENILAIAN, ADMIN INTERNAL, ADMIN OUTSOURCING) --}}
                    @if($isSuperAdmin || $isAdminAbsensi || $isAdminPenilaian || $isAdminInternal || $isAdminOutsourcing)
                        @include('dashboard.input')
                        @include('dashboard.rekap')
                        @include('dashboard.rekap_spl')
                    @endif

                    {{-- MENU REKAP GAJI LEMBUR (KHUSUS SUPERADMIN, ADMIN ABSENSI, ADMIN PENILAIAN, ADMIN OUTSOURCING) --}}
                    @if($isSuperAdmin || $isAdminAbsensi || $isAdminPenilaian || $isAdminOutsourcing)
                        @include('dashboard.lembur')
                    @endif

                    {{-- MODUL STATIONERY --}}
                    @if($isSuperAdmin || $isAdminStationery)
                        @include('dashboard.stationery')

                        {{-- TAB CLOSING PERIODE (STATIONERY) --}}
                        <div id="tab-closing-stationery" class="tab-content hidden flex-1 flex-col gap-6 min-h-0">
                            @include('dashboard.closing_periode')
                        </div>
                    @endif

                    @if($isSuperAdmin)
                        @include('dashboard.users')
                        @include('dashboard.lokasi_absen')
                    @endif

                    @if($isSuperAdmin || $isAdminAbsensi || $isAdminPenilaian)
                        @include('dashboard.aspek_penilaian')
                        @include('dashboard.laporan_penilaian')
                    @endif

                    <div class="mt-auto pt-6 pb-2 text-center shrink-0 print-hide">
                        <p class="text-[9px] font-medium text-slate-400 uppercase tracking-wide opacity-70">
                            &copy; 2026 INDOFOOD CBP SUCCESS MAKMUR. ALL RIGHTS RESERVED. <span class="mx-2 text-slate-300">|</span> VERSI 1.2.0 (Modern)
                        </p>
                    </div>

                </div>
            </main>
        </div>
    </div>

    @include('dashboard.profile')
    @include('dashboard.modals')

    {{-- MODAL TAMBAH SHIFT TETAP ADA JIKA MASIH DIGUNAKAN --}}
    <div id="modalAddShift" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-black text-slate-700 text-sm uppercase tracking-widest">Tambah Master Shift</h3>
                <button type="button" onclick="document.getElementById('modalAddShift').classList.add('hidden')" class="text-slate-400 hover:text-rose-500"><i class="fas fa-times text-lg"></i></button>
            </div>
            <form action="{{ route('shifts.store') }}#master-shift" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Kode WST</label>
                        <input type="text" name="wst_code" required class="form-input-modern">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Tipe Grup WS</label>
                        <input type="text" name="group_ws_type" class="form-input-modern">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Teks WST</label>
                    <input type="text" name="wst_text" class="form-input-modern">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Waktu Mulai</label>
                        <input type="time" name="time_start" required class="form-input-modern">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Waktu Selesai</label>
                        <input type="time" name="time_end" required class="form-input-modern">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Istirahat 1 Mulai</label>
                        <input type="time" name="break1_start" class="form-input-modern">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Istirahat 1 Selesai</label>
                        <input type="time" name="break1_end" class="form-input-modern">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Istirahat 2 Mulai</label>
                        <input type="time" name="break2_start" class="form-input-modern">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Istirahat 2 Selesai</label>
                        <input type="time" name="break2_end" class="form-input-modern">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_overnight" id="is_overnight" class="w-4 h-4 text-indofood-blue rounded">
                    <label for="is_overnight" class="text-xs font-bold text-slate-600">Shift Overnight (H+1)</label>
                </div>

                <button type="submit" class="w-full bg-indofood-blue text-white font-extrabold py-3 rounded-xl uppercase text-xs tracking-widest shadow-md hover:bg-blue-800 transition">SIMPAN MASTER</button>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isMobile = window.innerWidth < 1024;

            if (isMobile) {
                sidebar.classList.toggle('-translate-x-full');
                sidebar.classList.toggle('translate-x-0');

                if(overlay) {
                    overlay.classList.toggle('hidden');
                    overlay.classList.toggle('opacity-0');
                    overlay.classList.toggle('pointer-events-none');
                }
            } else {
                sidebar.classList.toggle('lg:-ml-72');
            }

            document.querySelectorAll('.modal-full-content').forEach(modal => {
                if (isMobile) {
                    modal.style.left = '0';
                } else {
                    modal.style.left = sidebar.classList.contains('lg:-ml-72') ? '0' : '18rem';
                }
            });
        }

        let ringkasanBarChart = null;
        let ringkasanPieChart = null;

        function toggleMenu(menuId, iconId) {
            const container = document.getElementById(menuId);
            const icon = document.getElementById(iconId);

            if (container.classList.contains('menu-expanded')) {
                container.classList.remove('menu-expanded');
                container.classList.add('menu-collapsed');
                icon.style.transform = 'rotate(-90deg)';
            } else {
                container.classList.remove('menu-collapsed');
                container.classList.add('menu-expanded');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function updateWitaClock() {
            const now = new Date();
            const timeStr = new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Makassar', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).format(now);
            const label = document.getElementById('wita-header-display');
            if(label) label.innerText = "System Time: " + timeStr + " WITA";
            const liveBadgeClock = document.getElementById('realtime-last-sync');
            if(liveBadgeClock) liveBadgeClock.innerText = timeStr + " WITA";
        }
        setInterval(updateWitaClock, 1000);
        updateWitaClock();

        @if(session('success'))
            Swal.fire({ title: 'BERHASIL!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#004a99' });
        @endif
        @if(session('error'))
            Swal.fire({ title: 'GAGAL!', text: "{{ session('error') }}", icon: 'error', confirmButtonColor: '#dc2626' });
        @endif
        @if($errors->any())
            Swal.fire({
                title: 'GAGAL MENYIMPAN!',
                html: '<div class="text-left text-xs text-red-600 font-bold space-y-1.5 p-2 bg-red-50 rounded-lg">@foreach($errors->all() as $err)<div><i class="fas fa-exclamation-circle mr-1 text-red-500"></i> {{ $err }}</div>@endforeach</div>',
                icon: 'error',
                confirmButtonColor: '#dc2626'
            });
        @endif

        {{-- SCRIPT PAKSA PINDAH TAB JIKA ADA SESSION ATAU REQUEST / HASH --}}
        @if(session('active_tab'))
            setTimeout(() => { switchTab("{{ session('active_tab') }}"); }, 50);
        @elseif(request('active_tab'))
            setTimeout(() => { switchTab("{{ request('active_tab') }}"); }, 50);
        @endif

        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.replace('#', '');
            if (hash && document.getElementById('tab-' + hash)) {
                setTimeout(() => { switchTab(hash); }, 50);
            }
        });

        function switchTab(id) {
            if (id === 'dashboard-karyawan') id = 'form-absen';
            const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
            const isAdminStationery = {{ $isAdminStationery ? 'true' : 'false' }};
            const isAdminAbsensi = {{ $isAdminAbsensi ? 'true' : 'false' }};
            const isAdminPenilaian = {{ $isAdminPenilaian ? 'true' : 'false' }};
            const isAdminInternal = {{ $isAdminInternal ? 'true' : 'false' }};
            const isAdminOutsourcing = {{ $isAdminOutsourcing ? 'true' : 'false' }};
            const isStandar = {{ $isStandar ? 'true' : 'false' }};

            if((id === 'kelola-user' || id === 'lokasi-absen') && !isSuperAdmin) {
                Swal.fire('Akses Ditolak', 'Hanya Super Admin yang bisa mengakses menu ini.', 'error');
                return;
            }

            if((id === 'stationery' || id === 'analitik-stationery' || id === 'transaksi-stationery' || id === 'laporan-stationery' || id === 'pengadaan-stationery') && !isSuperAdmin && !isAdminStationery) {
                Swal.fire('Akses Ditolak', 'Hanya Admin Stationery yang bisa mengakses menu ini.', 'error');
                return;
            }

            if(isAdminInternal && !['input', 'rekap', 'pengajuan-spl', 'rekap-spl'].includes(id)) {
                Swal.fire('Akses Ditolak', 'Admin Internal hanya berhak mengurus perihal Absensi dan Pengajuan SPL.', 'warning');
                return;
            }

            if(isAdminOutsourcing && !['rekap-spl', 'lembur', 'input', 'rekap'].includes(id)) {
                Swal.fire('Akses Ditolak', 'Admin Outsourcing hanya berhak mengakses Rekap SPL, Rekap Lembur, dan Absensi.', 'warning');
                return;
            }

            if(isStandar && id !== 'form-absen') {
                Swal.fire('Akses Ditolak', 'Karyawan hanya memiliki akses ke Form Absen.', 'warning');
                return;
            }

            const pageTitles = {
                'ringkasan': 'Dashboard / Overview',
                'dashboard-karyawan': 'Dashboard Personal',
                'form-absen': 'Form Absensi Karyawan',
                'karyawan': 'Data Karyawan',
                'master-shift': 'Master Data Shift',
                'jadwal-karyawan': 'Plotting Jadwal Karyawan',
                'input': 'Rekap Absensi',
                'pengajuan-spl': 'Pengajuan Surat Perintah Lembur',
                'rekap-spl': 'Rekap Surat Perintah Lembur (Bukti SPL)',
                'input-lembur': 'Input Lembur Karyawan',
                'rekap': 'Laporan Absensi',
                'lembur': 'Gaji Lembur',
                'analitik-stationery': 'Dashboard Stationery',
                'stationery': 'Master Stok',
                'transaksi-stationery': 'Transaksi Barang',
                'kelola-user': 'Manajemen User',
                'lokasi-absen': 'Pengaturan Lokasi & Radius Presensi',
                'laporan-stationery': 'Laporan Barang',
                'pengadaan-stationery': 'Pengadaan Barang',
                'closing-stationery': 'Closing Periode',
                'aspek-penilaian': 'Aspek Penilaian Karyawan',
                'laporan-penilaian': 'Laporan Penilaian Bulanan'
            };
            const headerTitleEl = document.getElementById('header-title');
            if (headerTitleEl && pageTitles[id]) {
                headerTitleEl.innerText = pageTitles[id];
            }

            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('flex'));
            document.querySelectorAll('.side-nav-btn').forEach(el => el.classList.remove('active'));

            const targetTab = document.getElementById('tab-' + id);
            if (targetTab) {
                targetTab.classList.remove('hidden');
                targetTab.classList.add('flex');

                targetTab.style.animation = 'slideUpFadeIn 0.5s ease-out forwards';
                setTimeout(() => {
                    targetTab.style.transform = 'none';
                }, 500);
            }
            if (document.getElementById('btn-' + id)) {
                document.getElementById('btn-' + id).classList.add('active');
            }
            if (id === 'karyawan' && document.getElementById('btn-karyawan-penilaian')) {
                document.getElementById('btn-karyawan-penilaian').classList.add('active');
            }

            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('main-sidebar');
                if (sidebar && sidebar.classList.contains('translate-x-0')) {
                    toggleSidebar();
                }
            }

            window.location.hash = id;
            try {
                localStorage.setItem('lastDashboardTab', id);
            } catch (e) {
                console.warn('LocalStorage tidak tersedia untuk tab history:', e);
            }
            if(id === 'analitik-stationery' && typeof updateStationeryChart === 'function') {
                updateStationeryChart('init');
            }

            if(id === 'ringkasan') {
                let waktuTunggu = [100, 400, 800];
                waktuTunggu.forEach(waktu => {
                    setTimeout(() => {
                        if(ringkasanBarChart) {
                            ringkasanBarChart.resize();
                            ringkasanBarChart.update();
                        }
                        if(ringkasanPieChart) {
                            ringkasanPieChart.resize();
                            ringkasanPieChart.update();
                        }
                    }, waktu);
                });
                if (typeof window.fetchRealtimeStats === 'function') {
                    window.fetchRealtimeStats(false);
                }
            }

            if(id === 'form-absen') {
                setTimeout(() => {
                    if (typeof userMinimap !== 'undefined' && userMinimap) {
                        userMinimap.invalidateSize();
                    }
                    if (typeof startGPSTracking === 'function') {
                        startGPSTracking();
                    }
                }, 400);
            }

            if(window.innerWidth < 1024) {
                const sidebar = document.getElementById('main-sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                if(!sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                    if(overlay) {
                        overlay.classList.add('hidden', 'opacity-0', 'pointer-events-none');
                    }
                }
            }
        }

        function hitungAkmLembur(prefix) {
            let durasi = parseFloat(document.getElementById(prefix + '_durasi').value) || 0;
            let tipeHari = document.getElementById(prefix + '_tipe').value;
            let j15 = 0, j2 = 0, j3 = 0, j4 = 0;

            if (tipeHari === 'Kerja') {
                if (durasi > 0) { j15 = durasi > 1 ? 1 : durasi; j2 = durasi > 1 ? durasi - 1 : 0; }
            } else if (tipeHari === 'Libur') {
                if (durasi <= 7) { j2 = durasi; } else if (durasi === 8) { j2 = 7; j3 = 1; } else if (durasi > 8) { j2 = 7; j3 = 1; j4 = durasi - 8; }
            }

            if(document.getElementById(prefix + '_15')) document.getElementById(prefix + '_15').value = j15;
            if(document.getElementById(prefix + '_2')) document.getElementById(prefix + '_2').value = j2;
            if(document.getElementById(prefix + '_3')) document.getElementById(prefix + '_3').value = j3;
            if(document.getElementById(prefix + '_4')) document.getElementById(prefix + '_4').value = j4;

            let total = (j15 * 1.5) + (j2 * 2) + (j3 * 3) + (j4 * 4);
            document.getElementById(prefix + '_total').value = total;
        }

        function timeToDec(t) { if(!t) return 0; let p = t.split(':'); return parseInt(p[0]) + (parseInt(p[1])/60); }

        function hitungLemburDetail() {
            let valMulai = document.getElementById('in_mulai').value;
            let valSelesai = document.getElementById('in_selesai').value;
            if (!valMulai || !valSelesai) return;

            let m = timeToDec(valMulai), s = timeToDec(valSelesai);
            let i = parseFloat(document.getElementById('in_istirahat').value) || 0;
            let t = document.getElementById('in_tipe').value;

            let j15 = 0, j2 = 0, j3 = 0, j4 = 0;

            if (s < m) s += 24;
            let r = (s - m) - (i / 60); if (r < 0) r = 0;

            document.getElementById('hide_riil').value = r.toFixed(2);
            document.getElementById('display_riil').innerText = r.toFixed(1).replace('.0', '') + " Jam";

            if (t === 'Kerja') {
                if (r > 0) { j15 = r >= 1 ? 1 : r; j2 = r > 1 ? r - 1 : 0; }
            } else {
                if (r <= 7) { j2 = r; } else if (r <= 8) { j2 = 7; j3 = r - 7; } else { j2 = 7; j3 = 1; j4 = r - 8; }
            }
            let akm = (j15 * 1.5) + (j2 * 2) + (j3 * 3) + (j4 * 4);
            document.getElementById('in_akm').value = akm.toFixed(1).replace('.0', '');
            document.getElementById('hide_15').value = j15; document.getElementById('hide_2').value = j2;
            document.getElementById('hide_3').value = j3; document.getElementById('hide_4').value = j4;
        }

        function toggleLembur(selectId, divId) {
            const select = document.getElementById(selectId);
            const div = document.getElementById(divId);
            const prefix = selectId === 'input_status_kehadiran' || selectId === 'add_status' ? 'add' : 'edit';

            if (select.value === 'Lembur') { div.classList.remove('hidden'); div.classList.add('flex'); }
            else { div.classList.add('hidden'); div.classList.remove('flex');
                if(document.getElementById(prefix + '_durasi')) document.getElementById(prefix + '_durasi').value = '';
                if(document.getElementById(prefix + '_total')) document.getElementById(prefix + '_total').value = '';
            }
        }

        function openAddEmployeeModal() { document.getElementById('addEmployeeModal').classList.remove('opacity-0', 'pointer-events-none'); }
        function closeAddEmployeeModal() { document.getElementById('addEmployeeModal').classList.add('opacity-0', 'pointer-events-none'); }
        function closeEditEmployeeModal() { document.getElementById('editEmployeeModal').classList.add('opacity-0', 'pointer-events-none'); }

        function openEditEmployeeModal(btn) {
            try {
                let base64str = btn.dataset.employee;
                let decodedStr = decodeURIComponent(escape(window.atob(base64str)));
                let empData = JSON.parse(decodedStr);
                if (empData.jam_kerja && (String(empData.jam_kerja).startsWith('=') || String(empData.jam_kerja).toUpperCase().includes('VLOOKUP'))) {
                    empData.jam_kerja = (empData.tipe_hari_kerja && String(empData.tipe_hari_kerja).includes('5')) ? '8 Jam' : '7 Jam';
                }
                document.getElementById('editEmployeeForm').action = `/employees/${empData.id}`;
                for (let key in empData) {
                    let input = document.getElementById('edit_emp_' + key);
                    if (input) {
                        if (empData[key] !== null && empData[key] !== undefined) {
                            if (input.type === 'date') input.value = String(empData[key]).substring(0, 10);
                            else input.value = empData[key];
                        } else input.value = '';
                    }
                }
                document.getElementById('editEmployeeModal').classList.remove('opacity-0', 'pointer-events-none');
            } catch (e) {
                console.error("Gagal memuat data karyawan:", e); Swal.fire('Error!', 'Gagal memuat data.', 'error');
            }
        }

        function applyEmployeeFilters() {
            const searchInput = (document.getElementById("employeeSearch")?.value || "").trim().toUpperCase();
            const filterJabatan = (document.getElementById("filterJabatan")?.value || "").trim().toUpperCase();
            const sortType = document.getElementById("sortKaryawan")?.value || "default";
            const tbody = document.getElementById("employeeTableBody");
            if (!tbody) return;

            let rows = Array.from(tbody.querySelectorAll("tr.employee-row"));
            if (rows.length === 0) return;

            let visibleCount = 0;
            rows.forEach(row => {
                const nik = (row.dataset.nik || row.cells[1]?.textContent || "").trim().toUpperCase();
                const nama = (row.dataset.nama || row.cells[2]?.textContent || "").trim().toUpperCase();
                const jabatan = (row.dataset.jabatan || row.cells[6]?.textContent || row.cells[5]?.textContent || "").trim().toUpperCase();

                const matchSearch = searchInput === "" || nik.includes(searchInput) || nama.includes(searchInput);
                const matchJabatan = filterJabatan === "" || filterJabatan === "SEMUA JABATAN" || jabatan.includes(filterJabatan);

                if (matchSearch && matchJabatan) { 
                    row.style.display = ""; 
                    visibleCount++; 
                } else { 
                    row.style.display = "none"; 
                }
            });

            let noMatchRow = document.getElementById("noEmployeeMatchRow");
            if (visibleCount === 0) {
                if (!noMatchRow) {
                    noMatchRow = document.createElement("tr");
                    noMatchRow.id = "noEmployeeMatchRow";
                    noMatchRow.innerHTML = `<td colspan="75" class="p-12 text-center text-slate-400 font-bold italic border-none text-xs"><i class="fas fa-search mr-2 text-slate-300 text-sm"></i> Tidak ada data karyawan yang cocok</td>`;
                    tbody.appendChild(noMatchRow);
                } else {
                    noMatchRow.style.display = "";
                }
            } else if (noMatchRow) {
                noMatchRow.style.display = "none";
            }

            if (sortType !== "default") {
                rows.sort((a, b) => {
                    const namaA = a.dataset.nama || ""; const namaB = b.dataset.nama || "";
                    const tmkA = a.dataset.tmk || ""; const tmkB = b.dataset.tmk || "";
                    if (sortType === "az") return namaA.localeCompare(namaB);
                    if (sortType === "za") return namaB.localeCompare(namaA);
                    if (sortType === "newest") return tmkB.localeCompare(tmkA);
                    if (sortType === "oldest") return tmkA.localeCompare(tmkB);
                    return 0;
                });
                rows.forEach(row => tbody.appendChild(row));
            }
            let currentNo = 1;
            rows.forEach(row => { if (row.style.display !== "none") { row.cells[0].textContent = currentNo++; } });
            const totalEl = document.getElementById("totalKaryawan");
            if(totalEl) totalEl.innerText = visibleCount;
        }

        function filterRekap() {
            const searchName = document.getElementById("rekapSearch").value.toUpperCase();
            const searchStatus = document.getElementById("filterStatus").value.toUpperCase();
            let searchDate = document.getElementById("filterDate").value;

            if (searchDate) { const parts = searchDate.split('-'); searchDate = `${parts[2]}/${parts[1]}/${parts[0]}`; }
            const tr = document.getElementById("tableRekap").getElementsByTagName("tr");
            let visibleCount = 0;

            for (let i = 1; i < tr.length; i++) {
                const name = tr[i].cells[1]?.textContent.toUpperCase() || "";
                const date = tr[i].cells[2]?.textContent || "";
                const status = tr[i].cells[3]?.textContent.toUpperCase() || "";

                if (name.includes(searchName) && (searchStatus === "" || status.includes(searchStatus)) && (searchDate === "" || date === searchDate)) {
                    tr[i].style.display = ""; visibleCount++; tr[i].cells[0].innerText = visibleCount;
                } else { tr[i].style.display = "none"; }
            }
        }

        let asc = true;
        function sortRekap(n) {
            const t = document.getElementById("tableRekap"); let r = Array.from(t.rows).slice(1); asc = !asc;
            r.sort((a, b) => asc ? a.cells[n].innerText.localeCompare(b.cells[n].innerText) : b.cells[n].innerText.localeCompare(a.cells[n].innerText));
            r.forEach(row => t.tBodies[0].appendChild(row));
            let rows = t.getElementsByTagName("tr"); for (let i = 1; i < rows.length; i++) { rows[i].cells[0].innerText = i; }
            filterRekap();
        }

        function openEditModalAbsen(id, n, s, jam = '') {
            document.getElementById('editForm').action = `/attendance/${id}`;
            document.getElementById('edit_nama').value = n;
            document.getElementById('edit_status').value = s;
            const jamDiv = document.getElementById('edit_lembur_box'); const jamInput = document.getElementById('edit_total');
            if(s === 'Lembur') { jamDiv.classList.remove('hidden'); jamDiv.classList.add('flex'); jamInput.value = jam || ''; }
            else { jamDiv.classList.add('hidden'); jamDiv.classList.remove('flex'); jamInput.value = ''; }
            document.getElementById('editModal').classList.remove('opacity-0', 'pointer-events-none');
        }

        function closeEditModalAbsen() {
            document.getElementById('editModal').classList.add('opacity-0', 'pointer-events-none');
        }

        function openEditProfileModal() {
            const modal = document.getElementById('editProfileModal');
            if (!modal) return;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            if (modal.children && modal.children[0]) {
                modal.children[0].classList.remove('scale-95');
            }
            document.body.style.overflow = 'hidden';
        }

        function closeEditProfileModal() {
            const modal = document.getElementById('editProfileModal');
            if (!modal) return;
            modal.classList.add('opacity-0', 'pointer-events-none');
            if (modal.children && modal.children[0]) {
                modal.children[0].classList.add('scale-95');
            }
            document.body.style.overflow = '';
        }

        window.previewFotoModal = function(url, nama = '') {
            if (!url) return;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: nama ? `<span class="text-xs font-black uppercase text-slate-700 tracking-wider">${nama}</span>` : 'Foto Absensi',
                    imageUrl: url,
                    imageAlt: 'Foto Absensi',
                    imageWidth: 320,
                    imageHeight: 320,
                    showCloseButton: true,
                    showConfirmButton: false,
                    background: '#ffffff',
                    padding: '1.25rem',
                    customClass: {
                        image: 'rounded-2xl object-cover shadow-md border-2 border-slate-100'
                    }
                });
            } else {
                window.open(url, '_blank');
            }
        };

        window.addEventListener('DOMContentLoaded', () => {
            const h = window.location.hash.substring(1);
            const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
            const isAdminStationery = {{ $isAdminStationery ? 'true' : 'false' }};
            const isAdminAbsensi = {{ $isAdminAbsensi ? 'true' : 'false' }};
            const isAdminPenilaian = {{ $isAdminPenilaian ? 'true' : 'false' }};
            const isAdminInternal = {{ $isAdminInternal ? 'true' : 'false' }};
            const isAdminOutsourcing = {{ $isAdminOutsourcing ? 'true' : 'false' }};
            const isStandar = {{ $isStandar ? 'true' : 'false' }};

            const validTabs = ['ringkasan', 'form-absen', 'karyawan', 'master-shift', 'jadwal-karyawan', 'input', 'input-lembur', 'rekap', 'lembur', 'pengajuan-spl', 'rekap-spl', 'kelola-user', 'lokasi-absen', 'stationery', 'analitik-stationery', 'transaksi-stationery', 'laporan-stationery', 'pengadaan-stationery', 'closing-stationery', 'aspek-penilaian', 'laporan-penilaian'];

            const storedTab = (() => {
                try {
                    let tab = localStorage.getItem('lastDashboardTab');
                    if (tab === 'closing-stationery') {
                        localStorage.removeItem('lastDashboardTab');
                        return null;
                    }
                    return tab;
                } catch (e) {
                    return null;
                }
            })();

            if (isStandar) {
                switchTab('form-absen');
            } else if (isAdminInternal) {
                const intTabs = ['input', 'rekap', 'pengajuan-spl', 'rekap-spl'];
                if (h && intTabs.includes(h)) {
                    switchTab(h);
                } else {
                    switchTab('input');
                }
            } else if (isAdminOutsourcing) {
                const outTabs = ['rekap-spl', 'lembur', 'input', 'rekap'];
                if (h && outTabs.includes(h)) {
                    switchTab(h);
                } else {
                    switchTab('rekap-spl');
                }
            } else if (h && validTabs.includes(h)) {
                switchTab(h);
            } else {
                if (isAdminStationery && !isSuperAdmin) {
                    switchTab('analitik-stationery');
                } else if (isAdminPenilaian && !isSuperAdmin) {
                    switchTab('aspek-penilaian');
                } else {
                    switchTab('ringkasan');
                }
            }

            setTimeout(() => {
                try {
                    let dataPegawai = [];
                    document.querySelectorAll('#employeeTableBody tr').forEach(r => {
                        let nik = r.cells[1] ? r.cells[1].innerText.trim() : ''; let nama = r.cells[2] ? r.cells[2].innerText.trim() : '';
                        if(nik && nama) dataPegawai.push({nik: nik, nama: nama});
                    });
                    document.querySelectorAll('#tableRekapAbsen tbody tr, #tableRekap tbody tr').forEach(r => {
                        let idName = r.cells[1] ? r.cells[1].innerText.trim() : '';
                        if(idName.includes('-')) { let parts = idName.split('-'); if(parts.length >= 2) dataPegawai.push({nik: parts[0].trim(), nama: parts[1].trim()}); }
                    });
                    dataPegawai = dataPegawai.filter((v,i,a) => a.findIndex(t => (t.nik === v.nik)) === i);
                    let dlNik = document.getElementById('dl_nik_global') || document.createElement('datalist'); dlNik.id = 'dl_nik_global'; dlNik.innerHTML = '';
                    let dlNama = document.getElementById('dl_nama_global') || document.createElement('datalist'); dlNama.id = 'dl_nama_global'; dlNama.innerHTML = '';
                    dataPegawai.forEach(p => {
                        let opt1 = document.createElement('option'); opt1.value = p.nik; opt1.text = p.nama;
                        let opt2 = document.createElement('option'); opt2.value = p.nama; opt2.text = p.nik;
                        dlNik.appendChild(opt1); dlNama.appendChild(opt2);
                    });
                    document.body.appendChild(dlNik); document.body.appendChild(dlNama);
                    document.querySelectorAll('input[name="id_karyawan"]').forEach((inNik, i) => {
                        let inNama = document.querySelectorAll('input[name="nama_karyawan"]')[i];
                        if(inNik && inNama) {
                            inNik.setAttribute('list', 'dl_nik_global'); inNama.setAttribute('list', 'dl_nama_global');
                            inNik.addEventListener('input', (e) => { let found = dataPegawai.find(p => p.nik === e.target.value); if(found) inNama.value = found.nama; });
                        }
                    });
                } catch(err) { console.error("Error Autocomplete:", err); }
            }, 500);

            const mainContent = document.getElementById('main-content');
            if (mainContent) {
                Array.from(mainContent.children).forEach(child => {
                    child.style.opacity = '0';
                    child.style.transform = 'translateY(20px)';
                });
                setTimeout(() => {
                    Array.from(mainContent.children).forEach((child, index) => {
                        child.style.animation = `slideUpFadeIn 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) ${index * 0.1}s both`;
                    });
                }, 300);
            }

            if (typeof Chart === 'undefined') {
                console.error("Gagal! Library Chart.js belum berhasil dimuat browser.");
            } else {
                Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
                Chart.defaults.color = '#475569';
                Chart.defaults.animation.duration = 1500;
                Chart.defaults.animation.easing = 'easeOutQuart';

                // Helper: create seamless diagonal stripe pattern for pending/empty segments
                // 45 degree seamless diagonal stripes helper for Donezo styling
                function createStripePattern(color = '#cbd5e1', bgColor = '#f8fafc', stripeWidth = 2.5, size = 14) {
                    const pCanvas = document.createElement('canvas');
                    pCanvas.width = size;
                    pCanvas.height = size;
                    const pctx = pCanvas.getContext('2d');
                    pctx.fillStyle = bgColor;
                    pctx.fillRect(0, 0, size, size);
                    pctx.strokeStyle = color;
                    pctx.lineWidth = stripeWidth;
                    pctx.lineCap = 'square';
                    const half = size / 2;
                    pctx.beginPath();
                    pctx.moveTo(-half, half);
                    pctx.lineTo(half, -half);
                    pctx.moveTo(0, size);
                    pctx.lineTo(size, 0);
                    pctx.moveTo(half, size + half);
                    pctx.lineTo(size + half, half);
                    pctx.stroke();
                    return pctx.createPattern(pCanvas, 'repeat');
                }

                // Helper for drawing rounded rect with fallback for older canvas implementations
                function drawPillBadge(ctx, x, y, w, h, r) {
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(x, y, w, h, r);
                    } else {
                        ctx.moveTo(x + r, y);
                        ctx.lineTo(x + w - r, y);
                        ctx.quadraticCurveTo(x + w, y, x + w, y + r);
                        ctx.lineTo(x + w, y + h - r);
                        ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
                        ctx.lineTo(x + r, y + h);
                        ctx.quadraticCurveTo(x, y + h, x, y + h - r);
                        ctx.lineTo(x, y + r);
                        ctx.quadraticCurveTo(x, y, x + r, y);
                    }
                    ctx.closePath();
                }

                // ==============================================================
                // REALTIME DASHBOARD STATE & CHART REFERENCES
                // ==============================================================
                let realtimeBarPeakIdx = -1;
                let realtimeBarPctText = '0%';
                let realtimeBarRawLabels = @json($trendLabels ?? []);
                let realtimeBarRawHadir = @json($trendHadir ?? []);
                let realtimeBarTotalStaff = {{ ($countHadir ?? 0) + ($countLembur ?? 0) + ($countIzin ?? 0) + ($countAlpa ?? 0) }};
                let realtimeDayLetters = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

                let realtimeGaugeHadir = {{ $countHadir ?? 0 }};
                let realtimeGaugeLembur = {{ $countLembur ?? 0 }};
                let realtimeGaugeIzin = {{ $countIzin ?? 0 }};
                let realtimeGaugeAlpa = {{ $countAlpa ?? 0 }};
                let realtimeGaugeTotalReal = {{ ($countHadir ?? 0) + ($countLembur ?? 0) + ($countIzin ?? 0) + ($countAlpa ?? 0) }};

                let gradHadirActiveRef = null;
                let gradHadirDarkRef = null;
                let patternStripedRef = null;
                let gradCompletedRef = null;
                let patternPendingRef = null;

                // ==============================================================
                // 1. PROJECT ANALYTICS BAR CHART (DONEZO CAPSULE & STRIPE STYLE)
                // ==============================================================
                try {
                    let barChartCanvas = document.getElementById('barChart');
                    if (barChartCanvas) {
                        let ctxBar = barChartCanvas.getContext('2d');
                        
                        // Active solid/gradients
                        gradHadirActiveRef = ctxBar.createLinearGradient(0, 0, 0, 300);
                        gradHadirActiveRef.addColorStop(0, '#0284c7');
                        gradHadirActiveRef.addColorStop(1, '#004a99');

                        gradHadirDarkRef = ctxBar.createLinearGradient(0, 0, 0, 300);
                        gradHadirDarkRef.addColorStop(0, '#0369a1');
                        gradHadirDarkRef.addColorStop(1, '#002b5c');

                        patternStripedRef = createStripePattern('#cbd5e1', '#f8fafc', 2.5, 14);

                        const defaultLetters = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                        realtimeDayLetters = realtimeBarRawLabels.length === 7 
                            ? realtimeBarRawLabels.map((lbl, idx) => defaultLetters[idx % 7])
                            : defaultLetters;

                        const maxHadirVal = Math.max(...realtimeBarRawHadir, 0);
                        let peakIdx = -1;
                        let highest = 0;
                        if (maxHadirVal > 0) {
                            realtimeBarRawHadir.forEach((val, idx) => {
                                if (val > highest) {
                                    highest = val;
                                    peakIdx = idx;
                                }
                            });
                        }
                        realtimeBarPeakIdx = peakIdx;

                        const actualPeakVal = peakIdx >= 0 ? (realtimeBarRawHadir[peakIdx] || 0) : 0;
                        realtimeBarPctText = realtimeBarTotalStaff > 0 && actualPeakVal > 0 
                            ? Math.round((actualPeakVal / realtimeBarTotalStaff) * 100) + '%' 
                            : '0%';

                        // Cadence of bar heights matching Donezo's natural aesthetics
                        const donezoStripedCadence = [65, 78, 74, 94, 85, 62, 75];
                        const emptyPlaceholderCadence = [28, 28, 28, 28, 28, 28, 28];

                        const displayValues = [];
                        const barColors = [];
                        const borderColors = [];
                        const borderWidths = [];

                        for (let idx = 0; idx < 7; idx++) {
                            const val = realtimeBarRawHadir[idx] !== undefined ? realtimeBarRawHadir[idx] : 0;
                            if (val > 0) {
                                // Scale active attendance height dynamically between 68 and 96
                                const scaledH = maxHadirVal > 0 
                                    ? 68 + Math.round((val / maxHadirVal) * 26) 
                                    : 78;
                                displayValues.push(scaledH);
                                if (idx === realtimeBarPeakIdx) {
                                    barColors.push(gradHadirActiveRef);
                                } else {
                                    barColors.push(gradHadirDarkRef);
                                }
                                borderColors.push('transparent');
                                borderWidths.push(0);
                            } else {
                                if (maxHadirVal === 0) {
                                    displayValues.push(emptyPlaceholderCadence[idx]);
                                    barColors.push('#f1f5f9');
                                    borderColors.push('#e2e8f0');
                                    borderWidths.push(1);
                                } else {
                                    displayValues.push(donezoStripedCadence[idx % 7]);
                                    barColors.push(patternStripedRef);
                                    borderColors.push('#cbd5e1');
                                    borderWidths.push(1.5);
                                }
                            }
                        }

                        // Plugin for floating pill badge above peak bar (Donezo badge)
                        const donezoFloatingBadgePlugin = {
                            id: 'donezoFloatingBadge',
                            afterDatasetsDraw(chart) {
                                const maxVal = Math.max(...realtimeBarRawHadir, 0);
                                if (maxVal <= 0 || realtimeBarPeakIdx < 0) return; // Do not draw floating badge if 0%

                                const meta = chart.getDatasetMeta(0);
                                if (!meta || !meta.data || !meta.data.length) return;
                                if (realtimeBarPeakIdx >= meta.data.length) return;
                                
                                const bar = meta.data[realtimeBarPeakIdx];
                                if (!bar) return;

                                const ctx = chart.ctx;
                                ctx.save();
                                const x = bar.x;
                                const barTopY = bar.y;
                                
                                const badgeW = 46;
                                const badgeH = 22;
                                const radius = 7;
                                const pointerH = 5;
                                const gap = 4;
                                
                                const badgeY = barTopY - pointerH - gap - badgeH;
                                const badgeX = x - badgeW / 2;

                                // Pill background & border
                                ctx.fillStyle = '#f0f9ff';
                                ctx.strokeStyle = '#38bdf8';
                                ctx.lineWidth = 1.5;
                                drawPillBadge(ctx, badgeX, badgeY, badgeW, badgeH, radius);
                                ctx.fill();
                                ctx.stroke();

                                // Downward pointer triangle
                                ctx.fillStyle = '#38bdf8';
                                ctx.beginPath();
                                ctx.moveTo(x - 4, badgeY + badgeH);
                                ctx.lineTo(x + 4, badgeY + badgeH);
                                ctx.lineTo(x, badgeY + badgeH + pointerH);
                                ctx.closePath();
                                ctx.fill();

                                // Badge text
                                ctx.fillStyle = '#0284c7';
                                ctx.font = "800 11px 'Plus Jakarta Sans', sans-serif";
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                ctx.fillText(realtimeBarPctText, x, badgeY + (badgeH / 2));

                                ctx.restore();
                            }
                        };

                        ringkasanBarChart = new Chart(barChartCanvas, {
                            type: 'bar',
                            data: {
                                labels: realtimeDayLetters,
                                datasets: [{
                                    data: displayValues,
                                    backgroundColor: barColors,
                                    borderColor: borderColors,
                                    borderWidth: borderWidths,
                                    borderRadius: 50,
                                    borderSkipped: false,
                                    maxBarThickness: 46,
                                    categoryPercentage: 0.88,
                                    barPercentage: 0.95
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                layout: {
                                    padding: { top: 38, bottom: 5, left: 5, right: 5 }
                                },
                                plugins: {
                                    legend: { display: false },
                                    datalabels: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                        titleFont: { size: 12, weight: 'bold' },
                                        bodyFont: { size: 12, weight: '500' },
                                        padding: 12,
                                        cornerRadius: 10,
                                        displayColors: false,
                                        callbacks: {
                                            title: function(items) {
                                                const idx = items[0].dataIndex;
                                                return realtimeBarRawLabels[idx] || `Hari ${realtimeDayLetters[idx] || (idx + 1)}`;
                                            },
                                            label: function(context) {
                                                const idx = context.dataIndex;
                                                const val = realtimeBarRawHadir[idx] || 0;
                                                if (val > 0) {
                                                    const rate = realtimeBarTotalStaff > 0 ? Math.round((val / realtimeBarTotalStaff) * 100) : 0;
                                                    return ` Kehadiran: ${val} Orang (${rate}%)`;
                                                }
                                                return ' Libur / Tidak ada presensi';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        display: false,
                                        min: 0,
                                        max: 105
                                    },
                                    x: {
                                        grid: { display: false },
                                        border: { display: false },
                                        ticks: {
                                            color: '#94a3b8',
                                            font: { weight: '800', size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                                            padding: 12
                                        }
                                    }
                                }
                            },
                            plugins: [donezoFloatingBadgePlugin]
                        });
                    }
                } catch(e) { console.error("Gagal render Donezo Bar Chart:", e); }

                // ==============================================================
                // 2. PROJECT PROGRESS SEMI-CIRCLE GAUGE (DONEZO STYLE)
                // ==============================================================
                try {
                    let pieChartCanvas = document.getElementById('pieChart');
                    if (pieChartCanvas) {
                        let ctxGauge = pieChartCanvas.getContext('2d');
                        
                        patternPendingRef = createStripePattern('#94a3b8', '#f8fafc', 2.5, 14);

                        gradCompletedRef = ctxGauge.createLinearGradient(0, 0, 0, 240);
                        gradCompletedRef.addColorStop(0, '#0284c7');
                        gradCompletedRef.addColorStop(1, '#004a99');

                        let colorInProgress = '#7c3aed';
                        let colorIzin = '#f59e0b';

                        const isGaugeEmpty = (realtimeGaugeHadir === 0 && realtimeGaugeLembur === 0 && realtimeGaugeIzin === 0);

                        const gaugeData = realtimeGaugeTotalReal > 0 
                            ? (isGaugeEmpty 
                                ? [0, 0, 0, realtimeGaugeAlpa || 1] 
                                : [realtimeGaugeHadir, realtimeGaugeLembur, realtimeGaugeIzin, Math.max(realtimeGaugeAlpa, 0)]) 
                            : [0, 0, 0, 1];

                        const gaugeColors = isGaugeEmpty
                            ? ['#e2e8f0', '#e2e8f0', '#e2e8f0', '#f1f5f9']
                            : [gradCompletedRef, colorInProgress, colorIzin, '#e2e8f0'];

                        // Plugin for Donezo Big Center Percentage & Subtitle
                        const donezoGaugeCenterPlugin = {
                            id: 'donezoGaugeCenter',
                            afterDatasetsDraw(chart) {
                                const meta = chart.getDatasetMeta(0);
                                if (!meta || !meta.data || !meta.data.length) return;

                                const firstArc = meta.data[0];
                                if (!firstArc) return;

                                const ctx = chart.ctx;
                                ctx.save();
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';

                                const centerX = firstArc.x;
                                const centerY = firstArc.y;
                                const innerRadius = firstArc.innerRadius || 65;
                                
                                const midY = centerY - (innerRadius * 0.48);

                                const pct = realtimeGaugeTotalReal > 0 ? Math.round((realtimeGaugeHadir / realtimeGaugeTotalReal) * 100) : 0;

                                // Large bold percentage
                                ctx.font = "800 36px 'Plus Jakarta Sans', sans-serif";
                                ctx.fillStyle = pct > 0 ? "#0f172a" : "#94a3b8";
                                ctx.fillText(pct + "%", centerX, midY - 10);

                                // Subtitle
                                ctx.font = "700 11px 'Plus Jakarta Sans', sans-serif";
                                ctx.fillStyle = pct > 0 ? "#64748b" : "#94a3b8";
                                const subtitleText = pct > 0 ? "Presensi Selesai" : "Menunggu Presensi";
                                ctx.fillText(subtitleText, centerX, midY + 16);

                                ctx.restore();
                            }
                        };

                        ringkasanPieChart = new Chart(pieChartCanvas, {
                            type: 'doughnut',
                            data: {
                                labels: ['Hadir', 'Lembur', 'Izin', 'Tanpa Keterangan'],
                                datasets: [{
                                    data: gaugeData,
                                    backgroundColor: gaugeColors,
                                    borderColor: '#ffffff',
                                    borderWidth: 2,
                                    borderRadius: 14,
                                    spacing: 3,
                                    hoverOffset: 6
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                rotation: -90,
                                circumference: 180,
                                cutout: '72%',
                                layout: {
                                    padding: { top: 12, bottom: 4, left: 15, right: 15 }
                                },
                                plugins: {
                                    legend: { display: false },
                                    datalabels: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                        titleFont: { size: 13, weight: 'bold' },
                                        bodyFont: { size: 12, weight: '500' },
                                        padding: 12,
                                        cornerRadius: 10,
                                        boxPadding: 6,
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const val = realtimeGaugeTotalReal > 0 
                                                    ? [realtimeGaugeHadir, realtimeGaugeLembur, realtimeGaugeIzin, realtimeGaugeAlpa][context.dataIndex] 
                                                    : context.raw;
                                                const p = realtimeGaugeTotalReal > 0 ? Math.round((val / realtimeGaugeTotalReal) * 100) : 25;
                                                return ` ${label}: ${val} Orang (${p}%)`;
                                            }
                                        }
                                    }
                                }
                            },
                            plugins: [donezoGaugeCenterPlugin]
                        });
                    }
                } catch(e) { console.error("Gagal render Donezo Gauge Chart:", e); }

                // ==============================================================
                // 3. REALTIME SYNC ENGINE (POLLING, ANIMATION, & MANUAL TRIGGER)
                // ==============================================================
                function animateValue(elem, start, end, duration = 650) {
                    if (!elem) return;
                    if (isNaN(start)) start = 0;
                    if (isNaN(end)) end = 0;
                    if (start === end) {
                        elem.innerText = end;
                        return;
                    }
                    const range = end - start;
                    const startTime = performance.now();

                    function updateCounter(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        const ease = 1 - (1 - progress) * (1 - progress);
                        const currentVal = Math.round(start + (range * ease));
                        elem.innerText = currentVal;
                        if (progress < 1) {
                            requestAnimationFrame(updateCounter);
                        } else {
                            elem.innerText = end;
                        }
                    }
                    requestAnimationFrame(updateCounter);
                }

                function highlightCardChange(cardId) {
                    const card = document.getElementById(cardId);
                    if (!card) return;
                    card.classList.add('ring-4', 'ring-blue-400/40', 'scale-[1.02]');
                    setTimeout(() => {
                        card.classList.remove('ring-4', 'ring-blue-400/40', 'scale-[1.02]');
                    }, 1000);
                }

                window.updateDashboardRealtime = function(data) {
                    if (!data) return;

                    // 1. Total Hadir
                    const hadirEl = document.getElementById('stat-count-hadir');
                    if (hadirEl) {
                        const curHadir = parseInt(hadirEl.innerText.replace(/[^0-9]/g, '')) || 0;
                        const newHadir = parseInt(data.countHadir) || 0;
                        if (curHadir !== newHadir) {
                            animateValue(hadirEl, curHadir, newHadir);
                            highlightCardChange('card-hadir');
                        }
                    }

                    // 2. Total Lembur
                    const lemburEl = document.getElementById('stat-count-lembur');
                    if (lemburEl) {
                        const curLembur = parseInt(lemburEl.innerText.replace(/[^0-9]/g, '')) || 0;
                        const newLembur = parseInt(data.countLembur) || 0;
                        if (curLembur !== newLembur) {
                            animateValue(lemburEl, curLembur, newLembur);
                            highlightCardChange('card-lembur');
                        }
                    }

                    // 3. Izin / Sakit
                    const izinEl = document.getElementById('stat-count-izin');
                    if (izinEl) {
                        const curIzin = parseInt(izinEl.innerText.replace(/[^0-9]/g, '')) || 0;
                        const newIzin = parseInt(data.countIzin) || 0;
                        if (curIzin !== newIzin) {
                            animateValue(izinEl, curIzin, newIzin);
                            highlightCardChange('card-izin');
                        }
                    }

                    // 4. Tanpa Keterangan
                    const alpaEl = document.getElementById('stat-count-alpa');
                    if (alpaEl) {
                        const curAlpa = parseInt(alpaEl.innerText.replace(/[^0-9]/g, '')) || 0;
                        const newAlpa = parseInt(data.countAlpa) || 0;
                        if (curAlpa !== newAlpa) {
                            animateValue(alpaEl, curAlpa, newAlpa);
                            highlightCardChange('card-alpa');
                        }
                    }

                    // 5. Update Time Sync Badge
                    const syncEl = document.getElementById('realtime-last-sync');
                    if (syncEl && data.lastUpdated) {
                        syncEl.innerText = data.lastUpdated;
                    }

                    // 6. Update Tren Kehadiran Mingguan Bar Chart
                    if (ringkasanBarChart && Array.isArray(data.trendLabels) && Array.isArray(data.trendHadir)) {
                        realtimeBarRawLabels = data.trendLabels;
                        realtimeBarRawHadir = data.trendHadir.map(v => parseInt(v) || 0);
                        realtimeBarTotalStaff = parseInt(data.totalStaff) || parseInt(data.totalReal) || 0;

                        const defaultLetters = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                        realtimeDayLetters = realtimeBarRawLabels.length === 7 
                            ? realtimeBarRawLabels.map((lbl, idx) => defaultLetters[idx % 7])
                            : defaultLetters;

                        const maxHadirVal = Math.max(...realtimeBarRawHadir, 0);
                        let peakIdx = -1;
                        let highest = 0;
                        if (maxHadirVal > 0) {
                            realtimeBarRawHadir.forEach((val, idx) => {
                                if (val > highest) {
                                    highest = val;
                                    peakIdx = idx;
                                }
                            });
                        }
                        realtimeBarPeakIdx = peakIdx;

                        const actualPeakVal = peakIdx >= 0 ? (realtimeBarRawHadir[peakIdx] || 0) : 0;
                        realtimeBarPctText = realtimeBarTotalStaff > 0 && actualPeakVal > 0 
                            ? Math.round((actualPeakVal / realtimeBarTotalStaff) * 100) + '%' 
                            : '0%';

                        const donezoStripedCadence = [65, 78, 74, 94, 85, 62, 75];
                        const emptyPlaceholderCadence = [28, 28, 28, 28, 28, 28, 28];
                        const newDisplayValues = [];
                        const newBarColors = [];
                        const newBorderColors = [];
                        const newBorderWidths = [];

                        for (let idx = 0; idx < 7; idx++) {
                            const val = realtimeBarRawHadir[idx] !== undefined ? realtimeBarRawHadir[idx] : 0;
                            if (val > 0) {
                                const scaledH = maxHadirVal > 0 
                                    ? 68 + Math.round((val / maxHadirVal) * 26) 
                                    : 78;
                                newDisplayValues.push(scaledH);
                                if (idx === realtimeBarPeakIdx) {
                                    newBarColors.push(gradHadirActiveRef);
                                } else {
                                    newBarColors.push(gradHadirDarkRef);
                                }
                                newBorderColors.push('transparent');
                                newBorderWidths.push(0);
                            } else {
                                if (maxHadirVal === 0) {
                                    newDisplayValues.push(emptyPlaceholderCadence[idx]);
                                    newBarColors.push('#f1f5f9');
                                    newBorderColors.push('#e2e8f0');
                                    newBorderWidths.push(1);
                                } else {
                                    newDisplayValues.push(donezoStripedCadence[idx % 7]);
                                    newBarColors.push(patternStripedRef);
                                    newBorderColors.push('#cbd5e1');
                                    newBorderWidths.push(1.5);
                                }
                            }
                        }

                        ringkasanBarChart.data.labels = realtimeDayLetters;
                        ringkasanBarChart.data.datasets[0].data = newDisplayValues;
                        ringkasanBarChart.data.datasets[0].backgroundColor = newBarColors;
                        ringkasanBarChart.data.datasets[0].borderColor = newBorderColors;
                        ringkasanBarChart.data.datasets[0].borderWidth = newBorderWidths;
                        ringkasanBarChart.update();

                        const barEmptyNote = document.getElementById('bar-empty-note');
                        if (barEmptyNote) {
                            if (maxHadirVal === 0) barEmptyNote.classList.remove('hidden');
                            else barEmptyNote.classList.add('hidden');
                        }
                    }

                    // 7. Update Rasio Kehadiran Hari Ini Semi-Circle Gauge Chart
                    if (ringkasanPieChart) {
                        realtimeGaugeHadir = parseInt(data.countHadir) || 0;
                        realtimeGaugeLembur = parseInt(data.countLembur) || 0;
                        realtimeGaugeIzin = parseInt(data.countIzin) || 0;
                        realtimeGaugeAlpa = parseInt(data.countAlpa) || 0;
                        realtimeGaugeTotalReal = realtimeGaugeHadir + realtimeGaugeLembur + realtimeGaugeIzin + realtimeGaugeAlpa;

                        const isGaugeEmptyNow = (realtimeGaugeHadir === 0 && realtimeGaugeLembur === 0 && realtimeGaugeIzin === 0);
                        const newGaugeData = realtimeGaugeTotalReal > 0 
                            ? (isGaugeEmptyNow 
                                ? [0, 0, 0, realtimeGaugeAlpa || 1] 
                                : [realtimeGaugeHadir, realtimeGaugeLembur, realtimeGaugeIzin, Math.max(realtimeGaugeAlpa, 0)]) 
                            : [0, 0, 0, 1];

                        const newGaugeColors = isGaugeEmptyNow
                            ? ['#e2e8f0', '#e2e8f0', '#e2e8f0', '#f1f5f9']
                            : [gradCompletedRef, '#7c3aed', '#f59e0b', '#e2e8f0'];

                        ringkasanPieChart.data.datasets[0].data = newGaugeData;
                        ringkasanPieChart.data.datasets[0].backgroundColor = newGaugeColors;
                        ringkasanPieChart.update();

                        const gaugeEmptyNote = document.getElementById('gauge-empty-note');
                        if (gaugeEmptyNote) {
                            if (realtimeGaugeHadir === 0) {
                                gaugeEmptyNote.innerHTML = '<i class="fas fa-clock text-amber-500 text-xs"></i><span>Menunggu data presensi masuk hari ini</span>';
                            } else {
                                gaugeEmptyNote.innerHTML = `<i class="fas fa-check-circle text-emerald-500 text-xs"></i><span>${realtimeGaugeHadir} dari ${realtimeGaugeTotalReal} karyawan telah presensi</span>`;
                            }
                        }
                    }
                };

                let isRealtimeFetching = false;
                window.fetchRealtimeStats = async function(isManual = false) {
                    const ringkasanTab = document.getElementById('tab-ringkasan');
                    const isRingkasanActive = ringkasanTab && !ringkasanTab.classList.contains('hidden');
                    if (!isRingkasanActive && !isManual) return;
                    if (isRealtimeFetching) return;

                    const iconSync = document.getElementById('icon-manual-sync');
                    if (isManual && iconSync) {
                        iconSync.classList.add('fa-spin');
                    }

                    try {
                        isRealtimeFetching = true;
                        const dateInput = document.getElementById('filter_tanggal_admin');
                        const tanggal = dateInput ? dateInput.value : '';
                        const url = `/api/dashboard/realtime-stats${tanggal ? '?tanggal=' + encodeURIComponent(tanggal) : ''}`;

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) return;

                        const res = await response.json();
                        const data = res && res.data ? res.data : res;
                        if (data && (data.countHadir !== undefined || data.status === 'success' || res.success)) {
                            window.updateDashboardRealtime(data);
                        }
                    } catch (err) {
                        console.warn('Realtime fetch error:', err);
                    } finally {
                        isRealtimeFetching = false;
                        if (isManual && iconSync) {
                            setTimeout(() => { iconSync.classList.remove('fa-spin'); }, 500);
                        }
                    }
                };

                window.triggerManualRealtimeSync = function() {
                    window.fetchRealtimeStats(true);
                };

                window.handleDateFilterChange = function(newDate) {
                    window.fetchRealtimeStats(true);
                };

                // Auto Polling every 5 seconds
                if (!window.realtimeDashboardInterval) {
                    window.realtimeDashboardInterval = setInterval(() => {
                        if (document.hidden) return;
                        window.fetchRealtimeStats(false);
                    }, 5000);
                }
            }

            const btnNotif = document.getElementById("btnNotifikasi");
            const dropdownNotif = document.getElementById("dropdownNotifikasi");
            if(btnNotif && dropdownNotif) {
                btnNotif.addEventListener("click", function(event) { event.stopPropagation(); dropdownNotif.classList.toggle("hidden"); });
                window.addEventListener("click", function(event) { if (!dropdownNotif.contains(event.target) && !btnNotif.contains(event.target)) { dropdownNotif.classList.add("hidden"); } });
            }

            if (window.location.hash === '#ringkasan' || window.location.hash === '') {
                setTimeout(() => {
                    if(ringkasanBarChart) { ringkasanBarChart.resize(); ringkasanBarChart.update(); }
                    if(ringkasanPieChart) { ringkasanPieChart.resize(); ringkasanPieChart.update(); }
                }, 850);
            }
        });
            </script>
</x-app-layout>
