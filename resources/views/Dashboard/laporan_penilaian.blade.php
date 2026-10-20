{{-- =========================================================
     LAPORAN REKAPITULASI PENILAIAN KARYAWAN
     ========================================================= --}}
<div id="tab-laporan-penilaian" class="tab-content hidden flex-1 flex-col gap-6">

    @php
        $authUser = Auth::user();
        $uRole = strtolower(str_replace([' ', '-'], '_', $authUser->role ?? ''));
        $uName = strtoupper(trim($authUser->name ?? ''));
        $isSuperAdmin = ($uRole === 'superadmin' || $uName === 'ADMIN SUPER');
        $isAdminPenilaian = ($uRole === 'admin_penilaian' || str_contains($uRole, 'penilaian'));

        $bulanQuery = request('bulan', date('m'));
        $tahunQuery = request('tahun', date('Y'));
        $bulanNameQuery = \App\Http\Controllers\PenilaianController::normalizeMonthName($bulanQuery);
        $bulanCodeQuery = \App\Http\Controllers\PenilaianController::normalizeMonthCode($bulanQuery);

        $laporan = \App\Models\Penilaian::where(function($q) use ($bulanQuery, $bulanNameQuery, $bulanCodeQuery) {
            $q->where('bulan', $bulanNameQuery)
              ->orWhere('bulan', $bulanQuery)
              ->orWhere('bulan', $bulanCodeQuery);
        })->where('tahun', $tahunQuery)->orderBy('total_skor', 'desc')->get();
    @endphp

    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 flex flex-col gap-6">
        
        {{-- Header Judul & Tombol Export --}}
        <div class="border-b border-slate-200 pb-5 flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-black uppercase text-slate-800 tracking-wide">
                    Laporan Rekapitulasi Penilaian Karyawan
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Rekapitulasi hasil evaluasi kinerja bulanan karyawan dan status rekomendasi kontrak.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('penilaian.export.pdf') }}?bulan={{ urlencode($bulanQuery) }}&tahun={{ urlencode($tahunQuery) }}"
                   download
                   class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow transition-all flex items-center gap-2 cursor-pointer no-underline">
                    <i class="fas fa-file-pdf"></i> Ekspor PDF
                </a>

                <a href="{{ route('penilaian.export.excel') }}?bulan={{ urlencode($bulanQuery) }}&tahun={{ urlencode($tahunQuery) }}"
                   download
                   class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-green-700 hover:bg-green-800 shadow transition-all flex items-center gap-2 cursor-pointer no-underline">
                    <i class="fas fa-file-excel"></i> Ekspor Excel
                </a>

                <button type="button"
                        onclick="switchTab('karyawan')"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 shadow transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-users"></i> Data Karyawan
                </button>

                <button type="button"
                        onclick="switchTab('aspek-penilaian')"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-edit"></i> Input Penilaian
                </button>
            </div>
        </div>

        {{-- Role Access Info Banner --}}
        @if ($isSuperAdmin)
            <div class="bg-gradient-to-r from-emerald-500/10 via-emerald-50 to-teal-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-base shadow">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-emerald-900 tracking-wider">Super Admin — Hak Akses Penuh</h4>
                        <p class="text-xs text-emerald-700 mt-0.5">Sebagai Super Admin, Anda memiliki wewenang untuk <strong>mengubah skor</strong> maupun <strong>menghapus</strong> data penilaian yang telah tersimpan.</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-emerald-200 text-emerald-900 text-[10px] font-black rounded-full uppercase tracking-wider flex items-center gap-1 shadow-sm">
                    <i class="fas fa-check-double"></i> Akses Penuh
                </span>
            </div>
        @elseif ($isAdminPenilaian)
            <div class="bg-gradient-to-r from-amber-500/10 via-amber-50 to-orange-50 border-l-4 border-amber-500 p-4 rounded-xl shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-600 text-white flex items-center justify-center font-bold text-base shadow">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase text-amber-900 tracking-wider">Admin Penilaian — Hanya Lihat Rekapitulasi</h4>
                        <p class="text-xs text-amber-700 mt-0.5">Admin Penilaian hanya berhak menilai data baru pada menu <strong>Aspek Penilaian</strong>. Penilaian yang telah tersimpan bersifat permanen dan hanya dapat diubah/dihapus oleh Super Admin.</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-amber-200 text-amber-900 text-[10px] font-black rounded-full uppercase tracking-wider flex items-center gap-1 shadow-sm">
                    <i class="fas fa-lock"></i> Hanya Lihat
                </span>
            </div>
        @endif

        {{-- Filter Bulan & Tahun --}}
        <form method="GET" action="{{ route('dashboard') }}" class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-wrap items-center gap-4">
            <input type="hidden" name="active_tab" value="laporan-penilaian">

            <div class="flex items-center gap-2">
                <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                <span class="text-xs font-bold uppercase text-slate-700">Filter Periode:</span>
            </div>

            <div class="flex items-center gap-2">
                @php
                    $bulanList = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    $selectedBulan = (int) $bulanCodeQuery;
                @endphp
                <select name="bulan" class="rounded-lg border-slate-300 shadow-sm text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2 bg-white">
                    @foreach($bulanList as $key => $val)
                        <option value="{{ sprintf('%02d', $key) }}" {{ $selectedBulan == $key ? 'selected' : '' }}>
                            {{ $val }}
                        </option>
                    @endforeach
                </select>

                <select name="tahun" class="rounded-lg border-slate-300 shadow-sm text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2 bg-white">
                    @foreach(range((int)date('Y')+1, (int)date('Y')-3) as $y)
                        <option value="{{ $y }}" {{ $tahunQuery == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-bold transition shadow flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-filter"></i> Tampilkan
                </button>
            </div>
        </form>

        {{-- Ringkasan Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl text-center shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-600">Total Dinilai</span>
                <p class="text-2xl font-black text-blue-900 mt-1">{{ count($laporan) }}</p>
                <span class="text-[9px] text-blue-500 font-bold">Periode {{ $bulanNameQuery }} {{ $tahunQuery }}</span>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-center shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Dipertahankan</span>
                <p class="text-2xl font-black text-emerald-900 mt-1">{{ $laporan->where('dipertahankan', 'YA')->count() }}</p>
                <span class="text-[9px] text-emerald-600 font-bold">Rekomendasi Lanjut Kontrak</span>
            </div>

            <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl text-center shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-wider text-rose-600">Cut Off</span>
                <p class="text-2xl font-black text-rose-900 mt-1">{{ $laporan->where('dipertahankan', '!=', 'YA')->count() }}</p>
                <span class="text-[9px] text-rose-600 font-bold">Tidak Direkomendasikan</span>
            </div>
        </div>

        {{-- Tabel Data Rekap --}}
        <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-sm">
            <table class="w-full text-xs text-left text-slate-600 border-collapse whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-700 uppercase font-black text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3">NIK</th>
                        <th class="px-4 py-3">Nama Karyawan</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">Subarea / Plant</th>
                        <th class="px-4 py-3 text-center">Disiplin</th>
                        <th class="px-4 py-3 text-center">Kualitas</th>
                        <th class="px-4 py-3 text-center">Produktif</th>
                        <th class="px-4 py-3 text-center">Kerjasama</th>
                        <th class="px-4 py-3 text-center">Tgjwb</th>
                        <th class="px-4 py-3 text-center">Total Skor</th>
                        <th class="px-4 py-3 text-center">Status Rekomendasi</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporan as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-slate-700">{{ $item->nik ?? '-' }}</td>
                            <td class="px-4 py-3 font-black text-slate-800 uppercase">{{ $item->employee_name ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium">{{ $item->position_text ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium">{{ $item->pers_subarea_text ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $item->skor_disiplin ?? 0 }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $item->skor_kualitas ?? 0 }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $item->skor_produktivitas ?? 0 }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $item->skor_kerjasama ?? 0 }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $item->skor_tanggung_jawab ?? 0 }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-black bg-blue-100 text-blue-800">
                                    {{ $item->total_skor ?? 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(($item->dipertahankan ?? '') == 'YA')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">DIPERTAHANKAN</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">CUT OFF</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 whitespace-normal min-w-[150px]">{{ $item->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($isSuperAdmin)
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button"
                                                onclick='openEditPenilaianModal(@json($item))'
                                                class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition shadow flex items-center gap-1 cursor-pointer"
                                                title="Ubah Penilaian (Super Admin)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('penilaian.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirmDeletePenilaian(event, this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition shadow flex items-center gap-1 cursor-pointer"
                                                    title="Hapus Penilaian (Super Admin)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-400 text-[10px] font-bold inline-flex items-center gap-1" title="Hanya Super Admin yang berhak merubah">
                                        <i class="fas fa-lock"></i> Terkunci
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-4 py-12 text-center">
                                <div class="text-slate-400">
                                    <i class="fas fa-clipboard-list text-3xl text-slate-300 mb-2"></i>
                                    <p class="font-bold text-sm text-slate-600">Belum ada data penilaian pada periode {{ $bulanNameQuery }} {{ $tahunQuery }}.</p>
                                    <p class="text-xs text-slate-400 mt-1">Data akan muncul otomatis setelah Anda melakukan penilaian pada menu Aspek Penilaian Karyawan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@if($isSuperAdmin)
{{-- MODAL UBAH PENILAIAN OLEH SUPER ADMIN --}}
<div id="modalEditPenilaian" style="display: none; position: fixed !important; inset: 0 !important; z-index: 999999 !important; background-color: rgba(15, 23, 42, 0.75) !important; backdrop-filter: blur(4px) !important; -webkit-backdrop-filter: blur(4px) !important;" class="hidden items-center justify-center p-4">
    <div style="background-color: #ffffff !important; opacity: 1 !important; position: relative !important; z-index: 1000000 !important; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5) !important;" class="w-full max-w-lg rounded-2xl overflow-hidden animate__animated animate__fadeInDown">
        <div class="px-6 py-4 flex justify-between items-center text-white font-black text-sm uppercase tracking-widest bg-blue-700">
            <span><i class="fas fa-edit mr-2"></i> Ubah Penilaian Karyawan (Super Admin)</span>
            <button type="button" onclick="closeEditPenilaianModal()" class="text-white hover:text-red-200 transition cursor-pointer"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form id="formEditPenilaian" action="" method="POST" class="p-6 space-y-4" style="background-color: #ffffff !important;">
            @csrf
            @method('PUT')
            
            <div class="bg-blue-50/70 border border-blue-200 rounded-xl p-3 flex justify-between items-center">
                <div>
                    <span id="edit_emp_name" class="block font-black text-slate-800 text-sm uppercase tracking-wide"></span>
                    <span id="edit_emp_nik" class="block font-mono text-xs text-blue-700 font-bold"></span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Periode</span>
                    <span id="edit_emp_periode" class="block font-black text-xs text-slate-700"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase mb-1">1. Disiplin (1-5)</label>
                    <select name="skor_disiplin" id="edit_skor_disiplin" onchange="calculateEditTotal()" class="w-full border rounded-lg p-2 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="1">1 - Kurang</option>
                        <option value="2">2 - Cukup</option>
                        <option value="3">3 - Baik</option>
                        <option value="4">4 - Sangat Baik</option>
                        <option value="5">5 - Istimewa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase mb-1">2. Kualitas Kerja (1-5)</label>
                    <select name="skor_kualitas" id="edit_skor_kualitas" onchange="calculateEditTotal()" class="w-full border rounded-lg p-2 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="1">1 - Kurang</option>
                        <option value="2">2 - Cukup</option>
                        <option value="3">3 - Baik</option>
                        <option value="4">4 - Sangat Baik</option>
                        <option value="5">5 - Istimewa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase mb-1">3. Produktivitas (1-5)</label>
                    <select name="skor_produktivitas" id="edit_skor_produktivitas" onchange="calculateEditTotal()" class="w-full border rounded-lg p-2 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="1">1 - Kurang</option>
                        <option value="2">2 - Cukup</option>
                        <option value="3">3 - Baik</option>
                        <option value="4">4 - Sangat Baik</option>
                        <option value="5">5 - Istimewa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase mb-1">4. Kerjasama (1-5)</label>
                    <select name="skor_kerjasama" id="edit_skor_kerjasama" onchange="calculateEditTotal()" class="w-full border rounded-lg p-2 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="1">1 - Kurang</option>
                        <option value="2">2 - Cukup</option>
                        <option value="3">3 - Baik</option>
                        <option value="4">4 - Sangat Baik</option>
                        <option value="5">5 - Istimewa</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-black text-slate-600 uppercase mb-1">5. Tanggung Jawab (1-5)</label>
                    <select name="skor_tanggung_jawab" id="edit_skor_tanggung_jawab" onchange="calculateEditTotal()" class="w-full border rounded-lg p-2 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="1">1 - Kurang</option>
                        <option value="2">2 - Cukup</option>
                        <option value="3">3 - Baik</option>
                        <option value="4">4 - Sangat Baik</option>
                        <option value="5">5 - Istimewa</option>
                    </select>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                <span class="text-xs font-black uppercase text-slate-700">Total Skor Dihitung:</span>
                <span id="edit_total_skor_preview" class="text-xl font-black text-blue-700 bg-blue-100 px-3 py-0.5 rounded-lg">0</span>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-600 uppercase mb-1">Status Rekomendasi</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-lg cursor-pointer hover:bg-emerald-50 transition">
                        <input type="radio" name="dipertahankan" id="edit_dipertahankan_ya" value="YA" class="accent-emerald-600">
                        <span class="text-xs font-bold text-emerald-800">DIPERTAHANKAN</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-lg cursor-pointer hover:bg-rose-50 transition">
                        <input type="radio" name="dipertahankan" id="edit_dipertahankan_cut" value="CUT OFF" class="accent-rose-600">
                        <span class="text-xs font-bold text-rose-800">CUT OFF</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-600 uppercase mb-1">Keterangan / Catatan</label>
                <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full border border-slate-300 rounded-lg p-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Masukkan catatan penilaian jika ada..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditPenilaianModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function openEditPenilaianModal(item) {
    const modal = document.getElementById('modalEditPenilaian');
    const form = document.getElementById('formEditPenilaian');
    if (!modal || !form) return;

    form.action = "{{ url('/penilaian') }}/" + item.id;
    document.getElementById('edit_emp_name').innerText = item.employee_name || '-';
    document.getElementById('edit_emp_nik').innerText = item.nik || '-';
    document.getElementById('edit_emp_periode').innerText = (item.bulan || '') + ' ' + (item.tahun || '');

    document.getElementById('edit_skor_disiplin').value = item.skor_disiplin || 1;
    document.getElementById('edit_skor_kualitas').value = item.skor_kualitas || 1;
    document.getElementById('edit_skor_produktivitas').value = item.skor_produktivitas || 1;
    document.getElementById('edit_skor_kerjasama').value = item.skor_kerjasama || 1;
    document.getElementById('edit_skor_tanggung_jawab').value = item.skor_tanggung_jawab || 1;

    if (item.dipertahankan === 'YA') {
        document.getElementById('edit_dipertahankan_ya').checked = true;
    } else {
        document.getElementById('edit_dipertahankan_cut').checked = true;
    }

    document.getElementById('edit_keterangan').value = item.keterangan || '';
    calculateEditTotal();

    modal.style.display = 'flex';
    modal.classList.remove('hidden');
}

function closeEditPenilaianModal() {
    const modal = document.getElementById('modalEditPenilaian');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
}

function calculateEditTotal() {
    const d = parseInt(document.getElementById('edit_skor_disiplin')?.value, 10) || 0;
    const k = parseInt(document.getElementById('edit_skor_kualitas')?.value, 10) || 0;
    const p = parseInt(document.getElementById('edit_skor_produktivitas')?.value, 10) || 0;
    const r = parseInt(document.getElementById('edit_skor_kerjasama')?.value, 10) || 0;
    const t = parseInt(document.getElementById('edit_skor_tanggung_jawab')?.value, 10) || 0;
    const total = d + k + p + r + t;
    const preview = document.getElementById('edit_total_skor_preview');
    if (preview) preview.innerText = total;
}

function confirmDeletePenilaian(e, form) {
    e.preventDefault();
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Penilaian?',
            text: 'Data penilaian karyawan ini akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        if (confirm('Yakin ingin menghapus data penilaian ini?')) {
            form.submit();
        }
    }
    return false;
}
</script>
