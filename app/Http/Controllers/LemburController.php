<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembur;
use App\Models\Employee;

class LemburController extends Controller
{
    /**
     * Menyimpan Data Lembur
     */
    public function store(Request $request)
    {

        $request->validate([
            'nik'         => 'required',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $employee = Employee::where('nik', $request->nik)->first();

        if (!$employee) {
            return back()->with('error', 'Gagal! Karyawan dengan NIK '.$request->nik.' tidak ditemukan di Master Data.')->withFragment('input-lembur');
        }

        Lembur::create([
            'employee_id' => $employee->id,
            'tanggal'     => $request->tanggal,
            'is_libur'    => $request->is_libur == 'Libur' ? 1 : 0,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'istirahat'   => $request->istirahat ?? 0,
            'jumlah_jam'  => $request->jumlah_jam ?? 0,
            'jam_1_5'     => $request->jam_1_5 ?? 0,
            'jam_2'       => $request->jam_2 ?? 0,
            'jam_3'       => $request->jam_3 ?? 0,
            'jam_4'       => $request->jam_4 ?? 0,
            'total_akumulasi_lembur' => $request->total_akumulasi_lembur ?? 0,
            'keterangan'  => $request->keterangan,
        ]);

        return back()->with('success', 'Data Lembur berhasil ditambahkan!')->withFragment('input-lembur');
    }

    /**
     * Menghapus Data Lembur
     */
    public function destroy($id)
    {
        Lembur::findOrFail($id)->delete();
        return back()->with('success', 'Data Lembur berhasil dihapus!')->withFragment('input-lembur');
    }
}
