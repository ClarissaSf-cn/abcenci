<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<style>
    /* CSS SUPER PEMAKSA GARIS KOTAK TABEL EXCEL & PRINT */
    table.excel-table { border-collapse: collapse !important; width: 100% !important; border: 2px solid #000 !important; }
    table.excel-table th, table.excel-table td { border: 1px solid #000 !important; padding: 6px 10px !important; color: #000 !important; }
    table.excel-table th { background-color: #f3f4f6 !important; font-weight: bold !important; text-align: center !important; }
    @media print { .page-break { page-break-before: always !important; } .print-container { box-shadow: none !important; border: none !important; margin-bottom: 0 !important; padding: 0 !important; } #tab-lembur { background-color: white !important; } }
</style>

{{-- ========================================================================= --}}
{{-- TAB 1: REKAPITULASI GAJI LEMBUR --}}
{{-- ========================================================================= --}}
<div id="tab-lembur" class="tab-content hidden h-full flex flex-col relative">

    @php
        \Carbon\Carbon::setLocale('id');
        $startDate = request('start_date', date('Y-m-01'));
        $endDate = request('end_date', date('Y-m-t'));
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();
        $jumlahHari = $start->diffInDays($end) + 1;
        $periodeTitle = strtoupper($start->translatedFormat('d F Y') . ' - ' . $end->translatedFormat('d F Y'));

        $lemburData = [];
        $lemburRecordsPerPerson = [];
        $grandTotalJam = 0;
        $tempRecords = [];

        foreach($rekap_semua as $data) {
            if ($data->status === 'Lembur') {
                $ketRaw = $data->catatan ?? '';
                $tglData = $data->created_at;

                $hiddenTag = '';
                if (preg_match('/~(.*?)~/', $ketRaw, $matches)) {
                    $hiddenTag = $matches[1];
                }

                $mulai = ''; $selesai = ''; $ist = ''; $riil = '';
                $j15 = ''; $j2 = ''; $j3 = ''; $j4 = '';
                $realTgl = '';

                if ($hiddenTag !== '') {
                    $hParts = explode('|', $hiddenTag);
                    foreach($hParts as $hp) {
                        $kv = explode(':', $hp, 2);
                        if(count($kv) == 2) {
                            if($kv[0]=='T') $realTgl = $kv[1];
                            if($kv[0]=='M') $mulai = $kv[1];
                            if($kv[0]=='S') $selesai = $kv[1];
                            if($kv[0]=='I') $ist = $kv[1];
                            if($kv[0]=='R') $riil = round((float)$kv[1], 2);
                            if($kv[0]=='15') $j15 = $kv[1] > 0 ? round((float)$kv[1], 2) : '';
                            if($kv[0]=='2') $j2 = $kv[1] > 0 ? round((float)$kv[1], 2) : '';
                            if($kv[0]=='3') $j3 = $kv[1] > 0 ? round((float)$kv[1], 2) : '';
                            if($kv[0]=='4') $j4 = $kv[1] > 0 ? round((float)$kv[1], 2) : '';
                        }
                    }
                }

                if ($realTgl !== '') {
                    try { $tglData = \Carbon\Carbon::parse($realTgl); } catch (\Exception $e) {}
                }

                $cleanCatatan = preg_replace('/~.*~/', '', $ketRaw);
                $cleanCatatan = str_replace('Assorting: ', '', $cleanCatatan);
                $parts = explode('|', $cleanCatatan);

                $namaArr = explode('-', trim($parts[0]));
                $nik = trim($namaArr[0]);
                $nama = count($namaArr) > 1 ? trim($namaArr[1]) : trim($namaArr[0]);

                if(request('dept')) {
                    $emp = \App\Models\Employee::where('nik', $nik)->first();
                    if (!$emp || trim($emp->pers_subarea_text) != request('dept')) continue;
                }

                $jam = 0; $ket = '';
                for ($i = 1; $i < count($parts); $i++) {
                    $p = trim($parts[$i]);
                    if ($p === '') continue;
                    $checkNum = str_ireplace(['akm:', 'jam', ' '], '', $p);
                    if (is_numeric(str_replace(',', '.', $checkNum)) && $jam == 0) {
                        $jam = (float) str_replace(',', '.', $checkNum);
                    } elseif (stripos($p, 'Ket:') !== false) {
                        $ket = trim(str_ireplace('Ket:', '', $p));
                    } elseif ($ket == '') {
                        $ket = $p;
                    }
                }

                if ($jam == 0 && $hiddenTag !== '') {
                    $jam = ((float)$j15 * 1.5) + ((float)$j2 * 2) + ((float)$j3 * 3) + ((float)$j4 * 4);
                }

                if (($mulai == '' || $mulai == '-') && $jam > 0) {
                    $hariLembur = strtolower($tglData->translatedFormat('l'));
                    $isLibur = ($hariLembur == 'minggu' || str_contains(strtolower($ket), 'libur'));
                    if ($isLibur) {
                        if ($jam <= 14) { $j2 = $jam / 2; $riil = $j2; }
                        elseif ($jam <= 17) { $j2 = 7; $j3 = ($jam - 14) / 3; $riil = 7 + $j3; }
                        else { $j2 = 7; $j3 = 1; $j4 = ($jam - 17) / 4; $riil = 8 + $j4; }
                        $mulai = '08:00'; $ist = $riil >= 4 ? 1 : 0; $selesaiInt = 8 + floor($riil) + $ist;
                    } else {
                        if ($jam <= 1.5) { $j15 = $jam / 1.5; $riil = $j15; }
                        else { $j15 = 1; $j2 = ($jam - 1.5) / 2; $riil = 1 + $j2; }
                        $mulai = '17:00'; $ist = $riil >= 4 ? 1 : 0; $selesaiInt = 17 + floor($riil) + $ist;
                    }
                    $ms = round(($riil - floor($riil)) * 60);
                    if ($ms == 60) { $selesaiInt += 1; $ms = 0; }
                    $selesai = sprintf("%02d:%02d", $selesaiInt > 23 ? $selesaiInt - 24 : $selesaiInt, $ms);
                    $j15 = $j15 > 0 ? floatval($j15) : ''; $j2 = $j2 > 0 ? floatval($j2) : '';
                    $j3 = $j3 > 0 ? floatval($j3) : ''; $j4 = $j4 > 0 ? floatval($j4) : '';
                    $riil = floatval($riil); $ist = $ist > 0 ? $ist : '';
                }

                $dateStr = $tglData->format('Y-m-d');

                $tempRecords[$nama][$dateStr] = [

                    'jam' => $jam, 'keterangan' => $ket,
                    'mulai' => $mulai, 'selesai' => $selesai, 'ist' => $ist, 'riil' => $riil ?: $jam,
                    'j15' => $j15, 'j2' => $j2, 'j3' => $j3, 'j4' => $j4
                ];

                if (!isset($lemburData[$nama])) { $lemburData[$nama] = 0; }
                $lemburData[$nama] += $jam;
                $grandTotalJam += $jam;
            }
        }

        foreach($tempRecords as $nama => $personData) {
            $lemburRecordsPerPerson[$nama] = [];
            for($d = 0; $d < $jumlahHari; $d++) {
                $currDate = $start->copy()->addDays($d);
                $tglStr = $currDate->format('Y-m-d');
                $hariStr = strtoupper($currDate->translatedFormat('l'));

                if(isset($personData[$tglStr])) {
                    $rec = $personData[$tglStr];
                    $lemburRecordsPerPerson[$nama][] = [
                        'tanggal' => $tglStr, 'hari' => $hariStr,
                        'mulai' => $rec['mulai'], 'selesai' => $rec['selesai'], 'ist' => $rec['ist'], 'riil' => $rec['riil'],
                        'j15' => $rec['j15'], 'j2' => $rec['j2'], 'j3' => $rec['j3'], 'j4' => $rec['j4'],
                        'jam' => $rec['jam'], 'keterangan' => $rec['keterangan']
                    ];
                } else {
                    $lemburRecordsPerPerson[$nama][] = [
                        'tanggal' => $tglStr, 'hari' => $hariStr,
                        'mulai' => '', 'selesai' => '', 'ist' => '', 'riil' => '0',
                        'j15' => '', 'j2' => '', 'j3' => '', 'j4' => '',
                        'jam' => '0', 'keterangan' => ''
                    ];
                }
            }
        }

        $defaultGp = 3496195;
        $defaultUpah = $defaultGp / 173;
    @endphp

    <!-- AREA 1: HALAMAN REKAP UTAMA -->
    <div id="view-rekap-lembur" class="animate-fade-in-up flex flex-col h-full bg-white w-full rounded-2xl sm:rounded-3xl shadow-sm border border-slate-100 p-3.5 sm:p-7">

        <div class="mb-6 border-b-2 border-slate-100 pb-5">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
                <div>
                    <h4 class="text-base sm:text-lg font-black uppercase text-slate-800 tracking-widest mb-1"><i class="fas fa-file-invoice-dollar mr-2 text-indigo-600"></i> Rekapitulasi Gaji Lembur</h4>
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">PT. Tropis Service - Sub Area Produksi</h2>
                    <p class="text-xs font-black text-indigo-700 mt-2 sm:mt-3 uppercase bg-indigo-50 inline-block px-3 py-1.5 rounded-lg border border-indigo-100">
                        Periode: {{ $periodeTitle }}
                    </p>
                </div>
                <div class="flex flex-col items-start sm:items-end gap-3 w-full sm:w-auto">
                    <form id="formPeriode" method="GET" class="bg-slate-50 p-2.5 sm:px-4 sm:py-2 rounded-xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
                        <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest"><i class="fas fa-calendar-alt mr-1"></i> Dari</label>
                        <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="text-xs font-bold text-slate-700 border border-slate-300 rounded px-2 py-1 outline-none bg-white flex-1 sm:flex-none" onchange="submitPeriode()">
                        <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Sampai</label>
                        <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-t')) }}" class="text-xs font-bold text-slate-700 border border-slate-300 rounded px-2 py-1 outline-none bg-white flex-1 sm:flex-none" onchange="submitPeriode()">

                        @php $depts = \App\Models\Employee::pluck('pers_subarea_text')->filter()->unique()->sort()->values(); @endphp
                        <select name="dept" onchange="submitPeriode()" class="text-xs font-bold text-slate-700 border border-slate-300 rounded px-2 py-1 outline-none bg-white w-full sm:w-auto mt-1 sm:mt-0">
                            <option value="">Semua Departemen</option>
                            @foreach($depts as $d)
                                <option value="{{ $d }}" {{ request('dept') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </form>
                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                        <button onclick="exportLemburSemuaToExcel()" class="flex-1 sm:flex-none bg-green-600 text-white px-3 sm:px-4 py-2 rounded-lg text-[10px] font-black uppercase shadow-sm hover:bg-green-700 transition-all text-center"><i class="fas fa-file-excel mr-1"></i> Export Excel Semua</button>
                        <button onclick="window.print()" class="bg-indigo-600 text-white px-3 sm:px-4 py-2 rounded-lg text-[10px] font-black uppercase shadow-sm hover:bg-indigo-700 transition-all text-center"><i class="fas fa-print mr-1"></i> Cetak</button>
                    </div>
                </div>
            </div>

            <div class="relative w-full md:w-80 mt-2">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
                <input type="text" id="cariKaryawanLembur" onkeyup="fungsiCariLembur()" class="bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 font-bold outline-none transition-shadow" placeholder="Cari Nama Karyawan...">
            </div>
        </div>

        <div class="overflow-x-auto w-full flex-1">
            <table class="w-full text-left border-collapse table-bordered whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-600 text-[10px] uppercase tracking-wider font-black border-b-2 border-slate-200">
                    <tr>
                        <th class="p-4 text-center w-12">No</th>
                        <th class="p-4">Nama</th>
                        <th class="p-4 text-center">Akm Lembur</th>
                        <th class="p-4 text-center">Upah Lembur per jam<br><span class="text-[8px] text-slate-400 font-bold">GP/173</span></th>
                        <th class="p-4 text-center">X Jml Lmbr</th>
                        <th class="p-4 text-center">% (Lembur/GP)</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($rekapLembur as $item)
                        @php $persentase = ($item->total_tagihan / 3496195) * 100; @endphp
                    <tr class="baris-rekap text-xs font-bold text-slate-700 border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="p-4 text-center nomor-urut-rekap">{{ $no++ }}</td>
                        <td class="p-4 text-blue-700 uppercase kolom-nama-karyawan">{{ $item->nama }}</td>
                        <td class="p-4 text-center font-black text-indigo-600 text-sm">{{ $item->total_jam_lembur }}</td>
                        <td class="p-4 text-center text-slate-500">Rp {{ number_format($item->upah_per_jam, 0, ',', '.') }}</td>
                        <td class="p-4 text-center font-black text-emerald-600 text-sm">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                        <td class="p-4 text-center text-slate-500">{{ number_format($persentase, 2, ',', '.') }}%</td>
                        <td class="p-4 text-center">
                            <button onclick="bukaHalamanDetail('{{ $item->nama }}')" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors shadow-sm border border-indigo-100">
                                <i class="fas fa-list mr-1"></i> Rincian
                            </button>
                        </td>
                    </tr>
                    @endforeach

                    <tr id="baris-total-rekap" class="bg-slate-100 text-xs font-black uppercase text-slate-800 border-t-2 border-slate-300">
                        <td colspan="2" class="p-4 text-center tracking-widest">Total Keseluruhan</td>
                        <td class="p-4 text-center text-sm text-indigo-700">{{ $rekapLembur->sum('total_jam_lembur') }}</td>
                        <td class="p-4"></td>
                        <td class="p-4 text-center text-emerald-600 text-base">Rp {{ number_format($rekapLembur->sum('total_tagihan'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>

            <div id="pesanKosongRekap" class="hidden py-10 text-center">
                <p class="text-slate-400 italic font-bold text-xs uppercase tracking-widest">Karyawan tidak ditemukan.</p>
            </div>
        </div>
    </div>

    <!-- AREA 2: HALAMAN FOKUS DETAIL LEMBUR (MODEL EXCEL VIEW) -->
    <div id="view-detail-lembur" class="hidden flex-col h-full bg-white w-full rounded-3xl shadow-sm border border-slate-100 p-7 absolute inset-0 z-20 overflow-hidden">

        <div class="flex flex-wrap justify-between items-center border-b-2 border-slate-100 pb-5 mb-5 shrink-0 gap-4">
            <div class="flex items-center gap-5">
                <button onclick="tutupHalamanDetail()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-black tracking-widest uppercase transition-colors shadow-sm flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </button>
                <div>
                    <h3 class="text-xl font-black text-indigo-800 uppercase tracking-widest" id="judulHalamanDetail">Rincian Lembur</h3>
                    <p class="text-[11px] font-bold text-slate-400 mt-1 uppercase">Riwayat kerja ekstra harian karyawan secara spesifik</p>
                </div>
            </div>

            <!-- TOMBOL EXPORT KHUSUS RINCIAN KARYAWAN -->
            <button onclick="exportSatuKaryawanKeExcel()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-xs font-black tracking-widest uppercase transition-colors shadow-sm flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Download Rincian Excel
            </button>
        </div>

        <div class="overflow-y-auto flex-1 bg-white rounded-xl border border-slate-300 shadow-inner">
            <table class="w-full text-left border-collapse whitespace-nowrap bg-white overflow-hidden" id="tabelHalamanDetail">

                {{-- HEADER TABEL (DITAMBAH UPAH HARIAN DAN % GP) --}}
                <thead class="bg-indigo-50 text-indigo-800 text-[10px] uppercase tracking-wider font-black sticky top-0 shadow-sm z-10">
                    <tr>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle w-10">No</th>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle">Tanggal</th>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle">Hari</th>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle">Mulai</th>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle">Selesai</th>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle">Istirahat</th>
                        <th class="p-2 text-center border border-indigo-200">Jlh Jam</th>
                        <th colspan="4" class="p-2 text-center border border-indigo-200">Akm. Jam Lembur</th>
                        <th class="p-2 text-center border border-indigo-200">Jlh Akm.</th>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle">Upah Harian</th>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle">% GP</th>
                        <th rowspan="2" class="p-3 text-center border border-indigo-200 align-middle">Keterangan</th>
                    </tr>
                    <tr>
                        <th class="p-2 text-center border border-indigo-200 bg-indigo-100/50">Lembur</th>
                        <th class="p-2 text-center border border-indigo-200 bg-indigo-100/50">1.5</th>
                        <th class="p-2 text-center border border-indigo-200 bg-indigo-100/50">2</th>
                        <th class="p-2 text-center border border-indigo-200 bg-indigo-100/50">3</th>
                        <th class="p-2 text-center border border-indigo-200 bg-indigo-100/50">4</th>
                        <th class="p-2 text-center border border-indigo-200 bg-indigo-100/50">Jam Lemb.</th>
                    </tr>
                </thead>
                <tbody id="bodyTabelHalamanDetail" class="text-[11px] font-bold text-slate-700">
                    {{-- LOOP DATA EXCEL LANGSUNG UNTUK MENAMPILKAN STRUKTUR HARIAN --}}
                    @foreach($lemburRecordsPerPerson as $nama => $records)
                        @php
                            $noUrut = 1;
                            $totalRiil = 0;
                            $totalAkm = 0;
                            $totalUang = 0;
                            $totalPersen = 0;
                        @endphp

                        @foreach($records as $rec)
                            @php
                                $riilVal = (float)($rec['riil'] ?: 0);
                                $akmVal = (float)($rec['jam'] ?: 0);
                                $upahHarian = $akmVal * $defaultUpah;
                                $persenHarian = ($upahHarian / $defaultGp) * 100;

                                $totalRiil += $riilVal;
                                $totalAkm += $akmVal;
                                $totalUang += $upahHarian;
                                $totalPersen += $persenHarian;
                            @endphp
                            <tr class="baris-halaman-detail border-b border-slate-200 hover:bg-indigo-50/40 transition" data-nama="{{ strtoupper($nama) }}" style="display: none;">
                                <td class="p-2 text-center text-slate-400 border-r border-slate-200">{{ $noUrut++ }}</td>
                                <td class="p-2 text-center border-r border-slate-200 text-slate-600 font-medium">{{ \Carbon\Carbon::parse($rec['tanggal'])->format('d/m/Y') }}</td>
                                <td class="p-2 text-center border-r border-slate-200">{{ $rec['hari'] }}</td>
                                <td class="p-2 text-center border-r border-slate-200 font-mono">{{ $rec['mulai'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 font-mono">{{ $rec['selesai'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200">{{ $rec['ist'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 text-indigo-600">{{ $rec['riil'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 text-slate-500">{{ $rec['j15'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 text-slate-500">{{ $rec['j2'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 text-slate-500">{{ $rec['j3'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 text-slate-500">{{ $rec['j4'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 font-black text-blue-700 bg-blue-50/30">{{ $rec['jam'] ?: '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 font-bold text-emerald-600 bg-emerald-50/20">Rp {{ $upahHarian > 0 ? number_format($upahHarian, 0, ',', '.') : '-' }}</td>
                                <td class="p-2 text-center border-r border-slate-200 font-bold text-rose-600 bg-rose-50/20">{{ $persenHarian > 0 ? number_format($persenHarian, 2, ',', '.') . '%' : '-' }}</td>
                                <td class="p-2 text-slate-500 uppercase">{{ $rec['keterangan'] ?: '-' }}</td>
                            </tr>
                        @endforeach

                        <!-- Baris Total Per Karyawan 1 Bulan -->
                        <tr class="baris-halaman-detail-total bg-indigo-100/50 text-indigo-900 border-b-2 border-indigo-200" data-nama="{{ strtoupper($nama) }}" style="display: none;">
                            <td colspan="6" class="p-3 text-right font-black uppercase tracking-widest border-r border-indigo-200">Total Keseluruhan (1 Bulan)</td>
                            <td class="p-3 text-center font-black text-indigo-700 border-r border-indigo-200 text-sm">{{ $totalRiil > 0 ? $totalRiil : '-' }}</td>
                            <td colspan="4" class="p-3 border-r border-indigo-200"></td>
                            <td class="p-3 text-center font-black text-blue-700 text-sm border-r border-indigo-200">{{ $totalAkm > 0 ? $totalAkm : '-' }}</td>
                            <td class="p-3 text-center font-black text-emerald-700 text-sm border-r border-indigo-200">Rp {{ $totalUang > 0 ? number_format($totalUang, 0, ',', '.') : '-' }}</td>
                            <td class="p-3 text-center font-black text-rose-700 text-sm border-r border-indigo-200">{{ $totalPersen > 0 ? number_format($totalPersen, 2, ',', '.') . '%' : '-' }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="pesanKosongHalaman" class="hidden py-20 text-center flex flex-col items-center justify-center h-full">
                <i class="fas fa-folder-open text-6xl text-slate-200 mb-4"></i>
                <p class="text-slate-400 font-black text-sm uppercase tracking-widest">Tidak ada rincian lembur.</p>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT PENGENDALI TAB GAJI LEMBUR & EXPORT --}}
<script>
    let karyawanFokus = "";

    function fungsiCariLembur() {
        let input = document.getElementById("cariKaryawanLembur").value.toUpperCase();
        let rows = document.querySelectorAll(".baris-rekap");
        let found = 0;
        let urutBaru = 1;

        rows.forEach(row => {
            let nama = row.querySelector(".kolom-nama-karyawan").textContent.toUpperCase();
            if (nama.indexOf(input) > -1) {
                row.style.display = "";
                row.querySelector(".nomor-urut-rekap").innerText = urutBaru++;
                found++;
            } else {
                row.style.display = "none";
            }
        });

        if (found === 0) {
            document.getElementById("pesanKosongRekap").classList.remove("hidden");
            document.getElementById("baris-total-rekap").style.display = "none";
        } else {
            document.getElementById("pesanKosongRekap").classList.add("hidden");
            document.getElementById("baris-total-rekap").style.display = "";
        }
    }

    function bukaHalamanDetail(namaKaryawan) {
        document.getElementById('view-rekap-lembur').classList.add('hidden');
        document.getElementById('view-rekap-lembur').classList.remove('flex');

        document.getElementById('view-detail-lembur').classList.remove('hidden');
        document.getElementById('view-detail-lembur').classList.add('flex');

        document.getElementById('judulHalamanDetail').innerText = "Rincian Lembur: " + namaKaryawan;
        karyawanFokus = namaKaryawan;

        const rows = document.querySelectorAll('.baris-halaman-detail');
        const rowsTotal = document.querySelectorAll('.baris-halaman-detail-total');
        let count = 0;
        let namaTarget = namaKaryawan.trim().toUpperCase();

        rows.forEach(row => {
            if (row.getAttribute('data-nama') === namaTarget) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        rowsTotal.forEach(row => {
            if (row.getAttribute('data-nama') === namaTarget) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        if (count === 0) {
            document.getElementById('pesanKosongHalaman').classList.remove('hidden');
            document.getElementById('tabelHalamanDetail').classList.add('hidden');
        } else {
            document.getElementById('pesanKosongHalaman').classList.add('hidden');
            document.getElementById('tabelHalamanDetail').classList.remove('hidden');
        }
    }

    function tutupHalamanDetail() {
        document.getElementById('view-detail-lembur').classList.add('hidden');
        document.getElementById('view-detail-lembur').classList.remove('flex');

        document.getElementById('view-rekap-lembur').classList.remove('hidden');
        document.getElementById('view-rekap-lembur').classList.add('flex');

        karyawanFokus = "";
    }

    const dataJSON = @json($lemburData);
    const recordsJSON = @json($lemburRecordsPerPerson);
    const periodeTeks = "{{ $periodeTitle }}";
    let gp = @json($defaultGp);
    let upahPerJam = @json($defaultUpah ?? ($defaultGp/173));

    function submitPeriode() {
        let f = document.getElementById('formPeriode');
        f.action = window.location.pathname + "#lembur";
        f.submit();
    }

    function exportLemburSemuaToExcel() {
        let rowsHtml = "";
        let no = 1;
        let totalAkm = 0;
        let totalUang = 0;

        for (let nama in dataJSON) {
            let jam = dataJSON[nama];
            let uang = Math.round(jam * upahPerJam);
            let persentase = gp > 0 ? ((uang / gp) * 100).toFixed(2) + "%" : "0%";

            rowsHtml += `
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">${no++}</td>
                <td style="border: 1px solid #000000;">${nama}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight:bold;">${jam}</td>
                <td style="border: 1px solid #000000; text-align: center;">Rp ${Math.round(upahPerJam).toLocaleString('id-ID')}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight:bold;">Rp ${uang.toLocaleString('id-ID')}</td>
                <td style="border: 1px solid #000000; text-align: center;">${persentase}</td>
            </tr>`;

            totalAkm += jam;
            totalUang += uang;
        }

        let tableHtml = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8"></head>
        <body>
            <table>
                <tr><td colspan="6" style="font-weight:bold;">PERINCIAN LEMBUR KARYAWAN PT. TROPIS SERVICE DI PT. INDOFOOD ( Persero )</td></tr>
                <tr><td colspan="6" style="font-weight:bold;">SUB AREA PRODUKSI</td></tr>
                <tr><td colspan="6" style="font-weight:bold;">PERIODE ${periodeTeks}</td></tr>
                <tr><td colspan="6"></td></tr>
                <tr>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">No</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">Nama</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">Akm. Lembur</th>
                    <th style="border: 1px solid #000000; background-color: #c7d2fe; text-align: center; font-weight:bold;">Upah Lembur per jam</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">X.hsl Lmbr</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">% (Lembur/GP)</th>
                </tr>
                <tr>
                    <th style="border: 1px solid #000000; background-color: #e0e7ff; text-align: center; font-weight:bold;">GP/173</th>
                </tr>
                ${rowsHtml}
                <tr>
                    <td colspan="2" style="border: 1px solid #000000; background-color: #fef08a; font-weight:bold; text-align: center;">TOTAL KESELURUHAN</td>
                    <td style="border: 1px solid #000000; background-color: #fef08a; font-weight:bold; text-align: center;">${totalAkm}</td>
                    <td style="border: 1px solid #000000; background-color: #fef08a;"></td>
                    <td style="border: 1px solid #000000; background-color: #fef08a; font-weight:bold; text-align: center;">Rp ${totalUang.toLocaleString('id-ID')}</td>
                    <td style="border: 1px solid #000000; background-color: #fef08a;"></td>
                </tr>
            </table>
        </body>
        </html>`;

        let blob = new Blob([tableHtml], {type: 'application/vnd.ms-excel'});
        let url = URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = "REKAP_LEMBUR_SEMUA_" + periodeTeks.replace(/ /g, "_") + ".xls";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function exportSatuKaryawanKeExcel() {
        if(!karyawanFokus) { Swal.fire('Error', 'Nama Karyawan tidak terdeteksi.', 'error'); return; }

        let records = recordsJSON[karyawanFokus];
        if(!records || records.length === 0) {
            Swal.fire('Info', 'Tidak ada data rincian untuk di-export.', 'info');
            return;
        }

        let rowsHtml = "";
        let totalJamEmp = 0;
        let totalUangEmp = 0;
        let totalPersenEmp = 0;

        records.forEach(rec => {
            let jamAkm = parseFloat(rec.jam) || 0;
            let upahHariIni = Math.round(jamAkm * upahPerJam);
            let persenHarian = gp > 0 ? (upahHariIni / gp) * 100 : 0;

            totalJamEmp += jamAkm;
            totalUangEmp += upahHariIni;
            totalPersenEmp += persenHarian;

            rowsHtml += `
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">${rec.tanggal}</td>
                <td style="border: 1px solid #000000; text-align: center;">${rec.hari.toUpperCase()}</td>
                <td style="border: 1px solid #000000; text-align: center; mso-number-format:'\@';">${rec.mulai || "-"}</td>
                <td style="border: 1px solid #000000; text-align: center; mso-number-format:'\@';">${rec.selesai || "-"}</td>
                <td style="border: 1px solid #000000; text-align: center;">${rec.ist || "-"}</td>
                <td style="border: 1px solid #000000; text-align: center;">${rec.riil === '' ? 0 : parseFloat(rec.riil)}</td>
                <td style="border: 1px solid #000000; text-align: center;">${rec.j15 || "-"}</td>
                <td style="border: 1px solid #000000; text-align: center;">${rec.j2 || "-"}</td>
                <td style="border: 1px solid #000000; text-align: center;">${rec.j3 || "-"}</td>
                <td style="border: 1px solid #000000; text-align: center;">${rec.j4 || "-"}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight:bold;">${jamAkm === 0 ? 0 : jamAkm}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight:bold; color:#065f46; background-color:#ecfdf5;">Rp ${upahHariIni > 0 ? upahHariIni.toLocaleString('id-ID') : '-'}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight:bold; color:#9f1239; background-color:#fff1f2;">${persenHarian > 0 ? persenHarian.toFixed(2) + '%' : '-'}</td>
                <td style="border: 1px solid #000000;">${rec.keterangan || "-"}</td>
            </tr>`;
        });

        let tableHtml = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8"></head>
        <body>
            <table>
                <tr><td colspan="14" style="font-weight:bold;">PERHITUNGAN LEMBUR KARYAWAN OUTSOURCING PT. TROPIS SERVICE</td></tr>
                <tr><td colspan="14" style="font-weight:bold;">PADA PT. INDOFOOD ( Persero )</td></tr>
                <tr><td colspan="14" style="font-weight:bold;">PERIODE ${periodeTeks}</td></tr>
                <tr><td colspan="14"></td></tr>
                <tr><td colspan="2" style="font-weight:bold;">NAMA</td><td>:</td><td colspan="11" style="font-weight:bold;">${karyawanFokus}</td></tr>
                <tr><td colspan="2" style="font-weight:bold;">BAGIAN</td><td>:</td><td colspan="11" style="font-weight:bold;">CS</td></tr>
                <tr><td colspan="14"></td></tr>
                <tr>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">TANGGAL</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">HARI</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">MULAI</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">SELESAI</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">ISTIRAHAT</th>
                    <th style="border: 1px solid #000000; background-color: #c7d2fe; text-align: center; font-weight:bold;">JLH JAM</th>
                    <th colspan="4" style="border: 1px solid #000000; background-color: #c7d2fe; text-align: center; font-weight:bold;">AKM. JAM LEMBUR</th>
                    <th style="border: 1px solid #000000; background-color: #c7d2fe; text-align: center; font-weight:bold;">JLH AKM.</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">UPAH HARIAN</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">% GP</th>
                    <th rowspan="2" style="border: 1px solid #000000; background-color: #c7d2fe; vertical-align: middle; text-align: center; font-weight:bold;">Keterangan</th>
                </tr>
                <tr>
                    <th style="border: 1px solid #000000; background-color: #e0e7ff; text-align: center; font-weight:bold;">LEMBUR</th>
                    <th style="border: 1px solid #000000; background-color: #e0e7ff; text-align: center; font-weight:bold;">1.5</th>
                    <th style="border: 1px solid #000000; background-color: #e0e7ff; text-align: center; font-weight:bold;">2</th>
                    <th style="border: 1px solid #000000; background-color: #e0e7ff; text-align: center; font-weight:bold;">3</th>
                    <th style="border: 1px solid #000000; background-color: #e0e7ff; text-align: center; font-weight:bold;">4</th>
                    <th style="border: 1px solid #000000; background-color: #e0e7ff; text-align: center; font-weight:bold;">JAM LEMB</th>
                </tr>
                ${rowsHtml}
                <tr>
                    <td colspan="9" style="border: 1px solid #000000;"></td>
                    <td style="border: 1px solid #000000; background-color: #fef08a; font-weight:bold; text-align: center;">TOTAL (1 BULAN)</td>
                    <td style="border: 1px solid #000000; background-color: #fef08a; font-weight:bold; text-align: center;">${totalJamEmp}</td>
                    <td style="border: 1px solid #000000; background-color: #fef08a; font-weight:bold; text-align: center; color:#065f46;">Rp ${totalUangEmp.toLocaleString('id-ID')}</td>
                    <td style="border: 1px solid #000000; background-color: #fef08a; font-weight:bold; text-align: center; color:#9f1239;">${totalPersenEmp.toFixed(2)}%</td>
                    <td style="border: 1px solid #000000;"></td>
                </tr>
            </table>
        </body>
        </html>`;

        let blob = new Blob([tableHtml], {type: 'application/vnd.ms-excel'});
        let url = URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;

        let safeFileName = karyawanFokus.replace(/ /g, "_").toUpperCase();
        a.download = "RINCIAN_LEMBUR_" + safeFileName + "_" + periodeTeks.replace(/ /g, "_") + ".xls";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>

{{-- TAB 2: INPUT LEMBUR TELAH DIPINDAHKAN KE input_lembur.blade.php --}}
@if(false)
<div id="tab-input-lembur-legacy" class="tab-content hidden h-full flex flex-col relative">
    <div class="bg-transparent flex-1 flex flex-col min-h-0 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6 pt-2 flex-1 min-h-0 overflow-y-auto">

            <div class="lg:col-span-1 glass-card p-6 border-t-8 border-blue-800 bg-white h-fit shadow-xl">
                <h4 class="text-sm font-black uppercase text-slate-700 tracking-widest mb-5 border-b pb-3">
                    <i class="fas fa-clock mr-2 text-blue-800"></i>Input Lembur
                </h4>

                <form action="{{ route('attendance.store') }}#input-lembur" method="POST" class="space-y-4" onsubmit="return gabungkanDataLembur()">
                    @csrf
                    <input type="hidden" name="action" value="assorting_tim">
                    <input type="hidden" name="status_kehadiran" value="Lembur">

                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase ml-1">NIK Karyawan</label>
                        <input type="text" name="id_karyawan" id="lembur_nik" placeholder="8000XXXX" required class="form-input-modern text-slate-900">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase ml-1">Nama Lengkap</label>
                        <input type="text" name="nama_karyawan" id="lembur_nama" required class="form-input-modern text-slate-900">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase ml-1">Tanggal Lembur (WITA)</label>
                        <input type="date" name="tanggal" id="in_tanggal" value="{{ \Carbon\Carbon::now('Asia/Makassar')->format('Y-m-d') }}" class="form-input-modern text-slate-900">
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-200 space-y-3 shadow-inner">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[9px] font-bold text-blue-800 uppercase">Jam Mulai</label>
                                <input type="time" id="in_mulai" class="form-input-modern p-1.5 text-center text-slate-900" oninput="hitungLemburDetail()" required>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-blue-800 uppercase">Jam Selesai</label>
                                <input type="time" id="in_selesai" class="form-input-modern p-1.5 text-center text-slate-900" oninput="hitungLemburDetail()" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[9px] font-bold text-blue-800 uppercase">Istirahat (Menit)</label>
                                <select id="in_istirahat" class="form-input-modern p-1.5 text-slate-900" onchange="hitungLemburDetail()">
                                    <option value="0">0</option>
                                    <option value="30">30</option>
                                    <option value="60">60</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-blue-800 uppercase">Tipe Hari</label>
                                <select id="in_tipe" class="form-input-modern p-1.5 text-slate-900" onchange="hitungLemburDetail()">
                                    <option value="Kerja">Hari Kerja</option>
                                    <option value="Libur">Hari Libur</option>
                                </select>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-blue-200 flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-[8px] font-black text-slate-500 uppercase">Durasi Riil:</span>
                                <span id="display_riil" class="text-xs font-bold text-slate-800">0 Jam</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-slate-500 uppercase block leading-none">Akm. Jam:</span>
                                <input type="text" name="jam_lembur" id="in_akm" readonly class="w-24 bg-transparent border-none text-right font-black text-blue-800 text-3xl p-0 outline-none" value="0">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="keterangan" id="input_ket_real">
                    <input type="hidden" id="hide_riil" value="0">
                    <input type="hidden" id="hide_15" value="0">
                    <input type="hidden" id="hide_2" value="0">
                    <input type="hidden" id="hide_3" value="0">
                    <input type="hidden" id="hide_4" value="0">

                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase ml-1">Keterangan</label>
                        <input type="text" id="input_ket_visible" placeholder="Contoh: Bongkar Muat" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-semibold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <button type="submit" class="w-full mt-3 flex items-center justify-center gap-2 bg-blue-800 text-white font-extrabold py-4 rounded-xl uppercase text-xs tracking-widest shadow-md hover:bg-blue-900 transition-colors">
                        <i class="fas fa-save text-lg"></i>
                        <span>SIMPAN DATA</span>
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 glass-card flex flex-col shadow-xl min-h-0 bg-white">
                <div class="p-5 border-b bg-slate-50 flex justify-between items-center shrink-0 rounded-t-xl">
                    <h4 class="text-xs font-black uppercase text-slate-700 tracking-widest">Riwayat Lembur (WITA)</h4>
                </div>
                <div class="overflow-y-auto flex-1 p-4">
                    <table class="w-full text-left text-sm table-bordered border-collapse">
                        <thead class="bg-slate-100 sticky top-0 z-10 font-bold text-[10px] uppercase text-slate-600">
                            <tr>
                                <th class="p-3 text-center">NO</th>
                                <th class="p-3">NAMA KARYAWAN</th>
                                <th class="p-3 text-center">AKM</th>
                                <th class="p-3">WAKTU & KET</th>
                                <th class="p-3 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @foreach($tim_assorting->where('status', 'Lembur') as $data)
                            @php
                                $rawCatatan = $data->catatan ?? '';
                                $cleanCatatan = preg_replace('/~.*~/', '', $rawCatatan);
                                $cleanCatatan = str_replace('Assorting: ', '', $cleanCatatan);
                                $parts = explode('|', $cleanCatatan);

                                $namaArr = explode('-', trim($parts[0]));
                                $namePart = count($namaArr) > 1 ? trim($namaArr[1]) : trim($namaArr[0]);

                                $jamAkm = '0';
                                $ketBersih = '';
                                for ($i = 1; $i < count($parts); $i++) {
                                    $p = trim($parts[$i]);
                                    if ($p === '') continue;
                                    $checkNum = str_ireplace(['akm:', 'jam', ' '], '', $p);
                                    if (is_numeric(str_replace(',', '.', $checkNum)) && $jamAkm == '0') {
                                        $jamAkm = str_replace(',', '.', $checkNum);
                                    } elseif (stripos($p, 'Ket:') !== false) {
                                        $ketBersih = trim(str_ireplace('Ket:', '', $p));
                                    } elseif ($ketBersih == '') {
                                        $ketBersih = $p;
                                    }
                                }

                                $jamInfo = "";
                                if(preg_match('/~(.*?)~/', $rawCatatan, $m)) {
                                    $hParts = explode('|', $m[1]);
                                    $mM = '-'; $sS = '-';
                                    foreach($hParts as $hp) {
                                        $kv = explode(':', $hp, 2);
                                        if(count($kv) == 2) {
                                            if($kv[0] == 'M') $mM = $kv[1];
                                            if($kv[0] == 'S') $sS = $kv[1];
                                        }
                                    }
                                    if ($mM !== '-' || $sS !== '-') {
                                        $jamInfo = "[$mM - $sS] ";
                                    }
                                }
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                                <td class="p-3 font-bold uppercase text-slate-800">{{ $namePart }}</td>
                                <td class="p-3 text-center font-black text-blue-800 bg-blue-50/30">{{ $jamAkm }} Jam</td>
                                <td class="p-3">
                                    <span class="text-[10px] font-mono font-bold text-indigo-600">{{ $jamInfo }}</span>
                                    <span class="text-slate-600 uppercase">{{ str_replace('Ket:', '', $ketBersih) }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('attendance.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-400 hover:text-red-600"><i class="fas fa-trash-alt"></i></button>
                                    </form>
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

{{-- SCRIPT PENGENDALI TAB INPUT LEMBUR --}}
<script>
    function timeToDec(t) {
        if(!t) return 0;
        let p = t.split(':');
        return parseInt(p[0]) + (parseInt(p[1])/60);
    }

    function hitungLemburDetail() {
        let valMulai = document.getElementById('in_mulai').value;
        let valSelesai = document.getElementById('in_selesai').value;

        if (!valMulai || !valSelesai) return;

        let m = timeToDec(valMulai);
        let s = timeToDec(valSelesai);
        let i = parseFloat(document.getElementById('in_istirahat').value) || 0;
        let t = document.getElementById('in_tipe').value;

        if (s < m) s += 24;

        let r = (s - m) - (i / 60);
        if (r < 0) r = 0;

        document.getElementById('hide_riil').value = r.toFixed(2);
        document.getElementById('display_riil').innerText = r.toFixed(1).replace('.0', '') + " Jam";

        let akm = 0;
        let j15 = 0, j2 = 0, j3 = 0, j4 = 0;

        if (t === 'Kerja') {
            if (r > 0) {
                j15 = r >= 1 ? 1 : r;
                j2 = r > 1 ? r - 1 : 0;
            }
        } else {
            if (r <= 7) {
                j2 = r;
            } else if (r <= 8) {
                j2 = 7; j3 = r - 7;
            } else {
                j2 = 7; j3 = 1; j4 = r - 8;
            }
        }

        akm = (j15 * 1.5) + (j2 * 2) + (j3 * 3) + (j4 * 4);

        document.getElementById('in_akm').value = akm.toFixed(1).replace('.0', '');
        document.getElementById('hide_15').value = j15.toFixed(2);
        document.getElementById('hide_2').value = j2.toFixed(2);
        document.getElementById('hide_3').value = j3.toFixed(2);
        document.getElementById('hide_4').value = j4.toFixed(2);
    }

    function gabungkanDataLembur() {
        let k = document.getElementById('input_ket_visible').value || 'Lembur';
        let a = document.getElementById('in_akm').value;
        let m = document.getElementById('in_mulai').value;
        let s = document.getElementById('in_selesai').value;
        let i = document.getElementById('in_istirahat').value;
        let r = document.getElementById('hide_riil').value;
        let j15 = document.getElementById('hide_15').value;
        let j2 = document.getElementById('hide_2').value;
        let j3 = document.getElementById('hide_3').value;
        let j4 = document.getElementById('hide_4').value;
        let tgl = document.getElementById('in_tanggal').value;

        document.getElementById('input_ket_real').value = "Akm:" + a + " | Ket:" + k + ` ~T:${tgl}|M:${m}|S:${s}|I:${i}|R:${r}|15:${j15}|2:${j2}|3:${j3}|4:${j4}~`;
        return true;
    }
</script>
@endif
