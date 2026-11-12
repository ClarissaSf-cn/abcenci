<div id="tab-karyawan" class="tab-content hidden h-full flex flex-col">
    <div class="glass-card rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col flex-1 min-h-0 h-full bg-white">

        {{-- SECTION 1: Header Utama (Judul & Tombol Aksi Utama) --}}
        <div class="px-5 sm:px-7 py-3.5 border-b border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 leading-tight">Database Master Karyawan</h3>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span id="totalKaryawan">{{ isset($employees) && method_exists($employees, 'total') ? $employees->total() : (isset($employees) ? $employees->count() : 0) }}</span> Record
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola data induk profil karyawan, informasi penugasan, dan jadwal shift</p>
            </div>

            {{-- Action Buttons: Update Data & Tambah Karyawan (Selalu Utuh & Tidak Terpotong) --}}
            <div class="flex items-center gap-2.5 shrink-0">
                {{-- Tombol Update Data (Import Excel) --}}
                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="m-0 shrink-0">
                    @csrf
                    <label class="px-4 rounded-xl text-white text-xs font-bold transition-all shadow-sm hover:opacity-90 active:scale-[0.98] flex items-center gap-2 cursor-pointer m-0 shrink-0" style="background-color: #16a34a !important; height: 38px;" title="Import file Data OS.xlsx">
                        <i class="fas fa-file-excel text-sm"></i>
                        <span>Update Data</span>
                        <input type="file" name="file_excel" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>

                {{-- Tombol Tambah Karyawan --}}
                <button onclick="openAddEmployeeModal()" class="px-4 rounded-xl text-white text-xs font-bold transition-all shadow-sm hover:opacity-95 active:scale-[0.98] flex items-center gap-2 cursor-pointer shrink-0" style="background-color: #004a99 !important; height: 38px;" title="Tambah Karyawan Baru">
                    <i class="fas fa-user-plus text-sm"></i>
                    <span>Tambah Karyawan</span>
                </button>
            </div>
        </div>

        {{-- SECTION 2: Toolbar Pencarian & Filter Tabel --}}
        <div class="px-5 sm:px-7 py-2.5 bg-slate-50 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-2.5 shrink-0">
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap flex-1">
                {{-- Search Box --}}
                <div class="relative flex items-center min-w-[200px] sm:min-w-[240px] max-w-sm flex-1">
                    <i class="fas fa-search text-slate-400 text-xs absolute left-3.5 pointer-events-none"></i>
                    <input type="text" 
                           id="employeeSearch" 
                           onkeyup="applyEmployeeFilters()" 
                           oninput="applyEmployeeFilters()" 
                           placeholder="Cari berdasarkan NIK atau Nama..." 
                           class="w-full pl-9 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 shadow-sm transition-all"
                           style="height: 38px;">
                </div>

                @php
                    $jabatans = isset($employees) ? $employees->map(function($e) {
                        return trim($e->position_text ?? $e->jabatan ?? '');
                    })->filter(function($val) {
                        return !empty($val) && $val !== '-';
                    })->unique()->sort()->values() : collect();
                @endphp

                {{-- Searchable Jabatan Dropdown --}}
                <div class="relative z-40 shrink-0" id="jabatanDropdownContainer">
                    <div class="flex items-center bg-white border border-slate-300 focus-within:border-blue-500 rounded-xl px-3 shadow-sm transition-all min-w-[160px] sm:min-w-[180px]" style="height: 38px;">
                        <i class="fas fa-briefcase text-slate-400 mr-2 text-xs shrink-0"></i>
                        <input type="text" 
                               id="filterJabatan" 
                               placeholder="Cari Jabatan" 
                               autocomplete="off" 
                               onfocus="openJabatanDropdown()" 
                               onclick="openJabatanDropdown()" 
                               oninput="handleJabatanInput(this.value)" 
                               onkeydown="handleJabatanKeydown(event)" 
                               class="text-xs p-0 border-none outline-none w-full font-semibold text-slate-800 bg-transparent placeholder-slate-400 focus:ring-0">
                        <button type="button" 
                                id="btnClearJabatan" 
                                onclick="clearJabatanFilter(event)" 
                                class="hidden text-slate-400 hover:text-red-500 mr-1 text-xs transition-colors cursor-pointer" 
                                title="Reset Jabatan">
                            <i class="fas fa-times-circle"></i>
                        </button>
                        <button type="button" 
                                onclick="toggleJabatanDropdown(event)" 
                                class="text-slate-400 hover:text-slate-600 focus:outline-none text-xs cursor-pointer shrink-0">
                            <i class="fas fa-chevron-down transition-transform duration-200" id="jabatanChevron"></i>
                        </button>
                    </div>

                    {{-- Dropdown List Options --}}
                    <div id="jabatanMenu" class="hidden absolute left-0 top-full mt-1.5 w-64 max-w-[calc(100vw-2rem)] max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-2xl shadow-xl z-50 divide-y divide-slate-100 py-1 text-left">
                        <div onclick="selectJabatan('')" 
                             class="jabatan-item px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex items-center justify-between transition-colors" 
                             data-value="">
                            <span>Semua Jabatan</span>
                            <i class="fas fa-check text-blue-600 text-xs hidden check-mark"></i>
                        </div>
                        @foreach($jabatans as $jab)
                            <div onclick="selectJabatan('{{ addslashes($jab) }}')" 
                                 class="jabatan-item px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex items-center justify-between transition-colors" 
                                 data-value="{{ strtolower($jab) }}">
                                <span>{{ $jab }}</span>
                                <i class="fas fa-check text-blue-600 text-xs hidden check-mark"></i>
                            </div>
                        @endforeach
                        <div id="jabatanEmptyMsg" class="hidden px-3 py-3 text-center text-xs text-slate-400 font-medium italic">
                            Jabatan tidak ditemukan
                        </div>
                    </div>
                </div>

                {{-- Sort Dropdown --}}
                <div class="relative flex items-center shrink-0">
                    <select id="sortKaryawan" onchange="applyEmployeeFilters()" class="text-xs pl-3 pr-8 rounded-xl bg-white border border-slate-300 outline-none shadow-sm cursor-pointer font-semibold text-slate-700 focus:border-blue-500 transition-all appearance-none" style="height: 38px;">
                        <option value="default">Urutkan: Default</option>
                        <option value="az">Nama A - Z</option>
                        <option value="za">Nama Z - A</option>
                        <option value="newest">TMK Terbaru</option>
                        <option value="oldest">TMK Terlama</option>
                    </select>
                    <i class="fas fa-chevron-down text-slate-400 text-[9px] absolute right-3 pointer-events-none"></i>
                </div>

                {{-- Tombol Reset Filter --}}
                <button type="button" id="btnResetFilters" onclick="resetAllEmployeeFilters()" class="hidden px-3 rounded-xl bg-slate-200/90 hover:bg-red-50 hover:text-red-600 text-slate-600 text-xs font-bold items-center gap-1.5 transition-all cursor-pointer shrink-0" style="height: 38px;" title="Reset Semua Filter">
                    <i class="fas fa-rotate-left text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>

            {{-- Right Hint / Indicator --}}
            <div class="text-[11px] text-slate-400 font-medium hidden sm:flex items-center gap-1.5 shrink-0">
                <i class="fas fa-sliders-h text-slate-300 text-xs"></i>
                <span>Filter & pencarian data tabel</span>
            </div>
        </div>

        {{-- Table Container with Horizontal & Vertical Scroll --}}
        <div class="overflow-auto flex-1 bg-white relative w-full h-0 table-scroll-container">
            <table id="tableEmployee" class="w-max min-w-full text-left text-xs whitespace-nowrap">
                <thead class="sticky top-0 z-30 shadow-xs">
                    <tr>
                        <th class="emp-col-no">NO</th>
                        <th class="emp-col-nik">NIK</th>
                        <th class="emp-col-nama">NAMA KARYAWAN</th>

                        <th class="p-3.5 text-center">USER</th>
                        <th class="p-3.5 text-center">AKSI</th>
                        <th class="p-3.5 text-center">JUMLAH</th>
                        <th class="p-3.5">POSITION TEXT</th>
                        <th class="p-3.5">EMPLOYEE GROUP</th>
                        <th class="p-3.5">PERS SUBAREA TEXT</th>
                        <th class="p-3.5">SUBSUBAREA</th>
                        <th class="p-3.5">TMK</th>
                        <th class="p-3.5">LENGTH OF SERVICE</th>
                        <th class="p-3.5">GENDER</th>
                        <th class="p-3.5">MARITAL STATUS</th>
                        <th class="p-3.5">BIRTHPLACE</th>
                        <th class="p-3.5">BIRTHDATE</th>
                        <th class="p-3.5">AGE</th>
                        <th class="p-3.5">RANGE UMUR</th>
                        <th class="p-3.5">RELIGION</th>
                        <th class="p-3.5">JAMSOSTEK ID</th>
                        <th class="p-3.5">BPJS ID</th>
                        <th class="p-3.5">PERSONAL TAX ID</th>
                        <th class="p-3.5">TAX STATUS</th>
                        <th class="p-3.5">NUMBER OF CHILD</th>
                        <th class="p-3.5">KTP - NOMOR KTP</th>
                        <th class="p-3.5">KTP - ALAMAT KTP</th>
                        <th class="p-3.5">REGION</th>
                        <th class="p-3.5">POSTAL CODE</th>
                        <th class="p-3.5">NO. HP</th>
                        <th class="p-3.5">EMAIL</th>
                        <th class="p-3.5">ESGRP TEXT</th>
                        <th class="p-3.5">LEVEL</th>
                        <th class="p-3.5">POSITION ID</th>
                        <th class="p-3.5">ORG UNIT ID</th>
                        <th class="p-3.5">ORG UNIT TEXT</th>
                        <th class="p-3.5">PERS SUBAREA ID</th>
                        <th class="p-3.5">ORG KEY ID</th>
                        <th class="p-3.5">ORG KEY TEXT</th>
                        <th class="p-3.5">BANTU OPR/HLP</th>
                        <th class="p-3.5">REGU / LINE</th>
                        <th class="p-3.5">LINE</th>
                        <th class="p-3.5">REGU</th>
                        <th class="p-3.5">AREA PROV MARKETING</th>
                        <th class="p-3.5">DISTRIK RAYON</th>
                        <th class="p-3.5">VENDOR OS</th>
                        <th class="p-3.5">COST CENTER</th>
                        <th class="p-3.5">COST CENTER TEXT</th>
                        <th class="p-3.5">EMP STATUS TEXT</th>
                        <th class="p-3.5">EXCUSE</th>
                        <th class="p-3.5">TGL AKHIR KERJA</th>
                        <th class="p-3.5">EDU - LEVEL</th>
                        <th class="p-3.5">EDU - INSTITUTION</th>
                        <th class="p-3.5">EDU - BRANCH</th>
                        <th class="p-3.5">PS TYPE TEXT</th>
                        <th class="p-3.5">STATUS INCENTIVE</th>
                        <th class="p-3.5">BANK KEYS</th>
                        <th class="p-3.5">BANK ACCOUNT NO</th>
                        <th class="p-3.5">VAKSIN 1</th>
                        <th class="p-3.5">MERK V1</th>
                        <th class="p-3.5">VAKSIN 2</th>
                        <th class="p-3.5">MERK V2</th>
                        <th class="p-3.5">VAKSIN 3</th>
                        <th class="p-3.5">MERK V3</th>
                        <th class="p-3.5">WORKSCHEDULE ID</th>
                        <th class="p-3.5">TIPE HARI KERJA</th>
                        <th class="p-3.5">SHIFT/NON SHIFT</th>
                        <th class="p-3.5 text-center bg-amber-500/10 text-amber-800 border-b-2 border-amber-300">NO SIDIK JARI</th>
                        <th class="p-3.5 text-center bg-amber-500/10 text-amber-800 border-b-2 border-amber-300">JAM KERJA</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody" class="divide-y divide-slate-100 text-slate-700 bg-white">
                    @isset($employees)
                        @foreach($employees as $emp)
                        @php
                            $nik = $emp->nik ?? $emp->NIK;
                            $nama = $emp->employee_name ?? $emp->nama;
                            $jabatan = $emp->position_text ?? $emp->jabatan;
                            $sidik = $emp->no_sidik_jari;
                            $jam = $emp->jam_kerja;
                            if (empty($jam) || str_starts_with((string)$jam, '=') || str_contains(strtoupper((string)$jam), 'VLOOKUP')) {
                                $jam = str_contains((string)($emp->tipe_hari_kerja ?? ''), '5') ? '8 Jam' : '7 Jam';
                            }
                        @endphp
                        <tr class="hover:bg-blue-50/50 transition-colors employee-row" data-nama="{{ $nama }}" data-nik="{{ $nik }}" data-jabatan="{{ $jabatan }}" data-tmk="{{ $emp->tmk }}">
                            {{-- Sticky NO --}}
                            <td class="emp-col-no">
                                <span class="text-xs font-semibold text-slate-400">
                                    {{ method_exists($employees, 'firstItem') ? $employees->firstItem() + $loop->index : $loop->iteration }}
                                </span>
                            </td>

                            {{-- Sticky NIK Badge --}}
                            <td class="emp-col-nik">
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-blue-50/90 text-blue-700 border border-blue-100 font-bold text-xs font-mono tracking-wide">
                                    {{ $nik }}
                                </span>
                            </td>

                            {{-- Sticky Nama Karyawan --}}
                            <td class="emp-col-nama">
                                <span class="font-bold text-slate-800 text-xs tracking-normal uppercase">
                                    {{ $nama }}
                                </span>
                            </td>

                            {{-- USER Role Badge --}}
                            <td class="p-3 text-center">
                                @if(!empty($emp->user) && $emp->user !== '-')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100/90 text-[11px] font-semibold">
                                        {{ $emp->user }}
                                    </span>
                                @else
                                    <span class="text-slate-300 font-semibold">-</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" 
                                            onclick="openEditEmployeeModal(this)" 
                                            data-employee="{{ base64_encode(json_encode($emp)) }}" 
                                            class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm cursor-pointer" 
                                            title="Edit Data Karyawan">
                                        <i class="fas fa-pen text-[10px]"></i>
                                    </button>
                                    <form action="{{ route('employees.destroy', $emp->id ?? 0) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan {{ addslashes($nama) }}?');" 
                                          class="inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-7 h-7 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm cursor-pointer" 
                                                title="Hapus Data Karyawan">
                                            <i class="fas fa-trash-can text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <td class="p-3 text-center text-xs font-semibold text-slate-500">{{ $emp->jumlah ?? '1' }}</td>
                            <td class="p-3 text-xs font-medium text-slate-700">{{ $jabatan }}</td>
                            <td class="p-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">
                                    {{ $emp->employee_group ?? 'Outsourcing' }}
                                </span>
                            </td>
                            <td class="p-3 text-xs font-medium text-slate-600">{{ $emp->pers_subarea_text ?? '-' }}</td>
                            <td class="p-3 text-xs font-medium text-slate-700">{{ $emp->sub_subarea ?? $emp->subsubarea ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono font-medium text-blue-600">{{ $emp->tmk ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->length_of_service ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->gender ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->marital_status ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600 uppercase">{{ $emp->birthplace ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-600">{{ $emp->birthdate ?? '-' }}</td>
                            <td class="p-3 text-xs font-semibold text-blue-700">{{ $emp->age ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->range_umur ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600 uppercase">{{ $emp->religion ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->jamsostek_id ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->bpjs_id ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->personal_tax_id ?? $emp->tax_id ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->tax_status ?? '-' }}</td>
                            <td class="p-3 text-xs text-center text-slate-600">{{ $emp->number_of_child ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono font-medium text-slate-700">{{ $emp->ktp_nomor_ktp ?? $emp->ktp_nomor ?? '-' }}</td>
                            <td class="p-3 text-xs whitespace-normal min-w-[200px] text-slate-600">{{ $emp->ktp_alamat_ktp ?? $emp->ktp_alamat ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600 uppercase">{{ $emp->region ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->ktp_postal_code ?? $emp->postal_code ?? '-' }}</td>
                            <td class="p-3 text-xs font-semibold text-slate-800">{{ $emp->no_hp ?? '-' }}</td>
                            <td class="p-3 text-xs font-medium text-blue-600">{{ $emp->email_0030 ?? $emp->email ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->esgrp_text ?? '-' }}</td>
                            <td class="p-3 text-xs uppercase font-semibold text-slate-400">{{ $emp->level ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->position_id ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->org_unit_id ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->org_unit_text ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->pers_subarea_id ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->org_key_id ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->org_key_text ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->bantu_oprhlp_prod_direct ?? $emp->bantu_opr_hlp ?? '-' }}</td>
                            <td class="p-3 text-xs font-medium text-slate-700">{{ $emp->regu_line ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->line ?? '-' }}</td>
                            <td class="p-3 text-xs text-center font-semibold text-slate-800">{{ $emp->regu ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->area_prov_marketing ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->distrik_rayon_marketing ?? '-' }}</td>
                            <td class="p-3 text-xs font-semibold text-slate-600">{{ $emp->vendor_os ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->cost_center ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600 uppercase">{{ $emp->cost_center_text ?? '-' }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 font-semibold text-[11px]">
                                    {{ $emp->emp_status_text ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3 text-xs italic text-slate-500">{{ $emp->excuse ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->tanggal_akhir_kerja ?? '-' }}</td>
                            <td class="p-3 text-xs font-medium text-slate-700">{{ $emp->last_education_level ?? $emp->pendidikan_level ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->last_education_institution ?? $emp->pendidikan_institusi ?? '-' }}</td>
                            <td class="p-3 text-xs italic text-slate-500">{{ $emp->last_education_branch_of_study ?? $emp->pendidikan_studi ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->ps_type0008_text ?? $emp->ps_type_text ?? '-' }}</td>
                            <td class="p-3 text-xs text-center font-medium text-slate-700">{{ $emp->status_incentive ?? '-' }}</td>
                            <td class="p-3 text-xs text-slate-600">{{ $emp->bank_keys ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono font-semibold text-blue-700 tracking-wider">{{ $emp->bank_account_no ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->vaksin1_tanggal ?? '-' }}</td>
                            <td class="p-3 text-xs italic text-slate-500">{{ $emp->vaksin1_merk ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->vaksin2_tanggal ?? '-' }}</td>
                            <td class="p-3 text-xs italic text-slate-500">{{ $emp->vaksin2_merk ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->vaksin3_tanggal ?? '-' }}</td>
                            <td class="p-3 text-xs italic text-slate-500">{{ $emp->vaksin3_merk ?? '-' }}</td>
                            <td class="p-3 text-xs font-mono text-slate-500">{{ $emp->workschedule_id ?? '-' }}</td>
                            <td class="p-3 text-xs font-semibold text-slate-700">{{ $emp->tipe_hari_kerja ?? '-' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-100 text-[11px] font-semibold">
                                    {{ $emp->shift_non_shift ?? $emp->shift_type ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200/80 font-mono font-bold text-xs">
                                    {{ $sidik ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-xs">
                                    {{ $jam ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="71" class="p-16 text-center text-slate-400 font-semibold italic border-none text-xs">
                                <i class="fas fa-file-excel mr-2 text-slate-300 text-base"></i> Belum ada data master. Silakan klik "Update Master" dan pilih file Excel.
                            </td>
                        </tr>
                    @endisset
                </tbody>
            </table>
        </div>

        {{-- Footer Information & Pagination --}}
        @isset($employees)
        <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 shrink-0">
            <p class="text-xs text-slate-500 font-medium">
                Menampilkan data induk master karyawan PT Indofood CBP Sukses Makmur Tbk
            </p>
            @if (method_exists($employees, 'links'))
                <div>{{ $employees->appends(request()->query())->links() }}</div>
            @endif
        </div>
        @endisset

    </div>
</div>

<style>
    /* Styling Tabel Karyawan Modern & Presisi */
    #tableEmployee {
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    #tableEmployee thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 11px !important;
        letter-spacing: 0.04em !important;
        text-transform: uppercase !important;
        border-top: none !important;
        border-bottom: 1.5px solid #e2e8f0 !important;
        border-right: 1px solid #f1f5f9 !important;
        border-left: none !important;
        padding: 0.85rem 1rem !important;
        white-space: nowrap !important;
    }
    #tableEmployee tbody td {
        border-bottom: 1px solid #f1f5f9 !important;
        border-right: 1px solid #f1f5f9 !important;
        border-top: none !important;
        border-left: none !important;
        padding: 0.75rem 1rem !important;
        font-size: 12px !important;
        color: #334155 !important;
        vertical-align: middle !important;
        background-color: #ffffff;
    }
    #tableEmployee tbody tr:nth-child(even) td {
        background-color: #fafbfc;
    }
    #tableEmployee tbody tr:hover td {
        background-color: #f0f7ff !important;
    }

    /* Sticky Columns (NO, NIK, NAMA) */
    .emp-col-no {
        position: sticky !important;
        left: 0 !important;
        width: 54px !important;
        min-width: 54px !important;
        max-width: 54px !important;
        z-index: 20 !important;
        text-align: center !important;
    }
    .emp-col-nik {
        position: sticky !important;
        left: 54px !important;
        width: 115px !important;
        min-width: 115px !important;
        max-width: 115px !important;
        z-index: 20 !important;
        text-align: center !important;
    }
    .emp-col-nama {
        position: sticky !important;
        left: 169px !important;
        min-width: 210px !important;
        max-width: 250px !important;
        z-index: 20 !important;
        border-right: 2px solid #cbd5e1 !important;
        box-shadow: 4px 0 10px -2px rgba(15, 23, 42, 0.06) !important;
    }
    #tableEmployee thead th.emp-col-no,
    #tableEmployee thead th.emp-col-nik,
    #tableEmployee thead th.emp-col-nama {
        z-index: 40 !important;
        background-color: #f1f5f9 !important;
    }

    /* Opaque backgrounds for sticky cells so horizontal scroll text never shines through */
    #tableEmployee tbody tr td.emp-col-no,
    #tableEmployee tbody tr td.emp-col-nik,
    #tableEmployee tbody tr td.emp-col-nama {
        background-color: #ffffff !important;
    }
    #tableEmployee tbody tr:nth-child(even) td.emp-col-no,
    #tableEmployee tbody tr:nth-child(even) td.emp-col-nik,
    #tableEmployee tbody tr:nth-child(even) td.emp-col-nama {
        background-color: #fafbfc !important;
    }
    #tableEmployee tbody tr:hover td.emp-col-no,
    #tableEmployee tbody tr:hover td.emp-col-nik,
    #tableEmployee tbody tr:hover td.emp-col-nama {
        background-color: #f0f7ff !important;
    }

    /* Scrollbar Ramping */
    .table-scroll-container::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }
    .table-scroll-container::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .table-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
    function openJabatanDropdown() {
        const menu = document.getElementById("jabatanMenu");
        const chevron = document.getElementById("jabatanChevron");
        if (!menu) return;
        menu.classList.remove("hidden");
        if (chevron) chevron.classList.add("rotate-180");
    }

    function closeJabatanDropdown() {
        const menu = document.getElementById("jabatanMenu");
        const chevron = document.getElementById("jabatanChevron");
        if (!menu) return;
        menu.classList.add("hidden");
        if (chevron) chevron.classList.remove("rotate-180");
    }

    function toggleJabatanDropdown(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        const menu = document.getElementById("jabatanMenu");
        if (!menu) return;
        if (menu.classList.contains("hidden")) {
            openJabatanDropdown();
            document.getElementById("filterJabatan")?.focus();
        } else {
            closeJabatanDropdown();
        }
    }

    function handleJabatanInput(val) {
        openJabatanDropdown();
        const query = (val || "").trim().toLowerCase();
        const btnClear = document.getElementById("btnClearJabatan");
        if (btnClear) {
            if (query.length > 0) btnClear.classList.remove("hidden");
            else btnClear.classList.add("hidden");
        }

        const items = document.querySelectorAll("#jabatanMenu .jabatan-item");
        let matchCount = 0;
        items.forEach(item => {
            const itemVal = item.getAttribute("data-value") || "";
            const itemText = item.innerText.trim().toLowerCase();
            if (itemVal === "") {
                if (query === "" || itemText.includes(query)) {
                    item.classList.remove("hidden");
                    matchCount++;
                } else {
                    item.classList.add("hidden");
                }
            } else if (itemVal.includes(query) || itemText.includes(query)) {
                item.classList.remove("hidden");
                matchCount++;
            } else {
                item.classList.add("hidden");
            }
        });

        const emptyMsg = document.getElementById("jabatanEmptyMsg");
        if (emptyMsg) {
            if (matchCount === 0) emptyMsg.classList.remove("hidden");
            else emptyMsg.classList.add("hidden");
        }

        if (typeof applyEmployeeFilters === "function") {
            applyEmployeeFilters();
        }
    }

    function selectJabatan(val) {
        const input = document.getElementById("filterJabatan");
        if (input) {
            input.value = val;
        }
        const btnClear = document.getElementById("btnClearJabatan");
        if (btnClear) {
            if (val && val.trim().length > 0) btnClear.classList.remove("hidden");
            else btnClear.classList.add("hidden");
        }

        const items = document.querySelectorAll("#jabatanMenu .jabatan-item");
        items.forEach(item => {
            const itemText = item.querySelector("span")?.innerText.trim() || "";
            const check = item.querySelector(".check-mark");
            if (check) {
                if ((val === "" && itemText === "Semua Jabatan") || (val !== "" && itemText.toLowerCase() === val.toLowerCase())) {
                    check.classList.remove("hidden");
                    item.classList.add("bg-blue-50", "text-blue-600");
                } else {
                    check.classList.add("hidden");
                    item.classList.remove("bg-blue-50", "text-blue-600");
                }
            }
            item.classList.remove("hidden");
        });

        const emptyMsg = document.getElementById("jabatanEmptyMsg");
        if (emptyMsg) emptyMsg.classList.add("hidden");

        closeJabatanDropdown();
        if (typeof applyEmployeeFilters === "function") {
            applyEmployeeFilters();
        }
    }

    function clearJabatanFilter(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        selectJabatan("");
        const input = document.getElementById("filterJabatan");
        if (input) input.focus();
    }

    function handleJabatanKeydown(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            closeJabatanDropdown();
            if (typeof applyEmployeeFilters === "function") {
                applyEmployeeFilters();
            }
        } else if (e.key === "Escape") {
            closeJabatanDropdown();
        }
    }

    function resetAllEmployeeFilters() {
        const searchInput = document.getElementById("employeeSearch");
        if (searchInput) searchInput.value = "";
        
        selectJabatan("");
        
        const sortSelect = document.getElementById("sortKaryawan");
        if (sortSelect) sortSelect.value = "default";
        
        if (typeof applyEmployeeFilters === "function") {
            applyEmployeeFilters();
        }
    }

    // Monitor filter changes to toggle Reset Filter button
    function updateResetFilterButtonState() {
        const searchVal = (document.getElementById("employeeSearch")?.value || "").trim();
        const jabatanVal = (document.getElementById("filterJabatan")?.value || "").trim();
        const sortVal = document.getElementById("sortKaryawan")?.value || "default";
        const btnReset = document.getElementById("btnResetFilters");
        if (!btnReset) return;

        if (searchVal !== "" || jabatanVal !== "" || sortVal !== "default") {
            btnReset.classList.remove("hidden");
            btnReset.classList.add("inline-flex");
        } else {
            btnReset.classList.add("hidden");
            btnReset.classList.remove("inline-flex");
        }
    }

    // Intercept filter calls to also update reset button state
    const originalApplyEmployeeFilters = window.applyEmployeeFilters;
    window.applyEmployeeFilters = function() {
        if (typeof originalApplyEmployeeFilters === "function") {
            originalApplyEmployeeFilters();
        }
        updateResetFilterButtonState();
    };

    document.addEventListener("click", function(e) {
        const container = document.getElementById("jabatanDropdownContainer");
        if (container && !container.contains(e.target)) {
            closeJabatanDropdown();
        }
    });
</script>
