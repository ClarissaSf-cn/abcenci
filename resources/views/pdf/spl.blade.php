<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Lembur (SPL) - {{ $spl->id }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 12mm 10mm 12mm 10mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #004a99;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .header-subtitle {
            font-size: 11px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #334155;
            width: 110px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 14px;
            font-size: 9.5px;
        }
        .data-table th, .data-table td {
            border: 1px solid #475569;
            padding: 4px 3px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5px;
        }
        .bg-jam-kerja {
            background-color: #e2e8f0 !important;
        }
        .bg-rencana {
            background-color: #dbeafe !important;
            color: #1e40af !important;
        }
        .bg-istirahat {
            background-color: #fef3c7 !important;
            color: #92400e !important;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .signatures-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures-table td {
            width: 25%;
            text-align: center;
            vertical-align: top;
            padding: 4px 10px;
        }
        .sig-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 45px;
        }
        .sig-name {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1.5px solid #1e293b;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 140px;
        }
        .sig-role {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
            font-weight: bold;
        }
        .total-highlight {
            font-weight: bold;
            color: #004a99;
            background-color: #f8fafc;
        }
    </style>
</head>
<body>

    {{-- Header Dokumen --}}
    <div class="header">
        <div class="header-title">SURAT PERINTAH LEMBUR (SPL)</div>
        <div class="header-subtitle">PEKERJA ALIH DAYA (OUTSOURCING)</div>
    </div>

    {{-- Informasi Dokumen --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Hari / Tanggal</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ \Carbon\Carbon::parse($spl->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</strong></td>
            <td class="info-label">No. Dokumen</td>
            <td style="width: 10px;">:</td>
            <td><strong>SPL-{{ str_pad($spl->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Nama Vendor</td>
            <td>:</td>
            <td><strong>{{ $spl->vendor ?: '-' }}</strong></td>
            <td class="info-label">Dibuat Pada</td>
            <td>:</td>
            <td>{{ $spl->created_at ? $spl->created_at->format('d/m/Y H:i') : '-' }}</td>
        </tr>
    </table>

    {{-- Tabel Rincian Pekerja Lembur --}}
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 75px;">NIK</th>
                <th rowspan="2" style="width: 140px;">Nama Pekerja</th>
                <th rowspan="2" style="width: 100px;">Jabatan</th>
                <th colspan="2" class="bg-jam-kerja">Jam Kerja</th>
                <th colspan="2" class="bg-rencana">Rencana Lembur</th>
                <th colspan="2" class="bg-istirahat">Istirahat Lembur</th>
                <th rowspan="2" style="width: 55px;">Total</th>
                <th rowspan="2">Alasan Lembur</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th class="bg-jam-kerja" style="width: 45px;">In</th>
                <th class="bg-jam-kerja" style="width: 45px;">Out</th>
                <th class="bg-rencana" style="width: 45px;">Awal</th>
                <th class="bg-rencana" style="width: 45px;">Akhir</th>
                <th class="bg-istirahat" style="width: 45px;">Awal</th>
                <th class="bg-istirahat" style="width: 45px;">Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($spl->details ?? []) as $d)
                @php
                    $jam = (float)($d->total_jam ?? 0);
                    if ($jam <= 0 && !empty($d->lembur_awal) && !empty($d->lembur_akhir)) {
                        try {
                            $tAwal = \Carbon\Carbon::parse($d->lembur_awal);
                            $tAkhir = \Carbon\Carbon::parse($d->lembur_akhir);
                            if ($tAkhir->lessThan($tAwal)) {
                                $tAkhir->addDay();
                            }
                            $diffM = $tAkhir->diffInMinutes($tAwal);
                            if (!empty($d->istirahat_awal) && !empty($d->istirahat_akhir)) {
                                $iAwal = \Carbon\Carbon::parse($d->istirahat_awal);
                                $iAkhir = \Carbon\Carbon::parse($d->istirahat_akhir);
                                if ($iAkhir->lessThan($iAwal)) {
                                    $iAkhir->addDay();
                                }
                                $diffM -= $iAkhir->diffInMinutes($iAwal);
                            }
                            $jam = max(0, round($diffM / 60, 1));
                        } catch (\Exception $e) {
                            $jam = 0;
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $d->nik ?: '-' }}</td>
                    <td style="font-weight: bold; text-transform: uppercase;">{{ $d->nama ?: '-' }}</td>
                    <td class="text-center">{{ $d->jabatan ?: '-' }}</td>
                    <td class="text-center">{{ $d->jam_in ? substr($d->jam_in, 0, 5) : '-' }}</td>
                    <td class="text-center">{{ $d->jam_out ? substr($d->jam_out, 0, 5) : '-' }}</td>
                    <td class="text-center">{{ $d->lembur_awal ? substr($d->lembur_awal, 0, 5) : '-' }}</td>
                    <td class="text-center">{{ $d->lembur_akhir ? substr($d->lembur_akhir, 0, 5) : '-' }}</td>
                    <td class="text-center">{{ $d->istirahat_awal ? substr($d->istirahat_awal, 0, 5) : '-' }}</td>
                    <td class="text-center">{{ $d->istirahat_akhir ? substr($d->istirahat_akhir, 0, 5) : '-' }}</td>
                    <td class="text-center total-highlight">{{ number_format($jam, 1) }} Jam</td>
                    <td>{{ $d->alasan ?: '-' }}</td>
                    <td>{{ $d->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding: 15px; color: #64748b;">Tidak ada rincian pekerja lembur.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Kolom Tanda Tangan / Approval (4 Kolom Sesuai Form Kertas Fisik) --}}
    <table class="signatures-table">
        <tr>
            {{-- Dibuat Oleh --}}
            <td>
                <div class="sig-title">Dibuat Oleh,</div>
                <div class="sig-name">
                    {{ $spl->dibuat_oleh ?: (optional($spl->createdBy)->name ?: 'ADMIN USER') }}
                </div>
                <div class="sig-role">Admin User</div>
            </td>

            {{-- Ditugaskan Oleh --}}
            <td>
                <div class="sig-title">Ditugaskan Oleh,</div>
                <div class="sig-name">
                    {{ $spl->ditugaskan_oleh ?: 'PIC OUTSOURCING' }}
                </div>
                <div class="sig-role">PIC</div>
            </td>

            {{-- Disetujui Oleh --}}
            <td>
                <div class="sig-title">Disetujui Oleh,</div>
                <div class="sig-name">
                    {{ $spl->disetujui_oleh ?: 'DEPT. HEAD USER' }}
                </div>
                <div class="sig-role">Dept. Head User</div>
            </td>

            {{-- Diketahui Oleh --}}
            <td>
                <div class="sig-title">Diketahui Oleh,</div>
                <div class="sig-name">
                    {{ $spl->diketahui_oleh ?: 'DEPT. HEAD HR' }}
                </div>
                <div class="sig-role">Dept. Head HR</div>
            </td>
        </tr>
    </table>

</body>
</html>
