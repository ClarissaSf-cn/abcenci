<?php

namespace App\Http\Controllers;

use App\Models\Stationery;
use App\Models\StationeryTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanStationeryController extends Controller
{

    public function exportOrderBarang(Request $request)
    {
        $fileName = 'Laporan_Order_Barang_Masuk_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $query = Stationery::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }
        $data = $query->get();

        $callback = function() use($data, $request) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Barang', 'Stock Now', 'Rata-rata penggunaan / bulan & siapa penggunanya', 'Kapan terakhir order', 'Kebutuhan / Reorder']);

            foreach ($data as $item) {

                $terakhirOrder = StationeryTransaction::where('stationery_id', $item->id)
                    ->where('type', 'masuk')
                    ->max('tanggal');

                if ($request->filled('start_date') || $request->filled('end_date')) {
                    if (!$terakhirOrder) {
                        continue;
                    }
                    $tglOrderFormat = Carbon::parse($terakhirOrder)->format('Y-m-d');
                    if ($request->filled('start_date') && $tglOrderFormat < $request->start_date) continue;
                    if ($request->filled('end_date') && $tglOrderFormat > $request->end_date) continue;
                }

                $trxKeluar = StationeryTransaction::where('stationery_id', $item->id)
                    ->where('type', 'keluar')
                    ->get();

                $rataPenggunaan = $trxKeluar->where('tanggal', '>=', Carbon::now()->subDays(30))->sum('jumlah');

                $penggunaUnik = array_filter(array_unique($trxKeluar->pluck('dept')->toArray()));
                $penggunaStr = count($penggunaUnik) > 0 ? implode(', ', $penggunaUnik) : 'BELUM ADA DATA';

                $tglFormat = $terakhirOrder ? Carbon::parse($terakhirOrder)->format('d M Y') : 'Belum pernah order';

                $status = ($item->total_stok <= $item->min_stok) ? 'Perlu order' : 'Aman';

                fputcsv($file, [
                    $item->kode . ' - ' . $item->nama_barang,
                    $item->total_stok,
                    $rataPenggunaan . ' ' . $item->satuan . ' / bulan (Pemakai: ' . $penggunaStr . ')',
                    $tglFormat,
                    $status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPemakaianBarang(Request $request)
    {
        $fileName = 'Laporan_Pemakaian_Barang_Keluar_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $query = StationeryTransaction::with('stationery')->where('type', 'keluar');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($qParent) use ($search) {

                $qParent->whereHas('stationery', function($q) use ($search) {
                    $q->where('kode', 'like', "%{$search}%")
                      ->orWhere('nama_barang', 'like', "%{$search}%");
                })

                ->orWhere('dept', 'like', "%{$search}%")
                ->orWhere('no_fpb', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        $callback = function() use($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['No PR (FPB)', 'Tanggal PR', 'Kode Barang', 'Nama Material', 'Jumlah Keluar', 'Departemen/Pemakai']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->no_fpb ?? 'PR-' . $row->id,
                    Carbon::parse($row->tanggal)->format('d-m-Y'),
                    $row->stationery->kode ?? '-',
                    $row->stationery->nama_barang ?? '-',
                    $row->jumlah . ' ' . ($row->stationery->satuan ?? ''),
                    $row->dept ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
