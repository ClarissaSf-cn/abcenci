<?php

namespace App\Http\Controllers;

use App\Exports\ShiftExport;
use App\Imports\ShiftImport;
use App\Models\Shift;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ShiftController extends Controller
{

    public function index()
    {
        return redirect()->to(route('dashboard').'#master-shift');
    }

    public function store(Request $request)
    {
        $request->validate([
            'wst_code' => 'required|string|max:10|unique:shifts,wst_code',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i',
            'break1_start' => 'nullable|date_format:H:i',
            'break1_end' => 'nullable|date_format:H:i',
            'break2_start' => 'nullable|date_format:H:i',
            'break2_end' => 'nullable|date_format:H:i',
        ]);

        $data = $request->all();
        $data['is_overnight'] = $request->has('is_overnight') ? true : false;
        $data['break1_duration'] = $this->calculateDuration($request->break1_start, $request->break1_end);
        $data['break2_duration'] = $this->calculateDuration($request->break2_start, $request->break2_end);

        Shift::create($data);

        return redirect()->to(route('dashboard').'#master-shift')->with('success', 'Master Shift berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'wst_code' => 'required|string|max:10|unique:shifts,wst_code,'.$id,
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i',
            'break1_start' => 'nullable|date_format:H:i',
            'break1_end' => 'nullable|date_format:H:i',
            'break2_start' => 'nullable|date_format:H:i',
            'break2_end' => 'nullable|date_format:H:i',
        ]);

        $shift = Shift::findOrFail($id);
        $data = $request->all();

        $data['is_overnight'] = $request->has('is_overnight') ? true : false;
        $data['break1_duration'] = $this->calculateDuration($request->break1_start, $request->break1_end);
        $data['break2_duration'] = $this->calculateDuration($request->break2_start, $request->break2_end);

        $shift->update($data);

        return redirect()->to(route('dashboard').'#master-shift')->with('success', 'Master Shift berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->to(route('dashboard').'#master-shift')->with('success', 'Master Shift berhasil dihapus!');
    }

    public function exportPdf()
    {
        $shifts = Shift::orderBy('wst_code', 'asc')->get();
        $waktuCetak = \Carbon\Carbon::now('Asia/Makassar')->format('d F Y - H:i:s');

        $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Laporan Data Master Shift</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #004a99; padding-bottom: 10px; }
                .header h2 { margin: 0; color: #004a99; font-size: 18px; text-transform: uppercase; }
                .header p { margin: 5px 0 0 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #cbd5e1; padding: 8px 5px; text-align: center; }
                th { background-color: #fff2b8; color: #1e293b; font-weight: bold; text-transform: uppercase; font-size: 10px; }
                tbody tr:nth-child(even) { background-color: #f8fafc; }
                tbody tr:hover { background-color: #e2efff; }
                .text-left { text-align: left; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Laporan Data Master Shift</h2>
                <p>Dicetak pada: ' . $waktuCetak . ' WITA</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 12%;">Kode Jam Kerja</th>
                        <th class="text-left">Keterangan</th>
                        <th style="width: 10%;">Awal Jam Kerja</th>
                        <th style="width: 10%;">Akhir Jam Kerja</th>
                        <th style="width: 10%;">Kode Istirahat</th>
                        <th style="width: 10%;">Mulai Istirahat</th>
                        <th style="width: 10%;">Akhir Istirahat</th>
                        <th style="width: 10%;">Total Istirahat</th>
                        <th style="width: 10%;">Total Jam Kerja</th>
                    </tr>
                </thead>
                <tbody>';

        if ($shifts->isEmpty()) {
            $html .= '<tr><td colspan="10">Belum ada data Master Shift yang terdaftar.</td></tr>';
        } else {
            foreach ($shifts as $index => $shift) {
                $start = \Carbon\Carbon::parse($shift->time_start)->format('H:i');
                $end = \Carbon\Carbon::parse($shift->time_end)->format('H:i');
                $kodeIstirahat = $shift->group_ws_type ?? '-';
                $mulaiIstirahat = $shift->break1_start ? \Carbon\Carbon::parse($shift->break1_start)->format('H:i') : '-';
                $akhirIstirahat = $shift->break1_end ? \Carbon\Carbon::parse($shift->break1_end)->format('H:i') : '-';
                $totalIstirahat = (($shift->break1_duration ?? 0) + ($shift->break2_duration ?? 0));
                $endTime = \Carbon\Carbon::parse($shift->time_end);
                $startTime = \Carbon\Carbon::parse($shift->time_start);
                if ($endTime->lt($startTime)) {
                    $endTime->addDay();
                }
                $totalMinutes = $startTime->diffInMinutes($endTime) - $totalIstirahat;
                $totalJam = intdiv($totalMinutes, 60);
                $totalMenit = $totalMinutes % 60;
                $totalJamKerja = $totalJam . '.' . sprintf('%02d', $totalMenit);

                $html .= '<tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . $shift->wst_code . '</td>
                    <td class="text-left">' . $shift->wst_text . '</td>
                    <td>' . $start . '</td>
                    <td>' . $end . '</td>
                    <td>' . $kodeIstirahat . '</td>
                    <td>' . $mulaiIstirahat . '</td>
                    <td>' . $akhirIstirahat . '</td>
                    <td>' . $totalIstirahat . '</td>
                    <td>' . $totalJamKerja . '</td>
                </tr>';
            }
        }

        $html .= '</tbody></table></body></html>';

        $pdf = Pdf::loadHTML($html);
        return $pdf->download('Data_Master_Shift.pdf');
    }

    public function import(Request $request)
    {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv']);

        try {
            Excel::import(new ShiftImport, $request->file('file_excel'));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Master Shift berhasil diimport!'
                ]);
            }

            return redirect()->to(route('dashboard').'#master-shift')->with('success', 'Master Shift berhasil diimport!');
        } catch (\Exception $e) {

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import gagal: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->to(route('dashboard').'#master-shift')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        return Excel::download(new ShiftExport, 'Master_Shift_' . date('Ymd_His') . '.xlsx');
    }

    public function bulkDestroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|distinct|exists:shifts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi data gagal.', 'errors' => $validator->errors()], 422);
        }

        $ids = $request->input('ids');

        try {
            Shift::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'deleted' => $ids]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus beberapa data.', 'error' => $e->getMessage()], 500);
        }
    }

    private function calculateDuration($start, $end)
    {
        if (!$start || !$end) return 0;

        $startTime = \Carbon\Carbon::createFromFormat('H:i', $start);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $end);

        if ($endTime->lt($startTime)) $endTime->addDay();

        return $startTime->diffInMinutes($endTime);
    }
}
