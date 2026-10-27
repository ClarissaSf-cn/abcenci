@php
    $uniqueShifts = collect($schedules)->flatMap(function($s) {
        return [$s->shift1->wst_code ?? null, $s->shift2->wst_code ?? null, $s->shift3->wst_code ?? null];
    })->filter()->unique();
@endphp

@php
    $scheduledNik = collect($schedules)->pluck('nik')->unique()->values();
    $emps = isset($employees) ? collect($employees) : collect();
    $totalEmployees = $emps->count();
    $registeredCount = $emps->filter(function($e) use ($scheduledNik) {
        $nik = $e->nik ?? '';
        return $nik && $scheduledNik->contains($nik);
    })->count();
    $unregisteredCount = $totalEmployees - $registeredCount;
@endphp

{{-- MENAMPILKAN PESAN ERROR JIKA VALIDASI CONTROLLER GAGAL --}}
@if ($errors->any())
    <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 mb-4 rounded-xl shadow-sm">
        <div class="flex items-center mb-2">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong class="font-bold text-[11px] uppercase tracking-wider">Gagal Menyimpan Jadwal!</strong>
        </div>
        <ul class="list-disc pl-6 text-[11px] font-semibold">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- DATALIST GLOBAL UNTUK NIK KARYAWAN --}}
<datalist id="dl_nik_global">
    @foreach($emps as $emp)
        <option value="{{ $emp->nik }}">{{ $emp->nama ?? $emp->user?->name ?? 'Tanpa Nama' }}</option>
    @endforeach
</datalist>

<div class="glass-card flex-1 overflow-hidden flex flex-col border-t-4 border-emerald-500 shadow-md rounded-2xl">
    <div class="px-4 sm:px-7 py-3.5 sm:py-5 border-b border-slate-100 bg-white flex flex-wrap justify-between items-center gap-3 sm:gap-4">
        <div>
            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest"><i class="fas fa-calendar-day text-emerald-500 mr-2"></i> Jadwal Karyawan Harian</h3>
            <p class="text-[10px] font-bold text-slate-400 mt-0.5 sm:mt-1 uppercase tracking-wide">Pemetaan/plotting penugasan shift per individu (Hanya Berlaku Sehari)</p>
        </div>

        {{-- KUMPULAN TOMBOL AKSI --}}
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            {{-- PERBAIKAN: target="_blank" DIHAPUS DI SINI --}}
            <a href="{{ route('schedules.export-pdf', ['shift_filter' => request('shift_filter'), 'shift_group' => request('shift_group')]) }}" class="bg-red-500 text-white px-3 sm:px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-red-600 transition shadow flex items-center">
                <i class="fas fa-file-pdf mr-1.5 sm:mr-2 text-sm"></i> PDF
            </a>

            <a href="{{ route('schedules.export-excel') }}" class="bg-green-600 text-white px-3 sm:px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-green-700 transition shadow flex items-center">
                <i class="fas fa-file-excel mr-1.5 sm:mr-2 text-sm"></i> EXCEL
            </a>

            <button onclick="document.getElementById('modalAddSchedule').classList.remove('hidden')" class="bg-indofood-blue text-white px-3 sm:px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-blue-800 transition shadow flex items-center">
                <i class="fas fa-calendar-plus mr-1.5 sm:mr-2 text-sm"></i> Plot Jadwal
            </button>
        </div>
    </div>

    {{-- AREA FILTER PENCARIAN SPESIFIK & TOMBOL AKSI MASSAL --}}
    <div class="px-4 sm:px-7 py-3 bg-slate-50 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3 shrink-0">

        {{-- Ringkasan singkat: tampilkan di bar filter (compact) di paling kiri --}}
        <div class="flex items-center gap-4 text-sm text-slate-600 order-1 md:order-1">
            <div class="flex items-center gap-2">
                <span class="text-slate-500">Total</span>
                <span class="font-black text-slate-800">{{ $totalEmployees }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500">Sudah terdaftar</span>
                <span class="font-black text-emerald-700">{{ $registeredCount }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500">Belum terdaftar</span>
                <span class="font-black text-amber-700">{{ $unregisteredCount }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2 h-9 order-2 md:order-2">
            {{-- Tombol ini otomatis muncul jika ada checkbox yang dicentang --}}
            <button onclick="prosesJadwalTerpilih()" id="btnBulkActionJadwal" class="hidden bg-slate-700 text-white px-4 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest hover:bg-slate-800 transition shadow-sm flex items-center">
                <i class="fas fa-tasks mr-2"></i> Proses Terpilih (<span id="countSelectedJadwal">0</span>)
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto order-3 md:order-3">
            {{-- FILTER BARU: Jenis Jadwal --}}
            <select id="filterJenisJadwal" onchange="filterSchedules()" class="text-[11px] font-semibold text-slate-600 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none shadow-sm cursor-pointer">
                <option value="">Semua Jenis Jadwal</option>
                <option value="reguler">Reguler</option>
                <option value="jadwal khusus">Jadwal Khusus</option>
                <option value="tidak normal">Tidak Normal</option>
                <option value="per tanggal">Per Tanggal</option>
            </select>

            {{-- Filter Spesifik: Shift Aktif --}}
            <select id="filterShiftCode" onchange="filterSchedules()" class="text-[11px] font-semibold text-slate-600 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none shadow-sm cursor-pointer">
                <option value="">Semua Shift</option>
                @foreach($uniqueShifts as $code)
                    <option value="{{ $code }}">{{ $code }}</option>
                @endforeach
            </select>

            {{-- Filter Global (Cari Teks) --}}
            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" id="searchSchedule" onkeyup="filterSchedules()" placeholder="Cari NIK, Nama, atau Tanggal..." class="w-full pl-9 pr-4 py-2 text-[11px] font-semibold text-slate-700 border border-slate-200 rounded-xl focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-shadow shadow-sm bg-white placeholder-slate-400">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto p-5 bg-slate-50 flex-1">
        <table class="w-full text-left border-collapse table-bordered whitespace-nowrap bg-white shadow-sm rounded-xl overflow-hidden" id="scheduleTable">
            <thead class="bg-slate-100 text-slate-600 text-[9px] uppercase tracking-wider font-black border-b-2 border-slate-200 sticky top-0 z-10">
                <tr>
                    {{-- KOLOM CHECKBOX SELECT ALL --}}
                    <th class="p-3 text-center w-10">
                        <input type="checkbox" id="checkAllSchedules" onclick="toggleAllSchedules(this)" class="w-3.5 h-3.5 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                    </th>
                    <th class="p-3.5 text-center">No</th>
                    <th class="p-3.5 text-center">Tanggal Penugasan</th>
                    <th class="p-3.5 text-center">Hari Kerja</th>
                    <th class="p-3.5 text-center">NIK Karyawan</th>
                    <th class="p-3.5">Nama Lengkap</th>
                    <th class="p-3.5">Jenis Jadwal</th>
                    <th class="p-3.5">Catatan Khusus</th>

                    {{-- 3 KOLOM SHIFT & JAM --}}
                    <th class="p-3.5 text-center bg-sky-50 border-l border-white">Shift Aktif 1</th>
                    <th class="p-3.5 text-center bg-sky-50">Jam Shift 1</th>
                    <th class="p-3.5 text-center bg-amber-50 border-l border-white">Shift Aktif 2</th>
                    <th class="p-3.5 text-center bg-amber-50">Jam Shift 2</th>
                    <th class="p-3.5 text-center bg-violet-100 border-l border-white">Shift Aktif 3</th>
                    <th class="p-3.5 text-center bg-violet-100">Jam Shift 3</th>
                    <th class="p-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-[11px]">
                @forelse($schedules as $index => $schedule)
                @php
                    $shift1 = $schedule->shift1 ?? null;
                    $shift2 = $schedule->shift2 ?? null;
                    $shift3 = $schedule->shift3 ?? null;

                    $jam1 = $shift1 ? \Carbon\Carbon::parse($shift1->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift1->time_end)->format('H:i') : '-';
                    $jam2 = $shift2 ? \Carbon\Carbon::parse($shift2->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift2->time_end)->format('H:i') : '-';
                    $jam3 = $shift3 ? \Carbon\Carbon::parse($shift3->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift3->time_end)->format('H:i') : '-';

                    $searchString = strtolower(\Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/y') . ' ' . ($schedule->hari_kerja ?? '') . ' ' . $schedule->nik . ' ' . ($schedule->employee?->nama ?? $schedule->karyawan?->nama ?? '') . ' ' . ($schedule->schedule_type ?? '') . ' ' . ($schedule->special_note ?? '') . ' ' . ($shift1->wst_code ?? '') . ' ' . ($shift2->wst_code ?? '') . ' ' . ($shift3->wst_code ?? ''));
                @endphp

                <tr class="text-slate-700 hover:bg-emerald-50/50 transition border-b border-slate-100 schedule-row"
                    data-search="{{ $searchString }}"
                    data-jenis="{{ strtolower($schedule->schedule_type ?? 'reguler') }}">

                    {{-- CHECKBOX INDIVIDU --}}
                    <td class="p-3 text-center">
                        <input type="checkbox" class="schedule-checkbox w-3.5 h-3.5 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer" value="{{ $schedule->id }}" onchange="updateBulkActionBtnSchedules()">
                    </td>

                    <td class="p-3.5 text-center schedule-no">{{ $index + 1 }}</td>
                    <td class="p-3.5 text-center text-slate-500">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') }}</td>
                    <td class="p-3.5 text-center font-bold text-slate-700">{{ $schedule->hari_kerja ?? '-' }}</td>
                    <td class="p-3.5 text-center"><span class="text-blue-700 underline">{{ $schedule->nik }}</span></td>
                    <td class="p-3.5 text-slate-800 uppercase">{{ $schedule->employee?->nama ?? $schedule->karyawan?->nama ?? $schedule->user?->name ?? '-' }}</td>
                    <td class="p-3.5 text-slate-800 font-bold uppercase">{{ $schedule->schedule_type ?? 'Reguler' }}</td>
                    <td class="p-3.5 text-slate-600">{{ $schedule->special_note ?? '-' }}</td>

                    {{-- KOLOM SHIFT 1 --}}
                    <td class="p-3.5 text-center bg-sky-50/30 border-l border-white">
                        @if($shift1)
                            <span class="bg-sky-100 text-sky-700 px-3 py-1 rounded border border-sky-200 font-bold">{{ $shift1->wst_code }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-3.5 text-center bg-sky-50/30 font-semibold text-sky-700">{{ $jam1 }}</td>

                    {{-- KOLOM SHIFT 2 --}}
                    <td class="p-3.5 text-center bg-amber-50/30 border-l border-white">
                        @if($shift2)
                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded border border-amber-200 font-bold">{{ $shift2->wst_code }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-3.5 text-center bg-amber-50/30 font-semibold text-amber-700">{{ $jam2 }}</td>

                    {{-- KOLOM SHIFT 3 --}}
                    <td class="p-3.5 text-center bg-violet-50/30 border-l border-white">
                        @if($shift3)
                            <span class="bg-violet-100 text-violet-700 px-3 py-1 rounded border border-violet-300 font-bold">{{ $shift3->wst_code }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-3.5 text-center bg-violet-50/30 font-semibold text-violet-700">{{ $jam3 }}</td>

                    {{-- TOMBOL AKSI CRUD (warna disamakan dengan rekap absensi) --}}
                    <td class="p-3 text-center border-l border-white">
                        <div class="flex justify-center gap-3">
                            {{-- EDIT: warna biru seperti rekap absensi --}}
                            <button type="button" data-schedule="{{ base64_encode(json_encode($schedule)) }}" onclick="openEditScheduleModal(this)" class="text-blue-500 hover:text-blue-700 hover:scale-110 transition-transform" title="Edit Jadwal">
                                <i class="fas fa-edit"></i>
                            </button>
                            {{-- HAPUS: warna merah seperti rekap absensi --}}
                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus plotting jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition-transform hover:scale-110" title="Hapus Jadwal">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRowSchedule"><td colspan="15" class="p-8 text-center text-slate-400 italic text-sm bg-white">Belum ada plotting jadwal karyawan hari ini.</td></tr>
                @endforelse

                {{-- Pesan Data Tidak Ditemukan Saat Filter Kosong --}}
                <tr id="noResultRowSchedule" class="hidden"><td colspan="15" class="p-8 text-center text-rose-500 italic text-sm font-bold bg-white">Pencarian / Filter tidak menemukan hasil.</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL PLOT JADWAL BARU --}}
<div id="modalAddSchedule" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-3 sm:p-4 md:p-6 bg-slate-900/60 backdrop-blur-sm transition-opacity" style="z-index: 9999; overflow-y: auto;">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden transform scale-100 transition-transform flex flex-col my-auto" style="max-height: calc(100vh - 2.5rem); max-height: calc(100dvh - 2.5rem);">
        <div class="px-6 py-4.5 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0" style="flex-shrink: 0;">
            <h3 class="font-bold text-slate-800 text-sm tracking-wide flex items-center gap-2"><i class="fas fa-calendar-plus text-blue-600"></i> Plotting Jadwal Shift</h3>
            <button type="button" onclick="document.getElementById('modalAddSchedule').classList.add('hidden')" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 transition cursor-pointer"><i class="fas fa-times text-sm"></i></button>
        </div>

        <form id="formAddSchedule" action="{{ route('schedules.store') }}#jadwal-karyawan" method="POST" class="p-5 sm:p-7 space-y-4 sm:space-y-5 overflow-y-auto flex-1 modal-scrollable-body" style="min-height: 0; flex: 1 1 auto; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Tanggal & Hari Kerja</label>
                    <div class="flex gap-2">
                        <input type="date" name="schedule_date" required class="form-input-modern w-full" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <select name="hari_kerja" id="input_hari_kerja" class="form-input-modern w-full" required>
                            <option value="" disabled selected>Pilih Hari Kerja</option>
                            <option value="4 HK">4 HK</option>
                            <option value="5 HK">5 HK</option>
                            <option value="6 HK">6 HK</option>
                            <option value="7 HK">7 HK</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">NIK & Nama Karyawan</label>
                    <div class="flex gap-2">
                        <input type="text" name="nik" id="inputNikJadwal" placeholder="Pilih/Ketik NIK" required class="form-input-modern w-full" list="dl_nik_global" oninput="autoFillNamaJadwal(this)">
                        <input type="text" id="inputNamaJadwal" placeholder="Otomatis terisi..." readonly class="form-input-modern w-full bg-slate-100 text-slate-500 border-slate-200 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Jenis Jadwal</label>
                    <select name="schedule_type" id="input_schedule_type" class="form-input-modern w-full" required>
                        <option value="Reguler">Reguler</option>
                        <option value="Jadwal Khusus">Jadwal Khusus</option>
                        <option value="Tidak Normal">Tidak Normal</option>
                        <option value="Per Tanggal">Per Tanggal</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Catatan Khusus</label>
                    <input type="text" name="special_note" id="input_special_note" class="form-input-modern w-full" placeholder="Contoh: Masuk jam 08:00">
                </div>
            </div>

            {{-- 3 INPUT SHIFT --}}
            <div class="pt-2">
                <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-2 border-b pb-1">Pilih Shift Kerja</label>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-[9px] font-bold text-sky-600 block mb-1">Shift 1</label>
                        <input type="text" id="inputShiftLabel1" placeholder="Pilih Shift 1" list="dl_shifts" class="form-input-modern bg-sky-50/30 border-sky-200 focus:border-sky-500 focus:ring-sky-500 w-full" autocomplete="off">
                        <input type="hidden" name="shift_1_id" id="inputShiftId1">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-amber-600 block mb-1">Shift 2</label>
                        <input type="text" id="inputShiftLabel2" placeholder="Pilih Shift 2" list="dl_shifts" class="form-input-modern bg-amber-50/30 border-amber-200 focus:border-amber-500 focus:ring-amber-500 w-full" autocomplete="off">
                        <input type="hidden" name="shift_2_id" id="inputShiftId2">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-slate-700 block mb-1">Shift 3</label>
                        <input type="text" id="inputShiftLabel3" placeholder="Pilih Shift 3" list="dl_shifts" class="form-input-modern bg-slate-50 border-slate-300 focus:border-slate-500 focus:ring-slate-500 w-full" autocomplete="off">
                        <input type="hidden" name="shift_3_id" id="inputShiftId3">
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 mt-2 italic">*Kosongkan form shift jika karyawan tidak bertugas di shift tersebut.</p>
            </div>

            <datalist id="dl_shifts">
                @if(isset($shifts) && count($shifts) > 0)
                    @foreach($shifts as $shift)
                        <option data-id="{{ $shift->id }}" value="{{ $shift->wst_code }} ({{ \Carbon\Carbon::parse($shift->time_start)->format('H:i') }}-{{ \Carbon\Carbon::parse($shift->time_end)->format('H:i') }})">{{ $shift->wst_code }}</option>
                    @endforeach
                @endif
            </datalist>

            <div class="pt-4 border-t border-slate-100 mt-2">
                <button type="submit" class="bg-indofood-blue w-full text-white font-extrabold py-3 rounded-xl uppercase text-xs tracking-widest shadow-md hover:bg-blue-800 transition">POST JADWAL</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT JADWAL --}}
<div id="modalEditSchedule" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-3 sm:p-4 md:p-6 bg-slate-900/60 backdrop-blur-sm transition-opacity" style="z-index: 9999; overflow-y: auto;">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden transform scale-100 transition-transform flex flex-col my-auto" style="max-height: calc(100vh - 2.5rem); max-height: calc(100dvh - 2.5rem);">
        <div class="px-6 py-4.5 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0" style="flex-shrink: 0;">
            <h3 class="font-bold text-slate-800 text-sm tracking-wide flex items-center gap-2"><i class="fas fa-calendar-check text-blue-600"></i> Edit Jadwal Shift</h3>
            <button type="button" onclick="document.getElementById('modalEditSchedule').classList.add('hidden')" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 transition cursor-pointer"><i class="fas fa-times text-sm"></i></button>
        </div>

        <form id="formEditSchedule" action="#jadwal-karyawan" method="POST" class="p-5 sm:p-7 space-y-4 sm:space-y-5 overflow-y-auto flex-1 modal-scrollable-body" style="min-height: 0; flex: 1 1 auto; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Tanggal & Hari Kerja</label>
                    <div class="flex gap-2">
                        <input type="date" name="schedule_date" id="edit_schedule_date" required class="form-input-modern w-full">
                        <select name="hari_kerja" id="edit_hari_kerja" class="form-input-modern w-full" required>
                            <option value="" disabled>Pilih Hari Kerja</option>
                            <option value="4 HK">4 HK</option>
                            <option value="5 HK">5 HK</option>
                            <option value="6 HK">6 HK</option>
                            <option value="7 HK">7 HK</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">NIK & Nama Karyawan</label>
                    <div class="flex gap-2">
                        <input type="text" name="nik" id="edit_inputNikJadwal" placeholder="Pilih/Ketik NIK" required class="form-input-modern w-full" list="dl_nik_global" oninput="autoFillNamaEditJadwal(this)">
                        <input type="text" id="edit_inputNamaJadwal" placeholder="Otomatis terisi..." readonly class="form-input-modern w-full bg-slate-100 text-slate-500 border-slate-200 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Jenis Jadwal</label>
                    <select name="schedule_type" id="edit_schedule_type" class="form-input-modern w-full" required>
                        <option value="Reguler">Reguler</option>
                        <option value="Jadwal Khusus">Jadwal Khusus</option>
                        <option value="Tidak Normal">Tidak Normal</option>
                        <option value="Per Tanggal">Per Tanggal</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1">Catatan Khusus</label>
                    <input type="text" name="special_note" id="edit_special_note" class="form-input-modern w-full" placeholder="Contoh: Datang siang">
                </div>
            </div>

            {{-- 3 INPUT SHIFT EDIT --}}
            <div class="pt-2">
                <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-2 border-b pb-1">Pilih Shift Kerja</label>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-[9px] font-bold text-sky-600 block mb-1">Shift 1</label>
                        <input type="text" id="edit_inputShiftLabel1" placeholder="Pilih Shift 1" list="dl_shifts" class="form-input-modern bg-sky-50/30 border-sky-200 w-full" autocomplete="off">
                        <input type="hidden" name="shift_1_id" id="edit_shift_id_1">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-amber-600 block mb-1">Shift 2</label>
                        <input type="text" id="edit_inputShiftLabel2" placeholder="Pilih Shift 2" list="dl_shifts" class="form-input-modern bg-amber-50/30 border-amber-200 w-full" autocomplete="off">
                        <input type="hidden" name="shift_2_id" id="edit_shift_id_2">
                    </div>
                    <div>
                        <label class="text-[9px] font-bold text-slate-700 block mb-1">Shift 3</label>
                        <input type="text" id="edit_inputShiftLabel3" placeholder="Pilih Shift 3" list="dl_shifts" class="form-input-modern bg-slate-50 border-slate-300 w-full" autocomplete="off">
                        <input type="hidden" name="shift_3_id" id="edit_shift_id_3">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-2">
                <button type="submit" class="bg-indofood-blue w-full text-white font-extrabold py-3 rounded-xl uppercase text-xs tracking-widest shadow-md hover:bg-blue-800 transition">SIMPAN PERUBAHAN</button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT UNTUK FILTER GABUNGAN, CHECKBOX & AUTOFILL --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalAddSchedule = document.getElementById('modalAddSchedule');
        const modalEditSchedule = document.getElementById('modalEditSchedule');
        if (modalAddSchedule) document.body.appendChild(modalAddSchedule);
        if (modalEditSchedule) document.body.appendChild(modalEditSchedule);
    });

    function openEditScheduleModal(btn) {
        try {
            let base64str = btn.dataset.schedule;
            let decodedStr = decodeURIComponent(escape(window.atob(base64str)));
            let scheduleData = JSON.parse(decodedStr);

            document.getElementById('formEditSchedule').action = `{{ url('schedules') }}/${scheduleData.id}#jadwal-karyawan`;

            document.getElementById('edit_schedule_date').value = scheduleData.schedule_date.substring(0, 10);
            document.getElementById('edit_hari_kerja').value = scheduleData.hari_kerja ?? '';
            document.getElementById('edit_inputNikJadwal').value = scheduleData.nik;
            document.getElementById('edit_schedule_type').value = scheduleData.schedule_type ?? 'Reguler';
            document.getElementById('edit_special_note').value = scheduleData.special_note ?? '';

            const shiftIds = [scheduleData.shift_1_id, scheduleData.shift_2_id, scheduleData.shift_3_id];

            for(let num = 1; num <= 3; num++) {
                let hiddenInput = document.getElementById('edit_shift_id_' + num);
                let labelInput = document.getElementById('edit_inputShiftLabel' + num);

                if(hiddenInput) hiddenInput.value = shiftIds[num-1] ?? '';
                if(labelInput) labelInput.value = '';

                if(shiftIds[num-1]) {
                    const dl = document.getElementById('dl_shifts');
                    if (dl) {
                        const opts = dl.querySelectorAll('option');
                        for (let i = 0; i < opts.length; i++) {
                            if (String(opts[i].dataset.id) === String(shiftIds[num-1])) {
                                labelInput.value = opts[i].value;
                                break;
                            }
                        }
                    }
                }
            }

            autoFillNamaEditJadwal(document.getElementById('edit_inputNikJadwal'));
            document.getElementById('modalEditSchedule').classList.remove('hidden');
        } catch (e) {
            console.error("Gagal memuat data jadwal:", e);
            Swal.fire('Error!', 'Gagal memuat data.', 'error');
        }
    }

    function autoFillNamaJadwal(inputNik) {
        let inputNama = document.getElementById('inputNamaJadwal');
        let datalist = document.getElementById('dl_nik_global');

        if (datalist && inputNama) {
            let options = datalist.options;
            let found = false;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === inputNik.value) {
                    inputNama.value = options[i].text;
                    found = true;
                    break;
                }
            }
            if (!found) inputNama.value = '';
        }
    }

    function autoFillNamaEditJadwal(inputNik) {
        let inputNama = document.getElementById('edit_inputNamaJadwal');
        let datalist = document.getElementById('dl_nik_global');

        if (datalist && inputNama) {
            let options = datalist.options;
            let found = false;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === inputNik.value) {
                    inputNama.value = options[i].text;
                    found = true;
                    break;
                }
            }
            if (!found) inputNama.value = '';
        }
    }

    function syncShiftSelection(inputElem, hiddenId, datalistId) {
        const hidden = document.getElementById(hiddenId);
        const dl = document.getElementById(datalistId);
        if (!hidden) return;
        hidden.value = '';

        if (dl && inputElem.value.trim() !== '') {
            const opts = dl.querySelectorAll('option');
            let typedVal = inputElem.value.toLowerCase().trim();

            for (let i = 0; i < opts.length; i++) {
                let optVal = opts[i].value.toLowerCase();
                if (optVal.includes(typedVal) || optVal === typedVal) {
                    hidden.value = opts[i].dataset.id || '';
                    break;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        ['1', '2', '3'].forEach(num => {
            const addLabel = document.getElementById('inputShiftLabel' + num);
            const editLabel = document.getElementById('edit_inputShiftLabel' + num);

            if (addLabel) {
                addLabel.addEventListener('input', function() { syncShiftSelection(this, 'inputShiftId' + num, 'dl_shifts'); });
            }
            if (editLabel) {
                editLabel.addEventListener('input', function() { syncShiftSelection(this, 'edit_shift_id_' + num, 'dl_shifts'); });
            }
        });

        const formAdd = document.getElementById('formAddSchedule');
        if (formAdd) {
            formAdd.addEventListener('submit', function(e) {
                const h1 = document.getElementById('inputShiftId1').value;
                const h2 = document.getElementById('inputShiftId2').value;
                const h3 = document.getElementById('inputShiftId3').value;
                if (!h1 && !h2 && !h3) {
                    e.preventDefault();
                    Swal.fire('Peringatan', 'Minimal satu Shift Kerja harus diisi secara valid melalui pilihan dropdown!', 'warning');
                }
            });
        }

        const formEdit = document.getElementById('formEditSchedule');
        if (formEdit) {
            formEdit.addEventListener('submit', function(e) {
                const h1 = document.getElementById('edit_shift_id_1').value;
                const h2 = document.getElementById('edit_shift_id_2').value;
                const h3 = document.getElementById('edit_shift_id_3').value;
                if (!h1 && !h2 && !h3) {
                    e.preventDefault();
                    Swal.fire('Peringatan', 'Minimal satu Shift Kerja harus diisi secara valid melalui pilihan dropdown!', 'warning');
                }
            });
        }
    });

    function filterSchedules() {
        let inputSearch = document.getElementById("searchSchedule").value.toLowerCase();
        let filterShift = document.getElementById("filterShiftCode").value;
        let filterJenis = document.getElementById("filterJenisJadwal").value.toLowerCase();

        let rows = document.querySelectorAll(".schedule-row");
        let visibleCount = 0;

        rows.forEach(row => {
            let searchData = row.getAttribute("data-search");
            let rowJenis = row.getAttribute("data-jenis");

            let matchSearch = searchData.includes(inputSearch);
            let matchShift = (filterShift === "" || searchData.includes(filterShift.toLowerCase()));
            let matchJenis = (filterJenis === "" || rowJenis === filterJenis);

            if (matchSearch && matchShift && matchJenis) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
                let cb = row.querySelector('.schedule-checkbox');
                if(cb) cb.checked = false;
            }
        });

        let currentNo = 1;
        rows.forEach(row => {
            if (row.style.display !== "none") {
                row.querySelector(".schedule-no").textContent = currentNo++;
            }
        });

        let noResultRow = document.getElementById('noResultRowSchedule');
        if (noResultRow) {
            noResultRow.style.display = (visibleCount === 0 && rows.length > 0) ? "" : "none";
        }

        updateBulkActionBtnSchedules();
    }

    function toggleAllSchedules(source) {
        let checkboxes = document.querySelectorAll('.schedule-checkbox');
        checkboxes.forEach(cb => {
            let row = cb.closest('tr');
            if (row.style.display !== 'none') {
                cb.checked = source.checked;
            }
        });
        updateBulkActionBtnSchedules();
    }

    function updateBulkActionBtnSchedules() {
        let selectedCount = document.querySelectorAll('.schedule-checkbox:checked').length;
        let btn = document.getElementById('btnBulkActionJadwal');
        let countSpan = document.getElementById('countSelectedJadwal');

        let visibleRows = Array.from(document.querySelectorAll('.schedule-row')).filter(r => r.style.display !== 'none');
        let visibleCheckboxes = visibleRows.map(r => r.querySelector('.schedule-checkbox'));
        let allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);

        document.getElementById('checkAllSchedules').checked = allChecked;

        if (selectedCount > 0) {
            btn.classList.remove('hidden');
            countSpan.textContent = selectedCount;
        } else {
            btn.classList.add('hidden');
        }
    }

    function prosesJadwalTerpilih() {
        let selected = [];
        document.querySelectorAll('.schedule-checkbox:checked').forEach(cb => {
            selected.push(cb.value);
        });

        if(selected.length > 0) {
            Swal.fire({
                title: 'Data Terpilih',
                text: "ID Jadwal yang Anda pilih: " + selected.join(', '),
                icon: 'info',
                confirmButtonColor: '#004a99'
            });
        }
    }
</script>
