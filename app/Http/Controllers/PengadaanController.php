<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pengadaan;
use App\Models\Stationery;
use App\Models\StationeryTransaction;
use Carbon\Carbon;

class PengadaanController extends Controller
{

    public function index()
    {

        $pengadaans = Pengadaan::with('stationery')->orderBy('created_at', 'desc')->get();

        $barangs = Stationery::all();

        return view('Dashboard.pengadaan', compact('pengadaans', 'barangs'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'stationery_id'   => 'required',
            'no_pr'           => 'required|string|max:255',
            'tgl_pr'          => 'required|date',
            'estimasi_tiba'   => 'required|date',
            'qty_item'        => 'required|integer|min:1',
            'tgl_released_pr' => 'nullable|date',
            'qty_po'          => 'nullable|integer',
            'no_po'           => 'nullable|string|max:255',
            'vendor'          => 'nullable|string|max:255',
            'no_dokumen'      => 'nullable|string|max:255',
        ]);

        $vendorInfo = $request->vendor ? " | Vendor: " . $request->vendor : "";
        $dokumenInfo = $request->no_dokumen ? " | Dok: " . $request->no_dokumen : "";
        $poGabungan = ($request->no_po ?? '-') . $vendorInfo . $dokumenInfo;

        Pengadaan::create([
            'stationery_id'   => $request->stationery_id,
            'no_pr'           => $request->no_pr,
            'tgl_pr'          => $request->tgl_pr,
            'estimasi_tiba'   => $request->estimasi_tiba,
            'tgl_released_pr' => $request->tgl_released_pr,
            'no_po'           => $poGabungan,
            'qty_item'        => $request->qty_item,
            'qty_po'          => $request->qty_po,
            'status'          => 'Menunggu',
        ]);

        return redirect('/dashboard#pengadaan-stationery')->with('success', 'Data Pengadaan (PR/PO) berhasil ditambahkan dan sedang menunggu kedatangan!');
    }

    public function updateStatus($id)
    {

        $pengadaan = Pengadaan::findOrFail($id);

        if ($pengadaan->status === 'Selesai') {
            return redirect()->back()->with('warning', 'Data pengadaan ini sudah diselesaikan sebelumnya!');
        }

        DB::beginTransaction();
        try {

            $pengadaan->status = 'Selesai';
            $pengadaan->save();

            $stationery = Stationery::findOrFail($pengadaan->stationery_id);

            $tglPr = Carbon::parse($pengadaan->tgl_pr)->startOfDay();
            $tglTibaAktual = Carbon::now()->startOfDay();

            $leadTimeAktual = (int) round($tglPr->diffInDays($tglTibaAktual));

            $leadTimeAktual = $leadTimeAktual > 0 ? $leadTimeAktual : 1;

            $stationery->lead_time = $leadTimeAktual;

            $avg = $stationery->avg_penggunaan ?? 0;
            $safety = $stationery->safety_stock ?? 0;
            $hitungMinStok = ceil(($avg * $leadTimeAktual) + $safety);
            $stationery->min_stok = $hitungMinStok > 0 ? $hitungMinStok : 5;

            $stationery->stok_masuk += $pengadaan->qty_item;
            $stationery->save();

            $stationeryTransaction = StationeryTransaction::create([
                'stationery_id' => $pengadaan->stationery_id,
                'type'          => 'masuk',
                'tanggal'       => now(),
                'jumlah'        => $pengadaan->qty_item,
                'vendor'        => 'Auto PR: ' . $pengadaan->no_pr,
                'no_dokumen'    => $pengadaan->no_po ?? '-',
                'keterangan'    => 'Barang datang. Lead time tercatat: ' . $leadTimeAktual . ' Hari'
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Status Pengadaan Selesai! Stok bertambah dan Lead Time otomatis diupdate menjadi {$leadTimeAktual} hari.");

        } catch (\Exception $e) {

            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'stationery_id' => 'required',
            'no_pr'         => 'required|string|max:255',
            'tgl_pr'        => 'required|date',
            'estimasi_tiba' => 'required|date',
            'qty_item'      => 'required|integer|min:1',
            'no_po'         => 'nullable|string|max:255',
            'vendor'        => 'nullable|string|max:255',
            'no_dokumen'    => 'nullable|string|max:255',
        ]);

        $vendorInfo = $request->vendor ? " | Vendor: " . $request->vendor : "";
        $dokumenInfo = $request->no_dokumen ? " | Dok: " . $request->no_dokumen : "";
        $poGabungan = ($request->no_po ?? '-') . $vendorInfo . $dokumenInfo;

        $pengadaan = Pengadaan::findOrFail($id);
        $pengadaan->update([
            'stationery_id' => $request->stationery_id,
            'no_pr'         => $request->no_pr,
            'tgl_pr'        => $request->tgl_pr,
            'estimasi_tiba' => $request->estimasi_tiba,
            'no_po'         => $poGabungan,
            'qty_item'      => $request->qty_item,
        ]);

        return redirect()->back()->with('success', 'Data Pengadaan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengadaan = Pengadaan::findOrFail($id);
        $pengadaan->delete();

        return redirect()->back()->with('success', 'Data Pengadaan berhasil dihapus!');
    }
}
