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

<div id="addEmployeeModal" class="fixed top-0 right-0 bottom-0 left-72 z-[100] bg-slate-100 opacity-0 pointer-events-none transition-opacity duration-300 shadow-[-10px_0_20px_rgba(0,0,0,0.1)]">
    <form action="{{ route('employees.store') }}" method="POST" class="w-full h-full flex flex-col">
        @csrf

        <div class="px-8 py-5 bg-white border-b-4 border-green-500 flex-none flex justify-between items-center shadow-sm z-20">
            <h4 class="text-xl font-black text-slate-800 uppercase tracking-widest"><i class="fas fa-user-plus mr-3 text-green-500"></i> Tambah Karyawan Baru</h4>
            <button type="button" onclick="closeAddEmployeeModal()" class="text-slate-400 hover:text-red-500 text-4xl font-black leading-none outline-none transition-colors">&times;</button>
        </div>

        <div class="px-8 py-8 flex-1 overflow-y-auto bg-slate-50 custom-scrollbar z-10">
            <div class="w-full max-w-7xl mx-auto space-y-8">
                @foreach($formGroups as $groupName => $group)
                    <div class="bg-white rounded-xl shadow-sm border {{ $group['border'] }} overflow-hidden">
                        <div class="{{ $group['bg'] }} px-6 py-4 border-b {{ $group['border'] }}">
                            <h5 class="text-sm font-black {{ $group['text'] }} uppercase tracking-widest">{{ $groupName }}</h5>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($group['fields'] as $name => $type)
                                <div class="flex flex-col">
                                    <label class="text-xs font-extrabold text-slate-500 uppercase mb-2 tracking-wide">{{ ucwords(str_replace('_', ' ', $name)) }}</label>
                                    <input type="{{ $type }}" name="{{ $name }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm text-slate-800 font-semibold focus:bg-white {{ $group['ring'] }} outline-none transition-all shadow-sm" {{ $name == 'nik' || $name == 'nama' ? 'required' : '' }}>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-8 py-5 bg-white border-t border-slate-200 flex-none flex justify-end gap-4 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)] z-20">
            <button type="button" onclick="closeAddEmployeeModal()" class="px-10 py-4 text-xs font-black bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-all">BATAL KEMBALI</button>
            <button type="submit" class="px-10 py-4 text-xs font-black text-white bg-green-600 rounded-xl shadow-lg hover:bg-green-700 transition-all"><i class="fas fa-save mr-2"></i> SIMPAN DATA KARYAWAN</button>
        </div>
    </form>
</div>

<div id="editEmployeeModal" class="fixed top-0 right-0 bottom-0 left-72 z-[100] bg-slate-100 opacity-0 pointer-events-none transition-opacity duration-300 shadow-[-10px_0_20px_rgba(0,0,0,0.1)]">
    <form id="editEmployeeForm" method="POST" class="w-full h-full flex flex-col">
        @csrf @method('PUT')

        <div class="px-8 py-5 bg-white border-b-4 border-blue-600 flex-none flex justify-between items-center shadow-sm z-20">
            <h4 class="text-xl font-black text-slate-800 uppercase tracking-widest"><i class="fas fa-edit mr-3 text-blue-600"></i> Koreksi Data Karyawan</h4>
            <button type="button" onclick="closeEditEmployeeModal()" class="text-slate-400 hover:text-red-500 text-4xl font-black leading-none outline-none transition-colors">&times;</button>
        </div>

        <div class="px-8 py-8 flex-1 overflow-y-auto bg-slate-50 custom-scrollbar z-10">
            <div class="w-full max-w-7xl mx-auto space-y-8">
                @foreach($formGroups as $groupName => $group)
                    <div class="bg-white rounded-xl shadow-sm border {{ $group['border'] }} overflow-hidden">
                        <div class="{{ $group['bg'] }} px-6 py-4 border-b {{ $group['border'] }}">
                            <h5 class="text-sm font-black {{ $group['text'] }} uppercase tracking-widest">{{ $groupName }}</h5>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($group['fields'] as $name => $type)
                                <div class="flex flex-col">
                                    <label class="text-xs font-extrabold text-slate-500 uppercase mb-2 tracking-wide">{{ ucwords(str_replace('_', ' ', $name)) }}</label>
                                    <input type="{{ $type }}" name="{{ $name }}" id="edit_emp_{{ $name }}" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm text-slate-800 font-semibold focus:bg-white {{ $group['ring'] }} outline-none transition-all shadow-sm" {{ $name == 'nik' || $name == 'nama' ? 'required' : '' }}>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-8 py-5 bg-white border-t border-slate-200 flex-none flex justify-end gap-4 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)] z-20">
            <button type="button" onclick="closeEditEmployeeModal()" class="px-10 py-4 text-xs font-black bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-all">BATAL KEMBALI</button>
            <button type="submit" class="px-10 py-4 text-xs font-black text-white bg-blue-600 rounded-xl shadow-lg hover:bg-blue-700 transition-all"><i class="fas fa-check-circle mr-2"></i> PERBARUI DATA KARYAWAN</button>
        </div>
    </form>
</div>

<div id="editModal" class="fixed top-0 right-0 bottom-0 left-72 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="absolute inset-0 z-0" onclick="closeEditModalAbsen()"></div>

    <form id="editForm" method="POST" class="bg-white w-full max-w-[400px] max-h-[90vh] flex flex-col rounded-2xl shadow-2xl relative border-t-8 border-indofood-blue overflow-hidden z-10">
        @csrf @method('PUT')

        <div class="p-6 border-b border-slate-200 flex-none bg-white relative z-20">
            <h4 class="text-center text-md font-black text-slate-800 uppercase tracking-widest">Koreksi Absensi</h4>
        </div>

        <div class="p-6 space-y-5 flex-1 bg-slate-50 overflow-y-auto custom-scrollbar">
            <input type="text" name="nama_karyawan" id="edit_nama" class="w-full form-input-modern shadow-sm">

            <select name="status" id="edit_status" class="w-full form-input-modern cursor-pointer shadow-sm" onchange="toggleLembur('edit_status', 'edit_lembur_box')">
                <option value="Hadir">Hadir</option>
                <option value="Lembur">Lembur</option>
                <option value="Izin">Izin / Sakit</option>
                <option value="Tanpa Keterangan">Tanpa Keterangan</option>
            </select>

            <div id="edit_lembur_box" class="hidden flex-col gap-3 bg-purple-100 p-4 rounded-xl border border-purple-300 shadow-inner">
                <label class="text-[9px] font-black text-purple-700 uppercase mb-1"><i class="fas fa-calculator mr-1"></i> Kalkulator Depnaker</label>
                <div class="grid grid-cols-2 gap-2 mb-1">
                    <div class="flex flex-col gap-1">
                        <label class="text-[8px] font-bold text-slate-600 uppercase">Durasi Riil (Jam)</label>
                        <input type="number" id="edit_durasi" step="0.5" min="0" class="form-input-modern border-purple-300 p-1.5 text-xs text-center shadow-sm w-full" oninput="hitungAkmLembur('edit')">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[8px] font-bold text-slate-600 uppercase">Tipe Hari</label>
                        <select id="edit_tipe" class="form-input-modern border-purple-300 p-1.5 text-xs cursor-pointer shadow-sm w-full" onchange="hitungAkmLembur('edit')">
                            <option value="Kerja">Hari Kerja</option>
                            <option value="Libur">Hari Libur</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[8px] font-bold text-slate-600 uppercase block mb-1">Distribusi Pengali</label>
                    <div class="grid grid-cols-4 gap-2">
                        <div><label class="text-[8px] font-bold text-slate-500 text-center block mb-0.5">1.5x</label><input type="text" id="edit_15" readonly class="w-full bg-purple-200 text-slate-700 text-center text-xs p-1.5 rounded border border-purple-300 cursor-not-allowed font-bold"></div>
                        <div><label class="text-[8px] font-bold text-slate-500 text-center block mb-0.5">2x</label><input type="text" id="edit_2" readonly class="w-full bg-purple-200 text-slate-700 text-center text-xs p-1.5 rounded border border-purple-300 cursor-not-allowed font-bold"></div>
                        <div><label class="text-[8px] font-bold text-slate-500 text-center block mb-0.5">3x</label><input type="text" id="edit_3" readonly class="w-full bg-purple-200 text-slate-700 text-center text-xs p-1.5 rounded border border-purple-300 cursor-not-allowed font-bold"></div>
                        <div><label class="text-[8px] font-bold text-slate-500 text-center block mb-0.5">4x</label><input type="text" id="edit_4" readonly class="w-full bg-purple-200 text-slate-700 text-center text-xs p-1.5 rounded border border-purple-300 cursor-not-allowed font-bold"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between bg-white px-3 py-2 mt-1 rounded-lg border border-purple-400 shadow-sm">
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Akm. Jam:</span>
                    <input type="text" name="jam_lembur" id="edit_total" class="w-16 bg-transparent border-none text-right font-black text-purple-700 text-sm outline-none cursor-not-allowed" readonly>
                </div>
            </div>
        </div>

        <div class="p-5 flex gap-3 border-t border-slate-200 flex-none bg-white relative z-20">
            <button type="button" onclick="closeEditModalAbsen()" class="flex-1 py-3 text-[10px] font-black bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-300 transition-all shadow-sm cursor-pointer relative z-50">BATAL</button>
            <button type="submit" class="flex-1 py-3 text-[10px] font-black text-white bg-indofood-blue rounded-xl shadow-lg hover:bg-blue-800 transition-all cursor-pointer relative z-50">UPDATE</button>
        </div>
    </form>
</div>
