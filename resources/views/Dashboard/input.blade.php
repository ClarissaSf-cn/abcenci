<div id="tab-input" class="tab-content hidden h-full flex flex-col">
    @php
        $filterDate = $filterDate ?? date('Y-m-d');
        $tim_assorting = $tim_assorting ?? collect();
    @endphp
    <div class="bg-transparent flex-1 flex flex-col min-h-0 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6 pt-2 flex-1 min-h-0 overflow-y-auto">

            <div class="lg:col-span-1 flex flex-col gap-6">
                <div class="glass-card p-6 sm:p-7 shrink-0 border-t-8 border-indofood-blue bg-white shadow-xl">
                    <h4 class="text-sm font-black uppercase text-slate-700 tracking-widest mb-6 border-b border-slate-100 pb-4 flex items-center">
                        <i class="fas fa-file-signature mr-2.5 text-indofood-blue text-base"></i>Input Kehadiran
                    </h4>

                    <form action="{{ route('attendance.store') }}#input" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="action" value="assorting_tim">

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 ml-0.5">NIK Karyawan</label>
                            <input type="text" name="id_karyawan" placeholder="Contoh: 80007007" required class="form-input-modern">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 ml-0.5">Nama Lengkap</label>
                            <input type="text" name="nama_karyawan" placeholder="Nama Lengkap" required class="form-input-modern">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-2 ml-0.5">TANGGAL</label>
                                <input type="date" name="tanggal" value="{{ $filterDate ?? date('Y-m-d') }}" class="form-input-modern">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-2 ml-0.5">SHIFT</label>
                                <select name="shift" id="input_shift" class="form-input-modern" onchange="updateJamByShift()">
                                    <option value="">- Pilih -</option>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                    <option value="NS">Non-Shift</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-blue-50/70 p-4 sm:p-5 rounded-2xl border border-blue-200/80 shadow-xs mt-6 mb-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider mb-2">JAM MASUK</label>
                                    <input type="time" id="input_jam_masuk" name="jam_masuk" class="form-input-modern text-center font-mono">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-extrabold text-rose-700 uppercase tracking-wider mb-2">JAM KELUAR</label>
                                    <input type="time" id="input_jam_keluar" name="jam_keluar" class="form-input-modern text-center font-mono">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 ml-0.5">Status Kehadiran</label>
                            <select name="status_kehadiran" id="add_status" class="form-input-modern cursor-pointer font-bold" onchange="toggleLembur('add_status', 'add_lembur_box')">
                                <option value="Hadir">✔️ Hadir</option>
                                <option value="Izin">📄 Izin / Sakit</option>
                                <option value="Tanpa Keterangan">❌ Tanpa Keterangan</option>
                            </select>
                        </div>

                        <input type="hidden" name="keterangan" id="input_ket_visible" class="form-input-modern text-[10px] bg-slate-50">

                        <div id="add_lembur_box" class="hidden flex-col gap-3.5 bg-purple-50/70 p-4 sm:p-5 rounded-2xl border border-purple-200 shadow-inner">
                            <label class="block text-[10px] font-black text-purple-700 uppercase tracking-wider mb-1"><i class="fas fa-calculator mr-1.5"></i> Kalkulator Lembur</label>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="number" id="add_durasi" step="0.5" placeholder="Jam Riil" class="form-input-modern p-2 text-xs text-center" oninput="hitungAkmLembur('add')">
                                <select id="add_tipe" class="form-input-modern p-2 text-xs" onchange="hitungAkmLembur('add')">
                                    <option value="Kerja">Hari Kerja</option>
                                    <option value="Libur">Hari Libur</option>
                                </select>
                            </div>
                            <div class="flex items-center justify-between bg-white px-3.5 py-2.5 rounded-xl border border-purple-200">
                                <span class="text-[10px] font-black text-slate-400 uppercase">Akm. Jam:</span>
                                <input type="text" name="jam_lembur" id="add_total" class="w-16 bg-transparent border-none text-right font-black text-purple-700 text-sm outline-none" readonly>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button type="submit" class="w-full bg-indofood-blue text-white font-black py-4 rounded-xl uppercase text-[11px] tracking-wider shadow-lg hover:bg-blue-800 hover:shadow-xl transition-all cursor-pointer">SIMPAN DATA</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 glass-card flex flex-col shadow-xl min-h-0 bg-white">
                <div class="p-5 border-b bg-slate-50 flex justify-between items-center shrink-0 rounded-t-xl">
                    <h4 class="text-xs font-black uppercase text-slate-700 tracking-widest">Log Aktivitas: {{ \Carbon\Carbon::parse($filterDate ?? date('Y-m-d'))->translatedFormat('d M Y') }}</h4>
                </div>
                <div class="overflow-y-auto flex-1 p-4">
                    <table class="w-full text-left text-sm table-bordered border-collapse">
                        <thead class="bg-slate-100 sticky top-0 z-10 font-bold text-[10px] uppercase text-slate-600">
                            <tr>
                                <th class="p-3 text-center w-10">NO</th>
                                <th class="p-3 text-center w-14">FOTO</th>
                                <th class="p-3">NAMA LENGKAP</th>
                                <th class="p-3 text-center">SHIFT</th>
                                <th class="p-3 text-center">MASUK</th>
                                <th class="p-3 text-center">KELUAR</th>
                                <th class="p-3 text-center">STATUS</th>
                                <th class="p-3 text-center w-20">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @php $noLog = 1; @endphp
                            @foreach($tim_assorting as $data)
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
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $noLog++ }}</td>
                                <td class="p-2 text-center align-middle">
                                    @if($data->foto)
                                        <img src="{{ asset('storage/' . $data->foto) }}" alt="Foto Absen" 
                                             onclick="previewFotoModal('{{ asset('storage/' . $data->foto) }}', '{{ $namePart }}')"
                                             loading="lazy" 
                                             class="w-9 h-9 object-cover rounded-lg mx-auto border border-slate-200 shadow-xs cursor-pointer hover:scale-110 transition-transform"
                                             title="Klik untuk perbesar foto">
                                    @else
                                        <div class="w-9 h-9 mx-auto rounded-lg bg-slate-100 flex items-center justify-center border border-slate-200 text-slate-300" title="Tanpa Foto">
                                            <i class="fas fa-camera text-xs"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3 font-bold text-slate-700 uppercase">{{ $namePart }}</td>
                                <td class="p-3 text-center">
                                    @if($shft != '-' && $shft != '')
                                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-black text-[9px]">S{{ $shft }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center font-mono font-bold {{ $isTelat ? 'text-red-500' : 'text-green-600' }}">{{ $jm != '' ? $jm : '-' }}
                                    @if($isTelat)
                                        <span class="block text-[8px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded uppercase mt-1 w-fit mx-auto shadow-sm">Telat</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center font-mono text-red-600 font-bold">{{ $jk != '' ? $jk : '-' }}</td>
                                <td class="p-3 text-center"><span class="font-bold text-slate-500 uppercase text-[9px]">{{ $data->status }}</span></td>
                                <td class="p-3 text-center">
                                    <div class="flex justify-center gap-3">
                                        <button onclick="openEditModalAbsen('{{ $data->id }}', '{{ $namePart }}', '{{ $data->status }}')" class="text-blue-500 hover:scale-110 transition-transform"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('attendance.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:scale-110 transition-transform"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
