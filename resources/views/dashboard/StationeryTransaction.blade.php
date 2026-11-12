<div id="tab-transaksi-stationery" class="tab-content hidden flex-1 flex-col gap-6">
    <div class="flex flex-wrap justify-between items-center bg-white p-5 rounded-xl border border-gray-100 shadow-sm gap-4 shrink-0">
        <div>
            <h3 class="text-xs font-black text-slate-600 uppercase tracking-widest">Riwayat Transaksi (In/Out)</h3>
            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Input Pembelian (Masuk) & Pemakaian (Keluar)</p>
        </div>
        <div class="flex gap-3">
            <button onclick="document.getElementById('modalBarangMasuk').classList.remove('hidden')" class="bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-green-700 shadow-md">
                <i class="fas fa-arrow-down mr-2"></i> Input Masuk
            </button>
            <button onclick="document.getElementById('modalBarangKeluar').classList.remove('hidden')" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-orange-600 shadow-md">
                <i class="fas fa-arrow-up mr-2"></i> Input Keluar
            </button>
        </div>
    </div>

    <div class="glass-card flex-1 overflow-hidden flex flex-col p-6">
        @php
            $transaksis = \App\Models\StationeryTransaction::with('stationery')->orderBy('created_at', 'desc')->get();
        @endphp
        <div class="overflow-y-auto">
            <table class="w-full text-left border-collapse table-bordered whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-600 text-[10px] uppercase tracking-wider sticky top-0">
                    <tr>
                        <th class="p-3 text-center w-12">No</th>
                        <th class="p-3 text-center">Tgl Input</th>
                        <th class="p-3 text-center">Jenis</th>
                        <th class="p-3">Kode & Nama Barang</th>
                        <th class="p-3 text-center">Jumlah</th>
                        <th class="p-3">Detail (Dept/Vendor/Ket)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $index => $trx)
                    <tr class="text-xs font-bold text-slate-700 border-b hover:bg-slate-50">
                        <td class="p-3 text-center">{{ $index + 1 }}</td>
                        <td class="p-3 text-center">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}</td>
                        <td class="p-3 text-center">
                            @if($trx->type == 'masuk')
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[9px] uppercase font-black shadow-sm"><i class="fas fa-arrow-down mr-1"></i> Masuk</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-[9px] uppercase font-black shadow-sm"><i class="fas fa-arrow-up mr-1"></i> Keluar</span>
                            @endif
                        </td>
                        <td class="p-3"><span class="text-blue-600">{{ $trx->stationery->kode ?? 'N/A' }}</span> - {{ $trx->stationery->nama_barang ?? 'Barang Dihapus' }}</td>
                        <td class="p-3 text-center text-sm font-black {{ $trx->type == 'masuk' ? 'text-green-600' : 'text-orange-600' }}">{{ $trx->jumlah }}</td>
                        <td class="p-3 text-[10px] text-slate-500 whitespace-normal min-w-[200px]">
                            @if($trx->type == 'masuk')
                                Vendor: {{ $trx->vendor ?? '-' }} | Dok: {{ $trx->no_dokumen ?? '-' }}
                            @else
                                Dept: {{ $trx->dept ?? '-' }} | FPB: {{ $trx->no_fpb ?? '-' }} | Ket: {{ $trx->keterangan ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-400 italic text-xs">Belum ada riwayat transaksi. Silakan input Barang Masuk / Keluar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalBarangMasuk" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" style="overflow-y: auto;">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col my-auto" style="max-height: calc(100vh - 2.5rem); max-height: calc(100dvh - 2.5rem);">
        <div class="bg-green-600 px-6 py-4 flex justify-between items-center text-white font-black text-sm uppercase tracking-widest shrink-0" style="flex-shrink: 0;">
            <span>Input Barang Masuk</span>
            <button type="button" onclick="document.getElementById('modalBarangMasuk').classList.add('hidden')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('stationery.transaction.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 modal-scrollable-body" style="min-height: 0; flex: 1 1 auto; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">
            @csrf
            <input type="hidden" name="type" value="masuk">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Pilih Master Barang</label>
                <select name="stationery_id" required class="form-input-modern">
                    <option value="">-- Pilih Barang --</option>
                    {{-- Mengambil data barang yang sudah didaftarkan di Master --}}
                    @foreach(\App\Models\Stationery::orderBy('nama_barang')->get() as $item)
                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Tgl Masuk</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="form-input-modern">
                </div>
                <div class="w-1/2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Jumlah Masuk</label>
                    <input type="number" name="jumlah" required min="1" class="form-input-modern">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Vendor / Supplier</label>
                <input type="text" name="vendor" class="form-input-modern" placeholder="Cth: PT. Bintang Terang">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">No Dokumen / Nota</label>
                <input type="text" name="no_dokumen" class="form-input-modern" placeholder="Cth: INV/2026/001">
            </div>
            <div class="flex gap-3 pt-4 shrink-0" style="flex-shrink: 0;">
                <button type="button" onclick="document.getElementById('modalBarangMasuk').classList.add('hidden')" class="flex-1 py-2 rounded-xl bg-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-300">BATAL</button>
                <button type="submit" class="flex-1 py-2 rounded-xl bg-green-600 text-white text-xs font-bold hover:bg-green-700">SIMPAN MASUK</button>
            </div>
        </form>
    </div>
</div>

<div id="modalBarangKeluar" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" style="overflow-y: auto;">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col my-auto" style="max-height: calc(100vh - 2.5rem); max-height: calc(100dvh - 2.5rem);">
        <div class="bg-orange-500 px-6 py-4 flex justify-between items-center text-white font-black text-sm uppercase tracking-widest shrink-0" style="flex-shrink: 0;">
            <span>Input Barang Keluar (Order)</span>
            <button type="button" onclick="document.getElementById('modalBarangKeluar').classList.add('hidden')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('stationery.transaction.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 modal-scrollable-body" style="min-height: 0; flex: 1 1 auto; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">
            @csrf
            <input type="hidden" name="type" value="keluar">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Pilih Master Barang</label>
                <select name="stationery_id" required class="form-input-modern">
                    <option value="">-- Pilih Barang --</option>
                    @foreach(\App\Models\Stationery::orderBy('nama_barang')->get() as $item)
                        <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama_barang }} (Sisa: {{ $item->total_stok }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Tgl Keluar</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="form-input-modern">
                </div>
                <div class="w-1/2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Jumlah Keluar</label>
                    <input type="number" name="jumlah" required min="1" class="form-input-modern">
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">No FPB</label>
                    <input type="text" name="no_fpb" class="form-input-modern" placeholder="Cth: 12345">
                </div>
                <div class="w-1/2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Departemen / User</label>
                    <input type="text" name="dept" class="form-input-modern" placeholder="Cth: HRD / Budi">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Keterangan / Keperluan</label>
                <input type="text" name="keterangan" class="form-input-modern" placeholder="Cth: Untuk rapat bulanan">
            </div>
            <div class="flex gap-3 pt-4 shrink-0" style="flex-shrink: 0;">
                <button type="button" onclick="document.getElementById('modalBarangKeluar').classList.add('hidden')" class="flex-1 py-2 rounded-xl bg-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-300">BATAL</button>
                <button type="submit" class="flex-1 py-2 rounded-xl bg-orange-500 text-white text-xs font-bold hover:bg-orange-600">SIMPAN KELUAR</button>
            </div>
        </form>
    </div>
</div>
