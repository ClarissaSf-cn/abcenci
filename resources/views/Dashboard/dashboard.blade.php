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
            padding: 0.75rem 1.15rem;
            margin-bottom: 0.35rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            border-radius: 1rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: capitalize;
            letter-spacing: 0.015em;
            position: relative;
            overflow: hidden;
        }
        .side-nav-btn i { width: 22px; margin-right: 12px; font-size: 1rem; opacity: 0.9; color: inherit; transition: 0.2s; }
        .side-nav-btn:hover { background: rgba(255, 255, 255, 0.12); color: #ffffff; transform: translateX(3px); }

        .side-nav-btn.active {
            background: #ffffff;
            color: #004a99;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            font-weight: 800;
            transform: translateX(0);
        }
        .side-nav-btn.active i { opacity: 1; color: #004a99; }
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
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="text-slate-500 hover:text-indofood-blue focus:outline-none transition-colors p-2.5 rounded-xl hover:bg-slate-100 flex items-center justify-center cursor-pointer">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
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

            <main id="main-content" class="flex-1 overflow-y-auto p-3 sm:p-5 lg:p-7 relative">
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
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-1 pb-1">
                                    <div>
                                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Utama</h1>
                                        <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">Ringkasan statistik kehadiran, jam kerja, dan aktivitas presensi karyawan secara realtime.</p>
                                    </div>

                                    {{-- WIDGET TANGGAL SEKARANG (Donezo Pill Style) --}}
                                    <div class="w-full sm:w-auto bg-white border border-slate-200/90 hover:border-slate-300 px-4 py-2.5 rounded-2xl shadow-2xs flex items-center gap-3 transition-all cursor-pointer">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-indofood-blue flex items-center justify-center text-sm shrink-0">
                                            <i class="fas fa-calendar-alt text-xs"></i>
                                        </div>
                                        <div class="text-left flex-1 sm:flex-none">
                                            <label for="filter_tanggal_admin" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none cursor-pointer block mb-0.5">Tanggal Presensi</label>
                                            <input type="date" id="filter_tanggal_admin" name="tanggal"
                                                   value="{{ request('tanggal', \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d')) }}"
                                                   onchange="window.location.href = window.location.pathname + '?tanggal=' + this.value + '#ringkasan';"
                                                   class="text-xs sm:text-sm font-extrabold text-slate-800 bg-transparent border-none p-0 m-0 focus:ring-0 cursor-pointer outline-none w-full">
                                        </div>
                                    </div>
                                </div>

                                {{-- ========================================== --}}
                                {{-- WIDGET KARTU STATISTIK (DONEZO STYLE) --}}
                                {{-- ========================================== --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mt-2">
                                    {{-- Card 1: Featured Card (Total Hadir) - Indofood Blue --}}
                                    <div class="bg-indofood-blue text-white rounded-3xl p-5 sm:p-6 shadow-xl shadow-blue-900/15 relative overflow-hidden flex flex-col justify-between min-h-[145px] sm:min-h-[160px] group hover:-translate-y-1 transition-all duration-300">
                                        <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                                        <div class="flex items-center justify-between relative z-10">
                                            <span class="text-xs sm:text-sm font-bold text-blue-100 tracking-wide">Total Hadir</span>
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/20 backdrop-blur-xs flex items-center justify-center text-white text-xs font-bold shadow-2xs group-hover:bg-white group-hover:text-indofood-blue transition-colors">
                                                <i class="fas fa-arrow-up-right"></i>
                                            </div>
                                        </div>

                                        <div class="relative z-10 my-2">
                                            <h3 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-none">
                                                {{ $countHadir ?? 0 }}
                                            </h3>
                                        </div>

                                        <div class="relative z-10">
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 text-[11px] font-semibold text-blue-50 backdrop-blur-2xs">
                                                <i class="fas fa-check-circle text-[10px] text-emerald-300"></i>
                                                <span>Hadir hari ini</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card 2: Total Lembur - Donezo White Card --}}
                                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-2xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[145px] sm:min-h-[160px] group">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs sm:text-sm font-bold text-slate-500 tracking-wide">Total Lembur</span>
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-purple-50 text-purple-600 border border-purple-100/80 flex items-center justify-center text-xs font-bold group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                                <i class="fas fa-arrow-up-right"></i>
                                            </div>
                                        </div>

                                        <div class="my-2">
                                            <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-none">
                                                {{ $countLembur ?? 0 }}
                                            </h3>
                                        </div>

                                        <div>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100/80 text-[11px] font-semibold">
                                                <i class="fas fa-clock text-[10px]"></i>
                                                <span>Jam lembur aktif</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card 3: Izin / Sakit - Donezo White Card --}}
                                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-2xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[145px] sm:min-h-[160px] group">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs sm:text-sm font-bold text-slate-500 tracking-wide">Izin / Sakit</span>
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-amber-50 text-amber-600 border border-amber-100/80 flex items-center justify-center text-xs font-bold group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                                <i class="fas fa-arrow-up-right"></i>
                                            </div>
                                        </div>

                                        <div class="my-2">
                                            <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-none">
                                                {{ $countIzin ?? 0 }}
                                            </h3>
                                        </div>

                                        <div>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100/80 text-[11px] font-semibold">
                                                <i class="fas fa-file-medical text-[10px]"></i>
                                                <span>Dispensasi & cuti</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card 4: Tanpa Keterangan - Donezo White Card --}}
                                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-2xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[145px] sm:min-h-[160px] group">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs sm:text-sm font-bold text-slate-500 tracking-wide">Tanpa Ket.</span>
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-100 text-slate-600 border border-slate-200/80 flex items-center justify-center text-xs font-bold group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                                <i class="fas fa-arrow-up-right"></i>
                                            </div>
                                        </div>

                                        <div class="my-2">
                                            <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-none">
                                                {{ $countAlpa ?? 0 }}
                                            </h3>
                                        </div>

                                        <div>
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200/80 text-[11px] font-semibold">
                                                <i class="fas fa-exclamation-circle text-[10px]"></i>
                                                <span>Perlu konfirmasi</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Area Grafik / Chart (Donezo Style) --}}
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 mt-2">
                                    {{-- 1. Bar Chart (Project Analytics) --}}
                                    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-2xs border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition-all duration-300">
                                        <div class="flex items-center justify-between pb-3">
                                            <div>
                                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Project Analytics</h3>
                                                <p class="text-xs text-slate-400 font-medium mt-0.5">Tren statistik presensi 7 hari terakhir</p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full bg-slate-50 text-[11px] font-bold text-slate-600 border border-slate-200/70">
                                                7 Hari
                                            </span>
                                        </div>
                                        <div class="relative w-full flex-1 flex items-center justify-center min-h-[300px] max-h-[330px]">
                                            <canvas id="barChart" class="w-full h-full"></canvas>
                                        </div>
                                    </div>

                                    {{-- 2. Semi-Circle Gauge (Project Progress) --}}
                                    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-2xs border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition-all duration-300">
                                        <div class="flex items-center justify-between pb-3">
                                            <div>
                                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Project Progress</h3>
                                                <p class="text-xs text-slate-400 font-medium mt-0.5">Rasio kehadiran karyawan hari ini</p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full bg-blue-50 text-[11px] font-bold text-indofood-blue border border-blue-100">
                                                Hari Ini
                                            </span>
                                        </div>
                                        <div class="relative w-full flex-1 flex items-center justify-center min-h-[210px] max-h-[240px]">
                                            <canvas id="pieChart" class="w-full h-full"></canvas>
                                        </div>

                                        {{-- Custom Donezo Legend at the bottom --}}
                                        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 pt-3 mt-1 border-t border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-indofood-blue shadow-2xs"></span>
                                                <span class="text-xs font-bold text-slate-700">Hadir</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-slate-900 shadow-2xs"></span>
                                                <span class="text-xs font-bold text-slate-700">Lembur</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-2xs"></span>
                                                <span class="text-xs font-bold text-slate-700">Izin</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-3.5 h-3.5 rounded-xs border border-slate-300 relative overflow-hidden shadow-2xs" style="background: repeating-linear-gradient(45deg, #cbd5e1, #cbd5e1 2px, #f8fafc 2px, #f8fafc 6px);"></span>
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
                        @include('Dashboard.input_lembur')

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

                    @if($isSuperAdmin || $isAdminStationery)

                    <div id="tab-analitik-stationery" class="tab-content hidden flex-1 flex-col gap-6">
                        <div class="grid grid-cols-2 lg:grid-cols-5 gap-5 shrink-0">
                            <div id="stat-card-1" class="glass-card p-5 border-l-4 border-blue-500 bg-blue-50/30">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis Barang</p>
                                <h3 class="text-2xl font-extrabold text-slate-700 mt-1" id="stat-total-barang">0</h3>
                            </div>
                            <div id="stat-card-2" class="glass-card p-5 border-l-4 border-indigo-500 bg-indigo-50/30">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Barang Masuk</p>
                                <h3 class="text-2xl font-extrabold text-indigo-600 mt-1" id="stat-total-in">0</h3>
                            </div>
                            <div id="stat-card-3" class="glass-card p-5 border-l-4 border-red-500 bg-red-50/30">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Barang Keluar</p>
                                <h3 class="text-2xl font-extrabold text-red-600 mt-1" id="stat-total-out">0</h3>
                            </div>
                            <div id="stat-card-4" class="glass-card p-5 border-l-4 border-green-500 bg-green-50/30">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest" id="title-aset">Nilai Aset Stok</p>
                                <h3 class="text-2xl font-extrabold text-green-600 mt-1 truncate" id="stat-total-aset">Rp 0</h3>
                            </div>
                            <div id="stat-card-5" class="glass-card p-5 border-l-4 border-yellow-500 bg-yellow-50/50 shadow-sm">
                                <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest">Stok Habis (0)</p>
                                <h3 class="text-2xl font-extrabold text-yellow-600 mt-1" id="stat-stok-habis">0</h3>
                            </div>
                        </div>

                        <div id="chart-card" class="glass-card p-7 flex-1 flex flex-col mt-2 h-auto pb-10">
                            <div class="flex flex-wrap justify-between items-center mb-6 gap-4 border-b border-slate-100 pb-5">
                                <div>
                                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest"><i class="fas fa-chart-pie text-indofood-blue mr-2"></i>Dashboard Analitik Top 5</h3>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Visualisasi Top 5 Kuantitas & Frekuensi</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                    <div class="flex flex-col">
                                        <label class="text-[9px] font-black text-slate-400 uppercase mb-1">Filter Hari</label>
                                        <input type="date" id="chartFilterHari" onchange="updateStationeryChart('hari')" class="form-input-modern text-xs py-2 px-3.5 w-36 shadow-sm cursor-pointer">
                                    </div>

                                    <div class="flex flex-col">
                                        <label class="text-[9px] font-black text-slate-400 uppercase mb-1">Filter Bulan</label>
                                        <select id="chartFilterBulan" onchange="updateStationeryChart('bulan')" class="form-input-modern text-xs py-2 px-3.5 w-36 shadow-sm cursor-pointer">
                                            <option value="">Semua Bulan</option>
                                            <option value="01">Januari</option>
                                            <option value="02">Februari</option>
                                            <option value="03">Maret</option>
                                            <option value="04">April</option>
                                            <option value="05">Mei</option>
                                            <option value="06">Juni</option>
                                            <option value="07">Juli</option>
                                            <option value="08">Agustus</option>
                                            <option value="09">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>

                                    <div class="flex flex-col">
                                        <label class="text-[9px] font-black text-slate-400 uppercase mb-1">Filter Barang</label>
                                        <select id="chartFilterBarang" onchange="updateStationeryChart('barang')" class="form-input-modern text-xs py-2 px-3.5 w-56 shadow-sm cursor-pointer">
                                            <option value="">Semua Barang (Top 5)</option>
                                            @foreach(\App\Models\Stationery::orderBy('nama_barang')->get() as $item)
                                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama_barang }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex flex-col justify-end h-full">
                                        <button onclick="downloadChart()" class="bg-blue-600 text-white px-5 py-2.5 mt-[15px] rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-blue-700 transition shadow-md flex items-center">
                                            <i class="fas fa-download mr-2"></i> PNG
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 w-full shrink-0 mt-2">
                                <div id="boxChartKuantitas" class="w-full bg-white rounded-3xl border border-blue-100 shadow-md p-6 flex flex-col transition-all duration-300">
                                    <h4 class="text-sm font-extrabold text-blue-700 uppercase tracking-widest mb-4 border-b-2 border-blue-50 pb-3 text-center">
                                        1. Grafik Kuantitas (Quantity)
                                    </h4>
                                    <div id="wrapperQty" class="relative w-full flex items-center justify-center" style="height: 380px;">
                                        <canvas id="stationeryOutChart" class="w-full h-full"></canvas>
                                    </div>
                                </div>

                                <div id="boxChartFrekuensi" class="w-full bg-white rounded-3xl border border-indigo-100 shadow-md p-6 flex flex-col transition-all duration-300">
                                    <h4 class="text-sm font-extrabold text-indigo-700 uppercase tracking-widest mb-4 border-b-2 border-indigo-50 pb-3 text-center">
                                        2. Grafik Frekuensi (Frequency)
                                    </h4>
                                    <div id="wrapperFreq" class="relative w-full flex items-center justify-center" style="height: 380px;">
                                        <canvas id="stationeryFreqChart" class="w-full h-full"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 w-full shrink-0 mt-8 pt-6 border-t border-slate-200">
                                <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100">
                                    <h4 class="text-xs font-extrabold text-blue-700 uppercase tracking-widest mb-4 flex items-center">
                                        <i class="fas fa-trophy mr-2 text-yellow-500 text-base"></i> Rincian Kuantitas (Top 5)
                                    </h4>
                                    <ul id="listTopKuantitas" class="space-y-2">
                                        </ul>
                                </div>

                                <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100">
                                    <h4 class="text-xs font-extrabold text-indigo-700 uppercase tracking-widest mb-4 flex items-center">
                                        <i class="fas fa-medal mr-2 text-yellow-500 text-base"></i> Rincian Frekuensi (Top 5)
                                    </h4>
                                    <ul id="listTopFrekuensi" class="space-y-2">
                                        </ul>
                                </div>
                            </div>

                        </div>
                    </div>

                    @include('dashboard.stationery')

                    {{-- TAB CLOSING PERIODE (STATIONERY) --}}
                    @if($isSuperAdmin || $isAdminStationery)
                        <div id="tab-closing-stationery" class="tab-content hidden flex-1 flex-col gap-6 min-h-0">
                            @include('Dashboard.closing_periode')
                        </div>
                    @endif

                    {{-- TAB TRANSAKSI BARANG (IN/OUT) --}}
                    <div id="tab-transaksi-stationery" class="tab-content hidden flex-1 flex-col gap-6">
                        <div id="trx-header" class="flex flex-wrap justify-between items-center glass-card p-6 gap-4 shrink-0">
                            <div>
                                <h3 class="text-sm font-black text-slate-600 uppercase tracking-widest">Riwayat Transaksi Barang</h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Pusat pencatatan barang masuk ke gudang dan pemakaian keluar</p>
                            </div>
                            <div class="flex gap-3">
                                <button onclick="document.getElementById('modalBarangMasuk').classList.remove('hidden')" class="bg-green-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-green-700 shadow-md transition">
                                    <i class="fas fa-box mr-2"></i> Catat Restock (Masuk)
                                </button>
                                <button onclick="document.getElementById('modalBarangKeluar').classList.remove('hidden')" class="bg-red-500 text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-red-600 shadow-md transition">
                                    <i class="fas fa-box-open mr-2"></i> Catat Pemakaian (Keluar)
                                </button>
                            </div>
                        </div>

                        <div id="trx-table-card" class="glass-card flex-1 overflow-hidden flex flex-col p-6">
                            @php
                                $transaksis = \App\Models\StationeryTransaction::with('stationery')->orderBy('created_at', 'desc')->get();
                            @endphp
                            <div class="overflow-y-auto">
                                <table class="w-full text-left border-collapse table-bordered whitespace-nowrap rounded-lg overflow-hidden">
                                    <thead class="bg-slate-100 text-slate-600 text-[10px] uppercase tracking-wider sticky top-0">
                                        <tr>
                                            <th class="p-3.5 text-center w-12">No</th>
                                            <th class="p-3.5 text-center">Tgl Input</th>
                                            <th class="p-3.5 text-center">Jenis</th>
                                            <th class="p-3.5">Kode & Nama Barang</th>
                                            <th class="p-3.5 text-center">Jumlah</th>
                                            <th class="p-3.5">Detail (Dept/Vendor/Ket)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transaksis as $index => $trx)
                                        <tr class="text-xs font-bold text-slate-700 border-b">
                                            <td class="p-3.5 text-center">{{ $index + 1 }}</td>
                                            <td class="p-3.5 text-center">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}</td>
                                            <td class="p-3.5 text-center">
                                                @if($trx->type == 'masuk')
                                                    <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-[9px] uppercase font-black shadow-sm border border-emerald-200"><i class="fas fa-arrow-down mr-1"></i> Masuk</span>
                                                @else
                                                    <span class="bg-rose-100 text-rose-700 px-2.5 py-1 rounded-full text-[9px] uppercase font-black shadow-sm border border-rose-200"><i class="fas fa-arrow-up mr-1"></i> Keluar</span>
                                                @endif
                                            </td>
                                            <td class="p-3.5"><span class="text-blue-600">{{ $trx->stationery->kode ?? 'N/A' }}</span> - {{ $trx->stationery->nama_barang ?? 'Barang Dihapus' }}</td>
                                            <td class="p-3.5 text-center text-sm font-black {{ $trx->type == 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $trx->jumlah }}</td>
                                            <td class="p-3.5 text-[10px] text-slate-500 whitespace-normal min-w-[200px]">
                                                @if($trx->type == 'masuk')
                                                    Vendor: <span class="font-bold">{{ $trx->vendor ?? '-' }}</span> | Dok: {{ $trx->no_dokumen ?? '-' }}
                                                @else
                                                    Dept: <span class="font-bold">{{ $trx->dept ?? '-' }}</span> | FPB: {{ $trx->no_fpb ?? '-' }} | Ket: {{ $trx->keterangan ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="p-8 text-center text-slate-400 italic text-sm bg-white">Belum ada riwayat transaksi.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TAB LAPORAN BARANG --}}
                    <div id="tab-laporan-stationery" class="tab-content hidden flex-1 flex-col gap-6">

                        {{-- MODUL 1: ORDER BARANG --}}
                        <div class="glass-card flex-1 overflow-hidden flex flex-col border-t-4 border-blue-600 mb-6 shadow-md rounded-2xl">
                            <div class="px-7 py-5 border-b border-slate-100 bg-white flex flex-wrap justify-between items-center gap-4">
                                <div>
                                    <h3 class="text-sm font-black text-blue-800 uppercase tracking-widest">1️⃣ Order Barang (Barang Masuk)</h3>
                                    <p class="text-[10px] font-bold text-slate-500 mt-1 tracking-wide">Tabel pantauan stok dan rencana reorder barang</p>
                                </div>
                                <a href="{{ route('stationery.export.order') }}" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-700 transition shadow-md flex items-center">
                                    <i class="fas fa-file-excel mr-2 text-sm"></i> Export Order Barang
                                </a>
                            </div>

                            <div class="overflow-x-auto p-5 bg-slate-50">
                                <table class="w-full text-left border-collapse table-bordered whitespace-nowrap bg-white shadow-sm rounded-xl overflow-hidden">
                                    <thead class="bg-blue-50 text-blue-800 text-[10px] uppercase tracking-wider font-black border-b-2 border-blue-200">
                                        <tr>
                                            <th class="p-3.5 text-center">Barang</th>
                                            <th class="p-3.5 text-center">Stock Now</th>
                                            <th class="p-3.5">Rata-rata penggunaan / bulan & siapa penggunanya</th>
                                            <th class="p-3.5 text-center">Kapan terakhir order</th>
                                            <th class="p-3.5 text-center">Kebutuhan / Reorder</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orderBarang as $item)
                                        <tr class="text-xs font-bold text-slate-700 hover:bg-blue-50/50 transition border-b border-slate-100">
                                            <td class="p-3.5 text-blue-700">{{ $item->kode }} - {{ $item->nama_barang }}</td>
                                            <td class="p-3.5 text-center text-lg {{ $item->total_stok <= $item->min_stok ? 'text-red-600' : 'text-emerald-600' }}">{{ $item->total_stok }}</td>

                                            <td class="p-3.5 text-slate-600">
                                                <span class="text-orange-600 font-black">{{ $item->rata_penggunaan }} {{ $item->satuan }}</span> / bulan
                                                <span class="text-[10px] text-slate-400 ml-1">(Berdasarkan data 30 hari terakhir)</span>
                                                <br>
                                                <span class="inline-block mt-1.5 text-[10px] font-black text-blue-700 bg-blue-100 px-3 py-1.5 rounded-full shadow-sm border border-blue-200 tracking-wide uppercase">
                                                    <i class="fas fa-user-tag mr-1"></i> Pemakai: {{ $item->siapa_pengguna }}
                                                </span>
                                            </td>

                                            <td class="p-3.5 text-center text-slate-500">
                                                {{ $item->terakhir_order ? \Carbon\Carbon::parse($item->terakhir_order)->format('d M Y') : 'Belum pernah order' }}
                                            </td>
                                            <td class="p-3.5 text-center">
                                                @if($item->status_reorder == 'Perlu order')
                                                    <span class="bg-rose-100 text-rose-700 px-3.5 py-2 rounded-full text-[9px] uppercase font-black shadow-sm border border-rose-200 animate-pulse"><i class="fas fa-exclamation-triangle mr-1"></i> Perlu Order</span>
                                                @else
                                                    <span class="bg-emerald-100 text-emerald-700 px-3.5 py-2 rounded-full text-[9px] uppercase font-black shadow-sm border border-emerald-200"><i class="fas fa-check mr-1"></i> Aman</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- MODUL 2: PEMAKAIAN BARANG --}}
                        <div class="glass-card flex-1 overflow-hidden flex flex-col border-t-4 border-rose-600 shadow-md rounded-2xl">
                            <div class="px-7 py-5 border-b border-slate-100 bg-white flex flex-wrap justify-between items-center gap-4">
                                <div>
                                    <h3 class="text-sm font-black text-rose-800 uppercase tracking-widest">2️⃣ Pemakaian Barang (Barang Keluar)</h3>
                                    <p class="text-[10px] font-bold text-slate-500 mt-1 tracking-wide">Tabel pencatatan riwayat pengambilan / pemakaian material</p>
                                </div>
                                <a href="{{ route('stationery.export.pemakaian') }}" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-700 transition shadow-md flex items-center">
                                    <i class="fas fa-file-excel mr-2 text-sm"></i> Export Pemakaian
                                </a>
                            </div>

                            <div class="overflow-x-auto p-5 bg-slate-50">
                                <table class="w-full text-left border-collapse table-bordered whitespace-nowrap bg-white shadow-sm rounded-xl overflow-hidden">
                                    <thead class="bg-rose-50 text-rose-800 text-[10px] uppercase tracking-wider font-black border-b-2 border-rose-200">
                                        <tr>
                                            <th class="p-3.5 text-center">No PR (FPB)</th>
                                            <th class="p-3.5 text-center">Tanggal PR</th>
                                            <th class="p-3.5 text-center">Kode Barang</th>
                                            <th class="p-3.5">Nama Material & Pemakai</th>
                                            <th class="p-3.5 text-center">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pemakaianBarang as $trx)
                                        <tr class="text-xs font-bold text-slate-700 hover:bg-rose-50/50 transition border-b border-slate-100">
                                            <td class="p-3.5 text-center font-black text-slate-600">{{ $trx->no_fpb ?? 'PR-' . $trx->id }}</td>
                                            <td class="p-3.5 text-center text-slate-500">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d-m-Y') }}</td>
                                            <td class="p-3.5 text-center text-blue-600">{{ $trx->kode ?? '-' }}</td>

                                            <td class="p-3.5">
                                                {{ $trx->stationery->nama_barang ?? '-' }}
                                                <span class="block text-[10px] text-slate-500 font-normal mt-1">
                                                    <i class="fas fa-user-tag text-rose-400 mr-1"></i> Pemakai: <span class="font-bold text-slate-700">{{ $trx->dept ?? '-' }}</span>
                                                </span>
                                            </td>

                                            <td class="p-3.5 text-center font-black text-rose-600 text-sm">{{ $trx->jumlah }} <span class="text-xs text-slate-500">{{ $trx->stationery->satuan ?? '' }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @endif

                    @if($isSuperAdmin)
                        @include('dashboard.users')
                    @endif

                    @if($isSuperAdmin || $isAdminAbsensi || $isAdminPenilaian)
                        @include('Dashboard.aspek_penilaian')
                        @include('Dashboard.laporan_penilaian')
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
            const isMobile = window.innerWidth <= 1024;

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
        }
        setInterval(updateWitaClock, 1000);

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

            if(id === 'kelola-user' && !isSuperAdmin) {
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

            if (window.innerWidth <= 1024) {
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
            }

            if(window.innerWidth <= 1024) {
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
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.children[0].classList.remove('scale-95');
        }

        function closeEditProfileModal() {
            const modal = document.getElementById('editProfileModal');
            modal.classList.add('opacity-0', 'pointer-events-none');
            modal.children[0].classList.add('scale-95');
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

            const validTabs = ['ringkasan', 'form-absen', 'karyawan', 'master-shift', 'jadwal-karyawan', 'input', 'input-lembur', 'rekap', 'lembur', 'pengajuan-spl', 'rekap-spl', 'kelola-user', 'stationery', 'analitik-stationery', 'transaksi-stationery', 'laporan-stationery', 'pengadaan-stationery', 'closing-stationery', 'aspek-penilaian', 'laporan-penilaian'];

            const storedTab = (() => {
                try {
                    return localStorage.getItem('lastDashboardTab');
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
                } else if (!h && storedTab && intTabs.includes(storedTab)) {
                    switchTab(storedTab);
                } else {
                    switchTab('input');
                }
            } else if (isAdminOutsourcing) {
                const outTabs = ['rekap-spl', 'lembur', 'input', 'rekap'];
                if (h && outTabs.includes(h)) {
                    switchTab(h);
                } else if (!h && storedTab && outTabs.includes(storedTab)) {
                    switchTab(storedTab);
                } else {
                    switchTab('rekap-spl');
                }
            } else if (h && validTabs.includes(h)) {
                switchTab(h);
            } else if (!h && storedTab && validTabs.includes(storedTab)) {
                switchTab(storedTab);
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
                // 1. PROJECT ANALYTICS BAR CHART (DONEZO CAPSULE & STRIPE STYLE)
                // ==============================================================
                try {
                    let barChartCanvas = document.getElementById('barChart');
                    if (barChartCanvas) {
                        let ctxBar = barChartCanvas.getContext('2d');
                        
                        // Active solid/gradients
                        let gradHadirActive = ctxBar.createLinearGradient(0, 0, 0, 300);
                        gradHadirActive.addColorStop(0, '#0284c7');
                        gradHadirActive.addColorStop(1, '#004a99');

                        let gradHadirDark = ctxBar.createLinearGradient(0, 0, 0, 300);
                        gradHadirDark.addColorStop(0, '#0369a1');
                        gradHadirDark.addColorStop(1, '#002b5c');

                        let patternStriped = createStripePattern('#cbd5e1', '#f8fafc', 2.5, 14);

                        const rawLabels = @json($trendLabels ?? []);
                        const rawHadir = @json($trendHadir ?? []);
                        
                        // Day initials matching Donezo (S, M, T, W, T, F, S)
                        const defaultLetters = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                        const dayLetters = rawLabels.length === 7 
                            ? rawLabels.map((lbl, idx) => defaultLetters[idx % 7])
                            : defaultLetters;

                        const maxHadirVal = Math.max(...rawHadir, 0);
                        let peakIdx = -1;
                        let highest = 0;
                        rawHadir.forEach((val, idx) => {
                            if (val > highest) {
                                highest = val;
                                peakIdx = idx;
                            }
                        });
                        if (peakIdx === -1) peakIdx = 2; // Default Tuesday (Donezo reference)

                        const totalStaff = {{ ($countHadir ?? 0) + ($countLembur ?? 0) + ($countIzin ?? 0) + ($countAlpa ?? 0) }};
                        const actualPeakVal = rawHadir[peakIdx] || 0;
                        const pctText = totalStaff > 0 && actualPeakVal > 0 
                            ? Math.round((actualPeakVal / totalStaff) * 100) + '%' 
                            : '74%';

                        // Cadence of bar heights matching Donezo's natural aesthetics (between 60% and 95%)
                        const donezoStripedCadence = [65, 78, 74, 94, 85, 62, 75];

                        const displayValues = [];
                        const barColors = [];
                        const borderColors = [];
                        const borderWidths = [];

                        for (let idx = 0; idx < 7; idx++) {
                            const val = rawHadir[idx] !== undefined ? rawHadir[idx] : 0;
                            if (val > 0) {
                                // Scale active attendance height dynamically between 68 and 96
                                const scaledH = maxHadirVal > 0 
                                    ? 68 + Math.round((val / maxHadirVal) * 26) 
                                    : 78;
                                displayValues.push(scaledH);
                                if (idx === peakIdx) {
                                    barColors.push(gradHadirActive);
                                } else {
                                    barColors.push(gradHadirDark);
                                }
                                borderColors.push('transparent');
                                borderWidths.push(0);
                            } else {
                                // Inactive/Weekend/0-attendance days show Donezo striped capsule pills
                                displayValues.push(donezoStripedCadence[idx % 7]);
                                barColors.push(patternStriped);
                                borderColors.push('#cbd5e1');
                                borderWidths.push(1.5);
                            }
                        }

                        // Plugin for floating pill badge above peak bar (Donezo '74%' badge)
                        const donezoFloatingBadgePlugin = {
                            id: 'donezoFloatingBadge',
                            afterDatasetsDraw(chart) {
                                const meta = chart.getDatasetMeta(0);
                                if (!meta || !meta.data || !meta.data.length) return;
                                if (peakIdx < 0 || peakIdx >= meta.data.length) return;
                                
                                const bar = meta.data[peakIdx];
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
                                ctx.fillText(pctText, x, badgeY + (badgeH / 2));

                                ctx.restore();
                            }
                        };

                        ringkasanBarChart = new Chart(barChartCanvas, {
                            type: 'bar',
                            data: {
                                labels: dayLetters,
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
                                                return rawLabels[idx] || `Hari ${dayLetters[idx]}`;
                                            },
                                            label: function(context) {
                                                const idx = context.dataIndex;
                                                const val = rawHadir[idx] || 0;
                                                if (val > 0) {
                                                    const rate = totalStaff > 0 ? Math.round((val / totalStaff) * 100) : 0;
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
                        
                        let patternPending = createStripePattern('#94a3b8', '#f8fafc', 2.5, 14);

                        let gradCompleted = ctxGauge.createLinearGradient(0, 0, 0, 240);
                        gradCompleted.addColorStop(0, '#0284c7');
                        gradCompleted.addColorStop(1, '#004a99');

                        let colorInProgress = '#0f172a';
                        let colorIzin = '#f59e0b';

                        const cHadir = {{ $countHadir ?? 0 }};
                        const cLembur = {{ $countLembur ?? 0 }};
                        const cIzin = {{ $countIzin ?? 0 }};
                        const cAlpa = {{ $countAlpa ?? 0 }};

                        const totalReal = cHadir + cLembur + cIzin + cAlpa;
                        const gaugeData = totalReal > 0 
                            ? [cHadir, cLembur, cIzin, Math.max(cAlpa, 1)] 
                            : [41, 15, 5, 39];

                        const gaugeColors = [gradCompleted, colorInProgress, colorIzin, patternPending];

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

                                const pct = totalReal > 0 ? Math.round((cHadir / totalReal) * 100) : 41;

                                // Large bold percentage
                                ctx.font = "800 36px 'Plus Jakarta Sans', sans-serif";
                                ctx.fillStyle = "#0f172a";
                                ctx.fillText(pct + "%", centerX, midY - 10);

                                // Subtitle
                                ctx.font = "700 11px 'Plus Jakarta Sans', sans-serif";
                                ctx.fillStyle = "#64748b";
                                ctx.fillText("Presensi Selesai", centerX, midY + 16);

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
                                                const val = totalReal > 0 ? [cHadir, cLembur, cIzin, cAlpa][context.dataIndex] : context.raw;
                                                const p = totalReal > 0 ? Math.round((val / totalReal) * 100) : 25;
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

        window.updateStationeryChart = function(trigger) {
            let monthFilter = document.getElementById('chartFilterBulan');
            let dayFilter = document.getElementById('chartFilterHari');
            let itemFilter = document.getElementById('chartFilterBarang');

            if (trigger === 'bulan' && monthFilter.value !== '') dayFilter.value = '';
            else if (trigger === 'hari' && dayFilter.value !== '') monthFilter.value = '';

            let month = monthFilter ? monthFilter.value : '';
            let day = dayFilter ? dayFilter.value : '';

            let rawItemValue = itemFilter ? itemFilter.value : '';
            let isSpecificItem = !isNaN(rawItemValue) && rawItemValue.trim() !== '';

            let sortMode = 'top5';

            let priceMap = {};
            masterData.forEach(m => {
                let rawHarga = String(m.harga_unit || m.harga_satuan || m.harga || '0');
                priceMap[m.id] = parseFloat(rawHarga.replace(/[^0-9]/g, '')) || 0;
            });

            let totalIn = 0, totalOut = 0, nominalPemakaian = 0;
            let aggregatedOutQty = {};
            let aggregatedOutFreq = {};
            let activeItems = new Set();

            if (!isSpecificItem) {
                masterData.forEach(m => {
                    aggregatedOutQty[m.nama_barang] = { qty: 0, satuan: m.satuan || 'Unit' };
                    aggregatedOutFreq[m.nama_barang] = 0;
                });
            }

            let filteredTrx = rawTrxData.filter(trx => {
                if (!trx.tanggal) return false;
                let dateOnly = String(trx.tanggal).split(' ')[0] || '';
                let trxMonth = dateOnly.includes('-') ? dateOnly.split('-')[1] : '';
                let matchMonth = (month === '' || trxMonth === month);
                let matchDay = (day === '' || dateOnly === day);
                return matchMonth && matchDay;
            });

            filteredTrx.forEach(trx => {
                activeItems.add(trx.stationery_id);
                let qty = parseFloat(trx.jumlah) || 0;

                if(trx.type && trx.type.toLowerCase() === 'masuk') {
                    totalIn += qty;
                } else if (trx.type && trx.type.toLowerCase() === 'keluar') {
                    totalOut += qty;
                    nominalPemakaian += (qty * (priceMap[trx.stationery_id] || 0));

                    if(!isSpecificItem || trx.stationery_id == rawItemValue) {
                        let name = trx.stationery ? trx.stationery.nama_barang : 'Barang Dihapus';
                        let satuan = trx.stationery ? trx.stationery.satuan : 'Unit';
                        if(aggregatedOutQty[name] === undefined) {
                            aggregatedOutQty[name] = { qty: 0, satuan: satuan };
                            aggregatedOutFreq[name] = 0;
                        }
                        aggregatedOutQty[name].qty += qty;
                        aggregatedOutFreq[name] += 1;
                    }
                }
            });

            let totalAsetGudang = 0, stokHabis = 0;
            masterData.forEach(m => {
                let stok = parseFloat(m.total_stok || m.stok || 0);
                totalAsetGudang += (stok * (priceMap[m.id] || 0));
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

            let sortedDataQty = Object.keys(aggregatedOutQty).map(key => ({ name: key, qty: aggregatedOutQty[key].qty, satuan: aggregatedOutQty[key].satuan }));
            if (isSpecificItem) sortedDataQty.sort((a, b) => b.qty - a.qty);
            else sortedDataQty = sortedDataQty.filter(d => d.qty > 0).sort((a, b) => b.qty - a.qty).slice(0, 5);

            let sortedDataFreq = Object.keys(aggregatedOutFreq).map(key => ({ name: key, freq: aggregatedOutFreq[key] }));
            if (isSpecificItem) sortedDataFreq.sort((a, b) => b.freq - a.freq);
            else sortedDataFreq = sortedDataFreq.filter(d => d.freq > 0).sort((a, b) => b.freq - a.freq).slice(0, 5);

            let totalQtySum = sortedDataQty.reduce((sum, d) => sum + d.qty, 0);
            if (sortedDataQty.length === 0 || totalQtySum === 0) {
                sortedDataQty = [{ name: 'Belum ada data', qty: 1, satuan: '' }];
            }

            let totalFreqSum = sortedDataFreq.reduce((sum, d) => sum + d.freq, 0);
            if (sortedDataFreq.length === 0 || totalFreqSum === 0) {
                sortedDataFreq = [{ name: 'Belum ada data', freq: 1 }];
            }

            const paletteQty = ['#38bdf8', '#fb7185', '#fbbf24', '#34d399', '#a78bfa', '#60a5fa', '#f472b6', '#fcd34d'];
            const paletteFreq = ['#818cf8', '#34d399', '#f87171', '#fbbf24', '#c084fc', '#38bdf8', '#fb7185', '#a3e635'];

            const listQtyEl = document.getElementById('listTopKuantitas');
            const listFreqEl = document.getElementById('listTopFrekuensi');

            if (listQtyEl) {
                listQtyEl.innerHTML = '';
                sortedDataQty.forEach((item, idx) => {
                    if (item.name !== 'Belum ada data') {
                        let color = paletteQty[idx % paletteQty.length];
                        listQtyEl.innerHTML += `
                            <li class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100 hover:shadow-md transition">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[11px] font-black shadow-sm" style="background-color: ${color}">${idx + 1}</span>
                                    <span class="font-extrabold text-slate-700 text-xs">${item.name}</span>
                                </div>
                                <span class="font-black text-blue-700 text-sm bg-blue-100 px-3 py-1 rounded-lg">${item.qty} <span class="text-[9px] text-blue-500 font-bold uppercase tracking-widest">${item.satuan}</span></span>
                            </li>
                        `;
                    } else {
                        listQtyEl.innerHTML = `<li class="text-center text-slate-400 text-xs py-4 font-medium italic">Belum ada data transaksi</li>`;
                    }
                });
            }

            if (listFreqEl) {
                listFreqEl.innerHTML = '';
                sortedDataFreq.forEach((item, idx) => {
                    if (item.name !== 'Belum ada data') {
                        let color = paletteFreq[idx % paletteFreq.length];
                        listFreqEl.innerHTML += `
                            <li class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-100 hover:shadow-md transition">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[11px] font-black shadow-sm" style="background-color: ${color}">${idx + 1}</span>
                                    <span class="font-extrabold text-slate-700 text-xs">${item.name}</span>
                                </div>
                                <span class="font-black text-indigo-700 text-sm bg-indigo-100 px-3 py-1 rounded-lg">${item.freq} <span class="text-[9px] text-indigo-500 font-bold uppercase tracking-widest">Kali</span></span>
                            </li>
                        `;
                    } else {
                        listFreqEl.innerHTML = `<li class="text-center text-slate-400 text-xs py-4 font-medium italic">Belum ada data transaksi</li>`;
                    }
                });
            }

            if(statChartInstance) { statChartInstance.destroy(); statChartInstance = null; }
            if(statFreqChartInstance) { statFreqChartInstance.destroy(); statFreqChartInstance = null; }

            const wrapperQty = document.getElementById('wrapperQty');
            const wrapperFreq = document.getElementById('wrapperFreq');

            if(!wrapperQty || !wrapperFreq) return;

            wrapperQty.innerHTML = '<canvas id="stationeryOutChart" class="w-full h-full"></canvas>';
            wrapperFreq.innerHTML = '<canvas id="stationeryFreqChart" class="w-full h-full"></canvas>';

            const canvasQty = document.getElementById('stationeryOutChart').getContext('2d');
            const canvasFreq = document.getElementById('stationeryFreqChart').getContext('2d');

            try {
                statChartInstance = new Chart(canvasQty, {
                    type: 'pie',
                    data: {
                        labels: sortedDataQty.map(d => d.name),
                        datasets: [{
                            data: sortedDataQty.map(d => d.qty),
                            backgroundColor: sortedDataQty.map((_, i) => sortedDataQty[0].name === 'Belum ada data' ? '#e2e8f0' : paletteQty[i % paletteQty.length]),
                            borderWidth: 2, borderColor: '#ffffff', hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, animation: { duration: 800 },
                        layout: { padding: 20 },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, font: { weight: 'bold', size: 11, family: "'Plus Jakarta Sans', sans-serif" }, padding: 15 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.96)', titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12, weight: '500' },
                                padding: 10, cornerRadius: 8, displayColors: true, boxPadding: 5,
                                callbacks: { label: (context) => context.label === 'Belum ada data' ? ' 0 Unit' : ` ${context.raw} Unit Terpakai` }
                            },
                            datalabels: {
                                display: function(context) {
                                    return context.dataset.data[context.dataIndex] > 0;
                                },
                                color: '#ffffff',
                                align: 'center',
                                anchor: 'center',
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 'bold' },
                                textAlign: 'center',
                                textStrokeColor: 'rgba(0,0,0,0.4)',
                                textStrokeWidth: 3,
                                formatter: (value, context) => {
                                    if (context.chart.data.labels[context.dataIndex] === 'Belum ada data') return '';
                                    let dataArr = context.chart.data.datasets[0].data;
                                    let sum = dataArr.reduce((a, b) => a + b, 0);
                                    let percentage = (value * 100 / sum).toFixed(1) + "%";
                                    let satuan = sortedDataQty[context.dataIndex].satuan || '';
                                    return value + " " + satuan + "\n(" + percentage + ")";
                                }
                            }
                        }
                    }
                });
            } catch(e) { console.error("Error drawing Qty Chart:", e); }

            try {
                statFreqChartInstance = new Chart(canvasFreq, {
                    type: 'pie',
                    data: {
                        labels: sortedDataFreq.map(d => d.name),
                        datasets: [{
                            data: sortedDataFreq.map(d => d.freq),
                            backgroundColor: sortedDataFreq.map((_, i) => sortedDataFreq[0].name === 'Belum ada data' ? '#e2e8f0' : paletteFreq[i % paletteFreq.length]),
                            borderWidth: 2, borderColor: '#ffffff', hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, animation: { duration: 800 },
                        layout: { padding: 20 },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, font: { weight: 'bold', size: 11, family: "'Plus Jakarta Sans', sans-serif" }, padding: 15 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.96)', titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12, weight: '500' },
                                padding: 10, cornerRadius: 8, displayColors: true, boxPadding: 5,
                                callbacks: { label: (context) => context.label === 'Belum ada data' ? ' 0 Kali' : ` ${context.raw} Kali Pengambilan` }
                            },
                            datalabels: {
                                display: function(context) {
                                    return context.dataset.data[context.dataIndex] > 0;
                                },
                                color: '#ffffff',
                                align: 'center',
                                anchor: 'center',
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 'bold' },
                                textAlign: 'center',
                                textStrokeColor: 'rgba(0,0,0,0.4)',
                                textStrokeWidth: 3,
                                formatter: (value, context) => {
                                    if (context.chart.data.labels[context.dataIndex] === 'Belum ada data') return '';
                                    let dataArr = context.chart.data.datasets[0].data;
                                    let sum = dataArr.reduce((a, b) => a + b, 0);
                                    let percentage = (value * 100 / sum).toFixed(1) + "%";
                                    return value + " Kali\n(" + percentage + ")";
                                }
                            }
                        }
                    }
                });
            } catch(e) { console.error("Error drawing Freq Chart:", e); }
        };
    </script>
</x-app-layout>
