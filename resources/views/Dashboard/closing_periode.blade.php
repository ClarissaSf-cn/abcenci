<style>
    /* ======================================================================
       JURUS PAMUNGKAS ANTI-MELOROT (ABSOLUTE OVERRIDE)
       ======================================================================
       CSS ini akan memaksa tab Closing Periode untuk "lepas" dari
       antrian flexbox yang rusak dan menempel paksa di posisi paling atas!
    */
    #tab-closing-stationery.flex {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 50 !important;
        background-color: #f8fafc !important;
        padding: 1.75rem !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        height: 100% !important;
        overflow-y: auto !important;
    }
</style>

<div class="w-full flex flex-col gap-6 pb-10">

    {{-- =======================================================
         BLOK 1: HEADER & FORM KUNCI PERIODE (Desain Putih Bersih)
         ======================================================= --}}
    <div class="glass-card shrink-0 flex flex-col border-t-4 border-red-600 shadow-md rounded-2xl bg-white">

        <div class="px-7 py-5 flex flex-wrap justify-between items-center gap-4 rounded-t-2xl">
            {{-- Kiri: Judul --}}
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                    <i class="fas fa-lock text-red-600 mr-2"></i> Closing Periode (Tutup Buku)
                </h3>
                <p class="text-[10px] font-bold text-slate-500 mt-1 tracking-wide uppercase">Kunci data transaksi bulanan agar tidak bisa diubah</p>
            </div>

            {{-- Kanan: Form Inputs --}}
            <form action="{{ route('closing.store') }}" method="POST" onsubmit="return confirm('Yakin MENGUNCI periode ini? Aksi ini tidak dapat dibatalkan.');" class="flex flex-wrap items-end gap-3">
                @csrf

                <div class="flex flex-col">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pilih Bulan</label>
                    <select name="bulan" required class="form-input-modern text-xs py-2 px-3.5 w-36 shadow-sm cursor-pointer border-slate-200 hover:border-red-400">
                        <option value="">-- Bulan --</option>
                        <option value="01">Januari</option><option value="02">Februari</option>
                        <option value="03">Maret</option><option value="04">April</option>
                        <option value="05">Mei</option><option value="06">Juni</option>
                        <option value="07">Juli</option><option value="08">Agustus</option>
                        <option value="09">September</option><option value="10">Oktober</option>
                        <option value="11">November</option><option value="12">Desember</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pilih Tahun</label>
                    <select name="tahun" required class="form-input-modern text-xs py-2 px-3.5 w-32 shadow-sm cursor-pointer border-slate-200 hover:border-red-400">
                        <option value="">-- Tahun --</option>
                        @php $thn = date('Y'); @endphp
                        @for($i = $thn + 1; $i >= $thn - 3; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="bg-red-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-red-700 transition shadow-md flex items-center h-[36px]">
                    <i class="fas fa-key mr-2"></i> Eksekusi Kunci
                </button>
            </form>
        </div>

        {{-- Alert Banner (Menyatu di Bawah Header) --}}
        <div class="bg-yellow-50 border-t border-yellow-100 text-yellow-800 px-7 py-3 text-[11px] font-semibold rounded-b-2xl flex items-center">
            <i class="fas fa-exclamation-triangle mr-2 text-yellow-500 text-sm"></i>
            <span>Perhatian: Setelah periode dikunci, transaksi di bulan tersebut <b class="font-black text-red-600">TIDAK DAPAT</b> ditambah atau diedit.</span>
        </div>
    </div>

    {{-- =======================================================
         BLOK 2: TABEL RIWAYAT (Desain Konsisten Laporan Barang)
         ======================================================= --}}
    <div class="glass-card flex-1 overflow-hidden flex flex-col shadow-md rounded-2xl bg-white min-h-[400px]">

        {{-- Header Tabel & FITUR FILTER --}}
        <div class="px-7 py-5 border-b border-slate-100 flex flex-wrap justify-between items-center gap-4 shrink-0 rounded-t-2xl">
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                    <i class="fas fa-history text-slate-400 mr-2"></i> Riwayat Tutup Buku
                </h3>
                <p class="text-[10px] font-bold text-slate-500 mt-1 tracking-wide uppercase">Daftar seluruh periode yang telah dikunci secara sistem</p>
            </div>

            {{-- TAMBAHAN: FITUR FILTER DATA --}}
            <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex flex-col">
                    <label class="text-[8px] font-black text-slate-400 uppercase ml-1">Filter Tahun</label>
                    <select id="filterClosingTahun" onchange="filterClosingTable()" class="form-input-modern text-xs py-1.5 px-3 w-28 shadow-sm cursor-pointer border-slate-200">
                        <option value="">Semua</option>
                        @php $thn = date('Y'); @endphp
                        @for($i = $thn + 1; $i >= $thn - 3; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="text-[8px] font-black text-slate-400 uppercase ml-1">Filter Bulan</label>
                    <select id="filterClosingBulan" onchange="filterClosingTable()" class="form-input-modern text-xs py-1.5 px-3 w-32 shadow-sm cursor-pointer border-slate-200">
                        <option value="">Semua Bulan</option>
                        <option value="01">Januari</option><option value="02">Februari</option><option value="03">Maret</option>
                        <option value="04">April</option><option value="05">Mei</option><option value="06">Juni</option>
                        <option value="07">Juli</option><option value="08">Agustus</option><option value="09">September</option>
                        <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Container Tabel Background Abu-abu --}}
        <div class="overflow-y-auto flex-1 p-5 bg-slate-50">
            <table class="w-full text-left border-collapse table-bordered whitespace-nowrap bg-white shadow-sm rounded-xl overflow-hidden" id="closingTable">
                <thead class="bg-slate-100 text-slate-700 text-[10px] uppercase tracking-wider font-black border-b-2 border-slate-200 sticky top-0 z-10">
                    <tr>
                        <th class="p-3.5 text-center w-14">No</th>
                        <th class="p-3.5 text-center">Periode (Bulan - Tahun)</th>
                        <th class="p-3.5 text-center">Status Data</th>
                        <th class="p-3.5 text-center">Waktu Eksekusi</th>
                        <th class="p-3.5 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat_closing ?? collect() as $index => $item)
                    <tr class="row-closing text-xs font-bold text-slate-700 hover:bg-slate-50 transition border-b border-slate-50" data-tahun="{{ $item->tahun }}" data-bulan="{{ $item->bulan }}">
                        <td class="p-3.5 text-center text-slate-400 closing-row-num">{{ $index + 1 }}</td>
                        <td class="p-3.5 text-center text-blue-700">
                            @php
                                $bln = [
                                    '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                                    '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                                    '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                                ];
                            @endphp
                            {{ $bln[$item->bulan] ?? $item->bulan }} - {{ $item->tahun }}
                        </td>
                        <td class="p-3.5 text-center">
                            @if($item->is_closed)
                                <span class="bg-red-100 text-red-700 px-3 py-1.5 rounded-md text-[9px] uppercase font-black shadow-sm border border-red-200">
                                    <i class="fas fa-lock mr-1"></i> Terkunci
                                </span>
                            @else
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-md text-[9px] uppercase font-black shadow-sm border border-emerald-200">
                                    <i class="fas fa-unlock mr-1"></i> Terbuka
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center text-slate-500 font-medium">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }} <span class="text-[9px] font-bold text-slate-400 ml-1">WITA</span>
                        </td>
                        <td class="p-3.5 text-center flex justify-center gap-2">

                            {{-- Tombol Buka Kunci (Edit/Toggle) --}}
                            <form action="{{ route('closing.update', $item->id ?? 0) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin MENGUBAH STATUS penguncian periode ini?');">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 px-2 py-1.5 rounded-md transition" title="Buka/Tutup Kunci">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('closing.destroy', $item->id ?? 0) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin MENGHAPUS permanen riwayat periode ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-2 py-1.5 rounded-md transition" title="Hapus Permanen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyClosingRow">
                        <td colspan="5" class="p-16 text-center bg-white">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl text-slate-200 mb-3"></i>
                                <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Belum ada riwayat tutup buku.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                    {{-- Row Pesan Filter Kosong --}}
                    <tr id="filterEmptyClosingRow" class="hidden">
                        <td colspan="5" class="p-16 text-center bg-white">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-search text-3xl text-slate-200 mb-3"></i>
                                <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Tidak ada periode yang sesuai dengan filter.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>

{{-- SCRIPT JAVASCRIPT UNTUK FITUR FILTER TABEL CLOSING --}}
<script>
    function filterClosingTable() {
        let valTahun = document.getElementById('filterClosingTahun').value;
        let valBulan = document.getElementById('filterClosingBulan').value;
        let rows = document.querySelectorAll('.row-closing');
        let count = 1;
        let visibleRows = 0;

        rows.forEach(row => {
            let rTahun = row.getAttribute('data-tahun');
            let rBulan = row.getAttribute('data-bulan');

            let matchTahun = (valTahun === "" || rTahun === valTahun);
            let matchBulan = (valBulan === "" || rBulan === valBulan);

            if (matchTahun && matchBulan) {
                row.style.display = '';
                row.querySelector('.closing-row-num').textContent = count++;
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        let filterEmptyRow = document.getElementById('filterEmptyClosingRow');
        let emptyRow = document.getElementById('emptyClosingRow');

        if (filterEmptyRow) {
            if (visibleRows === 0 && (!emptyRow || emptyRow.style.display === 'none')) {
                filterEmptyRow.classList.remove('hidden');
            } else {
                filterEmptyRow.classList.add('hidden');
            }
        }
    }
</script>
