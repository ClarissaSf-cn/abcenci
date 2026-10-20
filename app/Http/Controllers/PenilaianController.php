<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PenilaianController extends Controller
{
    private static $monthMap = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
        '4' => 'April', '5' => 'Mei', '6' => 'Juni',
        '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
    ];

    private static $nameToMonth = [
        'januari' => '01', 'februari' => '02', 'maret' => '03',
        'april' => '04', 'mei' => '05', 'juni' => '06',
        'juli' => '07', 'agustus' => '08', 'september' => '09',
        'oktober' => '10', 'november' => '11', 'desember' => '12',
    ];

    public static function normalizeMonthName($bulan)
    {
        $trimBulan = trim((string)$bulan);
        if (isset(self::$monthMap[$trimBulan])) {
            return self::$monthMap[$trimBulan];
        }
        $lower = strtolower($trimBulan);
        if (isset(self::$nameToMonth[$lower])) {
            return self::$monthMap[self::$nameToMonth[$lower]];
        }
        return $trimBulan ?: self::$monthMap[date('m')];
    }

    public static function normalizeMonthCode($bulan)
    {
        $trimBulan = trim((string)$bulan);
        $lower = strtolower($trimBulan);
        if (isset(self::$nameToMonth[$lower])) {
            return self::$nameToMonth[$lower];
        }
        if (is_numeric($trimBulan)) {
            return sprintf('%02d', (int)$trimBulan);
        }
        return date('m');
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(401, 'Silakan login terlebih dahulu.');
            }

            $uRole = strtolower(str_replace([' ', '-'], '_', $user->role ?? ''));
            $uName = strtoupper(trim($user->name ?? ''));
            $isSuperAdmin = ($uRole === 'superadmin' || $uName === 'ADMIN SUPER');
            $isAdminPenilaian = ($uRole === 'admin_penilaian' || str_contains($uRole, 'penilaian') || $uRole === 'admin_absensi');

            if (!$isSuperAdmin && !$isAdminPenilaian) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki wewenang penilaian.'], 403);
                }
                return redirect()->to(route('dashboard'))->with('error', 'Akses ditolak: Anda tidak memiliki wewenang penilaian.');
            }

            $bulanRaw = $request->input('bulan', date('F'));
            $bulanName = self::normalizeMonthName($bulanRaw);
            $tahun = (string) $request->input('tahun', date('Y'));
            $penilaianList = $request->input('penilaian', []);
            $userId = Auth::id();

            $createdCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            foreach ($penilaianList as $item) {
                $nik = trim($item['nik'] ?? '');
                if (!$nik) continue;

                $aspek = $item['aspek'] ?? [];
                $skorDisiplin = (int) ($aspek[0] ?? 0);
                $skorKualitas = (int) ($aspek[1] ?? 0);
                $skorProduktivitas = (int) ($aspek[2] ?? 0);
                $skorKerjasama = (int) ($aspek[3] ?? 0);
                $skorTanggungJawab = (int) ($aspek[4] ?? 0);

                $totalSkor = (int) ($item['total_skor'] ?? ($skorDisiplin + $skorKualitas + $skorProduktivitas + $skorKerjasama + $skorTanggungJawab));
                $dipertahankan = $item['dipertahankan'] ?? null;
                $keterangan = $item['keterangan'] ?? null;

                if ($totalSkor === 0 && empty($dipertahankan) && empty($keterangan)) {
                    continue;
                }

                $existing = Penilaian::where('nik', $nik)
                    ->where(function($q) use ($bulanName, $bulanRaw) {
                        $q->where('bulan', $bulanName)->orWhere('bulan', $bulanRaw);
                    })
                    ->where('tahun', $tahun)
                    ->first();

                if ($existing) {

                    if (!$isSuperAdmin) {
                        $skippedCount++;
                        continue;
                    }

                    $existing->update([
                        'employee_name' => $item['employee_name'] ?? $existing->employee_name,
                        'user' => $item['user'] ?? $existing->user,
                        'vendor_os' => $item['vendor_os'] ?? $existing->vendor_os,
                        'position_text' => $item['position_text'] ?? $existing->position_text,
                        'pers_subarea_text' => $item['pers_subarea_text'] ?? $existing->pers_subarea_text,
                        'skor_disiplin' => $skorDisiplin,
                        'skor_kualitas' => $skorKualitas,
                        'skor_produktivitas' => $skorProduktivitas,
                        'skor_kerjasama' => $skorKerjasama,
                        'skor_tanggung_jawab' => $skorTanggungJawab,
                        'aspek_scores' => $aspek,
                        'total_skor' => $totalSkor,
                        'dipertahankan' => $dipertahankan,
                        'keterangan' => $keterangan,
                        'created_by' => $userId,
                    ]);
                    $updatedCount++;
                } else {

                    Penilaian::create([
                        'nik' => $nik,
                        'bulan' => $bulanName,
                        'tahun' => $tahun,
                        'employee_name' => $item['employee_name'] ?? null,
                        'user' => $item['user'] ?? null,
                        'vendor_os' => $item['vendor_os'] ?? null,
                        'position_text' => $item['position_text'] ?? null,
                        'pers_subarea_text' => $item['pers_subarea_text'] ?? null,
                        'skor_disiplin' => $skorDisiplin,
                        'skor_kualitas' => $skorKualitas,
                        'skor_produktivitas' => $skorProduktivitas,
                        'skor_kerjasama' => $skorKerjasama,
                        'skor_tanggung_jawab' => $skorTanggungJawab,
                        'aspek_scores' => $aspek,
                        'total_skor' => $totalSkor,
                        'dipertahankan' => $dipertahankan,
                        'keterangan' => $keterangan,
                        'created_by' => $userId,
                    ]);
                    $createdCount++;
                }
            }

            $msg = '';
            if ($isSuperAdmin) {
                $msg = "Berhasil memproses penilaian ({$createdCount} data baru, {$updatedCount} data diperbarui) untuk periode {$bulanName} {$tahun}!";
            } else {
                $msg = "Berhasil menyimpan {$createdCount} data penilaian baru (Periode: {$bulanName} {$tahun}).";
                if ($skippedCount > 0) {
                    $msg .= " Catatan: {$skippedCount} karyawan yang sudah pernah dinilai tidak diubah karena hak ubah hanya dimiliki oleh Super Admin.";
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'created_count' => $createdCount,
                    'updated_count' => $updatedCount,
                    'skipped_count' => $skippedCount,
                ]);
            }

            return redirect()->to(route('dashboard') . '#aspek-penilaian')->with('success', $msg);
        } catch (\Throwable $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->to(route('dashboard') . '#aspek-penilaian')->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui penilaian tunggal (HANYA BISA DIAKSES OLEH SUPER ADMIN)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $uRole = strtolower(str_replace([' ', '-'], '_', $user->role ?? ''));
        $uName = strtoupper(trim($user->name ?? ''));
        $isSuperAdmin = ($uRole === 'superadmin' || $uName === 'ADMIN SUPER');

        if (!$isSuperAdmin) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Hanya Super Admin yang berhak mengubah penilaian.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Hanya Super Admin yang berhak mengubah penilaian.');
        }

        $penilaian = Penilaian::findOrFail($id);

        $skorDisiplin = (int) $request->input('skor_disiplin', $penilaian->skor_disiplin);
        $skorKualitas = (int) $request->input('skor_kualitas', $penilaian->skor_kualitas);
        $skorProduktivitas = (int) $request->input('skor_produktivitas', $penilaian->skor_produktivitas);
        $skorKerjasama = (int) $request->input('skor_kerjasama', $penilaian->skor_kerjasama);
        $skorTanggungJawab = (int) $request->input('skor_tanggung_jawab', $penilaian->skor_tanggung_jawab);
        $totalSkor = $skorDisiplin + $skorKualitas + $skorProduktivitas + $skorKerjasama + $skorTanggungJawab;

        $penilaian->update([
            'skor_disiplin' => $skorDisiplin,
            'skor_kualitas' => $skorKualitas,
            'skor_produktivitas' => $skorProduktivitas,
            'skor_kerjasama' => $skorKerjasama,
            'skor_tanggung_jawab' => $skorTanggungJawab,
            'aspek_scores' => [$skorDisiplin, $skorKualitas, $skorProduktivitas, $skorKerjasama, $skorTanggungJawab],
            'total_skor' => $totalSkor,
            'dipertahankan' => $request->input('dipertahankan', $penilaian->dipertahankan),
            'keterangan' => $request->input('keterangan', $penilaian->keterangan),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Penilaian karyawan berhasil diperbarui oleh Super Admin.']);
        }

        return back()->with('success', 'Penilaian karyawan berhasil diperbarui oleh Super Admin.');
    }

    /**
     * Menghapus penilaian (HANYA BISA DIAKSES OLEH SUPER ADMIN)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $uRole = strtolower(str_replace([' ', '-'], '_', $user->role ?? ''));
        $uName = strtoupper(trim($user->name ?? ''));
        $isSuperAdmin = ($uRole === 'superadmin' || $uName === 'ADMIN SUPER');

        if (!$isSuperAdmin) {
            return back()->with('error', 'Akses ditolak: Hanya Super Admin yang berhak menghapus penilaian.');
        }

        $penilaian = Penilaian::findOrFail($id);
        $penilaian->delete();

        return back()->with('success', 'Data penilaian berhasil dihapus oleh Super Admin.');
    }

    /**
     * Mengambil data penilaian berdasarkan periode untuk UI Aspek Penilaian (AJAX)
     */
    public function getData(Request $request)
    {
        $user = Auth::user();
        $uRole = strtolower(str_replace([' ', '-'], '_', $user->role ?? ''));
        $uName = strtoupper(trim($user->name ?? ''));
        $isSuperAdmin = ($uRole === 'superadmin' || $uName === 'ADMIN SUPER');

        $bulanRaw = $request->input('bulan', date('m'));
        $bulanName = self::normalizeMonthName($bulanRaw);
        $bulanCode = self::normalizeMonthCode($bulanRaw);
        $tahun = (string) $request->input('tahun', date('Y'));

        $items = Penilaian::where(function($q) use ($bulanName, $bulanCode, $bulanRaw) {
            $q->where('bulan', $bulanName)
              ->orWhere('bulan', $bulanCode)
              ->orWhere('bulan', $bulanRaw);
        })
        ->where('tahun', $tahun)
        ->get()
        ->keyBy('nik');

        return response()->json([
            'success' => true,
            'bulan_name' => $bulanName,
            'tahun' => $tahun,
            'is_superadmin' => $isSuperAdmin,
            'data' => $items,
        ]);
    }

    public function laporan(Request $request)
    {
        $bulanRaw = $request->input('bulan', date('m'));
        $bulanName = self::normalizeMonthName($bulanRaw);
        $bulanCode = self::normalizeMonthCode($bulanRaw);
        $tahun = (string) $request->input('tahun', date('Y'));

        $laporanList = Penilaian::where(function($q) use ($bulanName, $bulanCode) {
            $q->where('bulan', $bulanName)
              ->orWhere('bulan', $bulanCode);
        })
        ->where('tahun', $tahun)
        ->orderBy('total_skor', 'desc')
        ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $laporanList
            ]);
        }

        return redirect()->to(route('dashboard', ['bulan' => $bulanCode, 'tahun' => $tahun]) . '#laporan-penilaian');
    }

    public function exportPdf(Request $request)
    {
        $bulanRaw = $request->input('bulan', date('m'));
        $bulanName = self::normalizeMonthName($bulanRaw);
        $bulanCode = self::normalizeMonthCode($bulanRaw);
        $tahun = (string) $request->input('tahun', date('Y'));

        $laporanList = Penilaian::where(function($q) use ($bulanName, $bulanCode) {
            $q->where('bulan', $bulanName)
              ->orWhere('bulan', $bulanCode);
        })
        ->where('tahun', $tahun)
        ->orderBy('total_skor', 'desc')
        ->get();

        $waktuCetak = \Carbon\Carbon::now('Asia/Makassar')->format('d-m-Y H:i');

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Aspek Penilaian Karyawan</title>
            <style>
                @page { margin: 15mm; size: landscape; }
                body { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; }
                .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #004a99; padding-bottom: 10px; }
                .header h2 { margin: 0; color: #004a99; font-size: 16px; text-transform: uppercase; font-weight: bold; }
                .header p { margin: 4px 0 0 0; color: #64748b; font-size: 10px; }
                .meta-table { width: 100%; margin-bottom: 12px; font-size: 10px; }
                .meta-table td { padding: 3px 0; }
                table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
                table.data th, table.data td { border: 1px solid #cbd5e1; padding: 6px 4px; text-align: center; }
                table.data th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 9px; }
                table.data tbody tr:nth-child(even) { background-color: #f8fafc; }
                .text-left { text-align: left; }
                .status-ya { background-color: #dcfce7; color: #166534; font-weight: bold; padding: 2px 4px; border-radius: 3px; }
                .status-cut { background-color: #fee2e2; color: #991b1b; font-weight: bold; padding: 2px 4px; border-radius: 3px; }
                .score-box { font-weight: bold; color: #0369a1; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>PT INDOFOOD CBP SUKSES MAKMUR TBK</h2>
                <h2>LAPORAN EVALUASI & ASPEK PENILAIAN KARYAWAN</h2>
                <p>Periode: <strong>' . htmlspecialchars($bulanName . ' ' . $tahun) . '</strong> | Dicetak pada: ' . $waktuCetak . ' WITA</p>
            </div>

            <table class="meta-table">
                <tr>
                    <td style="width: 25%;"><strong>Total Karyawan Dinilai:</strong> ' . count($laporanList) . ' Orang</td>
                    <td style="width: 25%;"><strong>Dipertahankan:</strong> ' . $laporanList->where('dipertahankan', 'YA')->count() . ' Orang</td>
                    <td style="width: 25%;"><strong>Cut Off:</strong> ' . $laporanList->where('dipertahankan', '!=', 'YA')->count() . ' Orang</td>
                    <td style="width: 25%; text-align: right;"><strong>Status:</strong> Resmi</td>
                </tr>
            </table>

            <table class="data">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 10%;">NIK</th>
                        <th class="text-left" style="width: 18%;">Nama Karyawan</th>
                        <th class="text-left" style="width: 14%;">Jabatan</th>
                        <th class="text-left" style="width: 14%;">Subarea / Plant</th>
                        <th style="width: 8%;">Disiplin</th>
                        <th style="width: 8%;">Kualitas</th>
                        <th style="width: 8%;">Produktif</th>
                        <th style="width: 8%;">Kerjasama</th>
                        <th style="width: 8%;">Tgjwb</th>
                        <th style="width: 8%;">Total Skor</th>
                        <th style="width: 10%;">Rekomendasi</th>
                        <th class="text-left" style="width: 12%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>';

        if ($laporanList->isEmpty()) {
            $html .= '<tr><td colspan="13" style="padding: 20px; color: #94a3b8; font-style: italic;">Belum ada data penilaian pada periode ' . htmlspecialchars($bulanName . ' ' . $tahun) . '.</td></tr>';
        } else {
            foreach ($laporanList as $idx => $row) {
                $statusHtml = ($row->dipertahankan === 'YA')
                    ? '<span class="status-ya">DIPERTAHANKAN</span>'
                    : '<span class="status-cut">CUT OFF</span>';

                $html .= '<tr>
                    <td>' . ($idx + 1) . '</td>
                    <td>' . htmlspecialchars($row->nik ?? '-') . '</td>
                    <td class="text-left"><strong>' . htmlspecialchars($row->employee_name ?? '-') . '</strong></td>
                    <td class="text-left">' . htmlspecialchars($row->position_text ?? '-') . '</td>
                    <td class="text-left">' . htmlspecialchars($row->pers_subarea_text ?? '-') . '</td>
                    <td>' . ($row->skor_disiplin ?? 0) . '</td>
                    <td>' . ($row->skor_kualitas ?? 0) . '</td>
                    <td>' . ($row->skor_produktivitas ?? 0) . '</td>
                    <td>' . ($row->skor_kerjasama ?? 0) . '</td>
                    <td>' . ($row->skor_tanggung_jawab ?? 0) . '</td>
                    <td class="score-box">' . ($row->total_skor ?? 0) . '</td>
                    <td>' . $statusHtml . '</td>
                    <td class="text-left">' . htmlspecialchars($row->keterangan ?? '-') . '</td>
                </tr>';
            }
        }

        $html .= '</tbody></table></body></html>';

        if (class_exists(Pdf::class)) {
            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download("Laporan_Penilaian_{$bulanName}_{$tahun}.pdf");
        }

        return response($html)->header('Content-Type', 'text/html');
    }

    public function exportExcel(Request $request)
    {
        $bulanRaw = $request->input('bulan', date('m'));
        $bulanName = self::normalizeMonthName($bulanRaw);
        $bulanCode = self::normalizeMonthCode($bulanRaw);
        $tahun = (string) $request->input('tahun', date('Y'));

        $laporanList = Penilaian::where(function($q) use ($bulanName, $bulanCode) {
            $q->where('bulan', $bulanName)
              ->orWhere('bulan', $bulanCode);
        })
        ->where('tahun', $tahun)
        ->orderBy('total_skor', 'desc')
        ->get();

        $filename = "Laporan_Penilaian_{$bulanName}_{$tahun}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($laporanList, $bulanName, $tahun) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['LAPORAN ASPEK PENILAIAN KARYAWAN']);
            fputcsv($out, ['Periode:', $bulanName . ' ' . $tahun]);
            fputcsv($out, []);
            fputcsv($out, [
                'No', 'NIK', 'Nama Karyawan', 'Jabatan', 'Subarea / Plant',
                'Disiplin', 'Kualitas Kerja', 'Produktivitas', 'Kerjasama', 'Tanggung Jawab',
                'Total Skor', 'Status Rekomendasi', 'Keterangan'
            ]);

            foreach ($laporanList as $idx => $row) {
                fputcsv($out, [
                    $idx + 1,
                    $row->nik ?? '-',
                    $row->employee_name ?? '-',
                    $row->position_text ?? '-',
                    $row->pers_subarea_text ?? '-',
                    $row->skor_disiplin ?? 0,
                    $row->skor_kualitas ?? 0,
                    $row->skor_produktivitas ?? 0,
                    $row->skor_kerjasama ?? 0,
                    $row->skor_tanggung_jawab ?? 0,
                    $row->total_skor ?? 0,
                    $row->dipertahankan ?? 'CUT OFF',
                    $row->keterangan ?? '-',
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
