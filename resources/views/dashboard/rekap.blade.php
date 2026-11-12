<div id="tab-rekap" class="tab-content hidden h-full flex flex-col">
    <div class="glass-card overflow-hidden flex flex-col flex-1 shadow-md min-h-0">
        {{-- FILTER BAR (SATU BARIS RAPI & TERSTRUKTUR) --}}
        <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/90 flex flex-row flex-nowrap items-end gap-2.5 overflow-x-auto w-full shrink-0">

            {{-- 1. Mulai Tanggal --}}
            <div class="shrink-0">
                <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block ml-1 tracking-wider">Mulai Tgl</label>
                <input type="date" id="export_start" onchange="filterRekapPintar()" class="h-[38px] w-[130px] text-xs px-2.5 rounded-xl border border-slate-300 bg-white font-semibold text-slate-700 outline-none shadow-sm focus:border-indofood-blue focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer">
            </div>

            {{-- 2. Sampai Tanggal --}}
            <div class="shrink-0">
                <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block ml-1 tracking-wider">Sampai Tgl</label>
                <input type="date" id="export_end" onchange="filterRekapPintar()" class="h-[38px] w-[130px] text-xs px-2.5 rounded-xl border border-slate-300 bg-white font-semibold text-slate-700 outline-none shadow-sm focus:border-indofood-blue focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer">
            </div>

            {{-- 3. Status --}}
            <div class="shrink-0">
                <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block ml-1 tracking-wider">Status</label>
                <select id="filterStatusRekap" onchange="filterRekapPintar()" class="h-[38px] w-[125px] text-xs px-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-700 outline-none shadow-sm cursor-pointer focus:border-indofood-blue focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Semua Status</option>
                    <option value="HADIR">Hadir</option>
                    <option value="IZIN">Izin / Sakit</option>
                    <option value="TANPA KETERANGAN">Tanpa Keterangan</option>
                </select>
            </div>

            {{-- 4. Departemen --}}
            <div class="shrink-0">
                <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block ml-1 tracking-wider">Departemen</label>
                <select id="filterDepartmentRekap" onchange="filterRekapPintar()" class="h-[38px] w-[150px] text-xs px-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-700 outline-none shadow-sm cursor-pointer focus:border-indofood-blue focus:ring-2 focus:ring-blue-100 transition-all truncate">
                    <option value="">Semua Departemen</option>
                    @php
                        $departments = $employees->pluck('pers_subarea_text')->filter()->unique()->sort()->values();
                    @endphp
                    @foreach($departments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 5. Jabatan --}}
            <div class="shrink-0">
                <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block ml-1 tracking-wider">Jabatan</label>
                <select id="filterPositionRekap" onchange="filterRekapPintar()" class="h-[38px] w-[140px] text-xs px-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-700 outline-none shadow-sm cursor-pointer focus:border-indofood-blue focus:ring-2 focus:ring-blue-100 transition-all truncate">
                    <option value="">Semua Jabatan</option>
                    @php
                        $positions = $employees->pluck('jabatan')->filter()->unique()->sort()->values();
                    @endphp
                    @foreach($positions as $position)
                        <option value="{{ $position }}">{{ $position }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 6. Hari --}}
            <div class="shrink-0">
                <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block ml-1 tracking-wider">Hari</label>
                <select id="filterDayRekap" onchange="filterRekapPintar()" class="h-[38px] w-[115px] text-xs px-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-700 outline-none shadow-sm cursor-pointer focus:border-indofood-blue focus:ring-2 focus:ring-blue-100 transition-all">
                    <option value="">Semua Hari</option>
                    <option value="SENIN">Senin</option>
                    <option value="SELASA">Selasa</option>
                    <option value="RABU">Rabu</option>
                    <option value="KAMIS">Kamis</option>
                    <option value="JUMAT">Jumat</option>
                    <option value="SABTU">Sabtu</option>
                    <option value="MINGGU">Minggu</option>
                </select>
            </div>

            {{-- 7. Pencarian --}}
            <div class="shrink-0">
                <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block ml-1 tracking-wider">Pencarian</label>
                <div class="h-[38px] w-[180px] flex items-center bg-white border border-slate-300 rounded-xl px-3 shadow-sm focus-within:border-indofood-blue focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                    <i class="fas fa-search text-slate-400 text-xs mr-2 shrink-0"></i>
                    <input type="text" id="rekapSearchPintar" onkeyup="filterRekapPintar()" placeholder="Cari NIK / Nama..." class="text-xs border-none outline-none w-full font-semibold text-slate-700 bg-transparent placeholder:text-slate-400 focus:ring-0 p-0">
                </div>
            </div>

            {{-- 8. Kumpulan Tombol Export (Style Block seperti di Master Shift) --}}
            <div class="shrink-0 ml-auto flex items-center gap-2">
                <button type="button" onclick="exportRekapKePdf()" style="background-color: #ef4444; color: #ffffff;" class="bg-red-500 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-red-600 transition shadow flex items-center cursor-pointer">
                    <i class="fas fa-file-pdf mr-2 text-sm text-white"></i> PDF
                </button>
                <button type="button" onclick="exportRekapKeExcel()" style="background-color: #16a34a; color: #ffffff;" class="bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-green-700 transition shadow flex items-center cursor-pointer">
                    <i class="fas fa-file-excel mr-2 text-sm text-white"></i> EXCEL
                </button>
            </div>

        </div>

        <div class="overflow-auto flex-1 bg-white relative">
            <table id="tableRekapAbsen" class="w-full min-w-[750px] text-left text-sm table-bordered">
                <thead class="bg-indofood-blue text-white sticky top-0 z-10 text-[10px] uppercase font-bold shadow-md">
                    <tr>
                        <th class="p-4 text-center w-12 border-blue-800">NO</th>
                        <th class="p-4 text-center border-blue-800">FOTO</th>
                        <th class="p-4 border-blue-800">NAMA KARYAWAN (ID - NAMA)</th>
                        <th class="p-4 text-center border-blue-800">NO SIDIK JARI</th>
                        <th class="p-4 text-center border-blue-800">SHIFT</th>
                        <th class="p-4 text-center w-32 border-blue-800">TANGGAL</th>
                        <th class="p-4 text-center border-blue-800">JAM MASUK</th>
                        <th class="p-4 text-center border-blue-800">JAM KELUAR</th>
                        <th class="p-4 text-center w-32 border-blue-800">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs bg-white text-slate-700">
                    @php
                        $noAbsen = 1;
                        $processedData = [];
                    @endphp

                    @foreach($rekap_semua as $data)
                    @php
                        if($data->status == 'Lembur') continue;

                        $rawCatatan = str_replace('Assorting: ', '', $data->catatan);
                        $parts = explode(' | ', $rawCatatan);
                        $idAndName = $parts[0] ?? 'Tanpa Nama';
                        $nik = trim(explode('-', $idAndName)[0] ?? '');
                        $tanggalKey = $data->created_at->format('Y-m-d');

                        $uniqueKey = $nik . '_' . $tanggalKey;
                        if(in_array($uniqueKey, $processedData)) continue;
                        $processedData[] = $uniqueKey;

                        $emp = $employees->firstWhere('nik', $nik);
                        $sidikJari = $emp->no_sidik_jari ?? '-';
                        $shift = $emp->shift_type ?? '-';

                        $jamMasuk = '-';
                        $jamKeluar = '-';
                        $isTelat = false;
                        $isManualInput = false;

                        foreach($parts as $p) {
                            $p = trim($p);
                            if(str_starts_with($p, 'M:')) {
                                $jamM = trim(str_replace('M:', '', $p));
                                if (str_contains($jamM, '[TELAT]')) {
                                    $isTelat = true;
                                    $jamMasuk = trim(str_replace('[TELAT]', '', $jamM));
                                } else {
                                    $jamMasuk = $jamM;
                                }
                                $isManualInput = true;
                            }
                            if(str_starts_with($p, 'S:')) {
                                $jamKeluar = trim(str_replace('S:', '', $p));
                                $isManualInput = true;
                            }
                        }

                        if($jamMasuk === '-' || $jamKeluar === '-') {
                            $scansToday = collect($rekap_semua)->filter(function($item) use ($nik, $tanggalKey) {
                                $itemNik = trim(explode('-', str_replace('Assorting: ', '', $item->catatan))[0] ?? '');
                                return $itemNik == $nik && $item->created_at->format('Y-m-d') == $tanggalKey;
                            })->sortBy('created_at');

                            if($jamMasuk === '-' && $scansToday->count() > 0) {
                                $jamMasuk = $scansToday->first()->created_at->format('H:i');
                            }
                            if($jamKeluar === '-' && $scansToday->count() > 1) {
                                $jamKeluar = $scansToday->last()->created_at->format('H:i');
                            }
                        }

                    @endphp
                    <tr class="hover:bg-slate-50 transition-all" data-department="{{ strtoupper($emp->pers_subarea_text ?? 'Tidak Diketahui') }}" data-jabatan="{{ strtoupper($emp->jabatan ?? 'Tidak Diketahui') }}" data-hari="{{ strtoupper($data->created_at->translatedFormat('dddd')) }}">
                        <td class="p-4 text-center font-bold text-slate-400">{{ $noAbsen++ }}</td>

                        {{-- TAMPILAN FOTO --}}
                        <td class="p-2 text-center align-middle">
                            @if($data->foto)
                                <img src="{{ asset('storage/' . $data->foto) }}" alt="Foto Absen" 
                                     onclick="previewFotoModal('{{ asset('storage/' . $data->foto) }}', '{{ $idAndName }}')"
                                     loading="lazy" 
                                     class="w-12 h-12 object-cover rounded-lg mx-auto border border-slate-200 shadow-sm cursor-pointer hover:scale-110 transition-transform duration-300"
                                     title="Klik untuk perbesar foto">
                            @else
                                <div class="w-10 h-10 mx-auto rounded bg-slate-100 flex items-center justify-center border border-slate-200 text-slate-300" title="Tanpa Foto">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>

                        <td class="p-4 font-extrabold uppercase text-slate-800 tracking-wide">{{ $idAndName }}</td>
                        <td class="p-4 text-center font-mono font-bold text-indigo-600 bg-indigo-50/30">{{ $sidikJari }}</td>
                        <td class="p-4 text-center font-bold text-slate-600">{{ $shift }}</td>

                        <td class="p-4 text-center font-bold" data-tgl="{{ $data->created_at->format('Y-m-d') }}">{{ $data->created_at->format('d/m/Y') }}</td>

                        {{-- TAMPILAN JAM MASUK + INDIKATOR TELAT --}}
                        <td class="p-4 text-center font-black">
                            @if($isTelat)
                                <span class="text-red-500 text-sm">{{ $jamMasuk }}</span>
                                <span class="block text-[8px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded uppercase mt-1 w-fit mx-auto shadow-sm">Telat</span>
                            @else
                                <span class="{{ $jamMasuk !== '-' ? 'text-green-600 text-sm' : 'text-slate-400' }}">{{ $jamMasuk }}</span>
                            @endif
                        </td>

                        {{-- TAMPILAN JAM KELUAR + INDIKATOR PULANG DULU / LAMBAT --}}
                        <td class="p-4 text-center font-black">
                            @if($jamKeluar !== '-')
                                <span class="text-blue-600 text-sm">{{ $jamKeluar }}</span>

                                @php
                                    $isPulangAwal = false;
                                    $isPulangTerlambat = false;
                                    if(isset($emp->shift->time_end) && $jamKeluar !== '-' ){
                                        $timeEnd = trim((string) $emp->shift->time_end);
                                        if($timeEnd !== ''){
                                            $anchorDate = $tanggalKey . ' ';
                                            $jamKeluarTs = strtotime($anchorDate . $jamKeluar);
                                            $timeEndTs = strtotime($anchorDate . $timeEnd);

                                            if($jamKeluarTs !== false && $timeEndTs !== false){
                                                $isPulangAwal = $jamKeluarTs < $timeEndTs;
                                                $isPulangTerlambat = $jamKeluarTs > $timeEndTs;
                                            }
                                        }
                                    }
                                @endphp

                                @if($isPulangAwal)
                                    <span class="block text-[8px] bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded uppercase mt-1 w-fit mx-auto shadow-sm">
                                        Pulang Awal
                                    </span>
                                @elseif($isPulangTerlambat)
                                    <span class="block text-[8px] bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded uppercase mt-1 w-fit mx-auto shadow-sm">
                                        Telat Pulang
                                    </span>
                                @endif
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="p-4 text-center">
                            @php $cl = match($data->status) { 'Hadir' => 'bg-green-100 text-green-700 border-green-200', 'Izin' => 'bg-yellow-100 text-yellow-700 border-yellow-200', default => 'bg-red-100 text-red-700 border-red-200' }; @endphp
                            <span class="px-3 py-1.5 rounded border text-[9px] font-black uppercase tracking-widest shadow-sm {{ $cl }}">
                                {{ $data->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>
    function filterRekapPintar() {
        const searchName = document.getElementById("rekapSearchPintar").value.toUpperCase();
        const searchStatus = document.getElementById("filterStatusRekap").value.toUpperCase();
        const startDate = document.getElementById("export_start").value;
        const endDate = document.getElementById("export_end").value;
        const filterDepartment = document.getElementById("filterDepartmentRekap").value.toUpperCase();
        const filterPosition = document.getElementById("filterPositionRekap").value.toUpperCase();
        const filterDay = document.getElementById("filterDayRekap").value.toUpperCase();

        const table = document.getElementById("tableRekapAbsen");
        const tr = table.getElementsByTagName("tr");
        let visibleCount = 0;

        for (let i = 1; i < tr.length; i++) {
            const name = tr[i].cells[2]?.textContent.toUpperCase() || "";
            const rowDate = tr[i].cells[5]?.getAttribute('data-tgl') || "";
            const status = tr[i].cells[8]?.textContent.toUpperCase() || "";
            const rowDepartment = tr[i].dataset.department?.toUpperCase() || "";
            const rowPosition = tr[i].dataset.jabatan?.toUpperCase() || "";
            const rowDay = tr[i].dataset.hari?.toUpperCase() || "";

            const matchName = name.includes(searchName);
            const matchStatus = searchStatus === "" || status.includes(searchStatus);
            const matchDepartment = filterDepartment === "" || rowDepartment === filterDepartment;
            const matchPosition = filterPosition === "" || rowPosition === filterPosition;
            const matchDay = filterDay === "" || rowDay === filterDay;

            let matchDate = true;
            if (startDate && endDate) {
                matchDate = (rowDate >= startDate && rowDate <= endDate);
            } else if (startDate) {
                matchDate = (rowDate >= startDate);
            } else if (endDate) {
                matchDate = (rowDate <= endDate);
            }

            if (matchName && matchStatus && matchDate && matchDepartment && matchPosition && matchDay) {
                tr[i].style.display = "";
                visibleCount++;
                tr[i].cells[0].innerText = visibleCount;
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    function exportRekapKeExcel() {
        if(typeof XLSX === 'undefined') {
            Swal.fire('Error', 'Library Excel (SheetJS) belum siap.', 'error');
            return;
        }

        let table = document.getElementById("tableRekapAbsen");
        let rows = table.querySelectorAll("tbody tr");
        let dataToExport = [];

        dataToExport.push(["NO", "NAMA KARYAWAN", "NO SIDIK JARI", "SHIFT", "TANGGAL", "JAM MASUK", "JAM KELUAR", "STATUS"]);

        let no = 1;
        for (let i = 0; i < rows.length; i++) {
            let row = rows[i];
            if (row.style.display !== "none") {
                let cols = row.querySelectorAll("td");
                if (cols.length < 9) continue;
                dataToExport.push([
                    no++,
                    cols[2].innerText.trim(),
                    cols[3].innerText.trim(),
                    cols[4].innerText.trim(),
                    cols[5].innerText.trim(),
                    cols[6].innerText.replace(/Telat/ig, '').trim(),
                    cols[7].innerText.replace(/Pulang Awal|Telat Pulang/ig, '').trim(),
                    cols[8].innerText.trim()
                ]);
            }
        }

        if (dataToExport.length <= 1) {
            Swal.fire('Perhatian', 'Tidak ada data absensi yang ditampilkan untuk diekspor.', 'warning');
            return;
        }

        let ws = XLSX.utils.aoa_to_sheet(dataToExport);
        ws['!cols'] = [{wch:6}, {wch:35}, {wch:15}, {wch:12}, {wch:15}, {wch:14}, {wch:14}, {wch:15}];

        let wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Rekap Absen");
        
        let datePart = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        XLSX.writeFile(wb, `Laporan_Rekap_Absensi_${datePart}.xlsx`);
    }

    function getThumbnailBase64(imgEl) {
        return new Promise((resolve) => {
            if (!imgEl || !imgEl.src) {
                resolve(null);
                return;
            }
            if (imgEl.complete && imgEl.naturalWidth > 0) {
                try {
                    const canvas = document.createElement('canvas');
                    canvas.width = 70;
                    canvas.height = 70;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(imgEl, 0, 0, 70, 70);
                    resolve(canvas.toDataURL('image/jpeg', 0.8));
                    return;
                } catch (e) {
                }
            }
            const img = new Image();
            img.crossOrigin = 'Anonymous';
            img.onload = function() {
                try {
                    const canvas = document.createElement('canvas');
                    canvas.width = 70;
                    canvas.height = 70;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, 70, 70);
                    resolve(canvas.toDataURL('image/jpeg', 0.8));
                } catch (err) {
                    resolve(null);
                }
            };
            img.onerror = () => resolve(null);
            img.src = imgEl.src;
        });
    }

    async function exportRekapKePdf() {
        if (typeof window.jspdf === 'undefined' && typeof window.jsPDF === 'undefined') {
            Swal.fire('Error', 'Library jsPDF belum siap. Pastikan koneksi internet aktif.', 'error');
            return;
        }

        const table = document.getElementById("tableRekapAbsen");
        if (!table) return;

        const rows = table.querySelectorAll("tbody tr");
        const visibleRows = [];

        for (let i = 0; i < rows.length; i++) {
            if (rows[i].style.display !== "none") {
                const cols = rows[i].querySelectorAll("td");
                if (cols.length >= 9) {
                    visibleRows.push(cols);
                }
            }
        }

        if (visibleRows.length === 0) {
            Swal.fire('Perhatian', 'Tidak ada data absensi yang ditampilkan untuk diekspor.', 'warning');
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Menyiapkan Dokumen PDF...',
                html: '<span class="text-xs text-slate-500">Sedang memproses data dan menyematkan foto absensi...</span>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        const bodyData = [];
        let no = 1;
        for (let i = 0; i < visibleRows.length; i++) {
            const cols = visibleRows[i];
            const imgEl = cols[1].querySelector('img');
            const photoBase64 = await getThumbnailBase64(imgEl);

            const jamMasuk = cols[6].innerText.replace(/Telat/ig, '').trim();
            const jamKeluar = cols[7].innerText.replace(/Pulang Awal|Telat Pulang/ig, '').trim();
            const status = cols[8].innerText.trim();

            bodyData.push([
                no++,
                photoBase64 ? { photo: photoBase64 } : '-',
                cols[2].innerText.trim(),
                cols[3].innerText.trim(),
                cols[4].innerText.trim(),
                cols[5].innerText.trim(),
                jamMasuk || '-',
                jamKeluar || '-',
                status || '-'
            ]);
        }

        const { jsPDF } = window.jspdf || window;
        const doc = new jsPDF('l', 'pt', 'a4');

        const startDate = document.getElementById("export_start")?.value || "";
        const endDate = document.getElementById("export_end")?.value || "";
        const statusFilter = document.getElementById("filterStatusRekap")?.value || "";
        const deptFilter = document.getElementById("filterDepartmentRekap")?.value || "";
        const posFilter = document.getElementById("filterPositionRekap")?.value || "";
        const dayFilter = document.getElementById("filterDayRekap")?.value || "";
        const searchFilter = document.getElementById("rekapSearchPintar")?.value || "";

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.setTextColor(0, 75, 156);
        doc.text("PT INDOFOOD CBP SUKSES MAKMUR Tbk", 40, 36);

        doc.setFontSize(11);
        doc.setTextColor(30, 41, 59);
        doc.text("LAPORAN REKAPITULASI ABSENSI KARYAWAN", 40, 52);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);

        let infoFilter = [];
        if (startDate || endDate) {
            infoFilter.push(`Periode: ${startDate || 'Awal'} s/d ${endDate || 'Akhir'}`);
        } else {
            infoFilter.push("Periode: Semua Tanggal");
        }
        if (statusFilter) infoFilter.push(`Status: ${statusFilter}`);
        if (deptFilter) infoFilter.push(`Dept: ${deptFilter}`);
        if (posFilter) infoFilter.push(`Jabatan: ${posFilter}`);
        if (dayFilter) infoFilter.push(`Hari: ${dayFilter}`);
        if (searchFilter) infoFilter.push(`Cari: "${searchFilter}"`);
        infoFilter.push(`Total: ${bodyData.length} Record`);
        infoFilter.push(`Dicetak: ${new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}`);

        doc.text(infoFilter.join("  |  "), 40, 66);

        doc.setDrawColor(0, 75, 156);
        doc.setLineWidth(1.5);
        doc.line(40, 72, 802, 72);

        doc.autoTable({
            startY: 80,
            head: [["NO", "FOTO", "NAMA KARYAWAN (ID - NAMA)", "NO SIDIK JARI", "SHIFT", "TANGGAL", "JAM MASUK", "JAM KELUAR", "STATUS"]],
            body: bodyData,
            theme: 'grid',
            headStyles: {
                fillColor: [0, 75, 156],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                fontSize: 8,
                halign: 'center',
                valign: 'middle'
            },
            styles: {
                fontSize: 7.5,
                cellPadding: 4,
                valign: 'middle',
                textColor: [30, 41, 59]
            },
            alternateRowStyles: {
                fillColor: [248, 250, 252]
            },
            columnStyles: {
                0: { halign: 'center', cellWidth: 26 },
                1: { halign: 'center', cellWidth: 36, minCellHeight: 28 },
                2: { halign: 'left', fontStyle: 'bold' },
                3: { halign: 'center', cellWidth: 70 },
                4: { halign: 'center', cellWidth: 68 },
                5: { halign: 'center', cellWidth: 65 },
                6: { halign: 'center', cellWidth: 60 },
                7: { halign: 'center', cellWidth: 60 },
                8: { halign: 'center', cellWidth: 75, fontStyle: 'bold' }
            },
            didParseCell: function(data) {
                if (data.section === 'body' && data.column.index === 1) {
                    if (data.cell.raw && typeof data.cell.raw === 'object') {
                        data.cell.text = [''];
                    }
                }
                if (data.section === 'body' && data.column.index === 8) {
                    const st = (data.cell.raw || '').toUpperCase();
                    if (st.includes('HADIR')) {
                        data.cell.styles.textColor = [21, 128, 61];
                    } else if (st.includes('IZIN') || st.includes('SAKIT')) {
                        data.cell.styles.textColor = [180, 83, 9];
                    } else {
                        data.cell.styles.textColor = [185, 28, 28];
                    }
                }
            },
            didDrawCell: function(data) {
                if (data.section === 'body' && data.column.index === 1) {
                    const raw = data.cell.raw;
                    if (raw && typeof raw === 'object' && raw.photo) {
                        try {
                            const imgSize = 22;
                            const posX = data.cell.x + (data.cell.width - imgSize) / 2;
                            const posY = data.cell.y + (data.cell.height - imgSize) / 2;
                            doc.addImage(raw.photo, 'JPEG', posX, posY, imgSize, imgSize);
                        } catch (e) {
                            console.warn('Gagal render foto di cell PDF:', e);
                        }
                    }
                }
            },
            margin: { left: 40, right: 40, bottom: 30 }
        });

        if (typeof Swal !== 'undefined') {
            Swal.close();
        }

        let datePart = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        doc.save(`Laporan_Rekap_Absensi_${datePart}.pdf`);
    }
</script>
