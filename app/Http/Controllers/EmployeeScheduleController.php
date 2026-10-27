<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSchedule;
use App\Models\Shift;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ScheduleImport;

class EmployeeScheduleController extends Controller
{
    // Menampilkan halaman Jadwal Karyawan
    public function index()
    {
        // ✅ PERBAIKAN 1: Eager loading diperbaiki dari 'shift' menjadi shift1, shift2, shift3, employee
        // agar relasi yang dipakai di blade ($schedule->shift1, shift2, shift3, employee) tidak null
        $schedules = EmployeeSchedule::with(['shift1', 'shift2', 'shift3', 'employee'])
                        ->orderBy('schedule_date', 'desc')
                        ->get();

        // Mengambil data shift untuk ditampilkan di dropdown form
        $shifts = Shift::orderBy('wst_code', 'asc')->get();

        // ✅ PERBAIKAN 2: Tambah $employees agar blade tidak error saat hitung
        // registeredCount & unregisteredCount (variabel $employees dibutuhkan blade)
        $employees = \App\Models\Employee::orderBy('nama', 'asc')->get();

        return redirect()->to(route('dashboard') . '#jadwal-karyawan');
    }

    // Menyimpan Jadwal Karyawan (STORE)
    public function store(Request $request)
    {
        // NOTE:
        // Di form jadwal terdapat input shift: shift_1_id, shift_2_id, shift_3_id.
        // Table employee_schedules hanya punya 1 kolom shift_id (legacy),
        // jadi kita tetap simpan ketiganya dan ambil shift pertama yang terisi untuk shift_id.

        $request->validate([
            'nik'           => 'required|string|max:20',
            'schedule_date' => 'required|date',
            'schedule_type' => 'required|string|max:30',
            'special_note'  => 'nullable|string|max:500',

            
            'shift_1_id' => 'nullable|exists:shifts,id',
            'shift_2_id' => 'nullable|exists:shifts,id',
            'shift_3_id' => 'nullable|exists:shifts,id',
        ], [
            'nik.required'           => 'NIK Karyawan wajib diisi.',
            'schedule_date.required' => 'Tanggal tugas wajib diisi.',
            'schedule_type.required' => 'Jenis jadwal wajib dipilih.',
        ]);

        $shift1Id = $request->input('shift_1_id');
        $shift2Id = $request->input('shift_2_id');
        $shift3Id = $request->input('shift_3_id');

        if (!$shift1Id && !$shift2Id && !$shift3Id) {
            return redirect()->back()->withErrors([
                'shift_id' => 'Minimal satu Shift Kerja harus diisi.',
            ])->withInput()->with('active_tab', 'jadwal-karyawan');
        }

        // updateOrCreate agar tidak terjadi duplikasi jadwal di tanggal yang sama
        EmployeeSchedule::updateOrCreate(
            [
                'nik'           => $request->nik,
                'schedule_date' => $request->schedule_date,
            ],
            [
                'hari_kerja'    => $request->hari_kerja,
                'shift_1_id'    => $shift1Id,
                'shift_2_id'    => $shift2Id,
                'shift_3_id'    => $shift3Id,
                // Legacy: simpan shift_id agar kompatibel dengan data lama
                'shift_id'      => $shift1Id ?? $shift2Id ?? $shift3Id,
                'schedule_type' => $request->schedule_type,
                'special_note'  => $request->special_note,
            ]
        );

        // ✅ PERBAIKAN: redirect()->back() dengan membawa pesan active_tab agar javascript menangkapnya
        return redirect()->back()->with('success', 'Jadwal Karyawan berhasil disimpan!')->with('active_tab', 'jadwal-karyawan');
    }

    // Memperbarui data jadwal (UPDATE)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nik'           => 'required|string|max:20',
            'schedule_date' => 'required|date',
            'schedule_type' => 'required|string|max:30',
            'special_note'  => 'nullable|string|max:500',

            'shift_1_id' => 'nullable|exists:shifts,id',
            'shift_2_id' => 'nullable|exists:shifts,id',
            'shift_3_id' => 'nullable|exists:shifts,id',
        ], [
            'nik.required'           => 'NIK Karyawan wajib diisi.',
            'schedule_date.required' => 'Tanggal tugas wajib diisi.',
            'schedule_type.required' => 'Jenis jadwal wajib dipilih.',
        ]);

        $shift1Id = $request->input('shift_1_id');
        $shift2Id = $request->input('shift_2_id');
        $shift3Id = $request->input('shift_3_id');

        if (!$shift1Id && !$shift2Id && !$shift3Id) {
            return redirect()->back()->withErrors([
                'shift_id' => 'Minimal satu Shift Kerja harus diisi.',
            ])->withInput()->with('active_tab', 'jadwal-karyawan');
        }

        $schedule = EmployeeSchedule::findOrFail($id);
        $schedule->update([
            'nik'           => $request->nik,
            'hari_kerja'    => $request->hari_kerja,
            'shift_1_id'    => $shift1Id,
            'shift_2_id'    => $shift2Id,
            'shift_3_id'    => $shift3Id,
            // Legacy (agar relasi lama tidak putus)
            'shift_id'      => $shift1Id ?? $shift2Id ?? $shift3Id,
            'schedule_date' => $request->schedule_date,
            'schedule_type' => $request->schedule_type,
            'special_note'  => $request->special_note,
        ]);

        // ✅ PERBAIKAN: redirect()->back() dengan membawa pesan active_tab agar javascript menangkapnya
        return redirect()->back()->with('success', 'Jadwal Karyawan berhasil diperbarui!')->with('active_tab', 'jadwal-karyawan');
    }

    // Menghapus data jadwal (DELETE)
    public function destroy($id)
    {
        $schedule = EmployeeSchedule::findOrFail($id);
        $schedule->delete();

        // ✅ PERBAIKAN: redirect()->back() dengan membawa pesan active_tab agar javascript menangkapnya
        return redirect('/dashboard#jadwal-karyawan')->with('success', 'Jadwal Karyawan berhasil dihapus!');
    }

    // ==========================================
    // FUNGSI EXPORT EXCEL JADWAL KARYAWAN
    // ==========================================
    public function exportExcel()
    {
        $schedules = \App\Models\EmployeeSchedule::with(['employee', 'shift1', 'shift2', 'shift3'])
            ->orderBy('schedule_date', 'desc')
            ->get();

        $fileName = "Data_Jadwal_Karyawan_" . date('d-m-Y') . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<tr>
                <th style="background-color: #e2e8f0; font-weight: bold;">No</th>
                <th style="background-color: #e2e8f0; font-weight: bold;">Tanggal Penugasan</th>
                <th style="background-color: #e2e8f0; font-weight: bold;">Hari Kerja</th>
                <th style="background-color: #e2e8f0; font-weight: bold;">NIK Karyawan</th>
                <th style="background-color: #e2e8f0; font-weight: bold;">Nama Lengkap</th>
                <th style="background-color: #e2e8f0; font-weight: bold;">Jenis Jadwal</th>
                <th style="background-color: #e2e8f0; font-weight: bold;">Catatan Khusus</th>
                <th style="background-color: #e0f2fe; font-weight: bold;">Shift Aktif 1</th>
                <th style="background-color: #e0f2fe; font-weight: bold;">Jam Shift 1</th>
                <th style="background-color: #fef3c7; font-weight: bold;">Shift Aktif 2</th>
                <th style="background-color: #fef3c7; font-weight: bold;">Jam Shift 2</th>
                <th style="background-color: #ede9fe; font-weight: bold;">Shift Aktif 3</th>
                <th style="background-color: #ede9fe; font-weight: bold;">Jam Shift 3</th>
              </tr>';

        $no = 1;
        foreach ($schedules as $schedule) {
            $shift1 = $schedule->shift1;
            $shift2 = $schedule->shift2;
            $shift3 = $schedule->shift3;

            $jam1 = $shift1 ? \Carbon\Carbon::parse($shift1->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift1->time_end)->format('H:i') : '-';
            $jam2 = $shift2 ? \Carbon\Carbon::parse($shift2->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift2->time_end)->format('H:i') : '-';
            $jam3 = $shift3 ? \Carbon\Carbon::parse($shift3->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift3->time_end)->format('H:i') : '-';

            $nama = $schedule->employee->nama ?? '-';

            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') . '</td>';
            echo '<td style="text-align: center;">' . ($schedule->hari_kerja ?? '-') . '</td>';
            echo '<td style="mso-number-format:\'\@\';">' . $schedule->nik . '</td>';
            echo '<td>' . $nama . '</td>';
            echo '<td>' . strtoupper($schedule->schedule_type ?? 'Reguler') . '</td>';
            echo '<td>' . ($schedule->special_note ?? '-') . '</td>';
            echo '<td style="text-align: center;">' . ($shift1->wst_code ?? '-') . '</td>';
            echo '<td style="text-align: center;">' . $jam1 . '</td>';
            echo '<td style="text-align: center;">' . ($shift2->wst_code ?? '-') . '</td>';
            echo '<td style="text-align: center;">' . $jam2 . '</td>';
            echo '<td style="text-align: center;">' . ($shift3->wst_code ?? '-') . '</td>';
            echo '<td style="text-align: center;">' . $jam3 . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }

    // ==========================================
    // FUNGSI EXPORT PDF JADWAL KARYAWAN
    // ==========================================
    public function exportPdf()
    {
        $schedules  = EmployeeSchedule::with(['shift1', 'shift2', 'shift3', 'employee'])
            ->orderBy('schedule_date', 'desc')
            ->get();
        $waktuCetak = \Carbon\Carbon::now('Asia/Makassar')->format('d F Y - H:i:s');

        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Laporan Jadwal Karyawan Harian</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 8px; color: #333; }
                @page { margin: 10mm; size: landscape; }
                .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #059669; padding-bottom: 5px; }
                .header h2 { margin: 0; color: #059669; font-size: 14px; text-transform: uppercase; }
                .header p { margin: 5px 0 0 0; color: #666; font-size: 9px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #cbd5e1; padding: 4px; text-align: center; }
                th { background-color: #f1f5f9; color: #1e293b; font-weight: bold; text-transform: uppercase; font-size: 8px; }
                tbody tr:nth-child(even) { background-color: #f8fafc; }
                .text-left { text-align: left; }
                .bg-sky { background-color: #e0f2fe; }
                .bg-amber { background-color: #fef3c7; }
                .bg-violet { background-color: #ede9fe; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Laporan Jadwal Karyawan Harian</h2>
                <p>Dicetak pada: ' . $waktuCetak . ' WITA</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 3%;">No</th>
                        <th style="width: 8%;">Tanggal Penugasan</th>
                        <th style="width: 6%;">Hari Kerja</th>
                        <th style="width: 8%;">NIK Karyawan</th>
                        <th class="text-left" style="width: 12%;">Nama Lengkap</th>
                        <th style="width: 8%;">Jenis Jadwal</th>
                        <th style="width: 10%;">Catatan Khusus</th>
                        <th class="bg-sky">Shift 1</th>
                        <th class="bg-sky">Jam Shift 1</th>
                        <th class="bg-amber">Shift 2</th>
                        <th class="bg-amber">Jam Shift 2</th>
                        <th class="bg-violet">Shift 3</th>
                        <th class="bg-violet">Jam Shift 3</th>
                    </tr>
                </thead>
                <tbody>';

        if ($schedules->isEmpty()) {
            $html .= '<tr><td colspan="13">Belum ada data Jadwal Karyawan.</td></tr>';
        } else {
            foreach ($schedules as $index => $schedule) {
                $tanggal      = \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y');
                $nama         = $schedule->employee->nama ?? '-';
                $scheduleType = $schedule->schedule_type ?? 'Reguler';
                $specialNote  = $schedule->special_note ? e($schedule->special_note) : '-';
                $hariKerja    = $schedule->hari_kerja ?? '-';

                $shift1 = $schedule->shift1;
                $shift2 = $schedule->shift2;
                $shift3 = $schedule->shift3;

                $jam1 = $shift1 ? \Carbon\Carbon::parse($shift1->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift1->time_end)->format('H:i') : '-';
                $jam2 = $shift2 ? \Carbon\Carbon::parse($shift2->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift2->time_end)->format('H:i') : '-';
                $jam3 = $shift3 ? \Carbon\Carbon::parse($shift3->time_start)->format('H:i') . ' s/d ' . \Carbon\Carbon::parse($shift3->time_end)->format('H:i') : '-';

                $wst1 = $shift1->wst_code ?? '-';
                $wst2 = $shift2->wst_code ?? '-';
                $wst3 = $shift3->wst_code ?? '-';

                $html .= '<tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . $tanggal . '</td>
                    <td>' . $hariKerja . '</td>
                    <td>' . $schedule->nik . '</td>
                    <td class="text-left">' . strtoupper($nama) . '</td>
                    <td>' . strtoupper($scheduleType) . '</td>
                    <td>' . $specialNote . '</td>
                    <td class="bg-sky"><strong>' . $wst1 . '</strong></td>
                    <td class="bg-sky">' . $jam1 . '</td>
                    <td class="bg-amber"><strong>' . $wst2 . '</strong></td>
                    <td class="bg-amber">' . $jam2 . '</td>
                    <td class="bg-violet"><strong>' . $wst3 . '</strong></td>
                    <td class="bg-violet">' . $jam3 . '</td>
                </tr>';
            }
        }

        $html .= '</tbody></table></body></html>';

        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('Data_Jadwal_Karyawan.pdf');
    }

    // ==========================================
    // FUNGSI IMPORT JADWAL KARYAWAN (XLS, XLSX, CSV)
    // ==========================================
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
                        $fail('File harus berformat .xlsx, .xls, atau .csv');
                    }
                }
            ]
        ]);

        try {
            Excel::import(new ScheduleImport, $request->file('file_excel'));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal Karyawan berhasil diimport!'
                ]);
            }

            return redirect()->to(route('dashboard') . '#jadwal-karyawan')->with('success', 'Jadwal Karyawan berhasil diimport!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import gagal: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->to(route('dashboard') . '#jadwal-karyawan')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
