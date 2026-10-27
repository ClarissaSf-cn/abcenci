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
    // Menampilkan halaman tabel Data Pengadaan
    public function index()
    {
        // Mengambil semua data pengadaan dari database, diurutkan dari yang terbaru
        $pengadaans = Pengadaan::with('stationery')->orderBy('created_at', 'desc')->get();

        // Mengambil data master barang untuk pilihan di dropdown form tambah data
        $barangs = Stationery::all();

        // Mengarahkan ke file resources/views/Dashboard/pengadaan.blade.php
        return view('Dashboard.pengadaan', compact('pengadaans', 'barangs'));
    }

    // Menyimpan data PR/PO baru ke database
    public function store(Request $request)
    {
        // PERBAIKAN: Menambahkan validasi 'nullable' untuk field yang mungkin tidak wajib diisi
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

        // MENGGABUNGKAN VENDOR & DOKUMEN AGAR BISA DISIMPAN BERSAMAAN
        $vendorInfo = $request->vendor ? " | Vendor: " . $request->vendor : "";
        $dokumenInfo = $request->no_dokumen ? " | Dok: " . $request->no_dokumen : "";
        $poGabungan = ($request->no_po ?? '-') . $vendorInfo . $dokumenInfo;

        // Simpan ke database
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

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect('/dashboard#pengadaan-stationery')->with('success', 'Data Pengadaan (PR/PO) berhasil ditambahkan dan sedang menunggu kedatangan!');
    }

    // ========================================================
    // FUNGSI: Mengubah status pengadaan menjadi Selesai & Auto-Update Stok + Lead Time
    // ========================================================
    public function updateStatus($id)
    {
        // Cari data pengadaan berdasarkan ID
        $pengadaan = Pengadaan::findOrFail($id);

        
        if ($pengadaan->status === 'Selesai') {
            return redirect()->back()->with('warning', 'Data pengadaan ini sudah diselesaikan sebelumnya!');
        }

        // Gunakan DB Transaction agar jika salah satu gagal, semuanya dibatalkan (Data Aman)
        DB::beginTransaction();
        try {
            // 1. Ubah statusnya menjadi Selesai
            $pengadaan->status = 'Selesai';
            $pengadaan->save();

            // 2. Cari Master Barang (Stationery)
            $stationery = Stationery::findOrFail($pengadaan->stationery_id);

            // ========================================================
            // 3. LOGIKA BARU: HITUNG LEAD TIME AKTUAL SAAT DIVALIDASI
            // ========================================================
            // PERBAIKAN: Menggunakan startOfDay() agar perhitungan bersih dari jam/menit/detik
            $tglPr = Carbon::parse($pengadaan->tgl_pr)->startOfDay();
            $tglTibaAktual = Carbon::now()->startOfDay();

            // Hitung selisih hari dari Tgl PR sampai Hari Ini dan kunci jadi integer
            $leadTimeAktual = (int) round($tglPr->diffInDays($tglTibaAktual));

            // Minimal set ke 1 hari jika divalidasi di hari yang sama agar rumus perhitungan stok tidak error
            $leadTimeAktual = $leadTimeAktual > 0 ? $leadTimeAktual : 1;

            // Update Lead Time di Master Barang
            $stationery->lead_time = $leadTimeAktual;

            // Auto-update batas minimum stok (Safety Stock) menyesuaikan Lead Time baru
            $avg = $stationery->avg_penggunaan ?? 0;
            $safety = $stationery->safety_stock ?? 0;
            $hitungMinStok = ceil(($avg * $leadTimeAktual) + $safety);
            $stationery->min_stok = $hitungMinStok > 0 ? $hitungMinStok : 5;

            // 4. Tambah stok masuk ke Master Barang
            $stationery->stok_masuk += $pengadaan->qty_item;
            $stationery->save();

            // 5. Catat otomatis ke Riwayat Transaksi (In/Out)
            $stationeryTransaction = StationeryTransaction::create([
                'stationery_id' => $pengadaan->stationery_id,
                'type'          => 'masuk',
                'tanggal'       => now(),
                'jumlah'        => $pengadaan->qty_item,
                'vendor'        => 'Auto PR: ' . $pengadaan->no_pr,
                'no_dokumen'    => $pengadaan->no_po ?? '-',
                'keterangan'    => 'Barang datang. Lead time tercatat: ' . $leadTimeAktual . ' Hari'
            ]);

            // Simpan semua perubahan secara permanen
            DB::commit();

            return redirect()->back()->with('success', "Status Pengadaan Selesai! Stok bertambah dan Lead Time otomatis diupdate menjadi {$leadTimeAktual} hari.");

        } catch (\Exception $e) {
            // Batalkan semua proses jika terjadi error
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // ========================================================
    // FUNGSI: Mengupdate data pengadaan (Edit)
    // ========================================================
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
