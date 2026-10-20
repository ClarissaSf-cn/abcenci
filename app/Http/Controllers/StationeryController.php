<?php

namespace App\Http\Controllers;

use App\Models\Stationery;
use App\Models\Pengadaan;
use App\Models\StationeryTransaction;
use App\Models\ClosingPeriode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class StationeryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = Auth::user();
                $role = strtolower($user->role ?? '');
                $name = strtoupper($user->name);

                if (!in_array($role, ['superadmin', 'admin_stationery']) && $name !== 'ADMIN SUPER') {
                    return abort(403, 'Akses Terbatas: Hanya Admin Stationery atau Super Admin yang diizinkan.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * FUNGSI BANTUAN INTERNAL:
     * Mengecek apakah tanggal tertentu masuk dalam periode yang sudah di Closing.
     */
    private function isPeriodeClosed($tanggal)
    {
        $parsedDate = Carbon::parse($tanggal);
        $bulan = $parsedDate->format('m');
        $tahun = $parsedDate->format('Y');

        return ClosingPeriode::where('bulan', $bulan)
                             ->where('tahun', $tahun)
                             ->where('is_closed', true)
                             ->exists();
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'           => 'required|unique:stationeries,kode',
            'nama_barang'    => 'required|string|max:255',
            'kategori'       => 'required|string',
            'stok_awal'      => 'required|integer|min:0',
            'harga_unit'     => 'required|numeric|min:0',
            'satuan'         => 'required|string',
            'avg_penggunaan' => 'nullable|numeric|min:0',
            'lead_time'      => 'nullable|integer|min:0',
            'safety_stock'   => 'nullable|integer|min:0',
            'estimasi_tiba'  => 'nullable|date',
            'no_pr'          => 'nullable|string',
            'no_po'          => 'nullable|string',
            'vendor'         => 'nullable|string',
            'no_dokumen'     => 'nullable|string',
        ]);

        $avg = $request->avg_penggunaan ?? 0;
        $lead = $request->lead_time ?? 0;
        $safety = $request->safety_stock ?? 0;

        $hitungMinStok = ceil(($avg * $lead) + $safety);
        $finalMinStok = $hitungMinStok > 0 ? $hitungMinStok : 5;

        $minStokToSave = $request->filled('min_stok') ? $request->min_stok : $finalMinStok;

        $stationery = Stationery::create([
            'kode'           => strtoupper($request->kode),
            'nama_barang'    => $request->nama_barang,
            'kategori'       => $request->kategori,
            'stok_awal'      => 0,
            'stok_masuk'     => 0,
            'stok_keluar'    => 0,
            'harga_unit'     => $request->harga_unit,
            'satuan'         => $request->satuan,
            'avg_penggunaan' => $avg,
            'lead_time'      => $lead,
            'safety_stock'   => $safety,
            'min_stok'       => $minStokToSave,
            'is_active'      => $request->has('is_active') ? $request->is_active : 1,
        ]);

        $vendorInfo = $request->vendor ? " | Vendor: " . $request->vendor : "";
        $dokumenInfo = $request->no_dokumen ? " | Dok: " . $request->no_dokumen : "";
        $poGabungan = ($request->no_po ?? '-') . $vendorInfo . $dokumenInfo;

        Pengadaan::create([
            'stationery_id' => $stationery->id,
            'no_pr'         => $request->no_pr ?? 'PR-NEW-' . date('YmdHis'),
            'tgl_pr'        => date('Y-m-d'),
            'estimasi_tiba' => $request->estimasi_tiba ?? date('Y-m-d'),
            'no_po'         => $poGabungan,
            'qty_item'      => $request->stok_awal > 0 ? $request->stok_awal : 1,
            'status'        => 'Menunggu',
        ]);

        return redirect('/dashboard#pengadaan-stationery')->with('success', 'Master Barang berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $item = Stationery::findOrFail($id);

        $request->validate([
            'kode'           => 'required|unique:stationeries,kode,' . $id,
            'nama_barang'    => 'required|string|max:255',
            'kategori'       => 'required|string',
            'stok_awal'      => 'required|integer|min:0',
            'harga_unit'     => 'required|numeric|min:0',
            'satuan'         => 'required|string',
            'avg_penggunaan' => 'nullable|numeric|min:0',
            'lead_time'      => 'nullable|integer|min:0',
            'safety_stock'   => 'nullable|integer|min:0',
        ]);

        $avg = $request->avg_penggunaan ?? $item->avg_penggunaan ?? 0;
        $lead = $request->lead_time ?? $item->lead_time ?? 0;
        $safety = $request->safety_stock ?? $item->safety_stock ?? 0;

        $hitungMinStok = ceil(($avg * $lead) + $safety);
        $finalMinStok = $hitungMinStok > 0 ? $hitungMinStok : 5;

        $minStokToSave = $request->filled('min_stok') ? $request->min_stok : $finalMinStok;

        $item->update([
            'kode'           => strtoupper($request->kode),
            'nama_barang'    => $request->nama_barang,
            'kategori'       => $request->kategori,
            'stok_awal'      => $request->stok_awal,
            'harga_unit'     => $request->harga_unit,
            'satuan'         => $request->satuan,
            'avg_penggunaan' => $avg,
            'lead_time'      => $lead,
            'safety_stock'   => $safety,
            'min_stok'       => $minStokToSave,
            'is_active'      => $request->has('is_active') ? $request->is_active : $item->is_active,
        ]);

        return redirect('/dashboard#stationery')->with('success', 'Data Master Barang berhasil diperbarui!');
    }

    public function destroy($id)
    {

        Stationery::findOrFail($id)->delete();
        return redirect('/dashboard#stationery')->with('success', 'Barang telah dihapus secara permanen.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:stationeries,id'
        ]);

        $ids = $request->input('ids');

        try {
            Stationery::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' data barang berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function truncate()
    {
        Schema::disableForeignKeyConstraints();
        StationeryTransaction::truncate();
        Pengadaan::truncate();
        Stationery::truncate();
        Schema::enableForeignKeyConstraints();

        return redirect('/dashboard#stationery')->with('success', 'Semua data Master Barang dan Riwayat Transaksi berhasil dihapus total!');
    }

}
