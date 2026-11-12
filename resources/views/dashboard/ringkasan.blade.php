    <div id="tab-ringkasan" class="tab-content flex-1 flex flex-col gap-6">
    <div class="flex flex-wrap justify-between items-center bg-white p-5 rounded-xl border border-gray-100 shadow-sm gap-4 shrink-0">
        <div><h3 class="text-xs font-black text-slate-600 uppercase tracking-widest">Statistik Absensi</h3><p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Menampilkan data berdasarkan tanggal terpilih</p></div>
        <div class="flex items-center gap-3 bg-slate-50 px-4 py-2.5 rounded-lg border border-slate-200 shadow-inner">
            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest"><i class="fas fa-calendar-day text-indofood-blue mr-1.5"></i> Tanggal:</label>
            <input type="date" value="{{ request('date', date('Y-m-d')) }}" onchange="window.location.href='?date='+this.value+'#ringkasan'" class="text-xs font-bold bg-transparent border-none outline-none text-indofood-blue cursor-pointer p-0">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 shrink-0">
        <div class="glass-card p-6 border-l-4 border-green-500">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Hadir</p>
            <h3 class="text-3xl font-black text-slate-800">{{ $countHadir ?? 0 }} <span class="text-xs text-slate-400">Orang</span></h3>
        </div>
        <div class="glass-card p-6 border-l-4 border-purple-600">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Total Lembur</p>
            <h3 class="text-3xl font-black text-purple-700">{{ $countLembur ?? 0 }} <span class="text-xs text-slate-400">Orang</span></h3>
        </div>
        <div class="glass-card p-6 border-l-4 border-yellow-500">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Izin / Sakit</p>
            <h3 class="text-3xl font-black text-yellow-600">{{ $countIzin ?? 0 }} <span class="text-xs text-slate-400">Orang</span></h3>
        </div>
        <div class="glass-card p-6 border-l-4 border-slate-500">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Tanpa Keterangan</p>
            <h3 class="text-3xl font-black text-slate-600">{{ $countAlpa ?? 0 }} <span class="text-xs text-slate-400">Orang</span></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-1 min-h-[300px]">
        <div class="glass-card p-6 flex flex-col"><p class="text-xs font-black text-slate-400 uppercase mb-4 tracking-widest shrink-0">Tren Kehadiran (7 Hari)</p><div class="chart-container flex-1"><canvas id="barChart"></canvas></div></div>
        <div class="glass-card p-6 flex flex-col"><p class="text-xs font-black text-slate-400 uppercase mb-4 tracking-widest shrink-0">Distribusi Status</p><div class="chart-container flex-1"><canvas id="pieChart"></canvas></div></div>
    </div>
</div>
