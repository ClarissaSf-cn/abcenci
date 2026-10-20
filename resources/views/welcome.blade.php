<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        /* Layout Dasar Desktop Windows */
        nav.bg-white.border-b.border-gray-100 { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fa; margin: 0; overflow: hidden; height: 100vh; }

        .bg-indofood-blue { background: #004a99; }
        .text-indofood-blue { color: #004a99; }

        /* Kartu Glassmorphism Modern */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1rem;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
        }

        /* Sidebar Navigasi */
        .side-nav-btn {
            display: flex; align-items: center; width: 100%; padding: 0.85rem 1.25rem; margin-bottom: 0.4rem;
            font-size: 0.8rem; font-weight: 700; color: rgba(255, 255, 255, 0.6); border-radius: 0.75rem;
            transition: 0.3s; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .side-nav-btn i { width: 22px; margin-right: 12px; font-size: 1.1rem; opacity: 0.8; }
        .side-nav-btn:hover { background: rgba(255, 255, 255, 0.08); color: white; }
        .side-nav-btn.active { background: white; color: #004a99; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }

        /* Animasi Transisi Sub-Menu */
        #sub-menu-container { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; }
        .menu-collapsed { max-height: 0; opacity: 0; transform: translateY(-10px); pointer-events: none; }
        .menu-expanded { max-height: 500px; opacity: 1; transform: translateY(0); }

        .form-input-modern {
            width: 100%; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 0.5rem;
            padding: 0.6rem 0.8rem; font-size: 0.85rem; color:#1e293b; transition: 0.2s; font-weight: 600;
        }
        .form-input-modern:focus { border-color: #004a99; outline: none; box-shadow: 0 0 0 3px rgba(0, 74, 153, 0.1); }

        .chart-container { position: relative; height: 260px; width: 100%; }

        /* Scrollbar Dinamis & Elegan */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 10px; border: 2px solid #f1f5f9; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        .modal { transition: opacity 0.2s ease; position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; z-index: 100; }
        .modal-overlay { position: absolute; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(5px); }

        /* CSS KHUSUS TABEL */
        .table-bordered th, .table-bordered td { border: 1px solid #e2e8f0; }
        .table-bordered thead th { border-bottom: 2px solid #94a3b8; }

        /* 4 STICKY COLUMNS */
        .sticky-no { position: sticky; left: 0; min-width: 40px; background: #004a99; z-index: 20; border-right: 1px solid rgba(255,255,255,0.2) !important; }
        .sticky-nik { position: sticky; left: 40px; min-width: 80px; background: #004a99; z-index: 20; border-right: 1px solid rgba(255,255,255,0.2) !important; }
        .sticky-nama { position: sticky; left: 120px; min-width: 180px; background: #004a99; z-index: 20; border-right: 1px solid rgba(255,255,255,0.2) !important; }
        .sticky-aksi { position: sticky; left: 300px; min-width: 80px; background: #004a99; z-index: 20; border-right: 2px solid rgba(255,255,255,0.2) !important; }

        .body-sticky-no { position: sticky; left: 0; background: white; z-index: 10; border-right: 1px solid #e2e8f0 !important; }
        .body-sticky-nik { position: sticky; left: 40px; background: white; z-index: 10; border-right: 1px solid #e2e8f0 !important; }
        .body-sticky-nama { position: sticky; left: 120px; background: white; z-index: 10; border-right: 1px solid #e2e8f0 !important; }
        .body-sticky-aksi { position: sticky; left: 300px; background: white; z-index: 10; border-right: 2px solid #e2e8f0 !important; text-align: center; }
    </style>

    <div class="flex h-screen w-full overflow-hidden">
        <aside class="w-72 bg-indofood-blue text-white flex flex-col shadow-2xl z-30 shrink-0">
            <div class="p-8 text-center">
                <span class="text-3xl font-extrabold italic tracking-tighter uppercase">INDO<span class="text-red-500">FOOD</span></span>
                <p class="text-[9px] font-bold text-blue-300 tracking-[0.2em] mt-1 uppercase leading-none">CBP Success Makmur</p>
            </div>
            <div class="px-8 mb-6"><div class="border-b border-white border-opacity-10"></div></div>
            <nav class="flex-grow px-5 overflow-y-auto">
                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-4 px-2 opacity-60">Menu Navigasi</p>
                <button onclick="toggleMenu()" class="side-nav-btn mb-2 group flex justify-between items-center cursor-pointer">
                    <div class="flex items-center"><i class="fas fa-bars"></i><span>Fitur Utama</span></div>
                    <i id="chevron-icon" class="fas fa-chevron-right text-[11px] transition-transform duration-300"></i>
                </button>
                <div id="sub-menu-container" class="menu-collapsed space-y-1 pl-4 border-l border-white border-opacity-10 ml-4">
                    <button onclick="switchTab('ringkasan')" id="btn-ringkasan" class="side-nav-btn active"><i class="fas fa-th-large"></i> Ringkasan</button>
                    <button onclick="switchTab('karyawan')" id="btn-karyawan" class="side-nav-btn"><i class="fas fa-users"></i> Data Karyawan</button>
                    <button onclick="switchTab('input')" id="btn-input" class="side-nav-btn"><i class="fas fa-file-signature"></i> Rekap Input</button>
                    <button onclick="switchTab('rekap')" id="btn-rekap" class="side-nav-btn"><i class="fas fa-database"></i> Laporan Rekap</button>
                    <button onclick="switchTab('lembur')" id="btn-lembur" class="side-nav-btn"><i class="fas fa-money-bill-wave"></i> Hitung Lembur</button>
                </div>
            </nav>
            <div class="p-6 border-t border-white border-opacity-10">
                <form method="POST" action="{{ route('logout') }}"> @csrf
                    <button class="w-full flex items-center justify-center py-3 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl hover:bg-red-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest leading-none"><i class="fas fa-power-off mr-2"></i> Keluar</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 relative h-screen">
            <header class="bg-white px-8 py-4 flex justify-between items-center z-20 border-b shrink-0 h-[72px]">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-800 tracking-tight leading-none" id="header-title">Ringkasan Statistik</h1>
                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1.5">Enterprise Attendance System</p>
                </div>
                <div class="flex items-center gap-3 bg-slate-50 p-1.5 pr-4 rounded-xl border">
                    <div class="w-9 h-9 rounded-lg bg-indofood-blue text-white flex items-center justify-center font-black shadow-md text-lg">{{ substr(Auth::user()->name, 0, 2) }}</div>
                    <div><p class="text-[9px] font-black text-slate-400 uppercase leading-none">Administrator</p><p class="text-sm font-bold text-indofood-blue uppercase leading-none mt-1">{{ Auth::user()->name }}</p></div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-slate-50 relative">
                <div class="max-w-full mx-auto h-full flex flex-col">

                    @if(session('success'))
                    <div id="success-alert" class="bg-slate-900 text-white p-4 rounded-xl mb-4 flex items-center shadow-xl transition-opacity duration-500 shrink-0">
                        <i class="fas fa-check-circle text-green-400 mr-3 text-lg"></i><span class="text-xs font-bold uppercase tracking-wide">{{ session('success') }}</span>
                    </div>
                    @endif

                    <div id="tab-ringkasan" class="tab-content flex-1 flex flex-col gap-6">
                        <div class="flex flex-wrap justify-between items-center bg-white p-5 rounded-xl border border-gray-100 shadow-sm gap-4 shrink-0">
                            <div><h3 class="text-xs font-black text-slate-600 uppercase tracking-widest">Statistik Absensi</h3><p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Menampilkan data berdasarkan tanggal terpilih</p></div>
                            <div class="flex items-center gap-3 bg-slate-50 px-4 py-2.5 rounded-lg border border-slate-200 shadow-inner">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest"><i class="fas fa-calendar-day text-indofood-blue mr-1.5"></i> Tanggal:</label>
                                <input type="date" value="{{ request('date', date('Y-m-d')) }}" onchange="window.location.href='?date='+this.value+'#ringkasan'" class="text-xs font-bold bg-transparent border-none outline-none text-indofood-blue cursor-pointer p-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 shrink-0">
                            <div class="glass-card p-6 border-l-4 border-green-500">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Hadir</p>
                                <h3 class="text-3xl font-black text-slate-800">{{ $tim_assorting->where('status', 'Hadir')->count() }} <span class="text-xs text-slate-400">Orang</span></h3>
                            </div>
                            <div class="glass-card p-6 border-l-4 border-purple-600">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Total Lembur</p>
                                <h3 class="text-3xl font-black text-purple-700">{{ $tim_assorting->where('status', 'Lembur')->count() }} <span class="text-xs text-slate-400">Orang</span></h3>
                            </div>
                            <div class="glass-card p-6 border-l-4 border-yellow-500">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Izin / Sakit</p>
                                <h3 class="text-3xl font-black text-yellow-600">{{ $tim_assorting->where('status', 'Izin')->count() }} <span class="text-xs text-slate-400">Orang</span></h3>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-1 min-h-[300px]">
                            <div class="glass-card p-6 flex flex-col">
                                <p class="text-xs font-black text-slate-400 uppercase mb-4 tracking-widest shrink-0">Tren Kehadiran (7 Hari)</p>
                                <div class="chart-container flex-1"><canvas id="barChart"></canvas></div>
                            </div>
                            <div class="glass-card p-6 flex flex-col">
                                <p class="text-xs font-black text-slate-400 uppercase mb-4 tracking-widest shrink-0">Distribusi Status</p>
                                <div class="chart-container flex-1"><canvas id="pieChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-karyawan" class="tab-content hidden h-full flex flex-col">
                        <div class="glass-card overflow-hidden flex flex-col flex-1 shadow-xl min-h-0">

                            <div class="p-4 border-b bg-slate-50/80 flex justify-between items-center gap-4 shrink-0">
                                <div>
                                    <h4 class="text-xs font-black uppercase text-slate-700 tracking-widest">Database Karyawan</h4>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase italic">Total: <span id="totalKaryawan">{{ isset($employees) ? $employees->count() : 0 }}</span> Record</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-sm">
                                        <i class="fas fa-search text-slate-400 ml-3 text-xs"></i>
                                        <input type="text" id="employeeSearch" onkeyup="applyEmployeeFilters()" placeholder="Cari NIK / Nama..." class="text-xs p-1.5 border-none outline-none w-40 font-semibold focus:ring-0">
                                    </div>

                                    @php $jabatans = isset($employees) ? $employees->pluck('jabatan')->filter()->unique()->sort() : []; @endphp
                                    <select id="filterJabatan" onchange="applyEmployeeFilters()" class="text-[11px] p-2.5 rounded-xl border border-slate-300 outline-none shadow-sm cursor-pointer font-bold text-slate-700">
                                        <option value="">Semua Jabatan</option>
                                        @foreach($jabatans as $jab)
                                            <option value="{{ $jab }}">{{ $jab }}</option>
                                        @endforeach
                                    </select>

                                    <select id="sortKaryawan" onchange="applyEmployeeFilters()" class="text-[11px] p-2.5 rounded-xl border border-slate-300 outline-none shadow-sm cursor-pointer font-bold text-slate-700">
                                        <option value="default">Sort Default</option><option value="az">A - Z</option><option value="za">Z - A</option><option value="newest">TMK Terbaru</option><option value="oldest">TMK Terlama</option>
                                    </select>

                                    <button onclick="openAddEmployeeModal()" class="bg-blue-600 text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-md cursor-pointer hover:bg-blue-700 transition-all flex items-center">
                                        <i class="fas fa-user-plus mr-1.5 text-sm"></i> Tambah
                                    </button>

                                    <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="ml-1">
                                        @csrf
                                        <label class="bg-green-600 text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-md cursor-pointer hover:bg-green-700 transition-all flex items-center">
                                            <i class="fas fa-file-excel mr-1.5 text-sm"></i> File Excel
                                            <input type="file" name="file_excel" class="hidden" onchange="this.form.submit()">
                                        </label>
                                    </form>
                                </div>
                            </div>

                            <div class="overflow-auto flex-1 bg-white relative">
                                <table id="tableEmployee" class="w-full text-left text-xs table-bordered border-collapse whitespace-nowrap">
                                    <thead class="bg-indofood-blue text-white sticky top-0 z-30 shadow-md">
                                        <tr class="uppercase font-black tracking-widest text-[10px]">
                                            <th class="p-3 text-center sticky-no">NO</th>
                                            <th class="p-3 text-center sticky-nik">NIK</th>
                                            <th class="p-3 sticky-nama">NAMA KARYAWAN</th>
                                            <th class="p-3 text-center">USER</th>
                                            <th class="p-3 text-center sticky-aksi">AKSI</th>
                                            <th class="p-3">Jumlah</th><th class="p-3">Position text</th><th class="p-3">Employee Group</th>
                                            <th class="p-3">Pers subarea text</th><th class="p-3">SubSubarea</th><th class="p-3">TMK</th>
                                            <th class="p-3">Length of Service</th><th class="p-3">Gender</th><th class="p-3">Marital Status</th>
                                            <th class="p-3">Birthplace</th><th class="p-3">Birthdate</th><th class="p-3">Age</th>
                                            <th class="p-3">Range Umur</th><th class="p-3">Religion</th><th class="p-3">Jamsostek ID</th>
                                            <th class="p-3">BPJS ID</th><th class="p-3">Personal Tax ID</th><th class="p-3">Tax Status</th>
                                            <th class="p-3">Number Of Child</th><th class="p-3">KTP - Nomor KTP</th><th class="p-3">KTP - Alamat KTP</th>
                                            <th class="p-3">Region</th><th class="p-3">Postal Code</th><th class="p-3">No. HP</th><th class="p-3">Email</th>
                                            <th class="p-3">ESGrp text</th><th class="p-3">Level</th><th class="p-3">Position ID</th>
                                            <th class="p-3">Org unit ID</th><th class="p-3">Org unit text</th><th class="p-3">Pers subarea ID</th>
                                            <th class="p-3">Org key ID</th><th class="p-3">Org key text</th><th class="p-3">Bantu OPR/HLP</th>
                                            <th class="p-3">Regu / Line</th><th class="p-3">Line</th><th class="p-3">Regu</th>
                                            <th class="p-3">Area Prov Marketing</th><th class="p-3">Distrik Rayon</th><th class="p-3">Vendor OS</th>
                                            <th class="p-3">Cost Center</th><th class="p-3">Cost Center Text</th><th class="p-3">Emp status text</th>
                                            <th class="p-3">Excuse</th><th class="p-3">Tgl Akhir Kerja</th><th class="p-3">Edu - Level</th>
                                            <th class="p-3">Edu - Institution</th><th class="p-3">Edu - Branch</th><th class="p-3">PS Type Text</th>
                                            <th class="p-3">Status Incentive</th><th class="p-3">Bank Keys</th><th class="p-3">Bank Account No</th>
                                            <th class="p-3">Vaksin 1 Tgl</th><th class="p-3">Vaksin 1 Merk</th><th class="p-3">Vaksin 2 Tgl</th>
                                            <th class="p-3">Vaksin 2 Merk</th><th class="p-3">Vaksin 3 Tgl</th><th class="p-3">Vaksin 3 Merk</th>
                                            <th class="p-3">Workschedule ID</th><th class="p-3">Tipe Hari Kerja</th><th class="p-3">Shift/Non Shift</th>
                                            <th class="p-3">No Sidik Jari</th><th class="p-3">Jam Kerja</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employeeTableBody" class="divide-y divide-slate-200 text-slate-700 bg-white">
                                        @isset($employees)
                                            @foreach($employees as $emp)
                                            <tr class="hover:bg-blue-50 transition-all employee-row" data-nama="{{ $emp->nama }}" data-tmk="{{ $emp->tmk ? $emp->tmk->format('Y-m-d') : '1970-01-01' }}">
                                                <td class="p-3 font-black text-slate-400 text-center body-sticky-no">{{ $loop->iteration }}</td>
                                                <td class="p-3 font-black text-blue-700 text-center body-sticky-nik tracking-wider">{{ $emp->nik }}</td>
                                                <td class="p-3 font-extrabold uppercase text-slate-900 body-sticky-nama truncate max-w-[180px]">{{ $emp->nama }}</td>
                                                <td class="p-3 font-bold text-blue-700 text-center">{{ $emp->user ?? '-' }}</td>
                                                <td class="p-3 body-sticky-aksi">
                                                    <div class="flex justify-center gap-4">
                                                        <button type="button" onclick="openEditEmployeeModal(this)" data-employee="{{ base64_encode(json_encode($emp)) }}" class="text-blue-500 hover:text-blue-700 hover:scale-125 transition-all"><i class="fas fa-edit text-sm"></i></button>
                                                        <form action="{{ route('employees.destroy', $emp->id ?? 0) }}" method="POST" onsubmit="return confirm('Hapus permanen karyawan ini?');" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700 hover:scale-125 transition-all"><i class="fas fa-trash-alt text-sm"></i></button>
                                                        </form>
                                                    </div>
                                                </td>

                                                <td class="p-3 text-center font-semibold">{{ $emp->jumlah ?? '1' }}</td>
                                                <td class="p-3 font-bold text-slate-800">{{ $emp->jabatan }}</td>
                                                <td class="p-3 text-slate-500">{{ $emp->employee_group }}</td>
                                                <td class="p-3 font-semibold">{{ $emp->pers_subarea_text }}</td>
                                                <td class="p-3 font-bold text-slate-600">{{ $emp->sub_subarea }}</td>
                                                <td class="p-3 font-mono font-bold text-blue-600">{{ $emp->tmk ? $emp->tmk->format('d-M-Y') : '-' }}</td>
                                                <td class="p-3">{{ $emp->length_of_service }}</td>
                                                <td class="p-3 font-bold">{{ $emp->gender }}</td>
                                                <td class="p-3">{{ $emp->marital_status }}</td>
                                                <td class="p-3 uppercase">{{ $emp->birthplace }}</td>
                                                <td class="p-3 font-mono">{{ $emp->birthdate ? $emp->birthdate->format('Y-m-d') : '-' }}</td>
                                                <td class="p-3 font-bold text-center text-blue-600">{{ $emp->age }}</td>
                                                <td class="p-3 text-center">{{ $emp->range_umur }}</td>
                                                <td class="p-3 uppercase">{{ $emp->religion }}</td>
                                                <td class="p-3 font-mono text-slate-500">{{ $emp->jamsostek_id }}</td>
                                                <td class="p-3 font-mono text-slate-500">{{ $emp->bpjs_id }}</td>
                                                <td class="p-3 font-mono text-slate-500">{{ $emp->tax_id }}</td>
                                                <td class="p-3 font-semibold">{{ $emp->tax_status }}</td>
                                                <td class="p-3 text-center">{{ $emp->number_of_child }}</td>
                                                <td class="p-3 font-mono font-bold text-slate-700 tracking-tight">{{ $emp->ktp_nomor }}</td>
                                                <td class="p-3 text-[10px] whitespace-normal w-56 leading-relaxed">{{ $emp->ktp_alamat }}</td>
                                                <td class="p-3 font-semibold">{{ $emp->region }}</td>
                                                <td class="p-3 font-mono">{{ $emp->postal_code }}</td>
                                                <td class="p-3 font-bold text-slate-800">{{ $emp->no_hp }}</td>
                                                <td class="p-3 text-blue-500 font-semibold">{{ $emp->email }}</td>
                                                <td class="p-3">{{ $emp->esgrp_text }}</td>
                                                <td class="p-3 font-black uppercase text-slate-400 text-[10px]">{{ $emp->level }}</td>
                                                <td class="p-3 font-mono">{{ $emp->position_id }}</td>
                                                <td class="p-3 font-mono">{{ $emp->org_unit_id }}</td>
                                                <td class="p-3 uppercase text-[10px]">{{ $emp->org_unit_text }}</td>
                                                <td class="p-3">{{ $emp->pers_subarea_id }}</td>
                                                <td class="p-3">{{ $emp->org_key_id }}</td>
                                                <td class="p-3 text-[10px]">{{ $emp->org_key_text }}</td>
                                                <td class="p-3">{{ $emp->bantu_opr_hlp }}</td>
                                                <td class="p-3 font-bold text-slate-800">{{ $emp->regu_line }}</td>
                                                <td class="p-3">{{ $emp->line }}</td>
                                                <td class="p-3">{{ $emp->regu }}</td>
                                                <td class="p-3 text-[10px]">{{ $emp->area_prov_marketing }}</td>
                                                <td class="p-3 text-[10px]">{{ $emp->distrik_rayon_marketing }}</td>
                                                <td class="p-3 font-black text-slate-600">{{ $emp->vendor_os }}</td>
                                                <td class="p-3 font-mono">{{ $emp->cost_center }}</td>
                                                <td class="p-3 uppercase text-[10px]">{{ $emp->cost_center_text }}</td>
                                                <td class="p-3"><span class="px-2.5 py-1 rounded bg-green-100 text-green-700 font-black text-[9px] uppercase border border-green-200">{{ $emp->emp_status_text }}</span></td>
                                                <td class="p-3 italic text-slate-500">{{ $emp->excuse }}</td>
                                                <td class="p-3 font-mono">{{ $emp->tanggal_akhir_kerja ? $emp->tanggal_akhir_kerja->format('Y-m-d') : '-' }}</td>
                                                <td class="p-3 uppercase font-bold text-slate-800">{{ $emp->pendidikan_level }}</td>
                                                <td class="p-3 uppercase text-[10px] font-semibold">{{ $emp->pendidikan_institusi }}</td>
                                                <td class="p-3 text-[10px]">{{ $emp->pendidikan_studi }}</td>
                                                <td class="p-3 text-[10px]">{{ $emp->ps_type_text }}</td>
                                                <td class="p-3 text-center font-bold">{{ $emp->status_incentive }}</td>
                                                <td class="p-3 font-mono">{{ $emp->bank_keys }}</td>
                                                <td class="p-3 font-mono font-bold text-blue-700 tracking-wider">{{ $emp->bank_account_no }}</td>
                                                <td class="p-3 font-mono">{{ $emp->vaksin1_tanggal ? $emp->vaksin1_tanggal->format('Y-m-d') : '-' }}</td>
                                                <td class="p-3 text-[10px] uppercase font-semibold">{{ $emp->vaksin1_merk }}</td>
                                                <td class="p-3 font-mono">{{ $emp->vaksin2_tanggal ? $emp->vaksin2_tanggal->format('Y-m-d') : '-' }}</td>
                                                <td class="p-3 text-[10px] uppercase font-semibold">{{ $emp->vaksin2_merk }}</td>
                                                <td class="p-3 font-mono">{{ $emp->vaksin3_tanggal ? $emp->vaksin3_tanggal->format('Y-m-d') : '-' }}</td>
                                                <td class="p-3 text-[10px] uppercase font-semibold">{{ $emp->vaksin3_merk }}</td>
                                                <td class="p-3 font-mono">{{ $emp->workschedule_id }}</td>
                                                <td class="p-3 font-semibold">{{ $emp->tipe_hari_kerja }}</td>
                                                <td class="p-3 text-purple-600 font-bold uppercase text-[10px]">{{ $emp->shift_type }}</td>
                                                <td class="p-3 font-mono font-bold text-center tracking-widest text-slate-700">{{ $emp->no_sidik_jari }}</td>
                                                @php
                                                    $jam = $emp->jam_kerja;
                                                    if (empty($jam) || str_starts_with((string)$jam, '=') || str_contains(strtoupper((string)$jam), 'VLOOKUP')) {
                                                        $jam = str_contains((string)($emp->tipe_hari_kerja ?? ''), '5') ? '8 Jam' : '7 Jam';
                                                    }
                                                @endphp
                                                <td class="p-3 text-center font-black text-slate-900">{{ $jam }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="67" class="p-20 text-center text-slate-400 font-black uppercase tracking-widest italic border-none text-sm">Belum ada data master. Silakan unggah Data OS.xlsx</td></tr>
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="tab-input" class="tab-content hidden h-full flex flex-col">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 min-h-0">
                            <div class="lg:col-span-1 flex flex-col gap-6">
                                <div class="glass-card p-6 shrink-0">
                                    <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-5 border-b pb-3">Input Data Manual</h4>
                                    <form action="{{ route('attendance.store') }}#input" method="POST" class="space-y-4">
                                        @csrf <input type="hidden" name="action" value="assorting_tim">
                                        <input type="text" name="id_karyawan" placeholder="ID (Contoh: IND-001)" required class="form-input-modern">
                                        <input type="text" name="nama_karyawan" placeholder="Nama Lengkap" required class="form-input-modern">
                                        <div class="flex flex-col gap-1.5"><label class="text-[8px] font-black text-slate-400 uppercase ml-1">Pilih Tanggal</label><input type="date" name="tanggal" value="{{ $filterDate ?? date('Y-m-d') }}" class="form-input-modern"></div>
                                        <select name="status_kehadiran" class="form-input-modern cursor-pointer"><option value="Hadir">✔️ Hadir</option><option value="Lembur">🔥 Lembur</option><option value="Izin">📄 Izin / Sakit</option><option value="Tanpa Keterangan">❌ Tanpa Keterangan</option></select>
                                        <button class="w-full bg-indofood-blue text-white font-bold py-2.5 rounded-xl uppercase text-[10px] tracking-widest shadow-md hover:bg-blue-800 transition-all">Simpan Data</button>
                                    </form>
                                </div>
                                <div class="glass-card p-6 border-t-4 border-green-500 shrink-0">
                                    <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-4">Excel Batch Import</h4>
                                    <form action="{{ route('attendance.import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf <input type="file" name="file_excel" required class="form-input-modern text-[10px] mb-3 p-1">
                                        <button class="w-full bg-green-600 text-white font-bold py-2 rounded-xl uppercase text-[9px] tracking-widest hover:bg-green-700 transition-all">Unggah Batch</button>
                                    </form>
                                </div>
                            </div>
                            <div class="lg:col-span-2 glass-card flex flex-col shadow-xl min-h-0">
                                <div class="p-5 border-b bg-slate-50/50 flex justify-between items-center shrink-0">
                                    <h4 class="text-xs font-black uppercase text-slate-700 tracking-widest">Log Aktivitas: {{ \Carbon\Carbon::parse($filterDate ?? date('Y-m-d'))->translatedFormat('d M Y') }}</h4>
                                </div>
                                <div class="overflow-y-auto flex-1 bg-white relative">
                                    <table class="w-full text-left text-sm table-bordered">
                                        <thead class="bg-slate-100 sticky top-0 z-10 font-bold text-[10px] uppercase text-slate-600 shadow-sm"><tr><th class="p-3 text-center w-10">NO</th><th class="p-3">NAMA LENGKAP</th><th class="p-3 text-center w-24">WAKTU</th><th class="p-3 text-center w-20">AKSI</th></tr></thead>
                                        <tbody class="text-xs bg-white divide-y">
                                            @foreach($tim_assorting as $data)
                                            @php $name = explode(' - ', str_replace('Assorting: ', '', $data->catatan))[1] ?? $data->catatan; @endphp
                                            <tr class="hover:bg-slate-50 transition-all"><td class="p-3 text-center font-bold text-slate-400">{{ $loop->iteration }}</td><td class="p-3 font-bold text-slate-700 uppercase">{{ $name }}</td><td class="p-3 font-mono text-center text-[10px] text-slate-500">{{ $data->created_at->format('H:i:s') }}</td><td class="p-3"><div class="flex justify-center gap-3"><button onclick="openEditModal('{{ $data->id }}', '{{ $name }}', '{{ $data->status }}')" class="text-blue-500 hover:scale-110 transition-transform"><i class="fas fa-edit"></i></button><form action="{{ route('attendance.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-400 hover:scale-110 transition-transform"><i class="fas fa-trash-alt"></i></button></form></div></td></tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-rekap" class="tab-content hidden h-full flex flex-col">
                        <div class="glass-card overflow-hidden flex flex-col flex-1 shadow-xl min-h-0">
                            <div class="p-5 border-b bg-slate-50/80 flex flex-wrap justify-between items-end gap-4 shrink-0">
                                <form action="{{ route('attendance.export') }}" method="GET" class="flex flex-wrap gap-2 items-end">
                                    <div><label class="text-[9px] font-bold text-slate-500 uppercase mb-1 block ml-1">Mulai</label><input type="date" name="start_date" class="text-xs p-2 rounded-xl border outline-none border-slate-300 shadow-sm font-semibold text-slate-700"></div>
                                    <div><label class="text-[9px] font-bold text-slate-500 uppercase mb-1 block ml-1">Sampai</label><input type="date" name="end_date" class="text-xs p-2 rounded-xl border outline-none border-slate-300 shadow-sm font-semibold text-slate-700"></div>
                                    <button type="submit" class="bg-green-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-green-700 transition-all"><i class="fas fa-download mr-1.5"></i> Export Excel</button>
                                </form>
                                <div class="flex flex-wrap gap-2 items-center">
                                    <input type="date" id="filterDate" onchange="filterRekap()" class="text-xs p-2 rounded-xl border border-slate-300 outline-none shadow-sm font-semibold text-slate-700">
                                    <select id="filterStatus" onchange="filterRekap()" class="text-xs p-2.5 rounded-xl border border-slate-300 outline-none shadow-sm cursor-pointer font-bold text-slate-700"><option value="">Semua Status</option><option value="HADIR">Hadir</option><option value="LEMBUR">Lembur</option><option value="IZIN">Izin / Sakit</option><option value="TANPA KETERANGAN">Tanpa Keterangan</option></select>
                                    <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-sm">
                                        <i class="fas fa-search text-slate-400 ml-3 text-xs"></i>
                                        <input type="text" id="rekapSearch" onkeyup="filterRekap()" placeholder="Cari Personel..." class="text-xs p-1.5 border-none outline-none w-40 font-semibold focus:ring-0">
                                    </div>
                                    <button onclick="sortRekap(1)" class="bg-white border border-slate-300 p-2.5 rounded-xl text-[10px] font-bold shadow-sm text-slate-700 hover:bg-slate-50 transition-all uppercase tracking-widest"><i class="fas fa-sort-alpha-down mr-1"></i> A-Z</button>
                                </div>
                            </div>
                            <div class="overflow-auto flex-1 bg-white relative">
                                <table id="tableRekap" class="w-full text-left text-sm table-bordered">
                                    <thead class="bg-indofood-blue text-white sticky top-0 z-10 text-[10px] uppercase font-bold shadow-md"><tr><th class="p-4 text-center w-12 border-blue-800">NO</th><th class="p-4 border-blue-800">NAMA KARYAWAN (ID - NAMA)</th><th class="p-4 text-center w-32 border-blue-800">TANGGAL</th><th class="p-4 text-center w-32 border-blue-800">STATUS</th></tr></thead>
                                    <tbody class="divide-y divide-slate-100 text-xs bg-white text-slate-700">
                                        @foreach($rekap_semua as $data)
                                        <tr class="hover:bg-slate-50 transition-all"><td class="p-4 text-center font-bold text-slate-400">{{ $loop->iteration }}</td><td class="p-4 font-extrabold uppercase text-slate-800 tracking-wide">{{ str_replace('Assorting: ', '', $data->catatan) }}</td><td class="p-4 text-center font-bold">{{ $data->created_at->format('d/m/Y') }}</td><td class="p-4 text-center">@php $cl = match($data->status) { 'Hadir' => 'bg-green-100 text-green-700 border-green-200', 'Lembur' => 'bg-purple-100 text-purple-700 border-purple-200', 'Izin' => 'bg-yellow-100 text-yellow-700 border-yellow-200', default => 'bg-red-100 text-red-700 border-red-200' }; @endphp<span class="px-3 py-1.5 rounded border text-[9px] font-black uppercase tracking-widest shadow-sm {{ $cl }}">{{ $data->status }}</span></td></tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="tab-lembur" class="tab-content hidden h-full flex flex-col">
                        <div class="glass-card overflow-hidden flex flex-col flex-1 shadow-xl min-h-0">
                            <div class="p-5 border-b bg-slate-50/80 flex flex-wrap justify-between items-center gap-4 shrink-0">
                                <div>
                                    <h4 class="text-xs font-black uppercase text-slate-700 tracking-widest">Perincian Lembur Karyawan</h4>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase italic">Berdasarkan Input Rincian Harian</p>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="openInputLemburModal()" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase shadow-sm hover:bg-blue-700 transition-all flex items-center">
                                        <i class="fas fa-business-time mr-1.5"></i> Input Lembur Harian
                                    </button>
                                    <button class="bg-green-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase shadow-sm hover:bg-green-700 transition-all flex items-center">
                                        <i class="fas fa-file-excel mr-1.5"></i> Export Tagihan
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-auto flex-1 bg-white relative custom-scrollbar">
                                <table class="w-full text-left text-xs table-bordered border-collapse whitespace-nowrap">
                                    <thead class="bg-indofood-blue text-white sticky top-0 z-20 shadow-md">
                                        <tr class="uppercase font-black tracking-widest text-[10px]">
                                            <th class="p-4 text-center sticky left-0 bg-indofood-blue z-30 border-r border-blue-800 w-12">NO</th>
                                            <th class="p-4 sticky left-[48px] bg-indofood-blue z-30 border-r border-blue-800 w-48">NAMA KARYAWAN</th>
                                            <th class="p-4 text-right border-blue-800">GAJI POKOK</th>
                                            <th class="p-4 text-center border-blue-800">AKM. JAM LEMBUR</th>
                                            <th class="p-4 text-right border-blue-800">UPAH / JAM (GP/173)</th>
                                            <th class="p-4 text-right bg-blue-800 border-blue-900">TOTAL HASIL LEMBUR</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 text-slate-700 bg-white">
                                        @isset($rekapLembur)
                                            @forelse($rekapLembur as $lembur)
                                            <tr class="hover:bg-blue-50 transition-all">
                                                <td class="p-4 text-center font-bold text-slate-400 sticky left-0 bg-white border-r">{{ $loop->iteration }}</td>
                                                <td class="p-4 font-extrabold uppercase text-slate-900 sticky left-[48px] bg-white border-r">{{ $lembur->nama }}</td>
                                                <td class="p-4 text-right font-mono font-semibold text-slate-600">Rp {{ number_format(3496195, 0, ',', '.') }}</td>
                                                <td class="p-4 text-center font-black text-blue-600 text-sm">{{ floatval($lembur->total_jam) }}</td>
                                                <td class="p-4 text-right font-mono text-slate-500">Rp {{ number_format($lembur->upah_per_jam, 0, ',', '.') }}</td>
                                                <td class="p-4 text-right font-mono font-black text-green-700 bg-green-50">Rp {{ number_format($lembur->total_tagihan, 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="p-10 text-center text-slate-400 font-bold italic uppercase text-xs">Belum ada data lembur yang diinput.</td>
                                            </tr>
                                            @endforelse
                                        @else
                                            <tr>
                                                <td colspan="6" class="p-10 text-center text-slate-400 font-bold italic uppercase text-xs">Data lembur tidak tersedia dari server.</td>
                                            </tr>
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
            </main>
        </div>
    </div>

    <div id="editModal" class="modal opacity-0 pointer-events-none flex items-center justify-center">
        <div class="modal-overlay" onclick="closeEditModal()"></div>
        <div class="glass-card w-[400px] z-50 relative border-t-8 border-indofood-blue p-8 shadow-2xl rounded-2xl">
            <h4 class="text-center text-md font-black text-slate-800 uppercase tracking-widest mb-6">Koreksi Absensi</h4>
            <form id="editForm" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <input type="text" name="nama_karyawan" id="edit_nama" class="form-input-modern">
                <select name="status" id="edit_status" class="form-input-modern"><option value="Hadir">Hadir</option><option value="Lembur">Lembur</option><option value="Izin">Izin / Sakit</option><option value="Tanpa Keterangan">Tanpa Keterangan</option></select>
                <div class="flex gap-3 pt-4 border-t border-slate-100 mt-2">
                    <button type="button" onclick="closeEditModal()" class="flex-1 py-3 text-[10px] font-black bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all">BATAL</button>
                    <button type="submit" class="flex-1 py-3 text-[10px] font-black text-white bg-indofood-blue rounded-xl shadow-lg hover:bg-blue-800 transition-all">UPDATE</button>
                </div>
            </form>
        </div>
    </div>

    @php
        $formGroups = [
            '1. Data Personal' => [
                'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'ring' => 'focus:ring-blue-500 focus:border-blue-500',
                'fields' => ['nik'=>'text', 'nama'=>'text', 'gender'=>'text', 'birthplace'=>'text', 'birthdate'=>'date', 'age'=>'number', 'range_umur'=>'text', 'marital_status'=>'text', 'religion'=>'text', 'number_of_child'=>'number']
            ],
            '2. Kontak & Domisili' => [
                'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-700', 'ring' => 'focus:ring-indigo-500 focus:border-indigo-500',
                'fields' => ['ktp_nomor'=>'text', 'ktp_alamat'=>'text', 'region'=>'text', 'postal_code'=>'text', 'no_hp'=>'text', 'email'=>'email']
            ],
            '3. Penempatan & Pekerjaan' => [
                'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'ring' => 'focus:ring-emerald-500 focus:border-emerald-500',
                'fields' => ['user'=>'text', 'jabatan'=>'text', 'jumlah'=>'text', 'employee_group'=>'text', 'pers_subarea_text'=>'text', 'sub_subarea'=>'text', 'tmk'=>'date', 'length_of_service'=>'text', 'esgrp_text'=>'text', 'level'=>'text', 'position_id'=>'text', 'org_unit_id'=>'text', 'org_unit_text'=>'text', 'pers_subarea_id'=>'text', 'org_key_id'=>'text', 'org_key_text'=>'text', 'bantu_opr_hlp'=>'text', 'regu_line'=>'text', 'line'=>'text', 'regu'=>'text', 'area_prov_marketing'=>'text', 'distrik_rayon_marketing'=>'text', 'vendor_os'=>'text', 'cost_center'=>'text', 'cost_center_text'=>'text', 'emp_status_text'=>'text', 'excuse'=>'text', 'tanggal_akhir_kerja'=>'date', 'workschedule_id'=>'text', 'tipe_hari_kerja'=>'text', 'shift_type'=>'text', 'no_sidik_jari'=>'text', 'jam_kerja'=>'text']
            ],
            '4. BPJS & Pajak' => [
                'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700', 'ring' => 'focus:ring-amber-500 focus:border-amber-500',
                'fields' => ['jamsostek_id'=>'text', 'bpjs_id'=>'text', 'tax_id'=>'text', 'tax_status'=>'text']
            ],
            '5. Pendidikan & Keuangan' => [
                'bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-700', 'ring' => 'focus:ring-purple-500 focus:border-purple-500',
                'fields' => ['pendidikan_level'=>'text', 'pendidikan_institusi'=>'text', 'pendidikan_studi'=>'text', 'ps_type_text'=>'text', 'status_incentive'=>'text', 'bank_keys'=>'text', 'bank_account_no'=>'text']
            ],
            '6. Riwayat Vaksinasi' => [
                'bg' => 'bg-teal-50', 'border' => 'border-teal-200', 'text' => 'text-teal-700', 'ring' => 'focus:ring-teal-500 focus:border-teal-500',
                'fields' => ['vaksin1_tanggal'=>'date', 'vaksin1_merk'=>'text', 'vaksin2_tanggal'=>'date', 'vaksin2_merk'=>'text', 'vaksin3_tanggal'=>'date', 'vaksin3_merk'=>'text']
            ],
        ];
    @endphp

    <div id="addEmployeeModal" class="modal opacity-0 pointer-events-none flex items-center justify-center py-6">
        <div class="modal-overlay" onclick="closeAddEmployeeModal()"></div>
        <div class="bg-slate-50 w-[95vw] max-w-[1300px] h-[90vh] z-50 relative border-t-8 border-green-500 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            <div class="p-5 bg-white border-b border-slate-200 z-10 shrink-0 flex justify-between items-center">
                <h4 class="text-lg font-black text-slate-800 uppercase tracking-widest"><i class="fas fa-user-plus mr-2 text-green-500"></i> Tambah Karyawan Baru</h4>
                <button type="button" onclick="closeAddEmployeeModal()" class="text-slate-400 hover:text-red-500 text-2xl font-black focus:outline-none">&times;</button>
            </div>
            <form action="{{ route('employees.store') }}" method="POST" class="flex flex-col flex-1 min-h-0">
                @csrf
                <div class="p-6 overflow-y-auto flex-1 min-h-0 bg-slate-100 custom-scrollbar space-y-6">
                    @foreach($formGroups as $groupName => $group)
                        <div class="bg-white rounded-xl shadow-sm border {{ $group['border'] }} overflow-hidden">
                            <div class="{{ $group['bg'] }} px-5 py-3 border-b {{ $group['border'] }}">
                                <h5 class="text-sm font-black {{ $group['text'] }} uppercase tracking-widest">{{ $groupName }}</h5>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                                @foreach($group['fields'] as $name => $type)
                                    <div class="flex flex-col">
                                        <label class="text-xs font-extrabold text-slate-500 uppercase mb-1.5 tracking-wide">{{ ucwords(str_replace('_', ' ', $name)) }}</label>
                                        <input type="{{ $type }}" name="{{ $name }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2.5 text-sm text-slate-800 font-semibold focus:bg-white {{ $group['ring'] }} outline-none transition-all shadow-sm" {{ $name == 'nik' || $name == 'nama' ? 'required' : '' }}>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-5 border-t border-slate-200 bg-white z-10 shrink-0 flex justify-end gap-4">
                    <button type="button" onclick="closeAddEmployeeModal()" class="px-8 py-3 text-xs font-black bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all">BATAL</button>
                    <button type="submit" class="px-8 py-3 text-xs font-black text-white bg-green-600 rounded-xl shadow-lg hover:bg-green-700 transition-all">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editEmployeeModal" class="modal opacity-0 pointer-events-none flex items-center justify-center py-6">
        <div class="modal-overlay" onclick="closeEditEmployeeModal()"></div>
        <div class="bg-slate-50 w-[95vw] max-w-[1300px] h-[90vh] z-50 relative border-t-8 border-blue-500 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            <div class="p-5 bg-white border-b border-slate-200 z-10 shrink-0 flex justify-between items-center">
                <h4 class="text-lg font-black text-slate-800 uppercase tracking-widest"><i class="fas fa-edit mr-2 text-blue-500"></i> Koreksi Data Karyawan</h4>
                <button type="button" onclick="closeEditEmployeeModal()" class="text-slate-400 hover:text-red-500 text-2xl font-black focus:outline-none">&times;</button>
            </div>
            <form id="editEmployeeForm" method="POST" class="flex flex-col flex-1 min-h-0">
                @csrf @method('PUT')
                <div class="p-6 overflow-y-auto flex-1 min-h-0 bg-slate-100 custom-scrollbar space-y-6">
                    @foreach($formGroups as $groupName => $group)
                        <div class="bg-white rounded-xl shadow-sm border {{ $group['border'] }} overflow-hidden">
                            <div class="{{ $group['bg'] }} px-5 py-3 border-b {{ $group['border'] }}">
                                <h5 class="text-sm font-black {{ $group['text'] }} uppercase tracking-widest">{{ $groupName }}</h5>
                            </div>
                            <div class="p-5 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                                @foreach($group['fields'] as $name => $type)
                                    <div class="flex flex-col">
                                        <label class="text-xs font-extrabold text-slate-500 uppercase mb-1.5 tracking-wide">{{ ucwords(str_replace('_', ' ', $name)) }}</label>
                                        <input type="{{ $type }}" name="{{ $name }}" id="edit_emp_{{ $name }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2.5 text-sm text-slate-800 font-semibold focus:bg-white {{ $group['ring'] }} outline-none transition-all shadow-sm" {{ $name == 'nik' || $name == 'nama' ? 'required' : '' }}>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-5 border-t border-slate-200 bg-white z-10 shrink-0 flex justify-end gap-4">
                    <button type="button" onclick="closeEditEmployeeModal()" class="px-8 py-3 text-xs font-black bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all">BATAL</button>
                    <button type="submit" class="px-8 py-3 text-xs font-black text-white bg-blue-600 rounded-xl shadow-lg hover:bg-blue-700 transition-all">UPDATE DATA</button>
                </div>
            </form>
        </div>
    </div>

    <div id="inputLemburModal" class="modal opacity-0 pointer-events-none flex items-center justify-center py-6">
        <div class="modal-overlay" onclick="closeInputLemburModal()"></div>
        <div class="bg-slate-50 w-[450px] z-50 relative border-t-8 border-blue-600 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            <div class="p-5 bg-white border-b border-slate-200 z-10 shrink-0 flex justify-between items-center">
                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest"><i class="fas fa-clock mr-2 text-blue-600"></i> Input Lembur Harian</h4>
                <button type="button" onclick="closeInputLemburModal()" class="text-slate-400 hover:text-red-500 text-xl font-black focus:outline-none">&times;</button>
            </div>

            <form action="{{ route('lembur.store') }}" method="POST" class="flex flex-col space-y-4 p-6 bg-white">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Pilih Karyawan</label>
                    <select name="employee_id" class="form-input-modern mt-1" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @isset($employees)
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->nama }} - {{ $emp->nik }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Tanggal Lembur</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-input-modern mt-1" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-input-modern mt-1" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-input-modern mt-1" required>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Potong Istirahat (Jam)</label>
                    <input type="number" step="0.5" name="istirahat" placeholder="Contoh: 1 atau 0.5" value="0" class="form-input-modern mt-1">
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Keterangan Pekerjaan</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Service / Penarikan FG" class="form-input-modern mt-1">
                </div>

                <div class="pt-4 border-t mt-4 flex gap-3">
                    <button type="button" onclick="closeInputLemburModal()" class="flex-1 py-2.5 text-[10px] font-black bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all">BATAL</button>
                    <button type="submit" class="flex-1 py-2.5 text-[10px] font-black text-white bg-blue-600 rounded-xl shadow-lg hover:bg-blue-800 transition-all">SIMPAN LEMBUR</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function toggleMenu() {
            const container = document.getElementById('sub-menu-container'); const chevron = document.getElementById('chevron-icon');
            if (container.classList.contains('menu-expanded')) { container.classList.remove('menu-expanded'); container.classList.add('menu-collapsed'); chevron.style.transform = 'rotate(0deg)'; }
            else { container.classList.remove('menu-collapsed'); container.classList.add('menu-expanded'); chevron.style.transform = 'rotate(90deg)'; }
        }

        function switchTab(id) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden')); document.querySelectorAll('.side-nav-btn').forEach(el => el.classList.remove('active'));
            document.getElementById('tab-' + id).classList.remove('hidden'); document.getElementById('btn-' + id).classList.add('active');

            const titles = { 'ringkasan': 'Ringkasan Statistik', 'karyawan': 'Database Master Karyawan', 'input': 'Rekap Input Kehadiran', 'rekap': 'Laporan Database Rekap', 'lembur': 'Perincian Tagihan Lembur' };
            document.getElementById('header-title').innerText = titles[id]; window.location.hash = id;
        }

        function openInputLemburModal() { document.getElementById('inputLemburModal').classList.remove('opacity-0', 'pointer-events-none'); }
        function closeInputLemburModal() { document.getElementById('inputLemburModal').classList.add('opacity-0', 'pointer-events-none'); }

        function openAddEmployeeModal() { document.getElementById('addEmployeeModal').classList.remove('opacity-0', 'pointer-events-none'); }
        function closeAddEmployeeModal() { document.getElementById('addEmployeeModal').classList.add('opacity-0', 'pointer-events-none'); }

        function openEditEmployeeModal(btn) {
            let empData = JSON.parse(atob(btn.dataset.employee));
            if (empData.jam_kerja && (String(empData.jam_kerja).startsWith('=') || String(empData.jam_kerja).toUpperCase().includes('VLOOKUP'))) {
                empData.jam_kerja = (empData.tipe_hari_kerja && String(empData.tipe_hari_kerja).includes('5')) ? '8 Jam' : '7 Jam';
            }
            document.getElementById('editEmployeeForm').action = `/employees/${empData.id}`;
            for (let key in empData) {
                let input = document.getElementById('edit_emp_' + key);
                if (input) {
                    if (input.type === 'date' && empData[key]) { input.value = empData[key].substring(0, 10); }
                    else { input.value = empData[key]; }
                }
            }
            document.getElementById('editEmployeeModal').classList.remove('opacity-0', 'pointer-events-none');
        }
        function closeEditEmployeeModal() { document.getElementById('editEmployeeModal').classList.add('opacity-0', 'pointer-events-none'); }

        function applyEmployeeFilters() {
            const searchInput = document.getElementById("employeeSearch").value.toUpperCase();
            const filterJabatan = document.getElementById("filterJabatan").value.toUpperCase();
            const sortType = document.getElementById("sortKaryawan").value;
            const tbody = document.getElementById("employeeTableBody");
            let rows = Array.from(tbody.querySelectorAll("tr.employee-row"));
            if(rows.length === 0) return;

            let visibleCount = 0;
            rows.forEach(row => {
                const nik = row.cells[1]?.textContent.toUpperCase() || "";
                const nama = row.cells[2]?.textContent.toUpperCase() || "";
                const jabatan = row.cells[5]?.textContent.toUpperCase() || "";
                const matchSearch = nik.includes(searchInput) || nama.includes(searchInput);
                const matchJabatan = filterJabatan === "" || jabatan === filterJabatan;

                if (matchSearch && matchJabatan) { row.style.display = ""; visibleCount++; }
                else { row.style.display = "none"; }
            });

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
            const totalEl = document.getElementById("totalKaryawan"); if(totalEl) totalEl.innerText = visibleCount;
        }

        function filterRekap() {
            const searchName = document.getElementById("rekapSearch").value.toUpperCase(); const searchStatus = document.getElementById("filterStatus").value.toUpperCase(); let searchDate = document.getElementById("filterDate").value;
            if (searchDate) { const parts = searchDate.split('-'); searchDate = `${parts[2]}/${parts[1]}/${parts[0]}`; }
            const tr = document.getElementById("tableRekap").getElementsByTagName("tr"); let visibleCount = 0;
            for (let i = 1; i < tr.length; i++) {
                const name = tr[i].cells[1]?.textContent.toUpperCase() || ""; const date = tr[i].cells[2]?.textContent || ""; const status = tr[i].cells[3]?.textContent.toUpperCase() || "";
                const matchName = name.includes(searchName); const matchStatus = searchStatus === "" || status.includes(searchStatus); const matchDate = searchDate === "" || date === searchDate;
                if (matchName && matchStatus && matchDate) { tr[i].style.display = ""; visibleCount++; tr[i].cells[0].innerText = visibleCount; } else { tr[i].style.display = "none"; }
            }
        }

        let asc = true;
        function sortRekap(n) {
            const t = document.getElementById("tableRekap"); let r = Array.from(t.rows).slice(1); asc = !asc;
            r.sort((a, b) => asc ? a.cells[n].innerText.localeCompare(b.cells[n].innerText) : b.cells[n].innerText.localeCompare(a.cells[n].innerText));
            r.forEach(row => t.tBodies[0].appendChild(row)); let rows = t.getElementsByTagName("tr"); for (let i = 1; i < rows.length; i++) { rows[i].cells[0].innerText = i; } filterRekap();
        }

        function openEditModal(id, n, s) { document.getElementById('editForm').action = `/attendance/${id}`; document.getElementById('edit_nama').value = n; document.getElementById('edit_status').value = s; document.getElementById('editModal').classList.remove('opacity-0', 'pointer-events-none'); }
        function closeEditModal() { document.getElementById('editModal').classList.add('opacity-0', 'pointer-events-none'); }

        window.addEventListener('DOMContentLoaded', () => {
            const h = window.location.hash.substring(1);
            if (['ringkasan', 'karyawan', 'input', 'rekap', 'lembur'].includes(h)) switchTab(h);

            const alert = document.getElementById('success-alert'); if (alert) setTimeout(() => { alert.style.opacity = '0'; setTimeout(() => alert.remove(), 500); }, 3000);

            Chart.register(ChartDataLabels);
            new Chart(document.getElementById('barChart').getContext('2d'), { type: 'bar', data: { labels: {!! json_encode($trendLabels ?? []) !!}, datasets: [ { label: 'Hadir', data: {!! json_encode($trendHadir ?? []) !!}, backgroundColor: '#10b981', borderRadius: 4 }, { label: 'Lembur', data: {!! json_encode($trendLembur ?? []) !!}, backgroundColor: '#7c3aed', borderRadius: 4 }, { label: 'Izin', data: {!! json_encode($trendIzin ?? []) !!}, backgroundColor: '#f59e0b', borderRadius: 4 }, { label: 'Alpa', data: {!! json_encode($trendAlpa ?? []) !!}, backgroundColor: '#ef4444', borderRadius: 4 } ] }, options: { maintainAspectRatio: false, plugins: { datalabels: { color: '#fff', font: { size: 9 } } } } });
            new Chart(document.getElementById('pieChart').getContext('2d'), { type: 'doughnut', data: { labels: {!! json_encode($chartLabels ?? []) !!}, datasets: [{ data: {!! json_encode($chartValues ?? []) !!}, backgroundColor: ['#10b981', '#7c3aed', '#f59e0b', '#ef4444'], borderWidth: 0 }] }, options: { maintainAspectRatio: false, cutout: '65%' } });
        });
    </script>
</x-app-layout>
