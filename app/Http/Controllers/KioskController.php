<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class KioskController extends Controller
{
    public function getKaryawanByNik($nik)
    {

        $karyawan = Employee::where('nik', $nik)->first();

        if ($karyawan) {
            return response()->json([
                'nama' => $karyawan->nama,
                'posisi' => $karyawan->jabatan
            ]);
        }

        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    public function getAllKaryawan()
    {
        $karyawan = Employee::select('nik', 'nama', 'jabatan', 'vendor_os')
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json($karyawan);
    }
}
