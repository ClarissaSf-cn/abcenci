{{-- =========================================================
     ASPEK PENILAIAN KARYAWAN
     File ini hanya berisi isi halaman.
     Sidebar berada di file Admin Penilaian utama.
     ========================================================= --}}


<div id="tab-aspek-penilaian"
     class="tab-content hidden flex-1 flex-col gap-6">

    @php
        $authUser = Auth::user();
        $uRole = strtolower(str_replace([' ', '-'], '_', $authUser->role ?? ''));
        $uName = strtoupper(trim($authUser->name ?? ''));
        $isSuperAdmin = ($uRole === 'superadmin' || $uName === 'ADMIN SUPER');
        $isAdminPenilaian = ($uRole === 'admin_penilaian' || str_contains($uRole, 'penilaian'));

        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $bulanIni = sprintf('%02d', date('m'));
        $bulanIniNama = $namaBulan[$bulanIni] ?? 'Januari';
        $tahunIni = (int) date('Y');

        $initialPenilaians = \App\Models\Penilaian::where(function($q) use ($bulanIniNama, $bulanIni) {
            $q->where('bulan', $bulanIniNama)->orWhere('bulan', $bulanIni);
        })->where('tahun', $tahunIni)->get()->keyBy('nik');
    @endphp


    {{-- =====================================================
         FORM SIMPAN PENILAIAN
         ===================================================== --}}

    <form id="formSimpanPenilaian"
          action="{{ route('penilaian.store') }}"
          method="POST"
          class="flex flex-col gap-6">

        @csrf


        {{-- =================================================
             HEADER
             ================================================= --}}

        <div class="bg-white rounded-2xl shadow-xl
                    border border-slate-200 p-6">

            <div class="flex justify-between items-center
                        flex-wrap gap-4">

                <div>

                    <h2 class="text-2xl font-black uppercase
                               text-slate-800">

                        Aspek Penilaian Karyawan

                    </h2>

                    <p class="text-xs text-slate-500 mt-1">

                        Skor total karyawan akan terhitung
                        <strong>otomatis</strong> saat opsi dinilai.

                    </p>

                </div>


                <div class="flex items-center gap-2 flex-wrap">

                    {{-- =================================================
                         DATA KARYAWAN & TAMBAH KARYAWAN
                         ================================================= --}}

                    <button type="button"
                            onclick="switchTab('karyawan')"
                            style="background-color:#0284c7 !important;
                                   color:#ffffff !important;"
                            class="px-4 py-3 rounded-xl text-xs font-bold
                                   shadow hover:bg-sky-700 transition-all
                                   flex items-center gap-2
                                   cursor-pointer">

                        <i class="fas fa-users"></i>

                        Data Karyawan

                    </button>

                    <button type="button"
                            onclick="openAddEmployeeModal()"
                            style="background-color:#4f46e5 !important;
                                   color:#ffffff !important;"
                            class="px-4 py-3 rounded-xl text-xs font-bold
                                   shadow hover:bg-indigo-700 transition-all
                                   flex items-center gap-2
                                   cursor-pointer">

                        <i class="fas fa-user-plus"></i>

                        Tambah Karyawan

                    </button>

                    {{-- =================================================
                         EKSPOR PDF
                         ================================================= --}}

                    <a href="{{ route('penilaian.export.pdf') }}"
                       id="btnExportPdfPenilaian"
                       download
                       style="background-color:#dc2626 !important;
                              color:#ffffff !important;"
                       class="px-4 py-3 rounded-xl text-xs font-bold
                              shadow hover:bg-red-700 transition-all
                              flex items-center gap-2
                              cursor-pointer no-underline">

                        <i class="fas fa-file-pdf"></i>

                        Ekspor PDF

                    </a>


                    {{-- =================================================
                         EKSPOR EXCEL
                         ================================================= --}}

                    <a href="{{ route('penilaian.export.excel') }}"
                       id="btnExportExcelPenilaian"
                       download
                       style="background-color:#15803d !important;
                              color:#ffffff !important;"
                       class="px-4 py-3 rounded-xl text-xs font-bold
                              shadow hover:bg-green-800 transition-all
                              flex items-center gap-2
                              cursor-pointer no-underline">

                        <i class="fas fa-file-excel"></i>

                        Ekspor Excel

                    </a>


                    {{-- =================================================
                         TOMBOL SIMPAN
                         ================================================= --}}

                    <button type="submit"
                            id="btnSimpanPenilaian"
                            style="background-color:#059669 !important;
                                   color:#ffffff !important;"
                            class="px-5 py-3 rounded-xl text-xs font-bold
                                   shadow hover:bg-emerald-700
                                   transition-all flex items-center gap-2
                                   cursor-pointer">

                        <i class="fas fa-save"></i>

                        <span id="btnText">
                            Simpan Penilaian Bulanan
                        </span>

                    </button>

                </div>

            </div>

        </div>

        {{-- =================================================
             ROLE BANNER (HAK AKSES PENILAIAN)
             ================================================= --}}
        @if ($isSuperAdmin)
            <div class="bg-gradient-to-r from-emerald-500/10 via-emerald-50 to-teal-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-emerald-900 tracking-wider">Mode Super Admin — Hak Penuh</h4>
                        <p class="text-xs text-emerald-700 mt-0.5">Anda dapat melakukan <strong>penilaian baru</strong> dan memiliki hak istimewa untuk <strong>mengubah / memperbarui penilaian</strong> yang sudah tersimpan.</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-emerald-200 text-emerald-900 text-[11px] font-black rounded-full uppercase tracking-wider flex items-center gap-1 shadow-sm">
                    <i class="fas fa-check-double"></i> Akses Penuh
                </span>
            </div>
        @elseif ($isAdminPenilaian)
            <div class="bg-gradient-to-r from-amber-500/10 via-amber-50 to-orange-50 border-l-4 border-amber-500 p-4 rounded-xl shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold text-lg shadow">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-amber-900 tracking-wider">Mode Admin Penilaian — Hanya Menilai Baru</h4>
                        <p class="text-xs text-amber-700 mt-0.5">Anda berhak memberikan <strong>penilaian untuk karyawan yang belum dinilai</strong>. Karyawan yang telah dinilai pada periode ini akan <strong>terkunci otomatis</strong> dan tidak dapat diubah (pengubahan hanya oleh Super Admin).</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-amber-200 text-amber-900 text-[11px] font-black rounded-full uppercase tracking-wider flex items-center gap-1 shadow-sm">
                    <i class="fas fa-lock"></i> Hanya Menilai
                </span>
            </div>
        @endif

        @if ($isAdminPenilaian)

    {{-- =====================================================
             FILTER ADMIN PENILAIAN
             ===================================================== --}}

        <div id="filterSection"
             class="bg-white rounded-2xl shadow-xl
                    border border-slate-200 p-6 mt-2">

            <div class="flex justify-between items-center
                        mb-4 border-b pb-3">

                <h3 class="text-sm font-bold uppercase text-slate-700">
                    Filter Option
                </h3>

                <button type="button"
                        onclick="toggleFilter()"
                        class="text-xs text-blue-600 font-semibold
                               focus:outline-none hover:underline
                               cursor-pointer">

                    <span id="hideFilterText">
                        Hide Filter
                    </span>

                </button>

            </div>


            <div id="filterFormContent"
                 class="grid grid-cols-1 md:grid-cols-3 gap-4">


                {{-- =================================================
                     FILTER USER
                     ================================================= --}}

                <div class="flex flex-col gap-1">

                    <label class="text-xs font-bold text-slate-600">
                        User
                    </label>

                    <select id="adminFilterUser"
                            class="border border-slate-300
                                   rounded-lg p-2 text-xs
                                   focus:ring-2 focus:ring-cyan-500
                                   outline-none">

                        <option value="">
                            Semua User
                        </option>

                        <option value="IR Spv">
                            IR Spv
                        </option>

                        <option value="WHS Spv">
                            WHS Spv
                        </option>

                        <option value="GAS Spv">
                            GAS Spv
                        </option>

                        <option value="Prod Shift Spv (Reg. A)">
                            Prod Shift Spv (Reg. A)
                        </option>

                        <option value="Prod Shift Spv (Reg. B)">
                            Prod Shift Spv (Reg. B)
                        </option>

                        <option value="Prod Shift Spv (Reg. C)">
                            Prod Shift Spv (Reg. C)
                        </option>

                    </select>

                </div>


                {{-- =================================================
                     FILTER DEPARTEMEN
                     ================================================= --}}

                <div class="flex flex-col gap-1">

                    <label class="text-xs font-bold text-slate-600">
                        Departemen
                    </label>

                    <select id="adminFilterDepartemen"
                            class="border border-slate-300
                                   rounded-lg p-2 text-xs
                                   focus:ring-2 focus:ring-cyan-500
                                   outline-none">

                        <option value="">
                            Semua Departemen
                        </option>

                        <option value="MFG Production">
                            MFG Production
                        </option>

                        <option value="MFG Warehouse">
                            MFG Warehouse
                        </option>

                        <option value="ADM HR">
                            ADM HR
                        </option>

                    </select>

                </div>


                {{-- =================================================
                     FILTER VENDOR
                     ================================================= --}}

                <div class="flex flex-col gap-1">

                    <label class="text-xs font-bold text-slate-600">
                        Vendor
                    </label>

                    <select id="adminFilterVendor"
                            class="border border-slate-300
                                   rounded-lg p-2 text-xs
                                   focus:ring-2 focus:ring-cyan-500
                                   outline-none">

                        <option value="">
                            Semua Vendor
                        </option>

                        <option value="PT Tropis Service">
                            PT Tropis Service
                        </option>

                        <option value="PT. Fadanara Berkah Bersama">
                            PT. Fadanara Berkah Bersama
                        </option>

                        <option value="PT Garda Bhakti Nusantara">
                            PT Garda Bhakti Nusantara
                        </option>

                    </select>

                </div>


                {{-- =================================================
                     TOMBOL APPLY
                     ================================================= --}}

                <div class="md:col-span-3 flex justify-end mt-2">

                    <button type="button"
                            onclick="applyAdminFilter()"
                            style="background-color:#0891b2 !important;
                                   color:#ffffff !important;"
                            class="px-6 py-2 rounded-lg text-xs
                                   font-bold shadow
                                   hover:bg-cyan-700 transition-all
                                   flex items-center gap-2
                                   cursor-pointer">

                        <i class="fas fa-check"></i>

                        Apply

                    </button>

                </div>

            </div>

        </div>


        @endif


        {{-- =====================================================
             PANEL FILTER TABEL
             ===================================================== --}}

        <div class="bg-white rounded-2xl shadow-xl
                    border border-slate-200 overflow-hidden">

            <div class="p-4 bg-slate-50 border-b
                        border-slate-200 flex items-center
                        gap-3 flex-wrap">


                {{-- =================================================
                     PERIODE
                     ================================================= --}}

                <div class="flex items-center gap-2">

                    <i class="fas fa-calendar-alt text-blue-600"></i>

                    <span class="text-xs font-bold uppercase
                                 text-slate-700">

                        Periode:

                    </span>

                </div>


                {{-- =================================================
                     BULAN
                     ================================================= --}}

                <div class="relative inline-block">

                    @php

                        $namaBulan = [
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember'
                        ];

                        $bulanIni = sprintf('%02d', date('m'));

                    @endphp


                    <input type="text"
                           id="inputBulanPenilaian"
                           name="bulan"
                           list="listBulan"
                           value="{{ $namaBulan[$bulanIni] ?? '' }}"
                           placeholder="Pilih Bulan"
                           class="w-32 border border-slate-300
                                  rounded-lg pl-3 pr-7 py-1.5
                                  text-xs font-bold text-slate-700
                                  focus:outline-none
                                  focus:ring-2 focus:ring-blue-500
                                  bg-white"
                           required>


                    <datalist id="listBulan">

                        @foreach($namaBulan as $code => $name)

                            <option value="{{ $name }}"></option>

                        @endforeach

                    </datalist>

                </div>


                {{-- =================================================
                     TAHUN
                     ================================================= --}}

                <div class="relative inline-block">

                    @php
                        $tahunIni = (int) date('Y');
                    @endphp


                    <input type="text"
                           id="inputTahunPenilaian"
                           name="tahun"
                           list="listTahun"
                           value="{{ $tahunIni }}"
                           placeholder="Pilih Tahun"
                           class="w-28 border border-slate-300
                                  rounded-lg pl-3 pr-7 py-1.5
                                  text-xs font-bold text-slate-700
                                  focus:outline-none
                                  focus:ring-2 focus:ring-blue-500
                                  bg-white"
                           required>


                    <datalist id="listTahun">

                        @for(
                            $y = $tahunIni + 2;
                            $y >= $tahunIni - 5;
                            $y--
                        )

                            <option value="{{ $y }}"></option>

                        @endfor

                    </datalist>

                </div>


                {{-- =================================================
                     SEARCH NIK
                     ================================================= --}}

                <div class="relative flex items-center gap-1.5">

                    <i class="fas fa-id-card text-blue-600"></i>

                    <input type="text"
                           id="inputSearchNik"
                           placeholder="Cari NIK..."
                           autocomplete="off"
                           class="w-32 border border-slate-300
                                  rounded-lg px-3 py-1.5
                                  text-xs font-bold text-slate-700
                                  focus:outline-none
                                  focus:ring-2 focus:ring-blue-500
                                  bg-white">

                </div>


                {{-- =================================================
                     SEARCH NAMA
                     ================================================= --}}

                <div class="relative flex items-center gap-1.5">

                    <i class="fas fa-user text-blue-600"></i>

                    <input type="text"
                           id="inputSearchNama"
                           placeholder="Cari Nama Karyawan..."
                           autocomplete="off"
                           class="w-44 border border-slate-300
                                  rounded-lg px-3 py-1.5
                                  text-xs font-bold text-slate-700
                                  focus:outline-none
                                  focus:ring-2 focus:ring-blue-500
                                  bg-white">

                </div>


                {{-- =================================================
                     FILTER VENDOR TABEL
                     ================================================= --}}

                @php

                    $vendorList = collect($employees ?? [])
                        ->map(function ($emp) {

                            return is_array($emp)
                                ? ($emp['vendor_os'] ?? '')
                                : ($emp->vendor_os ?? '');

                        })
                        ->filter(function ($val) {

                            return trim((string) $val) !== '';

                        })
                        ->unique()
                        ->sort()
                        ->values();

                @endphp


                <div class="relative inline-block">

                    <div class="flex items-center gap-1.5">

                        <i class="fas fa-building text-blue-600"></i>

                        <input type="text"
                               id="tableFilterVendor"
                               list="listVendor"
                               placeholder="Semua Vendor"
                               autocomplete="off"
                               class="w-36 border border-slate-300
                                      rounded-lg pl-3 pr-7 py-1.5
                                      text-xs font-bold text-slate-700
                                      focus:outline-none
                                      focus:ring-2 focus:ring-blue-500
                                      bg-white">

                    </div>


                    <datalist id="listVendor">

                        @foreach($vendorList as $vendor)

                            <option value="{{ $vendor }}"></option>

                        @endforeach

                    </datalist>

                </div>


                {{-- =================================================
                     FILTER DEPARTEMEN TABEL
                     ================================================= --}}

                @php

                    $departmentList = collect($employees ?? [])
                        ->map(function ($emp) {

                            return is_array($emp)
                                ? (
                                    $emp['pers_subarea_text']
                                    ?? $emp['subarea']
                                    ?? ''
                                )
                                : (
                                    $emp->pers_subarea_text
                                    ?? $emp->subarea
                                    ?? ''
                                );

                        })
                        ->filter(function ($val) {

                            return trim((string) $val) !== '';

                        })
                        ->unique()
                        ->sort()
                        ->values();

                @endphp


                <div class="relative inline-block">

                    <div class="flex items-center gap-1.5">

                        <i class="fas fa-sitemap text-blue-600"></i>

                        <input type="text"
                               id="filterDepartment"
                               list="listDepartment"
                               placeholder="Semua Dept"
                               autocomplete="off"
                               class="w-36 border border-slate-300
                                      rounded-lg pl-3 pr-7 py-1.5
                                      text-xs font-bold text-slate-700
                                      focus:outline-none
                                      focus:ring-2 focus:ring-blue-500
                                      bg-white">

                    </div>


                    <datalist id="listDepartment">

                        @foreach($departmentList as $department)

                            <option value="{{ $department }}"></option>

                        @endforeach

                    </datalist>

                </div>


                {{-- =================================================
                     RESET FILTER
                     ================================================= --}}

                <button type="button"
                        id="resetFilter"
                        class="px-3 py-1.5 rounded-lg
                               bg-slate-200 hover:bg-slate-300
                               text-slate-700 text-xs font-bold
                               transition-all flex items-center
                               gap-1.5 cursor-pointer">

                    <i class="fas fa-sync-alt"></i>

                    Reset

                </button>


                {{-- =================================================
                     JUMLAH DATA
                     ================================================= --}}

                <div class="flex items-center gap-1.5
                            bg-blue-50 border border-blue-200
                            px-3 py-1.5 rounded-lg">

                    <span class="text-xs font-bold
                                 text-slate-700 uppercase">

                        Data Tampil:

                    </span>

                    <span id="jumlahDataTampil"
                          class="text-xs font-extrabold
                                 text-blue-700">

                        {{ count($employees ?? []) }}

                    </span>

                </div>

            </div>


            {{-- =====================================================
                 TABEL PENILAIAN
                 ===================================================== --}}

            <div class="overflow-x-auto
                        max-h-[650px]
                        overflow-y-auto">

                <table class="text-xs whitespace-nowrap
                              border-collapse w-max">

                    <thead>

                        {{-- =================================================
                             HEADER LEVEL 1
                             ================================================= --}}

                        <tr class="text-white text-center
                                   font-black sticky top-0 z-20">

                            <th colspan="13"
                                class="border border-slate-700
                                       p-3 text-left uppercase"
                                style="background-color:#1e3a8a !important;
                                       color:#ffffff !important;">

                                Data Karyawan

                            </th>


                            @php

                                $aspekList = [
                                    '1. DISIPLIN',
                                    '2. KUALITAS KERJA',
                                    '3. PRODUKTIVITAS',
                                    '4. KERJASAMA',
                                    '5. TANGGUNG JAWAB'
                                ];

                            @endphp


                            @foreach($aspekList as $aspek)

                                <th colspan="5"
                                    class="border border-slate-700
                                           p-2 uppercase text-center"
                                    style="background-color:#172554 !important;
                                           color:#ffffff !important;">

                                    {{ $aspek }}

                                </th>

                            @endforeach


                            <th colspan="1"
                                class="border border-slate-700
                                       p-2 uppercase text-center"
                                style="background-color:#0369a1 !important;
                                       color:#ffffff !important;">

                                Hasil Akhir

                            </th>


                            <th colspan="2"
                                class="border border-slate-700
                                       p-2 uppercase text-center"
                                style="background-color:#1e293b !important;
                                       color:#ffffff !important;">

                                6. Yang Perlu Dipertahankan

                            </th>


                            <th rowspan="2"
                                class="border border-slate-700
                                       p-3 text-center align-middle
                                       min-w-[180px]"
                                style="background-color:#0f172a !important;
                                       color:#ffffff !important;">

                                KET

                            </th>

                        </tr>


                        {{-- =================================================
                             HEADER LEVEL 2
                             ================================================= --}}

                        <tr class="font-black text-center
                                   uppercase sticky top-[41px] z-20">


                            @foreach([

                                'No',
                                'NIK',
                                'Status',
                                'Employee Name',
                                'Jumlah',
                                'Position Text',
                                'Pers Subarea Text',
                                'TMK',
                                'Length Of Service',
                                'Gender',
                                'Marital Status',
                                'Vendor OS',
                                'User'

                            ] as $header)

                                <th class="border border-slate-300
                                           p-2.5"
                                    style="background-color:#f1f5f9 !important;
                                           color:#334155 !important;">

                                    {{ $header }}

                                </th>

                            @endforeach


                            @for($a = 0; $a < 5; $a++)

                                <th class="border border-slate-400
                                           p-1 min-w-[34px]"
                                    style="background-color:#dc2626 !important;
                                           color:#ffffff !important;">

                                    1<br>

                                    <span class="text-[8px] font-normal">
                                        KRNG
                                    </span>

                                </th>


                                <th class="border border-slate-400
                                           p-1 min-w-[34px]"
                                    style="background-color:#f97316 !important;
                                           color:#ffffff !important;">

                                    2<br>

                                    <span class="text-[8px] font-normal">
                                        CKP
                                    </span>

                                </th>


                                <th class="border border-slate-400
                                           p-1 min-w-[34px]"
                                    style="background-color:#d97706 !important;
                                           color:#ffffff !important;">

                                    3<br>

                                    <span class="text-[8px] font-normal">
                                        BAIK
                                    </span>

                                </th>


                                <th class="border border-slate-400
                                           p-1 min-w-[34px]"
                                    style="background-color:#059669 !important;
                                           color:#ffffff !important;">

                                    4<br>

                                    <span class="text-[8px] font-normal">
                                        SBK
                                    </span>

                                </th>


                                <th class="border border-slate-400
                                           p-1 min-w-[34px]"
                                    style="background-color:#2563eb !important;
                                           color:#ffffff !important;">

                                    5<br>

                                    <span class="text-[8px] font-normal">
                                        ISTM
                                    </span>

                                </th>

                            @endfor


                            <th class="border border-slate-400
                                       p-1.5 min-w-[70px]"
                                style="background-color:#0284c7 !important;
                                       color:#ffffff !important;">

                                Skor

                            </th>


                            <th class="border border-slate-400
                                       p-1.5 min-w-[45px]"
                                style="background-color:#047857 !important;
                                       color:#ffffff !important;">

                                YA

                            </th>


                            <th class="border border-slate-400
                                       p-1.5 min-w-[65px]"
                                style="background-color:#b91c1c !important;
                                       color:#ffffff !important;">

                                CUT OFF

                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($employees ?? [] as $empIndex => $emp)

                        @php

                            $isObj = is_object($emp);

                            $namaValue = $isObj
                                ? ($emp->employee_name
                                    ?? $emp->nama
                                    ?? '')
                                : ($emp['employee_name']
                                    ?? $emp['nama']
                                    ?? '');

                            $userValue = $isObj
                                ? ($emp->user ?? '')
                                : ($emp['user'] ?? '');

                            $vendorValue = $isObj
                                ? ($emp->vendor_os ?? '')
                                : ($emp['vendor_os'] ?? '');

                            $departmentValue = $isObj
                                ? ($emp->pers_subarea_text
                                    ?? $emp->subarea
                                    ?? '')
                                : ($emp['pers_subarea_text']
                                    ?? $emp['subarea']
                                    ?? '');

                            $nikValue = $isObj
                                ? ($emp->nik ?? '')
                                : ($emp['nik'] ?? '');

                            $positionValue = $isObj
                                ? ($emp->position_text
                                    ?? $emp->position
                                    ?? $emp->jabatan
                                    ?? '')
                                : ($emp['position_text']
                                    ?? $emp['position']
                                    ?? $emp['jabatan']
                                    ?? '');

                            $jumlahValue = $isObj
                                ? ($emp->jumlah ?? '-')
                                : ($emp['jumlah'] ?? '-');

                            $tmkValue = $isObj
                                ? ($emp->tmk ?? '-')
                                : ($emp['tmk'] ?? '-');

                            $losValue = $isObj
                                ? ($emp->length_of_service
                                    ?? $emp->los
                                    ?? '-')
                                : ($emp['length_of_service']
                                    ?? $emp['los']
                                    ?? '-');

                            $genderValue = $isObj
                                ? ($emp->gender ?? '-')
                                : ($emp['gender'] ?? '-');

                            $maritalValue = $isObj
                                ? ($emp->marital_status ?? '-')
                                : ($emp['marital_status'] ?? '-');

                            $nikClean = trim((string) $nikValue);
                            $existing = $initialPenilaians->get($nikClean);
                            $hasExisting = !empty($existing);
                            $existingScores = $existing ? ($existing->aspek_scores ?? [
                                $existing->skor_disiplin ?? 0,
                                $existing->skor_kualitas ?? 0,
                                $existing->skor_produktivitas ?? 0,
                                $existing->skor_kerjasama ?? 0,
                                $existing->skor_tanggung_jawab ?? 0
                            ]) : [0, 0, 0, 0, 0];
                            $existingTotal = $existing ? ($existing->total_skor ?? 0) : 0;
                            $existingDipertahankan = $existing ? ($existing->dipertahankan ?? '') : '';
                            $existingKeterangan = $existing ? ($existing->keterangan ?? '') : '';

                            $isLocked = ($hasExisting && !$isSuperAdmin);

                        @endphp


                        <tr class="emp-row
                                   hover:bg-blue-50/80
                                   transition-colors
                                   {{ $isLocked ? 'bg-amber-50/40' : ($loop->even ? 'bg-slate-50/50' : 'bg-white') }}"

                            data-nik="{{ strtolower(trim($nikValue)) }}"

                            data-nama="{{ strtolower(trim($namaValue)) }}"

                            data-vendor="{{ strtolower(trim($vendorValue)) }}"

                            data-department="{{ strtolower(trim($departmentValue)) }}"

                            data-has-existing="{{ $hasExisting ? '1' : '0' }}"

                            data-locked="{{ $isLocked ? '1' : '0' }}">


                            {{-- =================================================
                                 DATA KARYAWAN
                                 ================================================= --}}

                            <td class="border p-2 text-center font-bold">
                                {{ $loop->iteration }}
                            </td>


                            <td class="border p-2 text-center font-medium">
                                {{ $nikValue ?: '-' }}
                            </td>

                            {{-- STATUS PENILAIAN --}}
                            <td class="border p-2 text-center status-col">
                                @if($hasExisting)
                                    @if($isSuperAdmin)
                                        <span class="status-badge px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-200 inline-flex items-center gap-1">
                                            <i class="fas fa-edit text-blue-600"></i> Dinilai (Bisa Diubah)
                                        </span>
                                    @else
                                        <span class="status-badge px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center gap-1">
                                            <i class="fas fa-lock text-amber-600"></i> Terkunci
                                        </span>
                                    @endif
                                @else
                                    <span class="status-badge px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200 inline-flex items-center gap-1">
                                        <i class="fas fa-pencil-alt text-slate-400"></i> Belum Dinilai
                                    </span>
                                @endif
                            </td>


                            <td class="border p-2 font-bold text-slate-800">
                                {{ $namaValue ?: '-' }}
                            </td>


                            <td class="border p-2 text-center">
                                {{ $jumlahValue }}
                            </td>


                            <td class="border p-2">
                                {{ $positionValue ?: '-' }}
                            </td>


                            <td class="border p-2">
                                {{ $departmentValue ?: '-' }}
                            </td>


                            <td class="border p-2 text-center">
                                {{ $tmkValue }}
                            </td>


                            <td class="border p-2 text-center">
                                {{ $losValue }}
                            </td>


                            <td class="border p-2 text-center">
                                {{ $genderValue }}
                            </td>


                            <td class="border p-2 text-center">
                                {{ $maritalValue }}
                            </td>


                            <td class="border p-2 font-semibold text-slate-600">
                                {{ $vendorValue ?: '-' }}
                            </td>


                            <td class="border p-2 font-semibold text-blue-700">
                                {{ $userValue ?: '-' }}
                            </td>


                            {{-- =================================================
                                 DATA TERSEMBUNYI UNTUK FORM
                                 ================================================= --}}

                            <input type="hidden"
                                   name="penilaian[{{ $empIndex }}][nik]"
                                   value="{{ $nikValue }}">


                            <input type="hidden"
                                   name="penilaian[{{ $empIndex }}][employee_name]"
                                   value="{{ $namaValue }}">


                            <input type="hidden"
                                   name="penilaian[{{ $empIndex }}][user]"
                                   value="{{ $userValue }}">


                            <input type="hidden"
                                   name="penilaian[{{ $empIndex }}][vendor_os]"
                                   value="{{ $vendorValue }}">


                            <input type="hidden"
                                   name="penilaian[{{ $empIndex }}][position_text]"
                                   value="{{ $positionValue }}">


                            <input type="hidden"
                                   name="penilaian[{{ $empIndex }}][pers_subarea_text]"
                                   value="{{ $departmentValue }}">


                            {{-- =================================================
                                 RADIO PENILAIAN
                                 ================================================= --}}

                            @for($aspekIdx = 0; $aspekIdx < 5; $aspekIdx++)
                                @php
                                    $currScore = (int) ($existingScores[$aspekIdx] ?? 0);
                                @endphp

                                @for($i = 1; $i <= 5; $i++)

                                    <td class="border p-1 text-center
                                               {{ $i === 5
                                                    ? 'border-r-2 border-r-slate-400'
                                                    : '' }}">

                                        <input type="radio"
                                               name="penilaian[{{ $empIndex }}][aspek][{{ $aspekIdx }}]"
                                               value="{{ $i }}"
                                               class="skor-radio w-3.5 h-3.5
                                                      cursor-pointer
                                                      accent-blue-600"
                                               {{ $currScore === $i ? 'checked' : '' }}
                                               {{ $isLocked ? 'disabled' : '' }}>

                                    </td>

                                @endfor

                            @endfor


                            {{-- =================================================
                                 TOTAL SKOR
                                 ================================================= --}}

                            <td class="border p-1 text-center
                                       font-extrabold text-blue-700
                                       bg-blue-50/50 text-sm
                                       border-r-2 border-r-slate-400">

                                <span class="total-skor">
                                    {{ $existingTotal }}
                                </span>

                                <input type="hidden"
                                       name="penilaian[{{ $empIndex }}][total_skor]"
                                       class="input-total-skor"
                                       value="{{ $existingTotal }}">

                            </td>


                            {{-- =================================================
                                 YANG PERLU DIPERTAHANKAN
                                 ================================================= --}}

                            <td class="border p-1 text-center
                                       bg-emerald-50/30">

                                <input type="radio"
                                       name="penilaian[{{ $empIndex }}][dipertahankan]"
                                       value="YA"
                                       class="radio-dipertahankan w-3.5 h-3.5
                                              cursor-pointer
                                              accent-emerald-600"
                                       {{ $existingDipertahankan === 'YA' ? 'checked' : '' }}
                                       {{ $isLocked ? 'disabled' : '' }}>

                            </td>


                            <td class="border p-1 text-center
                                       bg-red-50/30">

                                <input type="radio"
                                       name="penilaian[{{ $empIndex }}][dipertahankan]"
                                       value="CUT OFF"
                                       class="radio-dipertahankan w-3.5 h-3.5
                                              cursor-pointer
                                              accent-red-600"
                                       {{ $existingDipertahankan === 'CUT OFF' ? 'checked' : '' }}
                                       {{ $isLocked ? 'disabled' : '' }}>

                            </td>


                            {{-- =================================================
                                 KETERANGAN
                                 ================================================= --}}

                            <td class="border p-1.5">

                                <input type="text"
                                       name="penilaian[{{ $empIndex }}][keterangan]"
                                       value="{{ $existingKeterangan }}"
                                       class="input-keterangan w-full border rounded
                                              p-1.5 text-xs
                                              focus:outline-none
                                              focus:ring-1
                                              focus:ring-blue-500 {{ $isLocked ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : '' }}"
                                       placeholder="Keterangan..."
                                       {{ $isLocked ? 'readonly' : '' }}>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="40"
                                class="text-center p-10
                                       font-bold text-slate-400">

                                Tidak ada data karyawan

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </form>

</div>


{{-- =========================================================
     JAVASCRIPT ASPEK PENILAIAN
     ========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    hitungSkor();

    initFilter();

    initSubmit();

});


/*
|--------------------------------------------------------------------------
| HITUNG TOTAL SKOR
|--------------------------------------------------------------------------
*/

function hitungSkor() {

    const rows = document.querySelectorAll('.emp-row');

    rows.forEach(function(row) {

        const radios =
            row.querySelectorAll('.skor-radio:checked');

        let total = 0;

        radios.forEach(function(radio) {

            total += parseInt(radio.value, 10) || 0;

        });


        const totalSkor =
            row.querySelector('.total-skor');

        const inputTotal =
            row.querySelector('.input-total-skor');


        if (totalSkor) {

            totalSkor.innerText = total;

        }


        if (inputTotal) {

            inputTotal.value = total;

        }

    });

}


/*
|--------------------------------------------------------------------------
| PERUBAHAN RADIO SKOR
|--------------------------------------------------------------------------
*/

document.addEventListener('change', function(e) {

    if (
        e.target &&
        e.target.classList.contains('skor-radio')
    ) {

        hitungSkor();

    }

});


/*
|--------------------------------------------------------------------------
| FILTER KARYAWAN
|--------------------------------------------------------------------------
*/

function filterKaryawan() {

    const searchNik =
        document.getElementById('inputSearchNik')
        ?.value
        .toLowerCase()
        .trim() || '';


    const searchNama =
        document.getElementById('inputSearchNama')
        ?.value
        .toLowerCase()
        .trim() || '';


    const vendor =
        document.getElementById('tableFilterVendor')
        ?.value
        .toLowerCase()
        .trim() || '';


    const department =
        document.getElementById('filterDepartment')
        ?.value
        .toLowerCase()
        .trim() || '';


    const rows =
        document.querySelectorAll('.emp-row');


    let jumlahTampil = 0;


    rows.forEach(function(row) {

        const nik =
            row.dataset.nik || '';

        const nama =
            row.dataset.nama || '';

        const rowVendor =
            row.dataset.vendor || '';

        const rowDepartment =
            row.dataset.department || '';


        const cocokNik =
            !searchNik ||
            nik.includes(searchNik);


        const cocokNama =
            !searchNama ||
            nama.includes(searchNama);


        const cocokVendor =
            !vendor ||
            rowVendor.includes(vendor);


        const cocokDepartment =
            !department ||
            rowDepartment.includes(department);


        if (
            cocokNik &&
            cocokNama &&
            cocokVendor &&
            cocokDepartment
        ) {

            row.style.display = '';

            jumlahTampil++;

        } else {

            row.style.display = 'none';

        }

    });


    const jumlah =
        document.getElementById('jumlahDataTampil');


    if (jumlah) {

        jumlah.innerText = jumlahTampil;

    }

}


/*
|--------------------------------------------------------------------------
| AKTIFKAN FILTER
|--------------------------------------------------------------------------
*/

function initFilter() {

    const ids = [

        'inputSearchNik',
        'inputSearchNama',
        'tableFilterVendor',
        'filterDepartment'

    ];


    ids.forEach(function(id) {

        const element =
            document.getElementById(id);


        if (element) {

            element.addEventListener(
                'input',
                filterKaryawan
            );


            element.addEventListener(
                'change',
                filterKaryawan
            );

        }

    });


    const reset =
        document.getElementById('resetFilter');


    if (reset) {

        reset.addEventListener(
            'click',
            resetFilterKaryawan
        );

    }

}


/*
|--------------------------------------------------------------------------
| RESET FILTER
|--------------------------------------------------------------------------
*/

function resetFilterKaryawan() {

    const ids = [

        'inputSearchNik',
        'inputSearchNama',
        'tableFilterVendor',
        'filterDepartment'

    ];


    ids.forEach(function(id) {

        const element =
            document.getElementById(id);


        if (element) {

            element.value = '';

        }

    });


    filterKaryawan();

}


/*
|--------------------------------------------------------------------------
| FILTER ADMIN
|--------------------------------------------------------------------------
*/

function applyAdminFilter() {

    const user =
        document.getElementById(
            'adminFilterUser'
        )?.value || '';


    const departemen =
        document.getElementById(
            'adminFilterDepartemen'
        )?.value || '';


    const vendor =
        document.getElementById(
            'adminFilterVendor'
        )?.value || '';


    const url =
        new URL(window.location.href);


    if (user) {

        url.searchParams.set(
            'user',
            user
        );

    } else {

        url.searchParams.delete('user');

    }


    if (departemen) {

        url.searchParams.set(
            'departemen',
            departemen
        );

    } else {

        url.searchParams.delete('departemen');

    }


    if (vendor) {

        url.searchParams.set(
            'vendor',
            vendor
        );

    } else {

        url.searchParams.delete('vendor');

    }


    window.location.href =
        url.toString();

}


/*
|--------------------------------------------------------------------------
| TAMPILKAN / SEMBUNYIKAN FILTER
|--------------------------------------------------------------------------
*/

function toggleFilter() {

    const content =
        document.getElementById(
            'filterFormContent'
        );


    const text =
        document.getElementById(
            'hideFilterText'
        );


    if (!content) return;


    if (
        content.style.display === 'none'
    ) {

        content.style.display = 'grid';

        if (text) {

            text.innerText = 'Hide Filter';

        }

    } else {

        content.style.display = 'none';

        if (text) {

            text.innerText = 'Show Filter';

        }

    }

}


/*
|--------------------------------------------------------------------------
| SIMPAN PENILAIAN
|--------------------------------------------------------------------------
*/

function initSubmit() {

    const form =
        document.getElementById(
            'formSimpanPenilaian'
        );


    const button =
        document.getElementById(
            'btnSimpanPenilaian'
        );


    const buttonText =
        document.getElementById(
            'btnText'
        );


    if (!form) return;


    form.addEventListener(
        'submit',
        function(e) {

            e.preventDefault();


            if (button) {

                button.disabled = true;

            }


            if (buttonText) {

                buttonText.innerText =
                    'Menyimpan Penilaian...';

            }


            const formData =
                new FormData(form);


            const token =
                form.querySelector(
                    'input[name="_token"]'
                )?.value || '';


            fetch(
                form.action,
                {

                    method: 'POST',

                    headers: {

                        'X-CSRF-TOKEN': token,

                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'

                    },

                    body: formData

                }

            )

            .then(async function(response) {

                const type =
                    response.headers
                    .get('content-type');


                if (
                    !type ||
                    !type.includes(
                        'application/json'
                    )
                ) {

                    const text =
                        await response.text();

                    console.error(
                        'Server response:',
                        text
                    );

                    throw new Error(
                        'Server tidak memberikan respons JSON.'
                    );

                }


                const data =
                    await response.json();


                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Gagal menyimpan data penilaian.'
                    );

                }


                return data;

            })


            .then(function(data) {

                Swal.fire({

                    toast: true,

                    position: 'top-end',

                    icon: 'success',

                    title:
                        data.message ||
                        'Penilaian berhasil disimpan!',

                    showConfirmButton: false,

                    timer: 3000

                });

                reloadPenilaianData();

            })


            .catch(function(error) {

                console.error(
                    'Submit Error:',
                    error
                );


                Swal.fire({

                    icon: 'error',

                    title: 'Gagal Menyimpan',

                    text: error.message,

                    confirmButtonColor:
                        '#059669'

                });

            })


            .finally(function() {

                if (button) {

                    button.disabled = false;

                }


                if (buttonText) {

                    buttonText.innerText =
                        'Simpan Penilaian Bulanan';

                }

            });

        }

    );

    function updateExportLinksPenilaian() {
        const bulan = document.getElementById('inputBulanPenilaian')?.value || '';
        const tahun = document.getElementById('inputTahunPenilaian')?.value || '';
        const pdfBtn = document.getElementById('btnExportPdfPenilaian');
        const excelBtn = document.getElementById('btnExportExcelPenilaian');
        const pdfUrl = "{{ route('penilaian.export.pdf') }}?bulan=" + encodeURIComponent(bulan) + "&tahun=" + encodeURIComponent(tahun);
        const excelUrl = "{{ route('penilaian.export.excel') }}?bulan=" + encodeURIComponent(bulan) + "&tahun=" + encodeURIComponent(tahun);
        if (pdfBtn) pdfBtn.href = pdfUrl;
        if (excelBtn) excelBtn.href = excelUrl;
    }

    function reloadPenilaianData() {
        const bulan = document.getElementById('inputBulanPenilaian')?.value || '';
        const tahun = document.getElementById('inputTahunPenilaian')?.value || '';
        if (!bulan || !tahun) return;

        fetch(`{{ route('penilaian.data') }}?bulan=${encodeURIComponent(bulan)}&tahun=${encodeURIComponent(tahun)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success) return;
            const data = res.data || {};
            const isSuperAdmin = res.is_superadmin;

            const rows = document.querySelectorAll('.emp-row');
            rows.forEach(row => {
                const nik = (row.dataset.nik || '').trim();
                const record = data[nik] || data[nik.toUpperCase()] || null;

                const radios = row.querySelectorAll('.skor-radio');
                const radioDipertahankan = row.querySelectorAll('.radio-dipertahankan');
                const inputKet = row.querySelector('.input-keterangan');
                const totalSkorSpan = row.querySelector('.total-skor');
                const totalSkorInput = row.querySelector('.input-total-skor');
                const statusCol = row.querySelector('.status-col');

                if (record) {
                    const scores = record.aspek_scores || [
                        record.skor_disiplin || 0,
                        record.skor_kualitas || 0,
                        record.skor_produktivitas || 0,
                        record.skor_kerjasama || 0,
                        record.skor_tanggung_jawab || 0
                    ];

                    for (let asp = 0; asp < 5; asp++) {
                        const score = parseInt(scores[asp], 10) || 0;
                        for (let v = 1; v <= 5; v++) {
                            const r = row.querySelector(`.skor-radio[name*="[aspek][${asp}]"][value="${v}"]`);
                            if (r) {
                                r.checked = (score === v);
                                r.disabled = !isSuperAdmin;
                            }
                        }
                    }

                    radioDipertahankan.forEach(r => {
                        r.checked = (r.value === record.dipertahankan);
                        r.disabled = !isSuperAdmin;
                    });

                    if (inputKet) {
                        inputKet.value = record.keterangan || '';
                        inputKet.readOnly = !isSuperAdmin;
                        if (!isSuperAdmin) {
                            inputKet.classList.add('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');
                        } else {
                            inputKet.classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');
                        }
                    }

                    const total = record.total_skor || 0;
                    if (totalSkorSpan) totalSkorSpan.innerText = total;
                    if (totalSkorInput) totalSkorInput.value = total;

                    if (isSuperAdmin) {
                        if (statusCol) {
                            statusCol.innerHTML = `
                                <span class="status-badge px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-200 inline-flex items-center gap-1">
                                    <i class="fas fa-edit text-blue-600"></i> Dinilai (Bisa Diubah)
                                </span>`;
                        }
                        row.classList.remove('bg-amber-50/40');
                    } else {
                        if (statusCol) {
                            statusCol.innerHTML = `
                                <span class="status-badge px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center gap-1">
                                    <i class="fas fa-lock text-amber-600"></i> Terkunci
                                </span>`;
                        }
                        row.classList.add('bg-amber-50/40');
                    }
                } else {
                    radios.forEach(r => {
                        r.checked = false;
                        r.disabled = false;
                    });
                    radioDipertahankan.forEach(r => {
                        r.checked = false;
                        r.disabled = false;
                    });
                    if (inputKet) {
                        inputKet.value = '';
                        inputKet.readOnly = false;
                        inputKet.classList.remove('bg-slate-100', 'text-slate-500', 'cursor-not-allowed');
                    }
                    if (totalSkorSpan) totalSkorSpan.innerText = 0;
                    if (totalSkorInput) totalSkorInput.value = 0;

                    if (statusCol) {
                        statusCol.innerHTML = `
                            <span class="status-badge px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200 inline-flex items-center gap-1">
                                <i class="fas fa-pencil-alt text-slate-400"></i> Belum Dinilai
                            </span>`;
                    }
                    row.classList.remove('bg-amber-50/40');
                }
            });
            hitungSkor();
        })
        .catch(err => console.error('Gagal memuat data penilaian:', err));
    }

    const inBulan = document.getElementById('inputBulanPenilaian');
    const inTahun = document.getElementById('inputTahunPenilaian');
    if (inBulan) {
        inBulan.addEventListener('input', function() {
            updateExportLinksPenilaian();
            reloadPenilaianData();
        });
        inBulan.addEventListener('change', function() {
            updateExportLinksPenilaian();
            reloadPenilaianData();
        });
    }
    if (inTahun) {
        inTahun.addEventListener('input', function() {
            updateExportLinksPenilaian();
            reloadPenilaianData();
        });
        inTahun.addEventListener('change', function() {
            updateExportLinksPenilaian();
            reloadPenilaianData();
        });
    }
    updateExportLinksPenilaian();
}

</script>