<div id="tab-rekap-spl" class="tab-content hidden h-full flex flex-col">
    <div class="glass-card overflow-hidden flex flex-col flex-1 shadow-xl min-h-0">

        {{-- AREA FILTER BARU (ANTI-GUGUR & ANTI-NUMPUK) --}}
        <form method="GET" action="{{ route('dashboard') }}#rekap-spl" class="w-full bg-white border-b border-slate-100">

            <div class="flex flex-row flex-nowrap items-center gap-4 px-6 py-5 overflow-x-auto w-full">

                {{-- Mulai Tanggal --}}
                <div class="relative shrink-0">
                    <label class="absolute -top-2.5 left-2 bg-white px-1 text-[10px] font-extrabold text-black uppercase z-10">Mulai Tgl</label>
                    <input type="date" name="start" value="{{ request('start') }}" onchange="this.form.submit()" class="block w-[130px] text-[11px] font-semibold text-slate-600 border border-slate-300 rounded-md px-3 py-1.5 bg-transparent focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none cursor-pointer relative z-0">
                </div>

                {{-- Sampai Tanggal --}}
                <div class="relative shrink-0">
                    <label class="absolute -top-2.5 left-2 bg-white px-1 text-[10px] font-extrabold text-black uppercase z-10">Sampai Tgl</label>
                    <input type="date" name="end" value="{{ request('end') }}" onchange="this.form.submit()" class="block w-[130px] text-[11px] font-semibold text-slate-600 border border-slate-300 rounded-md px-3 py-1.5 bg-transparent focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none cursor-pointer relative z-0">
                </div>

                {{-- Departemen --}}
                <div class="relative shrink-0">
                    <select name="department" onchange="this.form.submit()" class="block w-[150px] text-[11px] font-semibold text-slate-600 border border-slate-300 rounded-md px-3 py-1.5 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none cursor-pointer">
                        <option value="">Semua Dept</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SEARCH VENDOR --}}
                <div class="relative shrink-0 w-[220px] ml-auto">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-search text-[10px]"></i>
                    </span>
                    <input type="text" name="vendor" placeholder="Cari Vendor (Tekan Enter)..." value="{{ request('vendor') }}" onchange="this.form.submit()" class="block w-full pl-8 pr-3 py-1.5 text-[11px] font-semibold text-slate-700 border border-slate-300 rounded-md focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none bg-white placeholder-slate-400">
                </div>

                {{-- Tombol Reset --}}
                @if(request('start') || request('end') || request('department') || request('vendor'))
                    <a href="{{ route('dashboard') }}#rekap-spl" class="shrink-0 text-rose-500 hover:text-rose-700 text-[10px] font-bold uppercase transition-colors flex items-center gap-1 bg-rose-50 px-3 py-1.5 rounded-md border border-rose-100" title="Reset Filter">
                        <i class="fas fa-times-circle"></i> Reset
                    </a>
                @endif

            </div>
        </form>

        {{-- BAGIAN TABEL --}}
        <div class="overflow-auto flex-1 bg-white relative px-6 pb-6 pt-0">
            <table class="w-full text-left text-sm table-bordered" style="margin-top: 0px !important;">
                <thead class="bg-indofood-blue text-white sticky -top-[1px] z-20 text-[10px] uppercase font-bold shadow-md">
                    <tr>
                        <th class="p-4 text-center w-12 border-blue-800">No</th>
                        <th class="p-4 text-center border-blue-800">Tanggal</th>
                        <th class="p-4 border-blue-800">Vendor</th>
                        <th class="p-4 border-blue-800">Dibuat Oleh</th>
                        <th class="p-4 text-center border-blue-800">#Detail</th>
                        <th class="p-4 text-center border-blue-800">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs bg-white text-slate-700">
                    @forelse($spls as $s)
                        <tr class="hover:bg-slate-50 transition-all">
                            <td class="p-4 text-center font-bold text-slate-400">{{ $loop->iteration + (($spls->currentPage()-1) * $spls->perPage()) }}</td>
                            <td class="p-4 font-bold uppercase text-slate-800">{{ \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') }}</td>
                            <td class="p-4 text-slate-700">{{ $s->vendor ?? '-' }}</td>
                            <td class="p-4 text-slate-700">{{ optional($s->createdBy)->name ?? '-' }}</td>
                            <td class="p-4 text-center font-black text-black">{{ $s->details_count ?? $s->details->count() }}</td>
                            <td class="p-4 text-center">
                                <div class="inline-flex gap-2 flex-wrap justify-center">
                                    <a href="{{ route('spl.show', $s->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded text-[10px] font-bold uppercase text-slate-700 hover:bg-slate-200 transition">Lihat</a>

                                    <a href="{{ route('spl.download', $s->id) }}" class="px-3 py-1.5 bg-blue-600 text-white rounded text-[10px] font-black uppercase hover:bg-blue-700 transition">PDF</a>

                                    <form action="{{ route('spl.destroy', $s->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded text-[10px] font-black uppercase hover:bg-red-700 transition" onclick="return confirm('Hapus SPL ini?');">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic text-sm">Tidak ada rekap SPL yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
            <div class="text-[11px] text-slate-500">Menampilkan {{ $spls->count() }} dari {{ $spls->total() }} rekap SPL</div>
            <div>
                {{ $spls->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
