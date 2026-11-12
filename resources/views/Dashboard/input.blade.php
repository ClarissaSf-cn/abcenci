<div id="tab-input" class="tab-content hidden h-full flex flex-col">
    @php
        $filterDate = $filterDate ?? date('Y-m-d');
        $tim_assorting = $tim_assorting ?? collect();
    @endphp
    <div class="bg-transparent flex-1 flex flex-col min-h-0 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 p-5 sm:p-7 pt-4 sm:pt-6 pb-20 flex-1 min-h-0 overflow-y-auto">

            {{-- FORM INPUT KEHADIRAN --}}
            <div class="lg:col-span-5 xl:col-span-5 flex flex-col gap-6">
                {{-- Global Datalist untuk NIK Karyawan --}}
                <datalist id="dl_nik_input">
                    @if(isset($employees))
                        @foreach($employees as $emp)
                            <option value="{{ $emp->nik }}" data-name="{{ $emp->nama ?? $emp->user?->name ?? '' }}">{{ $emp->nama ?? $emp->user?->name ?? 'Tanpa Nama' }}</option>
                        @endforeach
                    @endif
                </datalist>

                <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 sm:p-7 w-full">
                    {{-- Header Section --}}
                    <div class="flex items-center gap-3.5 pb-5 border-b border-gray-200">
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                            <i class="fas fa-file-signature text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 leading-tight">Input Kehadiran</h2>
                            <p class="text-sm text-gray-500 font-normal mt-0.5">Catat presensi kehadiran karyawan harian</p>
                        </div>
                    </div>

                    {{-- Form Input --}}
                    <form action="{{ route('attendance.store') }}#input" method="POST" class="pt-6 flex flex-col space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="assorting_tim">
                        <input type="hidden" name="keterangan" id="input_ket_visible">

                        <div class="flex flex-col space-y-4">
                            {{-- NIK Karyawan --}}
                            <div>
                                <label class="form-label-modern">
                                    <i class="fas fa-id-card text-blue-600"></i>
                                    <span>NIK KARYAWAN</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" name="id_karyawan" id="input_nik" placeholder="Contoh: 80007007" list="dl_nik_input" oninput="onNikInput(this)" required class="form-input-modern-clean pr-10" autocomplete="off">
                                    <i class="fas fa-search text-gray-400 text-xs absolute right-3.5 pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- Nama Lengkap --}}
                            <div>
                                <label class="form-label-modern">
                                    <i class="fas fa-user text-blue-600"></i>
                                    <span>NAMA LENGKAP</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" name="nama_karyawan" id="input_nama_karyawan" placeholder="Nama Lengkap Karyawan" required class="form-input-modern-clean pr-10">
                                    <i class="fas fa-user-check text-gray-400 text-xs absolute right-3.5 pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- Grid Layout (2 Kolom) Date & Shift --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label-modern">
                                        <i class="fas fa-calendar-alt text-blue-600"></i>
                                        <span>TANGGAL</span>
                                    </label>
                                    <input type="date" name="tanggal" value="{{ $filterDate ?? date('Y-m-d') }}" class="form-input-modern-clean cursor-pointer">
                                </div>
                                <div>
                                    <label class="form-label-modern">
                                        <i class="fas fa-briefcase text-blue-600"></i>
                                        <span>SHIFT</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <select name="shift" id="input_shift" class="form-input-modern-clean appearance-none pr-9 cursor-pointer" onchange="updateJamByShift()">
                                            <option value="">- Pilih Shift -</option>
                                            <option value="1">Shift 1 (07:00 - 15:00)</option>
                                            <option value="2">Shift 2 (15:00 - 23:00)</option>
                                            <option value="3">Shift 3 (23:00 - 07:00)</option>
                                            <option value="NS">Non-Shift (08:00 - 17:00)</option>
                                        </select>
                                        <i class="fas fa-chevron-down text-gray-400 text-xs absolute right-3.5 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Grid Layout (2 Kolom) Time In & Time Out --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label-modern">
                                        <i class="fas fa-sign-in-alt text-blue-600"></i>
                                        <span>JAM MASUK</span>
                                    </label>
                                    <input type="time" id="input_jam_masuk" name="jam_masuk" class="form-input-modern-clean font-medium text-gray-700 cursor-pointer">
                                </div>
                                <div>
                                    <label class="form-label-modern">
                                        <i class="fas fa-sign-out-alt text-blue-600"></i>
                                        <span>JAM KELUAR</span>
                                    </label>
                                    <input type="time" id="input_jam_keluar" name="jam_keluar" class="form-input-modern-clean font-medium text-gray-700 cursor-pointer">
                                </div>
                            </div>

                            {{-- Status Kehadiran --}}
                            <div>
                                <label class="form-label-modern">
                                    <i class="fas fa-check-circle text-blue-600"></i>
                                    <span>STATUS KEHADIRAN</span>
                                </label>
                                <div class="relative flex items-center">
                                    <select name="status_kehadiran" id="add_status" class="form-input-modern-clean appearance-none pr-9 cursor-pointer" onchange="toggleLembur('add_status', 'add_lembur_box')">
                                        <option value="Hadir">Hadir</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Tanpa Keterangan">Tanpa Keterangan</option>
                                        <option value="Lembur">Lembur</option>
                                    </select>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs absolute right-3.5 pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- Kalkulator Lembur (Otomatis Tampil Saat Status = Lembur) --}}
                            <div id="add_lembur_box" class="hidden flex-col gap-4 bg-purple-50/90 p-5 rounded-xl border border-purple-200 shadow-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-bold text-purple-900 flex items-center gap-1.5">
                                        <i class="fas fa-calculator text-purple-600"></i> Kalkulator Lembur
                                    </label>
                                    <span class="text-[10px] font-semibold text-purple-700 bg-purple-100 px-2.5 py-0.5 rounded-full border border-purple-200">Depnaker</span>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-2">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Jam Riil</label>
                                        <input type="number" id="add_durasi" step="0.5" placeholder="0" class="form-input-modern-clean text-center font-bold text-purple-800 border-purple-200 focus:border-purple-500" style="height: 44px !important;" oninput="hitungAkmLembur('add')">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Tipe Hari</label>
                                        <select id="add_tipe" class="form-input-modern-clean border-purple-200 focus:border-purple-500 cursor-pointer text-xs" style="height: 44px !important;" onchange="hitungAkmLembur('add')">
                                            <option value="Kerja">Hari Kerja</option>
                                            <option value="Libur">Hari Libur</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between bg-white px-4 py-2.5 rounded-xl border border-purple-200/80 shadow-xs">
                                    <span class="text-xs font-semibold text-slate-600">Akm. Jam:</span>
                                    <div class="flex items-baseline gap-1">
                                        <input type="text" name="jam_lembur" id="add_total" class="w-16 bg-transparent border-none text-right font-black text-purple-700 text-sm outline-none p-0" readonly placeholder="0">
                                        <span class="text-xs font-bold text-purple-600">Jam</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="pt-3">
                            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 active:scale-[0.99] text-white font-bold py-3.5 px-4 rounded-lg shadow-md transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-save text-base"></i>
                                <span>Simpan Data</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABEL LOG AKTIVITAS --}}
            <div class="lg:col-span-7 xl:col-span-7 glass-card rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col bg-white">
                <div class="px-6 sm:px-7 py-5 border-b border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800 leading-tight">Log Aktivitas Kehadiran</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">Catatan riwayat presensi masuk dan keluar karyawan secara realtime</p>
                    </div>

                    {{-- Kontrol Filter Tanggal & Pencarian --}}
                    @php
                        $witaToday = \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d');
                    @endphp
                    <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                        <div class="relative flex items-center">
                            <input type="date" 
                                   id="filterLogDate" 
                                   value="{{ $filterDate ?? $witaToday }}" 
                                   onchange="window.location.href = '?tanggal=' + this.value + '#input'" 
                                   class="bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer h-[38px]"
                                   title="Pilih tanggal log aktivitas">
                        </div>
                                   title="Pilih tanggal log aktivitas">
                        </div>

                        {{-- Search Filter Log --}}
                        <div class="relative flex items-center min-w-[170px] sm:min-w-[190px]">
                            <i class="fas fa-search text-gray-400 text-xs absolute left-3 pointer-events-none"></i>
                            <input type="text" 
                                   id="searchLogAttendance" 
                                   onkeyup="filterLogAttendance()" 
                                   placeholder="Cari log kehadiran..." 
                                   class="w-full pl-8 pr-3.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all h-[38px]">
                        </div>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1 p-5 bg-slate-50">
                    <table class="w-full text-left border-collapse whitespace-nowrap bg-white shadow-sm rounded-xl overflow-hidden" id="tableLogAttendance">
                        <thead class="sticky top-0 z-10 shadow-xs">
                            <tr>
                                <th class="p-3.5 text-center w-12">No</th>
                                <th class="p-3.5 text-center w-16">Foto</th>
                                <th class="p-3.5">Nama Lengkap</th>
                                <th class="p-3.5 text-center">Shift</th>
                                <th class="p-3.5 text-center">Masuk</th>
                                <th class="p-3.5 text-center">Keluar</th>
                                <th class="p-3.5 text-center">Status</th>
                                <th class="p-3.5 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 bg-white text-xs">
                            @php $noLog = 1; @endphp
                            @forelse($tim_assorting as $data)
                            @php
                                if($data->status == 'Lembur') continue;

                                $rawCatatan = str_replace('Assorting: ', '', $data->catatan);
                                $parts = explode(' | ', $rawCatatan);
                                $namePart = explode(' - ', $parts[0])[1] ?? $parts[0];

                                $shft = "-"; $jm = "-"; $jk = "-"; $isTelat = false;
                                foreach($parts as $p) {
                                    $p = trim($p);
                                    if(str_starts_with($p, 'Shift:')) $shft = str_replace('Shift:', '', $p);
                                    if(str_starts_with($p, 'M:')) {
                                        $jm = str_replace('M:', '', $p);
                                        if (str_contains($jm, '[TELAT]')) {
                                            $isTelat = true;
                                            $jm = trim(str_replace('[TELAT]', '', $jm));
                                        }
                                    }
                                    if(str_starts_with($p, 'S:')) $jk = str_replace('S:', '', $p);
                                }
                            @endphp
                            <tr class="hover:bg-blue-50/50 transition-colors border-b border-slate-100">
                                <td class="p-3.5 text-center">
                                    <span class="text-xs font-semibold text-slate-400">{{ $noLog++ }}</span>
                                </td>
                                <td class="p-3.5 text-center align-middle">
                                    @if($data->foto)
                                        <img src="{{ asset('storage/' . $data->foto) }}" alt="Foto Absen" 
                                             onclick="previewFotoModal('{{ asset('storage/' . $data->foto) }}', '{{ $namePart }}')"
                                             loading="lazy" 
                                             class="w-9 h-9 object-cover rounded-xl mx-auto border border-slate-200 shadow-xs cursor-pointer hover:scale-110 transition-transform"
                                             title="Klik untuk perbesar foto">
                                    @else
                                        <div class="w-9 h-9 mx-auto rounded-xl bg-slate-100 flex items-center justify-center border border-slate-200 text-slate-300" title="Tanpa Foto">
                                            <i class="fas fa-camera text-xs"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <span class="text-xs font-semibold text-slate-800 uppercase block leading-snug">{{ $namePart }}</span>
                                    
                                    {{-- Lokasi GPS & Jarak --}}
                                    @if(!empty($data->latitude) && !empty($data->longitude))
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200" title="Koordinat: {{ $data->latitude }}, {{ $data->longitude }}">
                                                <i class="fas fa-map-marker-alt text-[9px] text-emerald-600"></i>
                                                <span>{{ $data->lokasi_nama ?: 'Lokasi Valid' }} ({{ $data->jarak_meter ?? 0 }}m)</span>
                                            </span>
                                            <a href="https://www.google.com/maps?q={{ $data->latitude }},{{ $data->longitude }}" target="_blank" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold inline-flex items-center gap-1" title="Lihat Lokasi GPS di Google Maps">
                                                <i class="fas fa-external-link-alt text-[8px]"></i> Peta
                                            </a>
                                        </div>
                                    @elseif(str_contains($data->catatan, '[GPS:'))
                                        @php
                                            preg_match('/\[GPS:\s*([^\]]+)\]/', $data->catatan, $matchesGps);
                                            $gpsStr = $matchesGps[1] ?? '';
                                        @endphp
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 text-[10px] text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">
                                                <i class="fas fa-map-marker-alt text-[9px]"></i>
                                                <span>{{ $gpsStr }}</span>
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Keterangan Presensi --}}
                                    @php
                                        $ketDisplay = $data->keterangan;
                                        if (empty($ketDisplay) && str_contains($data->catatan, '| Ket:')) {
                                            $ketPart = explode('| Ket:', $data->catatan)[1] ?? '';
                                            $ketDisplay = trim(explode('[GPS:', $ketPart)[0] ?? '');
                                            if ($ketDisplay === '-') $ketDisplay = '';
                                        }
                                    @endphp
                                    @if(!empty($ketDisplay))
                                        <div class="text-[10px] text-slate-500 italic mt-0.5 truncate max-w-xs" title="{{ $ketDisplay }}">
                                            <i class="fas fa-comment-dots text-[9px] text-slate-400"></i> {{ $ketDisplay }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($shft != '-' && $shft != '')
                                        <span class="inline-block px-2.5 py-0.5 rounded-lg bg-blue-50/90 text-blue-700 border border-blue-100 font-bold text-xs font-mono">Shift {{ $shft }}</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="text-xs font-semibold font-mono {{ $isTelat ? 'text-rose-600' : 'text-emerald-600' }}">{{ $jm != '' ? $jm : '-' }}</span>
                                    @if($isTelat)
                                        <span class="block text-[9px] font-bold bg-rose-50 text-rose-600 border border-rose-200 px-1.5 py-0.5 rounded-md uppercase mt-1 w-fit mx-auto">Telat</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="text-xs font-semibold font-mono text-rose-600">{{ $jk != '' ? $jk : '-' }}</span>
                                </td>
                                <td class="p-3.5 text-center">
                                    @php
                                        $statusClass = match(strtolower($data->status)) {
                                            'hadir' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'pulang' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                            'izin', 'sakit' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            default => 'bg-slate-100 text-slate-700 border border-slate-200'
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusClass }}">{{ strtoupper($data->status) }}</span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <button onclick="openEditModalAbsen('{{ $data->id }}', '{{ $namePart }}', '{{ $data->status }}')" class="w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors flex items-center justify-center cursor-pointer" title="Edit Absen">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('attendance.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors flex items-center justify-center cursor-pointer" title="Hapus Data">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-10 text-center bg-white">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 border border-slate-200">
                                            <i class="fas fa-clipboard-list text-lg"></i>
                                        </div>
                                        <p class="text-xs font-bold text-slate-600">Belum ada aktivitas presensi pada tanggal ini.</p>
                                        <p class="text-[11px] text-slate-400">Log kehadiran akan diperbarui secara otomatis saat karyawan melakukan clock-in atau input data disimpan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Card Container Input Kehadiran */
    .card-input-kehadiran {
        background-color: #ffffff !important;
        border-radius: 1.5rem !important; /* rounded-3xl */
        border: 1px solid #f1f5f9 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.02) !important;
        width: 100% !important;
        overflow: hidden !important;
    }

    /* Card Header */
    .header-input-kehadiran {
        padding: 1.75rem 2rem 1.25rem 2rem !important;
        background-color: #ffffff !important;
        border-bottom: 1px solid #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
    }

    /* Card Body Form */
    .form-kehadiran-body {
        padding: 1.75rem 2rem 2.25rem 2rem !important;
        background-color: #ffffff !important;
    }
    @media (max-width: 640px) {
        .header-input-kehadiran {
            padding: 1.25rem 1.25rem 1rem 1.25rem !important;
        }
        .form-kehadiran-body {
            padding: 1.25rem 1.25rem 1.75rem 1.25rem !important;
        }
    }

    /* Spacing Antar Baris Form yang Konsisten dan Rapi (20px) */
    .form-group-item {
        margin-bottom: 1.25rem !important;
        position: relative !important;
    }
    .form-group-item:last-child {
        margin-bottom: 0 !important;
    }

    /* Label Modern Sesuai Mockup */
    .form-label-modern {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        font-size: 0.75rem !important; /* text-xs */
        font-weight: 700 !important;
        color: #4b5563 !important; /* text-gray-600 */
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        margin-bottom: 0.5rem !important;
        line-height: 1.2 !important;
    }
    .form-label-modern i {
        color: #2563eb !important; /* text-blue-600 */
        font-size: 0.8rem !important;
    }

    /* Input Modern Bersih, Putih & Proporsional */
    .form-input-modern-clean {
        width: 100% !important;
        height: 46px !important;
        padding: 0 1rem !important;
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important; /* border-gray-300 */
        border-radius: 0.5rem !important; /* rounded-lg */
        font-size: 0.875rem !important; /* 14px */
        font-weight: 500 !important;
        color: #374151 !important; /* text-gray-700 */
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        box-sizing: border-box !important;
    }
    .form-input-modern-clean:hover {
        border-color: #9ca3af !important;
    }
    .form-input-modern-clean:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
    }
    .form-input-modern-clean::placeholder {
        color: #9ca3af !important;
        font-weight: 400 !important;
    }

    /* Tombol Simpan Data Persis Desain */
    .btn-submit-kehadiran {
        width: 100% !important;
        height: 48px !important;
        background-color: #004a99 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        letter-spacing: 0.02em !important;
        border-radius: 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.5rem !important;
        border: none !important;
        cursor: pointer !important;
        box-shadow: 0 4px 12px rgba(0, 74, 153, 0.25) !important;
        transition: all 0.2s ease !important;
    }
    .btn-submit-kehadiran:hover {
        background-color: #003670 !important;
        box-shadow: 0 6px 18px rgba(0, 74, 153, 0.35) !important;
        transform: translateY(-1px) !important;
    }
    .btn-submit-kehadiran:active {
        transform: translateY(0) !important;
    }

    /* Styling Tabel Log Presensi */
    #tableLogAttendance {
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    #tableLogAttendance thead th {
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
    #tableLogAttendance tbody td {
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
    #tableLogAttendance tbody tr:hover td {
        background-color: #f0f7ff !important;
    }
</style>

<script>
    function updateJamByShift() {
        const shiftVal = document.getElementById('input_shift')?.value;
        const jamMasuk = document.getElementById('input_jam_masuk');
        const jamKeluar = document.getElementById('input_jam_keluar');
        if (!jamMasuk || !jamKeluar) return;
        
        if (shiftVal === '1') {
            jamMasuk.value = '07:00';
            jamKeluar.value = '15:00';
        } else if (shiftVal === '2') {
            jamMasuk.value = '15:00';
            jamKeluar.value = '23:00';
        } else if (shiftVal === '3') {
            jamMasuk.value = '23:00';
            jamKeluar.value = '07:00';
        } else if (shiftVal === 'NS') {
            jamMasuk.value = '08:00';
            jamKeluar.value = '17:00';
        }
    }

    function onNikInput(input) {
        const val = (input.value || '').trim();
        const dl = document.getElementById('dl_nik_input');
        if (!dl) return;
        const opt = Array.from(dl.options).find(o => o.value === val);
        if (opt && opt.dataset.name) {
            const nameInput = document.getElementById('input_nama_karyawan');
            if (nameInput) nameInput.value = opt.dataset.name;
        }
    }

    function filterLogAttendance() {
        const q = (document.getElementById('searchLogAttendance')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#tableLogAttendance tbody tr');
        rows.forEach(tr => {
            if (tr.children.length === 1) return; // skip baris 'Belum ada aktivitas'
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>
