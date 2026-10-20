<div id="tab-karyawan" class="tab-content hidden h-full flex flex-col">
    <div class="glass-card overflow-hidden flex flex-col flex-1 shadow-xl min-h-0 h-full bg-white">

        <div class="p-3 sm:p-4 border-b bg-slate-50/80 flex flex-col md:flex-row md:justify-between md:items-center gap-3 sm:gap-4 shrink-0">
            <div>
                <h4 class="text-xs font-black uppercase text-slate-700 tracking-widest">Database Master Karyawan</h4>
                <p class="text-[10px] font-bold text-slate-400 mt-0.5 sm:mt-1 uppercase italic">
                    Total: <span id="totalKaryawan">{{ isset($employees) && method_exists($employees, 'total') ? $employees->total() : (isset($employees) ? $employees->count() : 0) }}</span> Record
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <div class="flex items-center bg-white border border-slate-300 rounded-xl p-1 shadow-sm flex-1 sm:flex-none min-w-[140px]">
                    <i class="fas fa-search text-slate-400 ml-2.5 text-xs"></i>
                    <input type="text" id="employeeSearch" onkeyup="applyEmployeeFilters()" oninput="applyEmployeeFilters()" placeholder="Cari NIK / Nama..." class="text-xs p-1.5 border-none outline-none w-full sm:w-40 font-semibold focus:ring-0">
                </div>

                @php
                    $jabatans = isset($employees) ? $employees->map(function($e) {
                        return trim($e->position_text ?? $e->jabatan ?? '');
                    })->filter(function($val) {
                        return !empty($val) && $val !== '-';
                    })->unique()->sort()->values() : collect();
                @endphp

                <!-- Searchable Jabatan Dropdown -->
                <div class="relative z-40" id="jabatanDropdownContainer">
                    <div class="flex items-center bg-white border border-slate-300 rounded-xl px-2.5 py-1.5 shadow-sm hover:border-blue-400 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                        <input type="text" 
                               id="filterJabatan" 
                               placeholder="Cari Jabatan" 
                               autocomplete="off"
                               onfocus="openJabatanDropdown()" 
                               onclick="openJabatanDropdown()"
                               oninput="handleJabatanInput(this.value)" 
                               onkeydown="handleJabatanKeydown(event)"
                               class="text-[11px] p-0 border-none outline-none w-36 md:w-44 font-bold text-slate-700 bg-transparent placeholder-slate-400 focus:ring-0">
                        <button type="button" 
                                id="btnClearJabatan" 
                                onclick="clearJabatanFilter(event)" 
                                class="hidden text-slate-400 hover:text-red-500 mr-1.5 text-xs transition-colors"
                                title="Reset Jabatan">
                            <i class="fas fa-times-circle"></i>
                        </button>
                        <button type="button" 
                                onclick="toggleJabatanDropdown(event)" 
                                class="text-slate-400 hover:text-slate-600 focus:outline-none text-xs">
                            <i class="fas fa-chevron-down transition-transform duration-200" id="jabatanChevron"></i>
                        </button>
                    </div>

                    <!-- Dropdown List Options -->
                    <div id="jabatanMenu" class="hidden absolute left-0 right-0 top-full mt-1.5 max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-2xl z-50 divide-y divide-slate-100 py-1 text-left">
                        <div onclick="selectJabatan('')" 
                             class="jabatan-item px-3 py-2 text-[11px] font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex items-center justify-between transition-colors"
                             data-value="">
                            <span>Semua Jabatan</span>
                            <i class="fas fa-check text-blue-600 text-xs hidden check-mark"></i>
                        </div>
                        @foreach($jabatans as $jab)
                            <div onclick="selectJabatan('{{ addslashes($jab) }}')" 
                                 class="jabatan-item px-3 py-2 text-[11px] font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer flex items-center justify-between transition-colors"
                                 data-value="{{ strtolower($jab) }}">
                                <span>{{ $jab }}</span>
                                <i class="fas fa-check text-blue-600 text-xs hidden check-mark"></i>
                            </div>
                        @endforeach
                        <div id="jabatanEmptyMsg" class="hidden px-3 py-3 text-center text-[10px] text-slate-400 font-bold italic">
                            Jabatan tidak ditemukan
                        </div>
                    </div>
                </div>

                <select id="sortKaryawan" onchange="applyEmployeeFilters()" class="text-[11px] p-2.5 rounded-xl border border-slate-300 outline-none shadow-sm cursor-pointer font-bold text-slate-700">
                    <option value="default">Sort Default</option>
                    <option value="az">A - Z</option>
                    <option value="za">Z - A</option>
                    <option value="newest">TMK Terbaru</option>
                    <option value="oldest">TMK Terlama</option>
                </select>

                <button onclick="openAddEmployeeModal()" class="bg-blue-600 text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-md hover:bg-blue-700 transition-all flex items-center">
                    <i class="fas fa-user-plus mr-1.5 text-sm"></i> Tambah
                </button>

                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="ml-1">
                    @csrf
                    <label class="bg-green-600 text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-md cursor-pointer hover:bg-green-700 transition-all flex items-center">
                        <i class="fas fa-file-excel mr-1.5 text-sm"></i> Update Master
                        <input type="file" name="file_excel" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>

        <div class="overflow-auto flex-1 bg-white relative w-full h-0">
            <table id="tableEmployee" class="w-max min-w-full text-left text-xs table-bordered border-collapse whitespace-nowrap">
                <thead class="bg-indofood-blue text-white sticky top-0 z-30 shadow-md">
                    <tr class="uppercase font-black tracking-widest text-[10px]">
                        <th class="p-3 text-center sticky left-0 bg-indofood-blue z-40">NO</th>
                        <th class="p-3 text-center sticky left-12 bg-indofood-blue z-40">NIK</th>
                        <th class="p-3 sticky left-[105px] bg-indofood-blue z-40 border-r-2">NAMA KARYAWAN</th>

                        <th class="p-3 text-center">USER</th>
                        <th class="p-3 text-center">AKSI</th>
                        <th class="p-3">Jumlah</th><th class="p-3">Position text</th><th class="p-3">Employee Group</th>
                        <th class="p-3">Pers subarea text</th><th class="p-3">SubSubarea</th><th class="p-3">TMK</th>
                        <th class="p-3">Length of Service</th><th class="p-3">Gender</th><th class="p-3">Marital Status</th>
                        <th class="p-3">Birthplace</th><th class="p-3">Birthdate</th><th class="p-3">Age</th>
                        <th class="p-3">Range Umur</th><th class="p-3">Religion</th><th class="p-3">Jamsostek ID</th>
                        <th class="p-3">BPJS ID</th><th class="p-3">Personal Tax ID</th><th class="p-3">Tax Status</th>
                        <th class="p-3">Number Of Child</th><th class="p-3">KTP - Nomor KTP</th><th class="p-3">KTP - Alamat KTP</th>
                        <th class="p-3">Region</th><th class="p-3">Postal Code</th><th class="p-3">No. HP</th>
                        <th class="p-3">Email</th><th class="p-3">ESGrp text</th><th class="p-3">Level</th>
                        <th class="p-3">Position ID</th><th class="p-3">Org unit ID</th><th class="p-3">Org unit text</th>
                        <th class="p-3">Pers subarea ID</th><th class="p-3">Org key ID</th><th class="p-3">Org key text</th>
                        <th class="p-3">Bantu OPR/HLP</th><th class="p-3">Regu / Line</th><th class="p-3">Line</th>
                        <th class="p-3">Regu</th><th class="p-3">Area Prov Marketing</th><th class="p-3">Distrik Rayon</th>
                        <th class="p-3">Vendor OS</th><th class="p-3">Cost Center</th><th class="p-3">Cost Center Text</th>
                        <th class="p-3">Emp status text</th><th class="p-3">Excuse</th><th class="p-3">Tgl Akhir Kerja</th>
                        <th class="p-3">Edu - Level</th><th class="p-3">Edu - Institution</th><th class="p-3">Edu - Branch</th>
                        <th class="p-3">PS Type Text</th><th class="p-3">Status Incentive</th><th class="p-3">Bank Keys</th>
                        <th class="p-3">Bank Account No</th><th class="p-3">Vaksin 1</th><th class="p-3">Merk V1</th>
                        <th class="p-3">Vaksin 2</th><th class="p-3">Merk V2</th><th class="p-3">Vaksin 3</th>
                        <th class="p-3">Merk V3</th><th class="p-3">Workschedule ID</th><th class="p-3">Tipe Hari Kerja</th>
                        <th class="p-3">Shift/Non Shift</th><th class="p-3 bg-orange-600">No Sidik Jari</th>
                        <th class="p-3 bg-orange-600">Jam Kerja</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody" class="divide-y divide-slate-200 text-slate-700 bg-white">
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
                        <tr class="hover:bg-blue-50 transition-all employee-row" data-nama="{{ $nama }}" data-nik="{{ $nik }}" data-jabatan="{{ $jabatan }}" data-tmk="{{ $emp->tmk }}">
                            <td class="p-3 text-center sticky left-0 bg-white z-10 border-r">{{ method_exists($employees, 'firstItem') ? $employees->firstItem() + $loop->index : $loop->iteration }}</td>
                            <td class="p-3 font-black text-blue-700 text-center sticky left-12 bg-white z-10 border-r">{{ $nik }}</td>
                            <td class="p-3 font-extrabold uppercase text-slate-900 sticky left-[105px] bg-white z-10 border-r-2">{{ $nama }}</td>

                            <td class="p-3 text-center font-bold text-blue-700 border-r">{{ $emp->user ?? '-' }}</td>
                            <td class="p-3 text-center border-r">
                                <div class="flex justify-center gap-4">
                                    <button type="button" onclick="openEditEmployeeModal(this)" data-employee="{{ base64_encode(json_encode($emp)) }}" class="text-blue-500 hover:scale-125 transition-all"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('employees.destroy', $emp->id ?? 0) }}" method="POST" onsubmit="return confirm('Hapus?');" class="inline">@csrf @method('DELETE')<button type="submit" class="text-red-500 hover:scale-125 transition-all"><i class="fas fa-trash-alt"></i></button></form>
                                </div>
                            </td>

                            <td class="p-3">{{ $emp->jumlah ?? '-' }}</td>
                            <td class="p-3">{{ $jabatan }}</td>
                            <td class="p-3">{{ $emp->employee_group ?? '-' }}</td>
                            <td class="p-3">{{ $emp->pers_subarea_text ?? '-' }}</td>
                            <td class="p-3 font-bold">{{ $emp->sub_subarea ?? $emp->subsubarea ?? '-' }}</td>
                            <td class="p-3 font-mono text-blue-600">{{ $emp->tmk ?? '-' }}</td>
                            <td class="p-3">{{ $emp->length_of_service ?? '-' }}</td>
                            <td class="p-3">{{ $emp->gender ?? '-' }}</td>
                            <td class="p-3">{{ $emp->marital_status ?? '-' }}</td>
                            <td class="p-3 uppercase">{{ $emp->birthplace ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->birthdate ?? '-' }}</td>
                            <td class="p-3 text-blue-600 font-bold">{{ $emp->age ?? '-' }}</td>
                            <td class="p-3">{{ $emp->range_umur ?? '-' }}</td>
                            <td class="p-3 uppercase">{{ $emp->religion ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->jamsostek_id ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->bpjs_id ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->personal_tax_id ?? $emp->tax_id ?? '-' }}</td>
                            <td class="p-3">{{ $emp->tax_status ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $emp->number_of_child ?? '-' }}</td>
                            <td class="p-3 font-mono font-bold">{{ $emp->ktp_nomor_ktp ?? $emp->ktp_nomor ?? '-' }}</td>
                            <td class="p-3 text-[10px] whitespace-normal min-w-[200px]">{{ $emp->ktp_alamat_ktp ?? $emp->ktp_alamat ?? '-' }}</td>
                            <td class="p-3 uppercase">{{ $emp->region ?? '-' }}</td>
                            <td class="p-3">{{ $emp->ktp_postal_code ?? $emp->postal_code ?? '-' }}</td>
                            <td class="p-3 font-bold text-slate-800">{{ $emp->no_hp ?? '-' }}</td>
                            <td class="p-3 text-blue-500">{{ $emp->email_0030 ?? $emp->email ?? '-' }}</td>
                            <td class="p-3">{{ $emp->esgrp_text ?? '-' }}</td>
                            <td class="p-3 uppercase font-black opacity-40">{{ $emp->level ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->position_id ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->org_unit_id ?? '-' }}</td>
                            <td class="p-3 text-[10px]">{{ $emp->org_unit_text ?? '-' }}</td>
                            <td class="p-3">{{ $emp->pers_subarea_id ?? '-' }}</td>
                            <td class="p-3">{{ $emp->org_key_id ?? '-' }}</td>
                            <td class="p-3 text-[10px]">{{ $emp->org_key_text ?? '-' }}</td>
                            <td class="p-3">{{ $emp->bantu_oprhlp_prod_direct ?? $emp->bantu_opr_hlp ?? '-' }}</td>
                            <td class="p-3 font-bold">{{ $emp->regu_line ?? '-' }}</td>
                            <td class="p-3">{{ $emp->line ?? '-' }}</td>
                            <td class="p-3 text-center font-bold text-slate-800">{{ $emp->regu ?? '-' }}</td>
                            <td class="p-3 text-[10px]">{{ $emp->area_prov_marketing ?? '-' }}</td>
                            <td class="p-3 text-[10px]">{{ $emp->distrik_rayon_marketing ?? '-' }}</td>
                            <td class="p-3 font-black text-slate-500">{{ $emp->vendor_os ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->cost_center ?? '-' }}</td>
                            <td class="p-3 text-[10px] uppercase">{{ $emp->cost_center_text ?? '-' }}</td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-bold text-[9px]">{{ $emp->emp_status_text ?? '-' }}</span></td>
                            <td class="p-3 italic">{{ $emp->excuse ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->tanggal_akhir_kerja ?? '-' }}</td>
                            <td class="p-3 font-bold">{{ $emp->last_education_level ?? $emp->pendidikan_level ?? '-' }}</td>
                            <td class="p-3 text-[10px]">{{ $emp->last_education_institution ?? $emp->pendidikan_institusi ?? '-' }}</td>
                            <td class="p-3 text-[10px] italic">{{ $emp->last_education_branch_of_study ?? $emp->pendidikan_studi ?? '-' }}</td>
                            <td class="p-3">{{ $emp->ps_type0008_text ?? $emp->ps_type_text ?? '-' }}</td>
                            <td class="p-3 text-center font-bold">{{ $emp->status_incentive ?? '-' }}</td>
                            <td class="p-3">{{ $emp->bank_keys ?? '-' }}</td>
                            <td class="p-3 font-mono font-bold text-blue-700 tracking-wider">{{ $emp->bank_account_no ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->vaksin1_tanggal ?? '-' }}</td>
                            <td class="p-3 italic">{{ $emp->vaksin1_merk ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->vaksin2_tanggal ?? '-' }}</td>
                            <td class="p-3 italic">{{ $emp->vaksin2_merk ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->vaksin3_tanggal ?? '-' }}</td>
                            <td class="p-3 italic">{{ $emp->vaksin3_merk ?? '-' }}</td>
                            <td class="p-3 font-mono">{{ $emp->workschedule_id ?? '-' }}</td>
                            <td class="p-3 font-semibold">{{ $emp->tipe_hari_kerja ?? '-' }}</td>
                            <td class="p-3 font-bold text-purple-600">{{ $emp->shift_non_shift ?? $emp->shift_type ?? '-' }}</td>
                            <td class="p-3 font-black text-orange-600 text-center bg-orange-50">{{ $sidik ?? '-' }}</td>
                            <td class="p-3 text-center font-black text-slate-900 bg-orange-50">{{ $jam ?? '-' }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="71" class="p-20 text-center text-slate-300 font-black uppercase tracking-widest italic border-none text-sm">Belum ada data master. Silakan klik "Update Master" dan pilih file Data OS.xlsx</td></tr>
                    @endisset
                </tbody>
            </table>
        </div>

        @isset($employees)
        <div class="p-4 border-t bg-slate-50/80 flex justify-center">
            @if (method_exists($employees, 'links'))
                {{ $employees->appends(request()->query())->links() }}
            @endif
        </div>
        @endisset

    </div>
</div>

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

    document.addEventListener("click", function(e) {
        const container = document.getElementById("jabatanDropdownContainer");
        if (container && !container.contains(e.target)) {
            closeJabatanDropdown();
        }
    });
</script>
