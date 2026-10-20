{{-- TAB INPUT LEMBUR (TAMPILAN MODERN PERSIS GAYA PENGAJUAN SPL) --}}
<div id="tab-input-lembur" class="tab-content hidden flex-1 flex-col gap-6">

    {{-- KARTU UTAMA FORMULIR INPUT LEMBUR (DOKUMEN GAYA SPL) --}}
    <div class="glass-card flex-1 overflow-hidden flex flex-col shadow-xl rounded-2xl border-t-4 border-indofood-blue bg-white">
        
        <form id="formInputLembur" action="{{ route('attendance.store') }}#input-lembur" method="POST" onsubmit="return validateFormLembur()">
            @csrf
            <input type="hidden" name="action" value="assorting_tim">
            <input type="hidden" name="status_kehadiran" value="Lembur">

            {{-- 1. Header Panel (Sesuai Gaya Header SPL) --}}
            <div class="px-4 sm:px-7 py-3.5 sm:py-5 border-b border-slate-200 flex flex-wrap justify-between items-center gap-3 sm:gap-4 bg-slate-50">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-indofood-blue uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-clock text-indofood-blue"></i> Formulir Input Lembur Karyawan (WITA)
                    </h3>
                    <p class="text-xs font-bold text-slate-500 mt-0.5 sm:mt-1 tracking-wide">
                        Pencatatan Jam Lembur, Durasi Riil, dan Akumulasi Jam Kerja Sesuai Standar Depnaker
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-800 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-[10px] sm:text-[11px] font-black uppercase tracking-widest border border-blue-300 shadow-xs flex items-center gap-1.5">
                        <i class="fas fa-user-clock"></i> Form Lembur Multi-Baris
                    </span>
                </div>
            </div>

            {{-- 2. Metadata Bar (Tanggal, Hari, Tipe Hari, Default Istirahat) --}}
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-white">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <div>
                        <label class="text-[11px] font-black text-slate-600 uppercase ml-1">Tanggal Lembur (WITA)</label>
                        <input type="date" id="input_tanggal_lembur" name="tanggal" 
                               value="{{ $filterDate ?? \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d') }}"
                               onchange="updateHariLembur(this.value)"
                               class="mt-1 w-full p-2.5 border-2 border-slate-300 rounded-lg focus:border-indofood-blue outline-none text-sm font-bold text-slate-800 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-600 uppercase ml-1">Hari Lembur</label>
                        <input type="text" id="input_hari_lembur" 
                               value="{{ \Carbon\Carbon::parse($filterDate ?? \Carbon\Carbon::now('Asia/Makassar'))->locale('id')->isoFormat('dddd') }}" readonly
                               class="mt-1 w-full p-2.5 border-2 border-slate-200 rounded-lg bg-slate-100 text-slate-600 font-bold uppercase text-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-600 uppercase ml-1">Tipe Hari</label>
                        <select id="select_tipe_hari" name="tipe_hari" onchange="hitungSemuaBarisLembur()"
                                class="mt-1 w-full p-2.5 border-2 border-slate-300 rounded-lg focus:border-indofood-blue outline-none text-sm font-bold text-slate-800 transition-colors bg-white">
                            <option value="Kerja">Hari Kerja (1.5x jam ke-1, 2x jam ke-2+)</option>
                            <option value="Libur">Hari Libur (2x s.d 7 jam, 3x jam ke-8, 4x jam ke-9+)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-600 uppercase ml-1">Istirahat Default</label>
                        <select id="select_default_istirahat" onchange="terapkanIstirahatSemua(this.value)"
                                class="mt-1 w-full p-2.5 border-2 border-slate-300 rounded-lg focus:border-indofood-blue outline-none text-sm font-bold text-slate-800 transition-colors bg-white">
                            <option value="0">0 Menit (Tanpa Istirahat)</option>
                            <option value="30">30 Menit</option>
                            <option value="60">60 Menit (1 Jam)</option>
                        </select>
                    </div>
                </div>
            </div>

            <style>
                /* Styling NIK proporsional dan bersih tanpa animasi zoom/perbesar */
                .col-nik-lembur {
                    min-width: 160px !important;
                    width: 160px !important;
                }
                .nik-input-lembur {
                    min-width: 140px !important;
                    font-size: 14px !important;
                    font-weight: 800 !important;
                    letter-spacing: 1px !important;
                    font-family: 'Consolas', 'Courier New', monospace !important;
                }
                .nik-input-lembur:focus {
                    z-index: 20 !important;
                    background-color: #ffffff !important;
                    border-color: #004a99 !important;
                    box-shadow: 0 0 0 2px rgba(0, 74, 153, 0.25) !important;
                }
            </style>

            {{-- 3. Toolbar & Tabel Rincian Pekerja Lembur --}}
            <div class="flex-1 overflow-y-auto p-6 bg-white min-h-[320px]">
                <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                    <div>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-list text-indofood-blue"></i> Rincian Pekerja Lembur
                        </h4>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Tombol Tambah Baris --}}
                        <button type="button" onclick="addLemburRow()" 
                                class="bg-indofood-blue text-white px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider hover:bg-blue-800 transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                    </div>
                </div>

                {{-- Tabel Kontainer --}}
                <div class="overflow-x-auto rounded-xl border-2 border-slate-300 shadow-sm bg-white" id="lemburTableWrapper">
                    <table id="lemburTable" class="w-full min-w-[1300px] text-left whitespace-nowrap text-xs border-collapse transition-all duration-200">
                        <thead class="bg-slate-100 text-slate-800 text-[11px] uppercase font-black text-center align-middle">
                            <tr>
                                <th rowspan="2" class="p-3 border border-slate-300 w-12">No</th>
                                <th rowspan="2" class="p-3 border border-slate-300 col-nik-lembur bg-blue-50/60 text-blue-900">
                                    <span>NIK PEKERJA</span>
                                </th>
                                <th rowspan="2" class="p-3 border border-slate-300 min-w-[220px]">Nama Lengkap</th>
                                <th rowspan="2" class="p-3 border border-slate-300 min-w-[150px]">Jabatan / Posisi</th>
                                <th colspan="2" class="p-2 border border-slate-300 bg-blue-100 text-blue-900 min-w-[200px]">Jam Lembur (WITA)</th>
                                <th rowspan="2" class="p-3 border border-slate-300 bg-amber-100 text-amber-900 min-w-[110px]">Istirahat</th>
                                <th rowspan="2" class="p-3 border border-slate-300 min-w-[100px]">Durasi Riil</th>
                                <th rowspan="2" class="p-3 border border-slate-300 bg-indigo-50 text-indigo-900 min-w-[120px]">Akm. Jam</th>
                                <th rowspan="2" class="p-3 border border-slate-300 min-w-[200px]">Keterangan / Tugas</th>
                                <th rowspan="2" class="p-3 border border-slate-300 w-12"><i class="fas fa-cog text-slate-500"></i></th>
                            </tr>
                            <tr>
                                <th class="p-2 border border-slate-300 bg-blue-100 text-blue-900 min-w-[100px]">Mulai</th>
                                <th class="p-2 border border-slate-300 bg-blue-100 text-blue-900 min-w-[100px]">Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-700" id="tbody-lembur">
                            {{-- Baris 1 Default --}}
                            <tr class="hover:bg-slate-50 border-b border-slate-300">
                                <td class="p-2 text-center font-black text-slate-500">1</td>
                                <td class="p-2 col-nik-lembur">
                                    <div class="relative flex items-center">
                                        <input type="text" name="id_karyawan[]" placeholder="Pilih / NIK" required
                                               oninput="syncKaryawanLembur(this, 'nik')"
                                               onchange="syncKaryawanLembur(this, 'nik')"
                                               class="nik-input-lembur w-full px-2.5 py-2 pr-7 border-2 border-slate-300 rounded-lg focus:border-blue-600 focus:bg-white outline-none text-center font-black text-xs tracking-wider font-mono text-slate-900 bg-slate-50 cursor-pointer"
                                               autocomplete="off" list="karyawan_nik_list">
                                        <button type="button" onclick="openPickerDropdown(this, 'nik', 'lembur')" tabindex="-1" class="absolute right-1 text-slate-400 hover:text-blue-600 p-1 cursor-pointer" title="Klik untuk pilih NIK">
                                            <i class="fas fa-chevron-down text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="p-2">
                                    <div class="relative flex items-center">
                                        <input type="text" name="nama_karyawan[]" placeholder="Pilih / Nama Karyawan" required
                                               oninput="syncKaryawanLembur(this, 'nama')"
                                               onchange="syncKaryawanLembur(this, 'nama')"
                                               class="nama-lembur w-full p-2 pr-7 border border-slate-300 rounded focus:border-blue-500 outline-none uppercase font-black text-xs text-slate-800 cursor-pointer" 
                                               style="min-width: 210px;"
                                               autocomplete="off" list="karyawan_nama_list">
                                        <button type="button" onclick="openPickerDropdown(this, 'nama', 'lembur')" tabindex="-1" class="absolute right-1 text-slate-400 hover:text-blue-600 p-1 cursor-pointer" title="Klik untuk pilih Nama">
                                            <i class="fas fa-chevron-down text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="p-2">
                                    <input type="text" name="jabatan[]" placeholder="Jabatan" readonly
                                           class="jabatan-lembur w-full p-2 border border-slate-200 bg-slate-100 rounded text-slate-600 font-bold text-center text-xs" style="min-width: 140px;">
                                </td>
                                <td class="p-2">
                                    <input type="time" name="jam_mulai[]" required
                                           oninput="hitungBarisLembur(this.closest('tr'))"
                                           class="mulai-lembur w-full p-1.5 border border-blue-300 bg-blue-50/50 rounded text-center font-black text-blue-900 focus:ring-1 focus:ring-blue-500 outline-none" style="min-width: 90px;">
                                </td>
                                <td class="p-2">
                                    <input type="time" name="jam_selesai[]" required
                                           oninput="hitungBarisLembur(this.closest('tr'))"
                                           class="selesai-lembur w-full p-1.5 border border-blue-300 bg-blue-50/50 rounded text-center font-black text-blue-900 focus:ring-1 focus:ring-blue-500 outline-none" style="min-width: 90px;">
                                </td>
                                <td class="p-2">
                                    <select name="istirahat[]" onchange="hitungBarisLembur(this.closest('tr'))"
                                            class="ist-lembur w-full p-1.5 border border-amber-300 bg-amber-50/50 rounded text-center font-bold text-amber-900 outline-none" style="min-width: 95px;">
                                        <option value="0">0 mnt</option>
                                        <option value="30">30 mnt</option>
                                        <option value="60">60 mnt</option>
                                    </select>
                                </td>
                                <td class="p-2 text-center bg-slate-50 border-l border-r border-slate-300 font-bold text-slate-700" style="min-width: 95px;">
                                    <span class="display-riil text-xs">0 Jam</span>
                                    <input type="hidden" name="durasi_riil[]" class="hidden-riil" value="0">
                                </td>
                                <td class="p-2 text-center bg-blue-50/50 border-r border-slate-300 font-black text-blue-800 text-sm" style="min-width: 105px;">
                                    <span class="display-akm text-base">0</span> <span class="text-[10px]">Jam</span>
                                    <input type="hidden" name="jam_lembur[]" class="hidden-akm" value="0">
                                    {{-- Hidden tier Depnaker --}}
                                    <input type="hidden" name="jam_15[]" class="hidden-j15" value="0">
                                    <input type="hidden" name="jam_2[]" class="hidden-j2" value="0">
                                    <input type="hidden" name="jam_3[]" class="hidden-j3" value="0">
                                    <input type="hidden" name="jam_4[]" class="hidden-j4" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="text" name="keterangan[]" placeholder="Contoh: Bongkar Muat / Sortir"
                                           class="ket-lembur w-full p-2 border border-slate-300 rounded outline-none font-medium text-slate-800 focus:border-blue-500" style="min-width: 180px;">
                                </td>
                                <td class="p-2 text-center">
                                    <button type="button" onclick="removeLemburRow(this)" class="text-slate-300 hover:text-red-600 transition-colors cursor-pointer" title="Hapus Baris">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. Footer Action Bar (Sesuai Gaya Kertas SPL) --}}
            <div class="p-6 border-t border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-4 rounded-b-2xl">
                {{-- Statistik Ringkas Real-time --}}
                <div class="flex items-center gap-6 text-xs font-bold text-slate-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-users text-indofood-blue"></i>
                        <span>Total Pekerja:</span>
                        <span id="summaryTotalPekerja" class="font-black text-slate-900 bg-white px-2.5 py-1 rounded-md border border-slate-300 shadow-sm">1 Orang</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-hourglass-half text-amber-600"></i>
                        <span>Total Akumulasi:</span>
                        <span id="summaryTotalAkm" class="font-black text-blue-800 bg-white px-2.5 py-1 rounded-md border border-slate-300 shadow-sm text-sm">0 Jam</span>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center gap-3">
                    <button type="button" onclick="resetFormLembur()" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs hover:bg-slate-100 transition uppercase tracking-wider cursor-pointer">
                        Reset Form
                    </button>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-indofood-blue text-white font-black text-xs hover:bg-blue-800 transition shadow-md uppercase tracking-wider flex items-center gap-2 cursor-pointer border-none" style="background-color: #004a99;">
                        <i class="fas fa-save text-sm"></i> Simpan Data Lembur
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- KARTU 2: TABEL RIWAYAT LEMBUR HARI INI / TERPILIH (WITA) --}}
    <div class="glass-card flex flex-col shadow-xl rounded-2xl overflow-hidden bg-white border border-slate-200">
        <div class="p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-4">
            <div>
                <h4 class="text-xs font-black uppercase text-slate-800 tracking-widest flex items-center gap-2">
                    <i class="fas fa-history text-indofood-blue"></i> Riwayat Kehadiran Lembur Terdaftar (WITA)
                </h4>
                <p class="text-[11px] text-slate-500 font-semibold mt-0.5">
                    Data lembur yang tersimpan untuk tanggal: <span class="text-blue-800 font-bold">{{ \Carbon\Carbon::parse($filterDate ?? \Carbon\Carbon::now('Asia/Makassar'))->translatedFormat('d F Y') }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-black bg-blue-100 text-blue-800 px-3 py-1 rounded-full border border-blue-200">
                    {{ $tim_assorting->where('status', 'Lembur')->count() }} Data Lembur
                </span>
            </div>
        </div>

        <div class="overflow-x-auto p-4">
            <table class="w-full text-left text-xs border-collapse border border-slate-200">
                <thead class="bg-slate-100 font-bold text-[10px] uppercase text-slate-700 tracking-wider">
                    <tr>
                        <th class="p-3 text-center border border-slate-200 w-12">NO</th>
                        <th class="p-3 border border-slate-200 min-w-[200px]">NAMA KARYAWAN</th>
                        <th class="p-3 text-center border border-slate-200 min-w-[110px] bg-blue-50/50 text-blue-900">AKM. JAM</th>
                        <th class="p-3 border border-slate-200 min-w-[220px]">WAKTU & KETERANGAN</th>
                        <th class="p-3 text-center border border-slate-200 w-24">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($tim_assorting->where('status', 'Lembur') as $data)
                        @php
                            $rawCatatan = $data->catatan ?? '';
                            $parts = explode(' | ', str_replace('Assorting: ', '', $rawCatatan));
                            $namePart = explode(' - ', $parts[0])[1] ?? $parts[0];
                            $nikPart = explode(' - ', $parts[0])[0] ?? '';

                            $jamAkm = '0';
                            if (preg_match('/Akm:\s*([0-9.]+)/i', $rawCatatan, $m)) {
                                $jamAkm = trim($m[1]);
                            }

                            $jamInfo = '';
                            if (preg_match('/\|\s*M:\s*([0-9]{2}:[0-9]{2}).*?\|\s*S:\s*([0-9]{2}:[0-9]{2})/i', $rawCatatan, $m)) {
                                $jamInfo = "[".$m[1]." - ".$m[2]."] ";
                            }

                            $ketBersih = '-';
                            $cleanString = preg_replace('/~.*?~/', '', $rawCatatan);
                            if (preg_match('/Ket:\s*([^|~]+)/i', $cleanString, $m)) {
                                $ketBersih = trim($m[1]);
                            }
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 text-center font-bold text-slate-400 border border-slate-200">{{ $loop->iteration }}</td>
                            <td class="p-3 font-extrabold uppercase text-slate-800 border border-slate-200">
                                <div>{{ $namePart }}</div>
                                @if(!empty($nikPart) && $nikPart !== $namePart)
                                    <div class="text-[10px] font-mono text-slate-400 font-bold">NIK: {{ $nikPart }}</div>
                                @endif
                            </td>
                            <td class="p-3 text-center font-black text-blue-800 bg-blue-50/30 border border-slate-200 text-sm">
                                {{ $jamAkm }} Jam
                            </td>
                            <td class="p-3 border border-slate-200">
                                @if(!empty($jamInfo))
                                    <span class="text-[11px] font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200 mr-1.5">{{ $jamInfo }}</span>
                                @endif
                                <span class="text-slate-600 font-medium uppercase">{{ str_replace('Ket:', '', $ketBersih) }}</span>
                            </td>
                            <td class="p-3 text-center border border-slate-200">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" onclick="openEditModalAbsen('{{ $data->id }}', '{{ $namePart }}', '{{ $data->status }}', '{{ $jamAkm }}')" class="text-blue-600 hover:scale-110 transition-transform cursor-pointer" title="Koreksi Data">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <form action="{{ route('attendance.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Hapus data lembur ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 hover:scale-110 transition-transform cursor-pointer border-none bg-transparent" title="Hapus Data">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 font-semibold italic">
                                Belum ada data lembur yang tercatat untuk tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- JAVASCRIPT PERHITUNGAN & INTERAKSI GAYA SPL --}}
<script>
    function updateHariLembur(dateVal) {
        if (!dateVal) return;
        const d = new Date(dateVal);
        const hari = d.toLocaleDateString('id-ID', { weekday: 'long' });
        const inputHari = document.getElementById('input_hari_lembur');
        if (inputHari) inputHari.value = hari;

        const dayIdx = d.getDay();
        const selectTipe = document.getElementById('select_tipe_hari');
        if (selectTipe) {
            if (dayIdx === 0 || dayIdx === 6) {
                selectTipe.value = 'Libur';
            } else {
                selectTipe.value = 'Kerja';
            }
            hitungSemuaBarisLembur();
        }
    }

    function syncKaryawanLembur(input, mode) {
        const row = input.closest('tr');
        if (!row) return;
        const val = (input.value || '').trim();
        if (!val) return;

        const nikInput = row.querySelector('.nik-input-lembur');
        const namaInput = row.querySelector('.nama-lembur');
        const jabatanInput = row.querySelector('.jabatan-lembur');
        const empList = window.MASTER_KARYAWAN || [];

        let emp = null;
        if (mode === 'nik') {
            emp = empList.find(e => String(e.nik).toLowerCase() === val.toLowerCase());
            if (emp) {
                if (namaInput) namaInput.value = emp.nama;
                if (jabatanInput) jabatanInput.value = emp.jabatan || 'KARYAWAN';
            } else if (val.length >= 4) {
                fetch(`/api/karyawan/${encodeURIComponent(val)}`)
                    .then(res => res.ok ? res.json() : null)
                    .then(data => {
                        if (data && data.nama) {
                            if (namaInput) namaInput.value = data.nama;
                            if (jabatanInput) jabatanInput.value = data.posisi || 'KARYAWAN';
                        }
                    }).catch(() => {});
            }
        } else if (mode === 'nama') {
            emp = empList.find(e => String(e.nama).toLowerCase() === val.toLowerCase());
            if (emp) {
                if (nikInput) nikInput.value = emp.nik;
                if (jabatanInput) jabatanInput.value = emp.jabatan || 'KARYAWAN';
            }
        }
    }

    function handleNikInputLembur(input) {
        syncKaryawanLembur(input, 'nik');
    }

    function timeStringToDec(t) {
        if (!t) return 0;
        const p = t.split(':');
        return parseInt(p[0]) + (parseInt(p[1]) / 60);
    }

    function hitungBarisLembur(row) {
        if (!row) return;
        const inMulai = row.querySelector('.mulai-lembur');
        const inSelesai = row.querySelector('.selesai-lembur');
        const inIst = row.querySelector('.ist-lembur');

        if (!inMulai || !inSelesai) return;

        const valMulai = inMulai.value;
        const valSelesai = inSelesai.value;
        if (!valMulai || !valSelesai) {
            updateRowDisplay(row, 0, 0, 0, 0, 0, 0);
            updateSummaryLembur();
            return;
        }

        let m = timeStringToDec(valMulai);
        let s = timeStringToDec(valSelesai);
        const ist = parseFloat(inIst ? inIst.value : 0) || 0;
        const tipeHari = document.getElementById('select_tipe_hari')?.value || 'Kerja';

        if (s < m) s += 24;

        let r = (s - m) - (ist / 60);
        if (r < 0) r = 0;

        let akm = 0;
        let j15 = 0, j2 = 0, j3 = 0, j4 = 0;

        if (tipeHari === 'Kerja') {
            if (r > 0) {
                j15 = r >= 1 ? 1 : r;
                j2 = r > 1 ? r - 1 : 0;
            }
        } else {
            if (r <= 7) {
                j2 = r;
            } else if (r <= 8) {
                j2 = 7;
                j3 = r - 7;
            } else {
                j2 = 7;
                j3 = 1;
                j4 = r - 8;
            }
        }

        akm = (j15 * 1.5) + (j2 * 2) + (j3 * 3) + (j4 * 4);

        updateRowDisplay(row, r, akm, j15, j2, j3, j4);
        updateSummaryLembur();
    }

    function updateRowDisplay(row, r, akm, j15, j2, j3, j4) {
        const dispRiil = row.querySelector('.display-riil');
        const dispAkm = row.querySelector('.display-akm');
        const hidRiil = row.querySelector('.hidden-riil');
        const hidAkm = row.querySelector('.hidden-akm');
        const hidJ15 = row.querySelector('.hidden-j15');
        const hidJ2 = row.querySelector('.hidden-j2');
        const hidJ3 = row.querySelector('.hidden-j3');
        const hidJ4 = row.querySelector('.hidden-j4');

        if (dispRiil) dispRiil.innerText = r.toFixed(1).replace('.0', '') + ' Jam';
        if (dispAkm) dispAkm.innerText = akm.toFixed(1).replace('.0', '');

        if (hidRiil) hidRiil.value = r.toFixed(2);
        if (hidAkm) hidAkm.value = akm.toFixed(1).replace('.0', '');
        if (hidJ15) hidJ15.value = (j15 || 0).toFixed(2);
        if (hidJ2) hidJ2.value = (j2 || 0).toFixed(2);
        if (hidJ3) hidJ3.value = (j3 || 0).toFixed(2);
        if (hidJ4) hidJ4.value = (j4 || 0).toFixed(2);
    }

    function hitungSemuaBarisLembur() {
        document.querySelectorAll('#tbody-lembur tr').forEach(row => hitungBarisLembur(row));
    }

    function terapkanIstirahatSemua(val) {
        document.querySelectorAll('#tbody-lembur .ist-lembur').forEach(sel => {
            sel.value = val;
            hitungBarisLembur(sel.closest('tr'));
        });
    }

    function updateSummaryLembur() {
        const rows = document.querySelectorAll('#tbody-lembur tr');
        let totalPekerja = 0;
        let totalAkm = 0;

        rows.forEach(r => {
            const nik = r.querySelector('.nik-input-lembur')?.value.trim();
            if (nik) totalPekerja++;
            const akmVal = parseFloat(r.querySelector('.hidden-akm')?.value || 0);
            totalAkm += isNaN(akmVal) ? 0 : akmVal;
        });

        const spanPekerja = document.getElementById('summaryTotalPekerja');
        const spanAkm = document.getElementById('summaryTotalAkm');
        if (spanPekerja) spanPekerja.innerText = `${totalPekerja || rows.length} Orang`;
        if (spanAkm) spanAkm.innerText = `${totalAkm.toFixed(1).replace('.0', '')} Jam`;
    }

    function addLemburRow() {
        const tbody = document.getElementById('tbody-lembur');
        if (!tbody) return;

        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const defaultIst = document.getElementById('select_default_istirahat')?.value || '0';

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50 border-b border-slate-300';
        tr.innerHTML = `
            <td class="p-2 text-center font-black text-slate-500">${rowCount}</td>
            <td class="p-2 col-nik-lembur">
                <div class="relative flex items-center">
                    <input type="text" name="id_karyawan[]" placeholder="Pilih / NIK" required
                           oninput="syncKaryawanLembur(this, 'nik')"
                           onchange="syncKaryawanLembur(this, 'nik')"
                           class="nik-input-lembur w-full px-2.5 py-2 pr-7 border-2 border-slate-300 rounded-lg focus:border-blue-600 focus:bg-white outline-none text-center font-black text-xs tracking-wider font-mono text-slate-900 bg-slate-50 cursor-pointer"
                           autocomplete="off" list="karyawan_nik_list">
                    <button type="button" onclick="openPickerDropdown(this, 'nik', 'lembur')" tabindex="-1" class="absolute right-1 text-slate-400 hover:text-blue-600 p-1 cursor-pointer" title="Klik untuk pilih NIK">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                </div>
            </td>
            <td class="p-2">
                <div class="relative flex items-center">
                    <input type="text" name="nama_karyawan[]" placeholder="Pilih / Nama Karyawan" required
                           oninput="syncKaryawanLembur(this, 'nama')"
                           onchange="syncKaryawanLembur(this, 'nama')"
                           class="nama-lembur w-full p-2 pr-7 border border-slate-300 rounded focus:border-blue-500 outline-none uppercase font-black text-xs text-slate-800 cursor-pointer" 
                           style="min-width: 210px;"
                           autocomplete="off" list="karyawan_nama_list">
                    <button type="button" onclick="openPickerDropdown(this, 'nama', 'lembur')" tabindex="-1" class="absolute right-1 text-slate-400 hover:text-blue-600 p-1 cursor-pointer" title="Klik untuk pilih Nama">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                </div>
            </td>
            <td class="p-2">
                <input type="text" name="jabatan[]" placeholder="Jabatan" readonly
                       class="jabatan-lembur w-full p-2 border border-slate-200 bg-slate-100 rounded text-slate-600 font-bold text-center text-xs" style="min-width: 140px;">
            </td>
            <td class="p-2">
                <input type="time" name="jam_mulai[]" required
                       oninput="hitungBarisLembur(this.closest('tr'))"
                       class="mulai-lembur w-full p-1.5 border border-blue-300 bg-blue-50/50 rounded text-center font-black text-blue-900 focus:ring-1 focus:ring-blue-500 outline-none" style="min-width: 90px;">
            </td>
            <td class="p-2">
                <input type="time" name="jam_selesai[]" required
                       oninput="hitungBarisLembur(this.closest('tr'))"
                       class="selesai-lembur w-full p-1.5 border border-blue-300 bg-blue-50/50 rounded text-center font-black text-blue-900 focus:ring-1 focus:ring-blue-500 outline-none" style="min-width: 90px;">
            </td>
            <td class="p-2">
                <select name="istirahat[]" onchange="hitungBarisLembur(this.closest('tr'))"
                        class="ist-lembur w-full p-1.5 border border-amber-300 bg-amber-50/50 rounded text-center font-bold text-amber-900 outline-none" style="min-width: 95px;">
                    <option value="0" ${defaultIst == '0' ? 'selected' : ''}>0 mnt</option>
                    <option value="30" ${defaultIst == '30' ? 'selected' : ''}>30 mnt</option>
                    <option value="60" ${defaultIst == '60' ? 'selected' : ''}>60 mnt</option>
                </select>
            </td>
            <td class="p-2 text-center bg-slate-50 border-l border-r border-slate-300 font-bold text-slate-700" style="min-width: 95px;">
                <span class="display-riil text-xs">0 Jam</span>
                <input type="hidden" name="durasi_riil[]" class="hidden-riil" value="0">
            </td>
            <td class="p-2 text-center bg-blue-50/50 border-r border-slate-300 font-black text-blue-800 text-sm" style="min-width: 105px;">
                <span class="display-akm text-base">0</span> <span class="text-[10px]">Jam</span>
                <input type="hidden" name="jam_lembur[]" class="hidden-akm" value="0">
                <input type="hidden" name="jam_15[]" class="hidden-j15" value="0">
                <input type="hidden" name="jam_2[]" class="hidden-j2" value="0">
                <input type="hidden" name="jam_3[]" class="hidden-j3" value="0">
                <input type="hidden" name="jam_4[]" class="hidden-j4" value="0">
            </td>
            <td class="p-2">
                <input type="text" name="keterangan[]" placeholder="Contoh: Bongkar Muat / Sortir"
                       class="ket-lembur w-full p-2 border border-slate-300 rounded outline-none font-medium text-slate-800 focus:border-blue-500" style="min-width: 180px;">
            </td>
            <td class="p-2 text-center">
                <button type="button" onclick="removeLemburRow(this)" class="text-slate-300 hover:text-red-600 transition-colors cursor-pointer" title="Hapus Baris">
                    <i class="fas fa-times-circle text-lg"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        updateLemburRowNumbers();
        updateSummaryLembur();
    }

    function removeLemburRow(button) {
        const tbody = document.getElementById('tbody-lembur');
        if (!tbody) return;
        const rows = tbody.querySelectorAll('tr');
        if (rows.length <= 1) {
            alert('Minimal harus ada 1 baris input lembur.');
            return;
        }
        const row = button.closest('tr');
        if (row) row.remove();
        updateLemburRowNumbers();
        updateSummaryLembur();
    }

    function updateLemburRowNumbers() {
        const rows = document.querySelectorAll('#tbody-lembur tr');
        rows.forEach((row, index) => {
            const cell = row.querySelector('td:first-child');
            if (cell) cell.innerText = index + 1;
        });
    }

    function resetFormLembur() {
        if (!confirm('Yakin ingin mereset seluruh form input lembur?')) return;
        const tbody = document.getElementById('tbody-lembur');
        if (tbody) {
            const rows = tbody.querySelectorAll('tr');
            for (let i = 1; i < rows.length; i++) {
                rows[i].remove();
            }
            const firstRow = rows[0];
            if (firstRow) {
                firstRow.querySelectorAll('input').forEach(inp => inp.value = '');
                firstRow.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);
                updateRowDisplay(firstRow, 0, 0, 0, 0, 0, 0);
            }
        }
        updateSummaryLembur();
    }

    function validateFormLembur() {
        const rows = document.querySelectorAll('#tbody-lembur tr');
        let hasValidRow = false;

        for (const row of rows) {
            const nik = row.querySelector('.nik-input-lembur')?.value.trim();
            const nama = row.querySelector('.nama-lembur')?.value.trim();
            const mulai = row.querySelector('.mulai-lembur')?.value;
            const selesai = row.querySelector('.selesai-lembur')?.value;

            if (nik || nama || mulai || selesai) {
                if (!nik) {
                    alert('NIK Pekerja wajib diisi pada baris yang aktif.');
                    return false;
                }
                if (!nama) {
                    alert(`Nama Karyawan untuk NIK ${nik} wajib diisi.`);
                    return false;
                }
                if (!mulai || !selesai) {
                    alert(`Jam Mulai dan Jam Selesai wajib diisi untuk ${nama}.`);
                    return false;
                }
                hasValidRow = true;
            }
        }

        if (!hasValidRow) {
            alert('Silakan isi minimal 1 baris data lembur pekerja.');
            return false;
        }

        return true;
    }
</script>
