{{-- TAB PENGAJUAN SURAT PERINTAH LEMBUR (SPL) --}}
<div id="tab-pengajuan-spl" class="tab-content hidden flex-1 flex-col gap-6">
    <div id="spl-print-area" class="glass-card flex-1 overflow-hidden flex flex-col shadow-xl rounded-2xl border-t-4 border-indofood-blue bg-white">
        <form id="splForm" method="POST" action="{{ route('spl.store') }}">
            @csrf
            <input type="hidden" name="download" id="spl_download" value="0">

        {{-- Header Panel --}}
        <div class="px-4 sm:px-7 py-3.5 sm:py-5 border-b border-slate-200 flex flex-wrap justify-between items-center gap-3 sm:gap-4 bg-slate-50">
            <div>
                <h3 class="text-base sm:text-lg font-black text-indofood-blue uppercase tracking-widest">Surat Perintah Lembur Pekerja Alih Daya (Outsourcing)</h3>
                <p class="text-xs font-bold text-slate-500 mt-0.5 sm:mt-1 tracking-wide">Formulir Pengajuan & Pembuatan Dokumen Cetak</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="bg-amber-100 text-amber-700 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-[10px] sm:text-[11px] font-black uppercase tracking-widest border border-amber-300 shadow-xs">
                    <i class="fas fa-clock mr-1"></i> Status: Draft Baru
                </span>
            </div>
        </div>

        {{-- Form Header SPL (Sesuai Kertas Fisik) --}}
        <div class="p-4 sm:p-6 border-b border-slate-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-8">
                <div>
                    <label class="text-[11px] font-black text-slate-600 uppercase ml-1">Tanggal SPL</label>
                          <input type="date" id="input_tanggal_spl" name="tanggal" value="{{ \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d') }}"
                           onchange="document.getElementById('input_hari_spl').value = new Date(this.value).toLocaleDateString('id-ID', {weekday: 'long'});"
                           class="mt-1 w-full p-2.5 border-2 border-slate-300 rounded-lg focus:border-indofood-blue outline-none text-sm font-bold text-slate-800 transition-colors">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-600 uppercase ml-1">Hari SPL</label>
                    <input type="text" id="input_hari_spl" value="{{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->isoFormat('dddd') }}" readonly
                           class="mt-1 w-full p-2.5 border-2 border-slate-200 rounded-lg bg-slate-100 text-slate-600 font-bold uppercase text-sm cursor-not-allowed">
                </div>
                <div>
                    <label class="text-[11px] font-black text-slate-600 uppercase ml-1">Nama Vendor</label>
                    <input id="input_nama_vendor" list="list-vendor" name="nama_vendor" class="mt-1 w-full p-2.5 border-2 border-slate-300 rounded-lg focus:border-indofood-blue outline-none text-sm font-bold text-slate-800 transition-colors" placeholder="Pilih atau ketik vendor...">
                    <datalist id="list-vendor">
                        <option value="PT. TROPIS SERVICE">
                        @if(isset($master_karyawan))
                            @foreach($master_karyawan->pluck('vendor_os')->filter()->unique() as $v)
                                <option value="{{ $v }}">
                            @endforeach
                        @endif
                    </datalist>

                    {{-- Datalist Master Karyawan untuk NIK & Nama Pekerja --}}
                    <datalist id="karyawan_nik_list">
                        @if(isset($master_karyawan))
                            @foreach($master_karyawan as $emp)
                                <option value="{{ $emp->nik }}">{{ $emp->nama }} ({{ $emp->jabatan ?? '-' }})</option>
                            @endforeach
                        @endif
                    </datalist>

                    <datalist id="karyawan_nama_list">
                        @if(isset($master_karyawan))
                            @foreach($master_karyawan as $emp)
                                <option value="{{ $emp->nama }}">{{ $emp->nik }} ({{ $emp->jabatan ?? '-' }})</option>
                            @endforeach
                        @endif
                    </datalist>
                </div>
            </div>
        </div>

        <style>
            /* Styling NIK proporsional dan bersih tanpa animasi zoom/perbesar */
            .col-nik {
                min-width: 160px !important;
                width: 160px !important;
            }
            .nik-input {
                min-width: 140px !important;
                font-size: 14px !important;
                font-weight: 800 !important;
                letter-spacing: 1px !important;
                font-family: 'Consolas', 'Courier New', monospace !important;
            }
            .nik-input:focus {
                z-index: 20 !important;
                background-color: #ffffff !important;
                border-color: #004a99 !important;
                box-shadow: 0 0 0 2px rgba(0, 74, 153, 0.25) !important;
            }
        </style>

        {{-- Tabel Detail Pekerja (Sesuai Kertas Fisik) --}}
        <div class="flex-1 overflow-y-auto p-6 bg-white min-h-[300px]">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                <div>
                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-list text-indofood-blue"></i> Rincian Pekerja Lembur
                    </h4>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Input nomor NIK dan rincian jam lembur pekerja</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="addSPLRow()" class="bg-indofood-blue text-white px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider hover:bg-blue-800 transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border-2 border-slate-300 shadow-sm bg-white" id="splTableWrapper">
                <table id="splTable" class="w-full min-w-[1380px] text-left whitespace-nowrap text-xs border-collapse transition-all duration-200">
                    {{-- HEADER TABEL YANG DIPERJELAS --}}
                    <thead class="bg-slate-100 text-slate-800 text-[11px] uppercase font-black text-center align-middle">
                        <tr>
                            <th rowspan="2" class="p-3 border border-slate-300 w-12">No</th>
                            <th rowspan="2" class="p-3 border border-slate-300 col-nik bg-blue-50/60 text-blue-900">
                                <span>NIK PEKERJA</span>
                            </th>
                            <th rowspan="2" class="p-3 border border-slate-300 min-w-[220px]">Nama Pekerja</th>
                            <th rowspan="2" class="p-3 border border-slate-300 min-w-[160px]">Jabatan</th>
                            <th colspan="2" class="p-2 border border-slate-300 bg-slate-200 min-w-[190px]">Jam Kerja</th>
                            <th colspan="2" class="p-2 border border-slate-300 bg-blue-100 text-blue-900 min-w-[200px]">Rencana Lembur</th>
                            <th colspan="2" class="p-2 border border-slate-300 bg-amber-100 text-amber-900 min-w-[200px]">Istirahat Lembur</th>
                            <th rowspan="2" class="p-3 border border-slate-300 min-w-[95px] w-24">Total</th>
                            <th rowspan="2" class="p-3 border border-slate-300 min-w-[180px]">Alasan Lembur</th>
                            <th rowspan="2" class="p-3 border border-slate-300 min-w-[180px]">Keterangan Lembur</th>
                            <th rowspan="2" class="p-3 border border-slate-300 w-12"><i class="fas fa-cog text-slate-500"></i></th>
                        </tr>
                        <tr>
                            <th class="p-2 border border-slate-300 bg-slate-200 min-w-[95px]">In</th>
                            <th class="p-2 border border-slate-300 bg-slate-200 min-w-[95px]">Out</th>
                            <th class="p-2 border border-slate-300 bg-blue-100 text-blue-900 min-w-[100px]">Awal</th>
                            <th class="p-2 border border-slate-300 bg-blue-100 text-blue-900 min-w-[100px]">Akhir</th>
                            <th class="p-2 border border-slate-300 bg-amber-100 text-amber-900 min-w-[100px]">Awal</th>
                            <th class="p-2 border border-slate-300 bg-amber-100 text-amber-900 min-w-[100px]">Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700" id="tbody-spl">
                        {{-- Baris Input 1 --}}
                        <tr class="hover:bg-slate-50 border-b border-slate-300">
                            <td class="p-2 text-center font-black text-slate-500">1</td>
                            <td class="p-2 col-nik">
                                <div class="relative flex items-center">
                                    <input type="text" name="nik[]" placeholder="Pilih / NIK" 
                                           class="nik-input w-full px-2.5 py-2 pr-7 border-2 border-slate-300 rounded-lg focus:border-blue-600 focus:bg-white outline-none text-center font-black text-xs tracking-wider font-mono text-slate-900 bg-slate-50 cursor-pointer"
                                           autocomplete="off" list="karyawan_nik_list"
                                           oninput="syncKaryawanSpl(this, 'nik')"
                                           onchange="syncKaryawanSpl(this, 'nik')">
                                    <button type="button" onclick="openPickerDropdown(this, 'nik', 'spl')" tabindex="-1" class="absolute right-1 text-slate-400 hover:text-blue-600 p-1 cursor-pointer" title="Klik untuk pilih NIK">
                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="p-2">
                                <div class="relative flex items-center">
                                    <input type="text" name="nama_pekerja[]" placeholder="Pilih / Nama Pekerja" 
                                           class="nama-pekerja-spl w-full p-2 pr-7 border border-slate-300 rounded focus:border-blue-500 outline-none uppercase font-black text-xs text-slate-800 cursor-pointer" 
                                           style="min-width: 200px;"
                                           autocomplete="off" list="karyawan_nama_list"
                                           oninput="syncKaryawanSpl(this, 'nama')"
                                           onchange="syncKaryawanSpl(this, 'nama')">
                                    <button type="button" onclick="openPickerDropdown(this, 'nama', 'spl')" tabindex="-1" class="absolute right-1 text-slate-400 hover:text-blue-600 p-1 cursor-pointer" title="Klik untuk pilih Nama">
                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="p-2">
                                <input type="text" name="jabatan[]" placeholder="Jabatan" 
                                       class="jabatan-spl w-full p-2 border border-slate-300 bg-slate-50 rounded focus:border-blue-500 outline-none text-center font-bold text-xs text-slate-700" 
                                       style="min-width: 140px;">
                            </td>

                            <td class="p-2"><input type="time" name="jam_kerja_in[]" class="w-full p-1.5 border border-slate-300 rounded text-center font-bold" style="min-width: 85px;"></td>
                            <td class="p-2"><input type="time" name="jam_kerja_out[]" class="w-full p-1.5 border border-slate-300 rounded text-center font-bold" style="min-width: 85px;"></td>

                            <td class="p-2"><input type="time" name="rencana_awal[]" class="w-full p-1.5 border border-blue-300 bg-blue-50 rounded text-center font-black text-blue-800 focus:ring-1 focus:ring-blue-500 outline-none lembur-awal" style="min-width: 90px;" onchange="hitungTotalJam(this.closest('tr'))"></td>
                            <td class="p-2"><input type="time" name="rencana_akhir[]" class="w-full p-1.5 border border-blue-300 bg-blue-50 rounded text-center font-black text-blue-800 focus:ring-1 focus:ring-blue-500 outline-none lembur-akhir" style="min-width: 90px;" onchange="hitungTotalJam(this.closest('tr'))"></td>

                            <td class="p-2"><input type="time" name="istirahat_awal[]" class="w-full p-1.5 border border-amber-300 bg-amber-50 rounded text-center font-black text-amber-800 focus:ring-1 focus:ring-amber-500 outline-none ist-awal" style="min-width: 90px;" onchange="hitungTotalJam(this.closest('tr'))"></td>
                            <td class="p-2"><input type="time" name="istirahat_akhir[]" class="w-full p-1.5 border border-amber-300 bg-amber-50 rounded text-center font-black text-amber-800 focus:ring-1 focus:ring-amber-500 outline-none ist-akhir" style="min-width: 90px;" onchange="hitungTotalJam(this.closest('tr'))"></td>

                            <td class="p-2 text-center font-black text-xl text-blue-700 bg-slate-50 border-l border-r border-slate-300" style="min-width: 90px;">
                                <span class="total-jam">0 Jam</span>
                                <input type="hidden" name="total_jam[]" class="total-jam-input" value="0">
                            </td>

                            <td class="p-2"><input type="text" name="alasan[]" placeholder="Ketik alasan..." class="w-full p-2 border border-slate-300 rounded outline-none font-medium focus:border-blue-500" style="min-width: 170px;"></td>
                            <td class="p-2"><input type="text" name="keterangan[]" placeholder="Ketik keterangan..." class="w-full p-2 border border-slate-300 rounded outline-none font-medium focus:border-blue-500" style="min-width: 170px;"></td>

                            <td class="p-2 text-center">
                                <button type="button" onclick="removeSPLRow(this)" class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer" title="Hapus Baris"><i class="fas fa-times-circle text-lg"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Panel Tanda Tangan / Editable Approval (Sesuai Kertas Fisik) --}}
        <div class="p-8 bg-slate-50 border-t border-slate-300">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">

                {{-- Dibuat Oleh --}}
                <div class="flex flex-col justify-end">
                    <p class="text-xs font-bold text-slate-800 mb-12">Dibuat Oleh</p>
                    <div class="flex items-center justify-center gap-2">
                        <input type="text" name="dibuat_oleh" id="dibuat_oleh" value="{{ Auth::check() ? Auth::user()->name : '' }}" class="w-full text-center border-b-2 border-slate-800 bg-transparent outline-none font-black text-sm uppercase pb-1 focus:border-blue-600 transition-colors placeholder-slate-400" placeholder="Nama Pembuat">
                        <button type="button" onclick="clearSignature('dibuat_oleh', this)" title="Hapus" class="text-red-500 hover:text-red-700 ml-2">✖</button>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 mt-2">Admin User</p>
                </div>

                {{-- Ditugaskan Oleh --}}
                <div class="flex flex-col justify-end">
                    <p class="text-xs font-bold text-slate-800 mb-12">Ditugaskan Oleh</p>
                    <div class="flex items-center justify-center gap-2">
                        <input type="text" name="ditugaskan_oleh" id="ditugaskan_oleh" class="w-full text-center border-b-2 border-slate-800 bg-transparent outline-none font-black text-sm uppercase pb-1 focus:border-blue-600 transition-colors placeholder-slate-300" placeholder="Ketik Nama PIC">
                        <button type="button" onclick="clearSignature('ditugaskan_oleh', this)" title="Hapus" class="text-red-500 hover:text-red-700 ml-2">✖</button>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 mt-2">PIC</p>
                </div>

                {{-- Disetujui Oleh --}}
                <div class="flex flex-col justify-end">
                    <p class="text-xs font-bold text-slate-800 mb-12">Disetujui Oleh</p>
                    <div class="flex items-center justify-center gap-2">
                        <input type="text" name="disetujui_oleh" id="disetujui_oleh" class="w-full text-center border-b-2 border-slate-800 bg-transparent outline-none font-black text-sm uppercase pb-1 focus:border-blue-600 transition-colors placeholder-slate-300" placeholder="Ketik Dept Head">
                        <button type="button" onclick="clearSignature('disetujui_oleh', this)" title="Hapus" class="text-red-500 hover:text-red-700 ml-2">✖</button>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 mt-2">Dept. Head User</p>
                </div>

                {{-- Diketahui Oleh --}}
                <div class="flex flex-col justify-end">
                    <p class="text-xs font-bold text-slate-800 mb-12">Diketahui Oleh</p>
                    <div class="flex items-center justify-center gap-2">
                        <input type="text" name="diketahui_oleh" id="diketahui_oleh" class="w-full text-center border-b-2 border-slate-800 bg-transparent outline-none font-black text-sm uppercase pb-1 focus:border-blue-600 transition-colors placeholder-slate-300" placeholder="Ketik Nama HR">
                        <button type="button" onclick="clearSignature('diketahui_oleh', this)" title="Hapus" class="text-red-500 hover:text-red-700 ml-2">✖</button>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 mt-2">Dept. Head HR</p>
                </div>

            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="p-6 border-t border-slate-200 bg-white flex justify-end gap-4 rounded-b-2xl">
            <button type="button" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs hover:bg-slate-50 transition uppercase tracking-wider">
                Batal
            </button>

            {{-- Tombol Download PDF Baru --}}
            <button type="button" onclick="submitSPL(true)" class="px-6 py-3 rounded-xl bg-rose-600 text-white font-bold text-xs hover:bg-rose-700 transition shadow-md uppercase tracking-wider flex items-center border-none" style="background-color:#dc2626;">
                <i class="fas fa-file-pdf mr-2 text-base"></i> Download PDF
            </button>

                <button type="button" onclick="submitSPL(false)" class="px-8 py-3 rounded-xl bg-indofood-blue text-white font-bold text-xs hover:bg-blue-800 transition shadow-md uppercase tracking-wider flex items-center border-none" style="background-color:#004a99;">
                <i class="fas fa-paper-plane mr-2 text-base"></i> Simpan SPL
            </button>
        </div>

    </div>
</div>

<script>
function hitungTotalJam(row) {
    const awal = row.querySelector('.lembur-awal').value;
    const akhir = row.querySelector('.lembur-akhir').value;
    const istAwal = row.querySelector('.ist-awal').value;
    const istAkhir = row.querySelector('.ist-akhir').value;

    if (awal && akhir) {
        const [h1, m1] = awal.split(':').map(Number);
        const [h2, m2] = akhir.split(':').map(Number);
        let jamLembur = (h2 - h1) + ((m2 - m1) / 60);

        if (istAwal && istAkhir) {
            const [i1, j1] = istAwal.split(':').map(Number);
            const [i2, j2] = istAkhir.split(':').map(Number);
            const jamIst = (i2 - i1) + ((j2 - j1) / 60);
            jamLembur -= jamIst;
        }

        if (!Number.isFinite(jamLembur) || jamLembur < 0) {
            jamLembur = 0;
        }

        const display = jamLembur.toFixed(1) + ' Jam';
        const totalJamSpan = row.querySelector('.total-jam');
        if (totalJamSpan) totalJamSpan.innerText = display;
        const hidden = row.querySelector('.total-jam-input');
        if (hidden) hidden.value = jamLembur.toFixed(1);
    }
}

window.MASTER_KARYAWAN = window.MASTER_KARYAWAN || (function() {
    try {
        return @json($master_karyawan ?? []);
    } catch(e) {
        return [];
    }
})();

if (!window.MASTER_KARYAWAN || window.MASTER_KARYAWAN.length === 0) {
    fetch('/api/karyawan-all')
        .then(res => res.ok ? res.json() : [])
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                window.MASTER_KARYAWAN = data;
                populateKaryawanDatalists();
            }
        })
        .catch(() => {});
}

function populateKaryawanDatalists() {
    const list = window.MASTER_KARYAWAN || [];
    if (list.length === 0) return;

    ['karyawan_nik_list', 'karyawan_nik_list_lembur'].forEach(id => {
        const dl = document.getElementById(id);
        if (dl && dl.children.length === 0) {
            dl.innerHTML = list.map(e => `<option value="${e.nik}">${e.nama} (${e.jabatan || '-'})</option>`).join('');
        }
    });

    ['karyawan_nama_list', 'karyawan_nama_list_lembur'].forEach(id => {
        const dl = document.getElementById(id);
        if (dl && dl.children.length === 0) {
            dl.innerHTML = list.map(e => `<option value="${e.nama}">${e.nik} (${e.jabatan || '-'})</option>`).join('');
        }
    });
}
document.addEventListener('DOMContentLoaded', populateKaryawanDatalists);

if (typeof window.openPickerDropdown === 'undefined') {
    let activePickerTarget = null;
    let activePickerField = 'nik';
    let activePickerType = 'spl';

    function getOrCreatePickerDropdown() {
        let el = document.getElementById('globalKaryawanPicker');
        if (!el) {
            el = document.createElement('div');
            el.id = 'globalKaryawanPicker';
            el.className = 'fixed bg-white border border-slate-300 shadow-2xl rounded-xl z-[999999] hidden flex flex-col overflow-hidden text-xs';
            el.style.width = '350px';
            el.style.maxHeight = '350px';
            el.innerHTML = `
                <div class="p-2.5 bg-slate-100 border-b border-slate-200 flex items-center gap-2">
                    <i class="fas fa-search text-slate-400"></i>
                    <input type="text" id="pickerSearchInput" placeholder="Ketik NIK / Nama / Jabatan..." class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs font-bold outline-none focus:border-blue-600" autocomplete="off">
                    <button type="button" onclick="window.closePickerDropdown()" class="text-slate-400 hover:text-slate-700 px-1 text-sm font-bold">✕</button>
                </div>
                <div id="pickerListItems" class="flex-1 overflow-y-auto divide-y divide-slate-100" style="max-height: 280px;"></div>
            `;
            document.body.appendChild(el);

            const searchInput = el.querySelector('#pickerSearchInput');
            searchInput.addEventListener('input', function() {
                window.renderPickerList(this.value);
            });

            document.addEventListener('click', function(e) {
                if (!el.contains(e.target) && !e.target.closest('button[onclick*="openPickerDropdown"]')) {
                    window.closePickerDropdown();
                }
            });
        }
        return el;
    }

    window.openPickerDropdown = function(btnOrInput, field, type) {
        const input = btnOrInput.tagName === 'INPUT' ? btnOrInput : btnOrInput.closest('div').querySelector('input');
        if (!input) return;

        activePickerTarget = input;
        activePickerField = field;
        activePickerType = type;

        const dropdown = getOrCreatePickerDropdown();
        const rect = input.getBoundingClientRect();

        const left = Math.min(window.innerWidth - 360, Math.max(10, rect.left));
        let top = rect.bottom + 4;
        if (top + 330 > window.innerHeight) {
            top = Math.max(10, rect.top - 334);
        }

        dropdown.style.left = left + 'px';
        dropdown.style.top = top + 'px';
        dropdown.classList.remove('hidden');

        const searchInput = dropdown.querySelector('#pickerSearchInput');
        searchInput.value = '';
        window.renderPickerList('');
        setTimeout(() => searchInput.focus(), 60);
    };

    window.closePickerDropdown = function() {
        const el = document.getElementById('globalKaryawanPicker');
        if (el) el.classList.add('hidden');
        activePickerTarget = null;
    };

    window.renderPickerList = function(filter) {
        const container = document.getElementById('pickerListItems');
        if (!container) return;
        const q = (filter || '').trim().toLowerCase();

        const list = (window.MASTER_KARYAWAN || []).filter(emp => {
            if (!q) return true;
            const nik = (emp.nik || '').toLowerCase();
            const nama = (emp.nama || '').toLowerCase();
            const jabatan = (emp.jabatan || '').toLowerCase();
            const vendor = (emp.vendor_os || '').toLowerCase();
            return nik.includes(q) || nama.includes(q) || jabatan.includes(q) || vendor.includes(q);
        });

        if (list.length === 0) {
            container.innerHTML = `<div class="p-4 text-center text-slate-400 italic font-semibold">Tidak ditemukan data karyawan</div>`;
            return;
        }

        container.innerHTML = list.map(emp => `
            <div class="px-3 py-2 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition-colors group" onclick="window.selectEmployeeFromPicker('${emp.nik}')">
                <div class="pr-2 text-left">
                    <div class="font-black text-slate-800 text-xs uppercase group-hover:text-blue-700">${emp.nama}</div>
                    <div class="text-[10px] text-slate-500 font-medium">${emp.jabatan || 'Karyawan'} ${emp.vendor_os ? '• ' + emp.vendor_os : ''}</div>
                </div>
                <span class="font-mono font-bold text-xs bg-slate-100 text-slate-800 px-2 py-0.5 rounded group-hover:bg-blue-600 group-hover:text-white transition-colors">${emp.nik}</span>
            </div>
        `).join('');
    };

    window.selectEmployeeFromPicker = function(nik) {
        if (!activePickerTarget) return;
        const row = activePickerTarget.closest('tr');
        if (!row) return;

        const emp = (window.MASTER_KARYAWAN || []).find(e => String(e.nik) === String(nik));
        if (!emp) return;

        if (activePickerType === 'spl') {
            const nikInput = row.querySelector('.nik-input');
            const namaInput = row.querySelector('.nama-pekerja-spl');
            const jabatanInput = row.querySelector('.jabatan-spl');
            if (nikInput) nikInput.value = emp.nik;
            if (namaInput) namaInput.value = emp.nama;
            if (jabatanInput) jabatanInput.value = emp.jabatan || '';

            const vendorInput = document.getElementById('input_nama_vendor');
            if (vendorInput && !vendorInput.value && emp.vendor_os) {
                vendorInput.value = emp.vendor_os;
            }
        } else if (activePickerType === 'lembur') {
            const nikInput = row.querySelector('.nik-input-lembur');
            const namaInput = row.querySelector('.nama-lembur');
            const jabatanInput = row.querySelector('.jabatan-lembur');
            if (nikInput) nikInput.value = emp.nik;
            if (namaInput) namaInput.value = emp.nama;
            if (jabatanInput) jabatanInput.value = emp.jabatan || '';
        }

        window.closePickerDropdown();
    };
}

function syncKaryawanSpl(input, mode) {
    const row = input.closest('tr');
    if (!row) return;
    const val = (input.value || '').trim();
    if (!val) return;

    const nikInput = row.querySelector('.nik-input');
    const namaInput = row.querySelector('.nama-pekerja-spl');
    const jabatanInput = row.querySelector('.jabatan-spl');
    const empList = window.MASTER_KARYAWAN || [];

    let emp = null;
    if (mode === 'nik') {
        emp = empList.find(e => String(e.nik).toLowerCase() === val.toLowerCase());
        if (emp) {
            if (namaInput) namaInput.value = emp.nama;
            if (jabatanInput) jabatanInput.value = emp.jabatan || '';
            const vendorInput = document.getElementById('input_nama_vendor');
            if (vendorInput && !vendorInput.value && emp.vendor_os) {
                vendorInput.value = emp.vendor_os;
            }
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
            if (jabatanInput) jabatanInput.value = emp.jabatan || '';
            const vendorInput = document.getElementById('input_nama_vendor');
            if (vendorInput && !vendorInput.value && emp.vendor_os) {
                vendorInput.value = emp.vendor_os;
            }
        }
    }
}

function addSPLRow() {
    const tbody = document.getElementById('tbody-spl');
    if (!tbody) return;

    const rowCount = tbody.querySelectorAll('tr').length + 1;
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-slate-50 border-b border-slate-300';
    tr.innerHTML = `
        <td class="p-2 text-center font-black text-slate-500">${rowCount}</td>
        <td class="p-2 col-nik">
            <div class="relative flex items-center">
                <input type="text" name="nik[]" placeholder="Pilih / NIK" 
                       class="nik-input w-full px-2.5 py-2 pr-7 border-2 border-slate-300 rounded-lg focus:border-blue-600 focus:bg-white outline-none text-center font-black text-xs tracking-wider font-mono text-slate-900 bg-slate-50 cursor-pointer"
                       autocomplete="off" list="karyawan_nik_list"
                       oninput="syncKaryawanSpl(this, 'nik')"
                       onchange="syncKaryawanSpl(this, 'nik')">
                <button type="button" onclick="openPickerDropdown(this, 'nik', 'spl')" tabindex="-1" class="absolute right-1 text-slate-400 hover:text-blue-600 p-1 cursor-pointer" title="Klik untuk pilih NIK">
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
            </div>
        </td>
        <td class="p-2">
            <div class="relative flex items-center">
                <input type="text" name="nama_pekerja[]" placeholder="Pilih / Nama Pekerja" 
                       class="nama-pekerja-spl w-full p-2 pr-7 border border-slate-300 rounded focus:border-blue-500 outline-none uppercase font-black text-xs text-slate-800 cursor-pointer" 
                       style="min-width: 200px;"
                       autocomplete="off" list="karyawan_nama_list"
                       oninput="syncKaryawanSpl(this, 'nama')"
                       onchange="syncKaryawanSpl(this, 'nama')">
                <button type="button" onclick="openPickerDropdown(this, 'nama', 'spl')" tabindex="-1" class="absolute right-1 text-slate-400 hover:text-blue-600 p-1 cursor-pointer" title="Klik untuk pilih Nama">
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
            </div>
        </td>
        <td class="p-2">
            <input type="text" name="jabatan[]" placeholder="Jabatan" 
                   class="jabatan-spl w-full p-2 border border-slate-300 bg-slate-50 rounded focus:border-blue-500 outline-none text-center font-bold text-xs text-slate-700" 
                   style="min-width: 140px;">
        </td>
        <td class="p-2"><input type="time" name="jam_kerja_in[]" class="w-full p-1.5 border border-slate-300 rounded text-center font-bold" style="min-width: 85px;"></td>
        <td class="p-2"><input type="time" name="jam_kerja_out[]" class="w-full p-1.5 border border-slate-300 rounded text-center font-bold" style="min-width: 85px;"></td>
        <td class="p-2"><input type="time" name="rencana_awal[]" class="w-full p-1.5 border border-blue-300 bg-blue-50 rounded text-center font-black text-blue-800 focus:ring-1 focus:ring-blue-500 outline-none lembur-awal" style="min-width: 90px;" onchange="hitungTotalJam(this.closest('tr'))"></td>
        <td class="p-2"><input type="time" name="rencana_akhir[]" class="w-full p-1.5 border border-blue-300 bg-blue-50 rounded text-center font-black text-blue-800 focus:ring-1 focus:ring-blue-500 outline-none lembur-akhir" style="min-width: 90px;" onchange="hitungTotalJam(this.closest('tr'))"></td>
        <td class="p-2"><input type="time" name="istirahat_awal[]" class="w-full p-1.5 border border-amber-300 bg-amber-50 rounded text-center font-black text-amber-800 focus:ring-1 focus:ring-amber-500 outline-none ist-awal" style="min-width: 90px;" onchange="hitungTotalJam(this.closest('tr'))"></td>
        <td class="p-2"><input type="time" name="istirahat_akhir[]" class="w-full p-1.5 border border-amber-300 bg-amber-50 rounded text-center font-black text-amber-800 focus:ring-1 focus:ring-amber-500 outline-none ist-akhir" style="min-width: 90px;" onchange="hitungTotalJam(this.closest('tr'))"></td>
        <td class="p-2 text-center font-black text-xl text-blue-700 bg-slate-50 border-l border-r border-slate-300" style="min-width: 90px;">
            <span class="total-jam">0 Jam</span>
            <input type="hidden" name="total_jam[]" class="total-jam-input" value="0">
        </td>
        <td class="p-2"><input type="text" name="alasan[]" placeholder="Ketik alasan..." class="w-full p-2 border border-slate-300 rounded outline-none font-medium focus:border-blue-500" style="min-width: 170px;"></td>
        <td class="p-2"><input type="text" name="keterangan[]" placeholder="Ketik keterangan..." class="w-full p-2 border border-slate-300 rounded outline-none font-medium focus:border-blue-500" style="min-width: 170px;"></td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeSPLRow(this)" class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer" title="Hapus Baris"><i class="fas fa-times-circle text-lg"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
}

function removeSPLRow(button) {
    const row = button.closest('tr');
    if (!row) return;
    row.remove();
    updateSPLRowNumbers();
}

function updateSPLRowNumbers() {
    const rows = document.querySelectorAll('#tbody-spl tr');
    rows.forEach((row, index) => {
        const cell = row.querySelector('td:first-child');
        if (cell) cell.innerText = index + 1;
    });
}

function clearSignature(name, btn) {
    const input = document.querySelector(`[name="${name}"]`);
    if (!input) return;
    input.value = '';

    const form = document.getElementById('splForm');
    if (form) {
        let hid = document.getElementById(name + '_removed');
        if (!hid) {
            hid = document.createElement('input');
            hid.type = 'hidden';
            hid.name = name + '_removed';
            hid.id = name + '_removed';
            hid.value = '1';
            form.appendChild(hid);
        } else {
            hid.value = '1';
        }
    }

    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-50');
    }
}

function submitSPL(asPdf) {
    handleSaveSPL();

    const h = document.getElementById('spl_download');
    if (h) h.value = asPdf ? '1' : '0';
    const f = document.getElementById('splForm');
    if (f) f.submit();
}
function handleSaveSPL() {
    const tanggal = document.getElementById('input_tanggal_spl').value;
    const vendor = document.getElementById('input_nama_vendor').value;
    const rows = Array.from(document.querySelectorAll('#tbody-spl tr'));
    const data = rows.map(row => ({
        nik: row.querySelector('input[name="nik[]"]')?.value || '',
        nama: row.querySelector('input[name="nama_pekerja[]"]')?.value || '',
        jabatan: row.querySelector('input[name="jabatan[]"]')?.value || '',
        jamIn: row.querySelector('input[name="jam_kerja_in[]"]')?.value || '',
        jamOut: row.querySelector('input[name="jam_kerja_out[]"]')?.value || '',
        lemburAwal: row.querySelector('input[name="rencana_awal[]"]')?.value || '',
        lemburAkhir: row.querySelector('input[name="rencana_akhir[]"]')?.value || '',
        istirahatAwal: row.querySelector('input[name="istirahat_awal[]"]')?.value || '',
        istirahatAkhir: row.querySelector('input[name="istirahat_akhir[]"]')?.value || '',
        alasan: row.querySelector('input[name="alasan[]"]')?.value || '',
        keterangan: row.querySelector('input[name="keterangan[]"]')?.value || '',
        totalJam: row.querySelector('.total-jam')?.innerText || '0 Jam'
    }));

    if (!tanggal) {
        return Swal?.fire('Peringatan', 'Tanggal SPL belum diisi.', 'warning') || alert('Tanggal SPL belum diisi.');
    }

    if (!vendor) {
        return Swal?.fire('Peringatan', 'Nama Vendor belum diisi.', 'warning') || alert('Nama Vendor belum diisi.');
    }

    if (data.every(item => !item.nik && !item.nama)) {
        return Swal?.fire('Peringatan', 'Isi minimal satu baris data pekerja.', 'warning') || alert('Isi minimal satu baris data pekerja.');
    }

    localStorage.setItem('pengajuan_spl', JSON.stringify({ tanggal, vendor, data, savedAt: new Date().toISOString() }));
    return Swal?.fire('Berhasil', 'Data SPL berhasil disimpan sementara di browser.', 'success') || alert('Data SPL berhasil disimpan sementara di browser.');
}

function handleDownloadSPL() {
    const element = document.getElementById('spl-print-area');
    if (!element) return Swal?.fire('Error', 'Area SPL tidak ditemukan.', 'error') || alert('Area SPL tidak ditemukan.');

    if (typeof window.jspdf === 'undefined' && typeof window.jsPDF === 'undefined') {
        return Swal?.fire('Error', 'Library jsPDF belum dimuat. Pastikan file vendor dimuat.', 'error') || alert('Library jsPDF belum dimuat.');
    }

    (async () => {
        if (typeof window.html2canvas === 'undefined') {
            await new Promise((res, rej) => {
                const s = document.createElement('script');
                s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                s.onload = res; s.onerror = rej; document.head.appendChild(s);
            }).catch(() => {
                return Swal?.fire('Error', 'Gagal memuat html2canvas.', 'error') || alert('Gagal memuat html2canvas.');
            });
        }

        const prevOutline = element.style.outline;
        element.style.outline = 'none';

        const canvas = await window.html2canvas(element, { scale: 2 });
        element.style.outline = prevOutline;

        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf || window;
        const pdf = new jsPDF('p', 'pt', 'a4');

        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();

        const imgProps = pdf.getImageProperties(imgData);
        const imgWidth = pdfWidth;
        const imgHeight = (imgProps.height * imgWidth) / imgProps.width;

        let heightLeft = imgHeight;
        let position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pdfHeight;

        while (heightLeft > -10) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pdfHeight;
        }

        pdf.save(`SPL_${new Date().toISOString().slice(0,10)}.pdf`);
    })();
}
</script>
