@php
    $uniqueGroups = collect($shifts)->pluck('group_ws_type')->filter()->unique();
@endphp

<div class="glass-card flex-1 overflow-hidden flex flex-col border-t-4 border-indofood-blue shadow-md rounded-2xl">
    <div class="px-4 sm:px-7 py-3.5 sm:py-5 border-b border-slate-100 bg-white flex flex-wrap justify-between items-center gap-3 sm:gap-4">
        <div>
            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest"><i class="fas fa-clock text-indofood-blue mr-2"></i> Master Work Schedule</h3>
            <p class="text-[10px] font-bold text-slate-400 mt-0.5 sm:mt-1 uppercase tracking-wide">Kelola referensi jam kerja dan istirahat karyawan</p>
        </div>

        {{-- KUMPULAN TOMBOL AKSI --}}
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('shifts.export-pdf') }}" target="_blank" class="bg-red-500 text-white px-3 sm:px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-red-600 transition shadow flex items-center">
                <i class="fas fa-file-pdf mr-1.5 sm:mr-2 text-sm"></i> PDF
            </a>
            <a href="{{ route('shifts.export-excel') }}" class="bg-green-600 text-white px-3 sm:px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-emerald-700 transition shadow flex items-center">
                <i class="fas fa-file-excel mr-1.5 sm:mr-2 text-sm"></i> EXCEL
            </a>

            <form id="formImportShift" action="{{ route('shifts.import') }}" method="POST" enctype="multipart/form-data" class="m-0">
                @csrf
                <label for="inputImportFile" class="bg-indofood-blue text-white px-3 sm:px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-blue-800 transition shadow flex items-center cursor-pointer">
                    <i class="fas fa-file-excel mr-1.5 sm:mr-2 text-sm"></i> Import Shift
                </label>
                <input id="inputImportFile" type="file" name="file_excel" accept=".xlsx,.xls,.csv" class="hidden" onchange="uploadImportShift(this)">
            </form>

            <button onclick="document.getElementById('modalAddShift').classList.remove('hidden')" class="bg-indofood-blue text-white px-3 sm:px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-blue-800 transition shadow flex items-center">
                <i class="fas fa-plus-circle mr-1.5 sm:mr-2 text-sm"></i> Tambah Shift
            </button>
        </div>
    </div>

    {{-- AREA FILTER PENCARIAN SPESIFIK & TOMBOL AKSI MASSAL --}}
    <div class="px-4 sm:px-7 py-3 bg-slate-50 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3 shrink-0">
        <div class="flex items-center gap-2 h-9">
            <button onclick="prosesDataTerpilih()" id="btnBulkAction" class="hidden bg-slate-700 text-white px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest hover:bg-slate-800 transition shadow-sm flex items-center">
                <i class="fas fa-tasks mr-2"></i> Proses Terpilih (<span id="countSelected">0</span>)
            </button>

            <button onclick="bulkDeleteShifts()" id="btnBulkDelete" class="hidden bg-red-500 text-white px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest hover:bg-red-600 transition shadow-sm flex items-center">
                <i class="fas fa-trash-alt mr-2"></i> Hapus Terpilih
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select id="filterOvernight" onchange="filterShifts()" class="text-[11px] font-semibold text-slate-600 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-indofood-blue focus:ring-1 focus:ring-indofood-blue outline-none shadow-sm cursor-pointer">
                <option value="">Semua Lintas Hari</option>
                <option value="Ya">Lintas Hari: Ya (H+1)</option>
                <option value="Tidak">Lintas Hari: Tidak</option>
            </select>

            <select id="filterGroup" onchange="filterShifts()" class="text-[11px] font-semibold text-slate-600 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-indofood-blue focus:ring-1 focus:ring-indofood-blue outline-none shadow-sm cursor-pointer">
                <option value="">Semua Grup WS</option>
                @foreach($uniqueGroups as $group)
                    <option value="{{ $group }}">{{ $group }}</option>
                @endforeach
            </select>

            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" id="searchShift" onkeyup="filterShifts()" placeholder="Cari Kode / Keterangan WST..." class="w-full pl-9 pr-4 py-2 text-[11px] font-semibold text-slate-700 border border-slate-200 rounded-xl focus:border-indofood-blue focus:ring-1 focus:ring-indofood-blue outline-none transition-shadow shadow-sm bg-white placeholder-slate-400">
            </div>
        </div>
    </div>

    <div class="overflow-y-auto p-5 bg-slate-50 flex-1">
        <table class="w-full text-left border-collapse table-bordered whitespace-nowrap bg-white shadow-sm rounded-xl overflow-hidden" id="shiftTable">

            <thead class="bg-slate-100 text-slate-600 text-[9px] uppercase tracking-wider font-black border-b-2 border-slate-200 sticky top-0 z-10">
                <tr>
                    <th class="p-3 text-center w-10">
                        <input type="checkbox" id="checkAllShifts" onclick="toggleAllShifts(this)" class="w-3.5 h-3.5 text-indofood-blue rounded border-slate-300 focus:ring-indofood-blue cursor-pointer">
                    </th>
                            <th class="p-3 text-center">No</th>
                    <th class="p-3 text-center">Kode Jam Kerja</th>
                    <th class="p-3">Keterangan</th>
                    <th class="p-3 text-center">Awal Jam Kerja</th>
                    <th class="p-3 text-center">Akhir Jam Kerja</th>
                    <th class="p-3 text-center">Kode Istirahat</th>
                    <th class="p-3 text-center">Mulai Istiraha</th>
                    <th class="p-3 text-center">Akhir Istirahat</th>
                    <th class="p-3 text-center">Total Istirahat</th>
                    <th class="p-3 text-center">Total Jam Kerja</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-[11px]">
                @php
                    $page = method_exists($shifts, 'currentPage') ? $shifts->currentPage() : 1;
                    $perPage = method_exists($shifts, 'perPage') ? $shifts->perPage() : 15;
                    $offset = ($page - 1) * $perPage;
                @endphp

                @forelse($shifts as $index => $shift)
                <tr class="text-slate-700 hover:bg-blue-50/50 transition border-b border-slate-100 shift-row"
                    data-search="{{ strtolower($shift->wst_code . ' ' . $shift->wst_text) }}"
                    data-overnight="{{ $shift->is_overnight ? 'Ya' : 'Tidak' }}"
                    data-group="{{ $shift->group_ws_type }}">

                    <td class="p-3 text-center">
                        <input type="checkbox" class="shift-checkbox w-3.5 h-3.5 text-indofood-blue rounded border-slate-300 focus:ring-indofood-blue cursor-pointer" value="{{ $shift->id }}" onchange="updateBulkActionBtn()">
                    </td>

<td class="p-3 text-center shift-no">{{ $offset + $index + 1 }}</td>
                    <td class="p-3 text-center"><span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg border border-blue-200">{{ $shift->wst_code }}</span></td>
                    <td class="p-3">{{ $shift->wst_text }}</td>
                    <td class="p-3 text-center text-emerald-600">{{ \Carbon\Carbon::parse($shift->time_start)->format('H:i') }}</td>
                    <td class="p-3 text-center text-rose-600">{{ \Carbon\Carbon::parse($shift->time_end)->format('H:i') }}</td>
                    <td class="p-3 text-center">{{ $shift->group_ws_type ?? '-' }}</td>
                    <td class="p-3 text-center text-slate-500">{{ $shift->break1_start ? \Carbon\Carbon::parse($shift->break1_start)->format('H:i') : '-' }}</td>
                    <td class="p-3 text-center text-slate-500">{{ $shift->break1_end ? \Carbon\Carbon::parse($shift->break1_end)->format('H:i') : '-' }}</td>
                    <td class="p-3 text-center text-slate-700">{{ ($shift->break1_duration ?? 0) + ($shift->break2_duration ?? 0) }} menit</td>
                    <td class="p-3 text-center text-slate-700">
                        @php
                            $start = \Carbon\Carbon::parse($shift->time_start);
                            $end = \Carbon\Carbon::parse($shift->time_end);
                            if ($end->lt($start)) { $end->addDay(); }
                            $totalMinutes = $start->diffInMinutes($end) - (($shift->break1_duration ?? 0) + ($shift->break2_duration ?? 0));
                        @endphp
                        {{ intdiv($totalMinutes, 60) }} jam {{ $totalMinutes % 60 }} mnt
                    </td>

                    {{-- TOMBOL AKSI CRUD --}}
                    <td class="p-3 text-center">
                        <div class="flex justify-center gap-3">
                            <button type="button" data-shift="{{ base64_encode(json_encode($shift)) }}" onclick="openEditShiftModal(this)" class="text-blue-500 hover:scale-110 transition-transform" title="Edit Shift">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus master shift ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition-transform hover:scale-110" title="Hapus Shift">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow"><td colspan="12" class="p-8 text-center text-slate-400 italic text-sm bg-white">Belum ada data Master Shift.</td></tr>
                @endforelse

                <tr id="noResultRow" class="hidden"><td colspan="12" class="p-8 text-center text-rose-500 italic text-sm font-bold bg-white">Pencarian / Filter tidak menemukan hasil.</td></tr>
            </tbody>
        </table>

        <div class="mt-4 flex justify-center">
            @php
                $currentPage = method_exists($shifts, 'currentPage') ? $shifts->currentPage() : 1;
                $lastPage = method_exists($shifts, 'lastPage') ? $shifts->lastPage() : 1;
                $baseUrl = url('/dashboard');
            @endphp

            @if($lastPage > 1)
                <nav aria-label="Pagination Master Shift">
                    <ul class="inline-flex items-center gap-2">
                        @for($p = 1; $p <= $lastPage; $p++)
                            <li>
                                <a href="{{ $baseUrl }}?page={{ $p }}#master-shift"
                                   class="px-3 py-1 rounded-lg text-xs font-bold border transition {{ $p === $currentPage ? 'bg-indofood-blue text-white border-indofood-blue' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                                    {{ $p }}
                                </a>
                            </li>
                        @endfor
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div>

{{-- MODAL TAMBAH SHIFT --}}
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
                <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Keterangan WST</label>
                <input type="text" name="wst_text" class="form-input-modern">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Jam Mulai</label>
                    <input type="time" name="time_start" required class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Jam Selesai</label>
                    <input type="time" name="time_end" required class="form-input-modern">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Mulai Istirahat 1</label>
                    <input type="time" name="break1_start" class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Selesai Istirahat 1</label>
                    <input type="time" name="break1_end" class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Mulai Istirahat 2</label>
                    <input type="time" name="break2_start" class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Selesai Istirahat 2</label>
                    <input type="time" name="break2_end" class="form-input-modern">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_overnight" id="is_overnight" class="w-4 h-4 text-indofood-blue rounded">
                <label for="is_overnight" class="text-xs font-bold text-slate-600">Shift Lintas Hari (Overnight / H+1)</label>
            </div>

            <button type="submit" class="w-full bg-indofood-blue text-white font-extrabold py-3 rounded-xl uppercase text-xs tracking-widest shadow-md hover:bg-blue-800 transition">SIMPAN MASTER</button>
        </form>
    </div>
</div>

{{-- MODAL EDIT SHIFT --}}
<div id="modalEditShift" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-black text-slate-700 text-sm uppercase tracking-widest">Edit Master Shift</h3>
            <button type="button" onclick="document.getElementById('modalEditShift').classList.add('hidden')" class="text-slate-400 hover:text-rose-500"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form id="formEditShift" action="#" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Kode WST</label>
                    <input type="text" name="wst_code" id="edit_wst_code" required class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Tipe Grup WS</label>
                    <input type="text" name="group_ws_type" id="edit_group_ws_type" class="form-input-modern">
                </div>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Keterangan WST</label>
                <input type="text" name="wst_text" id="edit_wst_text" class="form-input-modern">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Jam Mulai</label>
                    <input type="time" name="time_start" id="edit_time_start" required class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Jam Selesai</label>
                    <input type="time" name="time_end" id="edit_time_end" required class="form-input-modern">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Mulai Istirahat 1</label>
                    <input type="time" name="break1_start" id="edit_break1_start" class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Selesai Istirahat 1</label>
                    <input type="time" name="break1_end" id="edit_break1_end" class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Mulai Istirahat 2</label>
                    <input type="time" name="break2_start" id="edit_break2_start" class="form-input-modern">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase block mb-1">Selesai Istirahat 2</label>
                    <input type="time" name="break2_end" id="edit_break2_end" class="form-input-modern">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_overnight" id="edit_is_overnight" class="w-4 h-4 text-indofood-blue rounded">
                <label for="edit_is_overnight" class="text-xs font-bold text-slate-600">Shift Lintas Hari (Overnight / H+1)</label>
            </div>

            <button type="submit" class="w-full bg-emerald-600 text-white font-extrabold py-3 rounded-xl uppercase text-xs tracking-widest shadow-md hover:bg-emerald-800 transition">PERBARUI MASTER</button>
        </form>
    </div>
</div>

{{-- SCRIPT UNTUK FILTER, CHECKBOX, DAN MODAL EDIT --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalAdd = document.getElementById('modalAddShift');
        const modalEdit = document.getElementById('modalEditShift');
        if (modalAdd) document.body.appendChild(modalAdd);
        if (modalEdit) document.body.appendChild(modalEdit);
    });

    async function uploadImportShift(input) {
        const file = input.files && input.files[0];
        if (!file) return;
        const form = document.getElementById('formImportShift');
        const action = form ? form.action : '/shifts/import';
        const csrfToken = '{{ csrf_token() }}';

        input.disabled = true;
        Swal.fire({
            title: 'Mengunggah file...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const fd = new FormData(form);
            if (!fd.get('file_excel')) fd.append('file_excel', file);

            const res = await fetch(action, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            Swal.close();

            const contentType = res.headers.get('content-type') || '';
            if (contentType.indexOf('application/json') !== -1) {
                const data = await res.json();
if (data.success) {
                    Swal.fire('Sukses', data.message || 'Import selesai', 'success').then(() => window.location.href = '/dashboard#master-shift');
                } else {
                    const msg = data.message || (data.errors ? JSON.stringify(data.errors) : 'Terjadi kesalahan saat import');
                    Swal.fire('Gagal', msg, 'error');
                }
            } else {
if (res.ok) {
                    Swal.fire('Selesai', 'Upload selesai. Memuat ulang untuk menampilkan hasil.', 'success').then(() => window.location.href = '/dashboard#master-shift');
                } else {
                    const text = await res.text();
                    Swal.fire('Gagal', text || 'Terjadi kesalahan saat upload', 'error');
                }
            }
        } catch (e) {
            Swal.close();
            console.error('Import error:', e);
            Swal.fire('Gagal', String(e), 'error');
        } finally {
            input.disabled = false;
            input.value = '';
        }
    }

    function openEditShiftModal(btn) {
        try {
            let base64str = btn.dataset.shift;
            let decodedStr = decodeURIComponent(escape(window.atob(base64str)));
            let shiftData = JSON.parse(decodedStr);

document.getElementById('formEditShift').action = `/shifts/${shiftData.id}`;

            document.getElementById('edit_wst_code').value = shiftData.wst_code;
            document.getElementById('edit_group_ws_type').value = shiftData.group_ws_type || '';
            document.getElementById('edit_wst_text').value = shiftData.wst_text || '';

            document.getElementById('edit_time_start').value = shiftData.time_start ? shiftData.time_start.substring(0, 5) : '';
            document.getElementById('edit_time_end').value = shiftData.time_end ? shiftData.time_end.substring(0, 5) : '';
            document.getElementById('edit_break1_start').value = shiftData.break1_start ? shiftData.break1_start.substring(0, 5) : '';
            document.getElementById('edit_break1_end').value = shiftData.break1_end ? shiftData.break1_end.substring(0, 5) : '';
            document.getElementById('edit_break2_start').value = shiftData.break2_start ? shiftData.break2_start.substring(0, 5) : '';
            document.getElementById('edit_break2_end').value = shiftData.break2_end ? shiftData.break2_end.substring(0, 5) : '';

            document.getElementById('edit_is_overnight').checked = shiftData.is_overnight == 1 ? true : false;

            document.getElementById('modalEditShift').classList.remove('hidden');
        } catch (e) {
            console.error("Gagal memuat data shift:", e);
            Swal.fire('Error!', 'Gagal memuat data.', 'error');
        }
    }

    function filterShifts() {
        let inputSearch = document.getElementById("searchShift").value.toLowerCase();
        let filterOvernight = document.getElementById("filterOvernight").value;
        let filterGroup = document.getElementById("filterGroup").value;

        let rows = document.querySelectorAll(".shift-row");
        let visibleCount = 0;

        rows.forEach(row => {
            let searchData = row.getAttribute("data-search");
            let rowOvernight = row.getAttribute("data-overnight");
            let rowGroup = row.getAttribute("data-group");

            let matchSearch = searchData.includes(inputSearch);
            let matchOvernight = (filterOvernight === "" || rowOvernight === filterOvernight);
            let matchGroup = (filterGroup === "" || rowGroup === filterGroup);

            if (matchSearch && matchOvernight && matchGroup) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
                let cb = row.querySelector('.shift-checkbox');
                if(cb) cb.checked = false;
            }
        });

        let currentNo = 1;
        rows.forEach(row => {
            if (row.style.display !== "none") {
                row.querySelector(".shift-no").textContent = currentNo++;
            }
        });

        let noResultRow = document.getElementById('noResultRow');
        if (noResultRow) {
            noResultRow.style.display = (visibleCount === 0 && rows.length > 0) ? "" : "none";
        }

        updateBulkActionBtn();
    }

    function toggleAllShifts(source) {
        let checkboxes = document.querySelectorAll('.shift-checkbox');
        checkboxes.forEach(cb => {
            let row = cb.closest('tr');
            if (row.style.display !== 'none') {
                cb.checked = source.checked;
            }
        });
        updateBulkActionBtn();
    }

    function updateBulkActionBtn() {
        let selectedCount = document.querySelectorAll('.shift-checkbox:checked').length;
        let btn = document.getElementById('btnBulkAction');
        let countSpan = document.getElementById('countSelected');
        let btnDelete = document.getElementById('btnBulkDelete');

        let visibleRows = Array.from(document.querySelectorAll('.shift-row')).filter(r => r.style.display !== 'none');
        let visibleCheckboxes = visibleRows.map(r => r.querySelector('.shift-checkbox'));
        let allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);

        document.getElementById('checkAllShifts').checked = allChecked;

        if (selectedCount > 0) {
            btn.classList.remove('hidden');
            if (btnDelete) btnDelete.classList.remove('hidden');
            countSpan.textContent = selectedCount;
        } else {
            btn.classList.add('hidden');
            if (btnDelete) btnDelete.classList.add('hidden');
        }
    }

    function prosesDataTerpilih() {
        let selected = [];
        document.querySelectorAll('.shift-checkbox:checked').forEach(cb => {
            selected.push(cb.value);
        });

        if(selected.length > 0) {
            Swal.fire({
                title: 'Data Terpilih',
                text: "ID Shift yang Anda pilih: " + selected.join(', '),
                icon: 'info',
                confirmButtonColor: '#004a99'
            });
        }
    }

    function bulkDeleteShifts() {
        let selected = Array.from(document.querySelectorAll('.shift-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) return;

        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Anda yakin ingin menghapus <b>${selected.length}</b> shift terpilih?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus'
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            const csrfToken = '{{ csrf_token() }}';
            const btnDelete = document.getElementById('btnBulkDelete');
            if (btnDelete) btnDelete.disabled = true;

            try {
                const res = await fetch('/shifts/bulk-destroy', {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ ids: selected })
                });

                if (!res.ok) {
                    const errorData = await res.json().catch(() => null);
                    const message = errorData?.message || 'Terjadi kesalahan saat menghapus shift.';
                    throw new Error(message);
                }

                selected.forEach(id => {
                    const cb = document.querySelector('.shift-checkbox[value="' + id + '"]');
                    if (cb) {
                        const row = cb.closest('tr');
                        if (row) row.remove();
                    }
                });

Swal.fire('Sukses', 'Data berhasil dihapus.', 'success').then(() => window.location.href = '/dashboard#master-shift');
                filterShifts();
            } catch (e) {
console.error('Bulk delete failed:', e);
Swal.fire('Gagal', e.message || 'Terjadi kesalahan saat menghapus shift.', 'error').then(() => window.location.href = '/dashboard#master-shift');
                console.error('Bulk delete failed:', e);
            } finally {
                if (btnDelete) btnDelete.disabled = false;
            }
        });
    }
</script>
