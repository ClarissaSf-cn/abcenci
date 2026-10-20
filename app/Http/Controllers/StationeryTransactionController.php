<?php

namespace App\Http\Controllers;

use App\Models\StationeryTransaction;
use App\Models\Stationery;
use App\Models\ClosingPeriode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StationeryTransactionController extends Controller
{
    /**
     * FUNGSI BANTUAN INTERNAL:
     * Mengecek apakah tanggal tertentu masuk dalam periode yang sudah di Closing (Tutup Buku).
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

    /**
     * Menyimpan transaksi barang masuk atau keluar
     * SEKALIGUS OTOMATIS MENYELARASKAN STOK DI MASTER DATA
     */
    public function store(Request $request)
    {

        $request->validate([
            'stationery_id' => 'required|exists:stationeries,id',
            'type'          => 'required|in:masuk,keluar',
            'jumlah'        => 'required|integer|min:1',
            'tanggal'       => 'required|date',
            'dept'          => 'nullable|string',
            'vendor'        => 'nullable|string',
            'no_fpb'        => 'nullable|string',
            'no_dokumen'    => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        if ($this->isPeriodeClosed($request->tanggal)) {
            return redirect('/dashboard#transaksi-stationery')
                ->with('error', 'GAGAL! Bulan transaksi ini sudah di Tutup Buku (Closing). Anda tidak dapat menambah data baru.');
        }

        $stationery = Stationery::findOrFail($request->stationery_id);

        if ($request->type === 'masuk') {

            $stationery->stok_masuk += $request->jumlah;
            $pesan = 'Berhasil! Riwayat dicatat & Master Stok BERTAMBAH otomatis.';
        } else {

            if ($stationery->total_stok < $request->jumlah) {
                return redirect('/dashboard#transaksi-stationery')
                        ->with('error', 'GAGAL! Sisa stok ' . $stationery->nama_barang . ' hanya ' . $stationery->total_stok . ', tidak cukup untuk dikeluarkan.');
            }

            $stationery->stok_keluar += $request->jumlah;
            $pesan = 'Berhasil! Riwayat dicatat & Master Stok BERKURANG otomatis.';
        }

        $stationery->save();

        StationeryTransaction::create([
            'stationery_id' => $request->stationery_id,
            'type'          => $request->type,
            'jumlah'        => $request->jumlah,
            'tanggal'       => $request->tanggal,
            'no_fpb'        => $request->no_fpb,
            'dept'          => $request->dept,
            'keterangan'    => $request->keterangan,
            'no_dokumen'    => $request->no_dokumen,
            'vendor'        => $request->vendor,
        ]);

        return redirect('/dashboard#transaksi-stationery')->with('success', $pesan);
    }

    /**
     * Memperbarui transaksi barang masuk/keluar
     * DAN MENYELARASKAN KEMBALI STOK DI MASTER DATA
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'stationery_id' => 'required|exists:stationeries,id',
            'tanggal'       => 'required|date',
            'jumlah'        => 'required|numeric|min:1',
        ]);

        $transaction = StationeryTransaction::findOrFail($id);

        if ($this->isPeriodeClosed($transaction->tanggal)) {
            return redirect('/dashboard#transaksi-stationery')
                ->with('error', 'GAGAL! Transaksi asli berada pada bulan yang sudah di Tutup Buku. Data tidak dapat diedit.');
        }
        if ($this->isPeriodeClosed($request->tanggal)) {
            return redirect('/dashboard#transaksi-stationery')
                ->with('error', 'GAGAL! Anda mencoba memindahkan transaksi ke bulan yang sudah di Tutup Buku.');
        }

        $oldStationeryId = $transaction->stationery_id;
        $oldJumlah       = $transaction->jumlah;
        $oldType         = strtolower($transaction->type);

        $newStationeryId = $request->stationery_id;
        $newJumlah       = $request->jumlah;

        if ($oldStationeryId == $newStationeryId) {

            $stationery = Stationery::findOrFail($oldStationeryId);

            if ($oldType === 'keluar') {

                $stationery->stok_keluar = $stationery->stok_keluar - $oldJumlah + $newJumlah;
            } elseif ($oldType === 'masuk') {
                $stationery->stok_masuk = $stationery->stok_masuk - $oldJumlah + $newJumlah;
            }
            $stationery->save();

        } else {

            $oldStationery = Stationery::findOrFail($oldStationeryId);
            $newStationery = Stationery::findOrFail($newStationeryId);

            if ($oldType === 'keluar') {

                $oldStationery->stok_keluar -= $oldJumlah;

                $newStationery->stok_keluar += $newJumlah;
            } elseif ($oldType === 'masuk') {
                $oldStationery->stok_masuk -= $oldJumlah;
                $newStationery->stok_masuk += $newJumlah;
            }

            $oldStationery->save();
            $newStationery->save();
        }

        $transaction->stationery_id = $newStationeryId;
        $transaction->tanggal       = $request->tanggal;
        $transaction->jumlah        = $newJumlah;
        $transaction->no_fpb        = $request->no_fpb ?? $transaction->no_fpb;
        $transaction->dept          = $request->dept ?? $transaction->dept;
        $transaction->keterangan    = $request->keterangan ?? $transaction->keterangan;
        $transaction->save();

        return redirect('/dashboard#transaksi-stationery')->with('success', 'Transaksi berhasil diedit dan Master Stok telah diselaraskan.');
    }

    /**
     * Menghapus transaksi dan mengembalikan stok barang
     */
    public function destroy($id)
    {
        $transaction = StationeryTransaction::findOrFail($id);

        if ($this->isPeriodeClosed($transaction->tanggal)) {
            return redirect('/dashboard#transaksi-stationery')
                ->with('error', 'GAGAL! Bulan transaksi ini sudah di Tutup Buku. Anda tidak dapat menghapus data ini.');
        }

        $stationery  = Stationery::findOrFail($transaction->stationery_id);
        $type        = strtolower($transaction->type);

        if ($type === 'keluar') {
            $stationery->stok_keluar -= $transaction->jumlah;
        } elseif ($type === 'masuk') {
            $stationery->stok_masuk -= $transaction->jumlah;
        }
        $stationery->save();

        $transaction->delete();

        return redirect('/dashboard#transaksi-stationery')->with('success', 'Riwayat transaksi dihapus dan stok barang telah dikembalikan.');
    }
}
