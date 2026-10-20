{{-- TAB ANALITIK STATIONERY --}}
<div id="tab-analitik-stationery" class="tab-content hidden flex-1 flex-col gap-4 overflow-hidden">

    {{-- WIDGET STATISTIK ATAS --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 shrink-0">
        <div class="glass-card p-3.5 border-l-4 border-blue-500 bg-blue-50/30">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Jenis Barang</p>
            <h3 class="text-lg font-extrabold text-slate-700" id="stat-total-barang">0</h3>
        </div>
        <div class="glass-card p-3.5 border-l-4 border-indigo-500 bg-indigo-50/30">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Barang Masuk</p>
            <h3 class="text-lg font-extrabold text-indigo-600" id="stat-total-in">0</h3>
        </div>
        <div class="glass-card p-3.5 border-l-4 border-red-500 bg-red-50/30">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Barang Keluar</p>
            <h3 class="text-lg font-extrabold text-red-600" id="stat-total-out">0</h3>
        </div>
        <div class="glass-card p-3.5 border-l-4 border-green-500 bg-green-50/30">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5" id="title-aset">Nilai Aset Stok</p>
            <h3 class="text-lg font-extrabold text-green-600 truncate" id="stat-total-aset">Rp 0</h3>
        </div>
        <div class="glass-card p-3.5 border-l-4 border-yellow-500 bg-yellow-50/50 shadow-sm">
            <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-0.5">Stok Habis (0)</p>
            <h3 class="text-lg font-extrabold text-yellow-600" id="stat-stok-habis">0</h3>
        </div>
    </div>

    <div class="glass-card p-5 flex-1 flex flex-col min-h-0 overflow-y-auto custom-scrollbar">

        {{-- HEADER FILTER & TOMBOL DOWNLOAD --}}
        <div class="flex flex-wrap justify-between items-center mb-5 gap-3 border-b-2 border-slate-100 pb-4 shrink-0">
            <div>
                <h3 class="text-base font-black text-slate-800 uppercase tracking-widest"><i class="fas fa-chart-pie text-indofood-blue mr-2"></i>Dashboard Analitik</h3>
                <p class="text-[11px] font-bold text-slate-500 mt-1 uppercase">Visualisasi Top 5 Kuantitas & Frekuensi</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex flex-col">
                    <label class="text-[9px] font-black text-slate-500 uppercase mb-1">Filter Hari</label>
                    <input type="date" id="chartFilterHari" onchange="updateStationeryChart('hari')" class="form-input-modern text-xs py-1.5 px-3 w-36 shadow-sm cursor-pointer border-gray-300">
                </div>
                <div class="flex flex-col">
                    <label class="text-[9px] font-black text-slate-500 uppercase mb-1">Filter Bulan</label>
                    <select id="chartFilterBulan" onchange="updateStationeryChart('bulan')" class="form-input-modern text-xs py-1.5 px-3 w-36 shadow-sm cursor-pointer border-gray-300">
                        <option value="">Semua Bulan</option>
                        <option value="01">Januari</option><option value="02">Februari</option><option value="03">Maret</option>
                        <option value="04">April</option><option value="05">Mei</option><option value="06">Juni</option>
                        <option value="07">Juli</option><option value="08">Agustus</option><option value="09">September</option>
                        <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="text-[9px] font-black text-slate-500 uppercase mb-1">Filter Barang</label>
                    <select id="chartFilterBarang" onchange="updateStationeryChart('barang')" class="form-input-modern text-xs py-1.5 px-3 w-48 shadow-sm cursor-pointer border-gray-300">
                        <option value="top5">Top 5 Paling Sering/Banyak</option>
                        <option value="bottom5">Top 5 Paling Dikit/Jarang</option>
                        <option value="all">Semua Barang top</option>
                        <optgroup label="Barang Spesifik:">
                            @foreach($dataStationery->sortBy('nama_barang') as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama_barang }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="flex flex-col justify-end h-full">
                    <label class="text-[9px] font-black text-slate-500 uppercase mb-1">Download PNG</label>
                    <div class="flex gap-2 mt-auto">
                        <button type="button" onclick="downloadChartPNG('stationeryOutChart', 'Quantity')" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-md flex items-center border border-blue-700">
                            <i class="fas fa-download mr-1"></i> QTY
                        </button>
                        <button type="button" onclick="downloadChartPNG('stationeryFreqChart', 'Frekuensi')" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-md flex items-center border border-indigo-700">
                            <i class="fas fa-download mr-1"></i> FREQ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- DUA KOTAK GRAFIK (DIJAMIN SEJAJAR & MUNCUL) --}}
        <div class="flex flex-col lg:flex-row gap-6 w-full shrink-0">
            {{-- KOTAK 1: KUANTITAS --}}
            <div class="flex-1 bg-white rounded-2xl border-2 border-blue-100 shadow-lg p-6 flex flex-col">
                <h4 class="text-sm font-extrabold text-blue-700 uppercase tracking-widest mb-4 border-b-2 border-blue-50 pb-2">
                    <i class="fas fa-box-open mr-2 text-lg"></i> 1. Kuantitas (Quantity)
                </h4>
                <div id="wrapperQty" class="relative w-full flex items-center justify-center" style="height: 350px;">
                    <canvas id="stationeryOutChart"></canvas>
                </div>
            </div>

            {{-- KOTAK 2: FREKUENSI --}}
            <div class="flex-1 bg-white rounded-2xl border-2 border-indigo-100 shadow-lg p-6 flex flex-col">
                <h4 class="text-sm font-extrabold text-indigo-700 uppercase tracking-widest mb-4 border-b-2 border-indigo-50 pb-2">
                    <i class="fas fa-history mr-2 text-lg"></i> 2. Frekuensi (Frequency)
                </h4>
                <div id="wrapperFreq" class="relative w-full flex items-center justify-center" style="height: 350px;">
                    <canvas id="stationeryFreqChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.updateStationeryChart = function(trigger) {
        let monthFilter = document.getElementById('chartFilterBulan');
        let dayFilter = document.getElementById('chartFilterHari');
        let itemFilter = document.getElementById('chartFilterBarang');

        if (trigger === 'bulan' && monthFilter.value !== '') dayFilter.value = '';
        else if (trigger === 'hari' && dayFilter.value !== '') monthFilter.value = '';

        let month = monthFilter ? monthFilter.value : '';
        let day = dayFilter ? dayFilter.value : '';

        let rawItemValue = itemFilter ? itemFilter.value : 'top5';
        let isSpecificItem = !isNaN(rawItemValue) && rawItemValue.trim() !== '';

        let sortMode = 'top5';
        let itemText = String(rawItemValue).toLowerCase();
        if (itemText.includes('bottom') || itemText.includes('dikit')) { sortMode = 'bottom5'; }
        else if (itemText === 'all' || itemText.includes('semua')) { sortMode = 'all'; }
        else if (itemText.includes('top15')) { sortMode = 'top5'; }

        let priceMap = {};
        masterData.forEach(m => {
            let rawHarga = String(m.harga_unit || m.harga_satuan || m.harga || '0');
            priceMap[m.id] = parseFloat(rawHarga.replace(/[^0-9]/g, '')) || 0;
        });

        let totalIn = 0, totalOut = 0, nominalPemakaian = 0;
        let aggregatedOutQty = {};
        let aggregatedOutFreq = {};
        let activeItems = new Set();

        if (!isSpecificItem) {
            masterData.forEach(m => {
                aggregatedOutQty[m.nama_barang] = 0;
                aggregatedOutFreq[m.nama_barang] = 0;
            });
        }

        let filteredTrx = rawTrxData.filter(trx => {
            if (!trx.tanggal) return false;
            let dateOnly = String(trx.tanggal).split(' ')[0] || '';
            let trxMonth = dateOnly.includes('-') ? dateOnly.split('-')[1] : '';
            let matchMonth = (month === '' || trxMonth === month);
            let matchDay = (day === '' || dateOnly === day);
            return matchMonth && matchDay;
        });

        filteredTrx.forEach(trx => {
            activeItems.add(trx.stationery_id);
            let qty = parseFloat(trx.jumlah) || 0;

            if(trx.type && trx.type.toLowerCase() === 'masuk') {
                totalIn += qty;
            } else if (trx.type && trx.type.toLowerCase() === 'keluar') {
                totalOut += qty;
                nominalPemakaian += (qty * (priceMap[trx.stationery_id] || 0));

                if(!isSpecificItem || trx.stationery_id == rawItemValue) {
                    let name = trx.stationery ? trx.stationery.nama_barang : 'Barang Dihapus';
                    if(aggregatedOutQty[name] === undefined) {
                        aggregatedOutQty[name] = 0;
                        aggregatedOutFreq[name] = 0;
                    }
                    aggregatedOutQty[name] += qty;
                    aggregatedOutFreq[name] += 1;
                }
            }
        });

        let totalAsetGudang = 0, stokHabis = 0;
        masterData.forEach(m => {
            let stok = parseFloat(m.total_stok || m.stok || 0);
            totalAsetGudang += (stok * (priceMap[m.id] || 0));
            if(stok <= 0) stokHabis++;
        });

        let displayBarangCount = (month === '' && day === '') ? masterData.length : activeItems.size;

        if(document.getElementById('stat-total-barang')) document.getElementById('stat-total-barang').innerText = displayBarangCount;
        if(document.getElementById('stat-total-in')) document.getElementById('stat-total-in').innerText = totalIn;
        if(document.getElementById('stat-total-out')) document.getElementById('stat-total-out').innerText = totalOut;
        if(document.getElementById('stat-stok-habis')) document.getElementById('stat-stok-habis').innerText = stokHabis;
        if (month !== '' || day !== '') {
            if(document.getElementById('title-aset')) document.getElementById('title-aset').innerText = "NILAI PEMAKAIAN (KELUAR)";
            if(document.getElementById('stat-total-aset')) document.getElementById('stat-total-aset').innerText = 'Rp ' + nominalPemakaian.toLocaleString('id-ID');
        } else {
            if(document.getElementById('title-aset')) document.getElementById('title-aset').innerText = "NILAI ASET STOK TOTAL";
            if(document.getElementById('stat-total-aset')) document.getElementById('stat-total-aset').innerText = 'Rp ' + totalAsetGudang.toLocaleString('id-ID');
        }

        let sortedDataQty = Object.keys(aggregatedOutQty).map(key => ({ name: key, qty: aggregatedOutQty[key] }));
        if (isSpecificItem) sortedDataQty.sort((a, b) => b.qty - a.qty);
        else if (sortMode === 'top5') sortedDataQty = sortedDataQty.filter(d => d.qty > 0).sort((a, b) => b.qty - a.qty).slice(0, 5);
        else if (sortMode === 'bottom5') sortedDataQty = sortedDataQty.filter(d => d.qty > 0).sort((a, b) => a.qty - b.qty).slice(0, 5);
        else sortedDataQty.sort((a, b) => b.qty - a.qty);

        let sortedDataFreq = Object.keys(aggregatedOutFreq).map(key => ({ name: key, freq: aggregatedOutFreq[key] }));
        if (isSpecificItem) sortedDataFreq.sort((a, b) => b.freq - a.freq);
        else if (sortMode === 'top5') sortedDataFreq = sortedDataFreq.filter(d => d.freq > 0).sort((a, b) => b.freq - a.freq).slice(0, 5);
        else if (sortMode === 'bottom5') sortedDataFreq = sortedDataFreq.filter(d => d.freq > 0).sort((a, b) => a.freq - b.freq).slice(0, 5);
        else sortedDataFreq.sort((a, b) => b.freq - a.freq);

        let totalQtySum = sortedDataQty.reduce((sum, d) => sum + d.qty, 0);
        if (sortedDataQty.length === 0 || totalQtySum === 0) {
            sortedDataQty = [{ name: 'Belum ada data', qty: 1 }];
        }

        let totalFreqSum = sortedDataFreq.reduce((sum, d) => sum + d.freq, 0);
        if (sortedDataFreq.length === 0 || totalFreqSum === 0) {
            sortedDataFreq = [{ name: 'Belum ada data', freq: 1 }];
        }

        const paletteQty = ['#38bdf8', '#fb7185', '#fbbf24', '#34d399', '#a78bfa', '#60a5fa', '#f472b6', '#fcd34d'];
        const paletteFreq = ['#818cf8', '#34d399', '#f87171', '#fbbf24', '#c084fc', '#38bdf8', '#fb7185', '#a3e635'];

        if(statChartInstance) { statChartInstance.destroy(); statChartInstance = null; }
        if(statFreqChartInstance) { statFreqChartInstance.destroy(); statFreqChartInstance = null; }

        const wrapper1 = document.getElementById('wrapperQty');
        const wrapper2 = document.getElementById('wrapperFreq');

        if(!wrapper1 || !wrapper2) return;

        wrapper1.innerHTML = '<canvas id="stationeryOutChart"></canvas>';
        wrapper2.innerHTML = '<canvas id="stationeryFreqChart"></canvas>';

        const canvasQty = document.getElementById('stationeryOutChart').getContext('2d');
        const canvasFreq = document.getElementById('stationeryFreqChart').getContext('2d');

        try {
            statChartInstance = new Chart(canvasQty, {
                type: 'pie',
                data: {
                    labels: sortedDataQty.map(d => d.name),
                    datasets: [{
                        label: 'Kuantitas Keluar',
                        data: sortedDataQty.map(d => d.qty),
                        backgroundColor: sortedDataQty.map((_, i) => sortedDataQty[0].name === 'Belum ada data' ? '#e2e8f0' : paletteQty[i % paletteQty.length]),
                        borderWidth: 2, borderColor: '#ffffff', hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 15, bottom: 15, left: 10, right: 10 } },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.96)', titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12, weight: '500' },
                            padding: 10, cornerRadius: 8, displayColors: true, boxPadding: 5, usePointStyle: true,
                            callbacks: { label: (context) => context.label === 'Belum ada data' ? ' 0 Unit' : ` ${context.raw} Unit Terpakai` }
                        },
                        datalabels: { color: '#ffffff', font: { weight: 'bold', size: 11 }, formatter: (v, context) => context.chart.data.labels[context.dataIndex] === 'Belum ada data' ? null : (v > 0 ? v : null) },
                        legend: {
                            display: true, position: 'right', labels: { font: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold', size: 10 }, color: '#475569', usePointStyle: true, padding: 15 }
                        }
                    },
                    animation: { duration: 500 }
                },
                plugins: [{
                    id: 'custom_canvas_background_color_1',
                    beforeDraw: (chart) => {
                        const ctx = chart.ctx; ctx.save(); ctx.globalCompositeOperation = 'destination-over';
                        ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, chart.width, chart.height); ctx.restore();
                    }
                }]
            });
        } catch(e) { console.error("Error drawing Qty Chart:", e); }

        try {
            statFreqChartInstance = new Chart(canvasFreq, {
                type: 'pie',
                data: {
                    labels: sortedDataFreq.map(d => d.name),
                    datasets: [{
                        label: 'Frekuensi Pengambilan',
                        data: sortedDataFreq.map(d => d.freq),
                        backgroundColor: sortedDataFreq.map((_, i) => sortedDataFreq[0].name === 'Belum ada data' ? '#e2e8f0' : paletteFreq[i % paletteFreq.length]),
                        borderWidth: 2, borderColor: '#ffffff', hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 15, bottom: 15, left: 10, right: 10 } },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.96)', titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12, weight: '500' },
                            padding: 10, cornerRadius: 8, displayColors: true, boxPadding: 5, usePointStyle: true,
                            callbacks: { label: (context) => context.label === 'Belum ada data' ? ' 0 Kali' : ` ${context.raw} Kali Pengambilan` }
                        },
                        datalabels: { color: '#ffffff', font: { weight: 'bold', size: 11 }, formatter: (v, context) => context.chart.data.labels[context.dataIndex] === 'Belum ada data' ? null : (v > 0 ? v : null) },
                        legend: {
                            display: true, position: 'right', labels: { font: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold', size: 10 }, color: '#475569', usePointStyle: true, padding: 15 }
                        }
                    },
                    animation: { duration: 500 }
                },
                plugins: [{
                    id: 'custom_canvas_background_color_2',
                    beforeDraw: (chart) => {
                        const ctx = chart.ctx; ctx.save(); ctx.globalCompositeOperation = 'destination-over';
                        ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, chart.width, chart.height); ctx.restore();
                    }
                }]
            });
        } catch(e) { console.error("Error drawing Freq Chart:", e); }
    };
</script>
