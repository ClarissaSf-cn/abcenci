<?php

namespace App\Imports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

        $waktuWita = Carbon::now('Asia/Makassar')->format('H:i:s');


        $rawValues = array_values($row);
        $isiTanggal = $row['tanggal'] ?? $rawValues[2] ?? null;

        $tanggalFinal = null;

        if (!empty($isiTanggal)) {
            try {
                if (is_numeric($isiTanggal)) {

                    $tanggalFinal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($isiTanggal)->format('Y-m-d');
                } else {

                    $tanggalFinal = Carbon::parse($isiTanggal)->format('Y-m-d');
                }
            } catch (\Exception $e) {

                $tanggalFinal = Carbon::now('Asia/Makassar')->format('Y-m-d');
            }
        } else {
            $tanggalFinal = Carbon::now('Asia/Makassar')->format('Y-m-d');
        }


        return new Attendance([
            'user_id'    => Auth::id(),
            'status'     => $row['status'] ?? $rawValues[3] ?? 'Hadir',
            'catatan'    => 'Assorting: ' . ($row['id_karyawan'] ?? $rawValues[0] ?? 'N/A') . ' - ' . ($row['nama_karyawan'] ?? $rawValues[1] ?? 'N/A'),
            'created_at' => $tanggalFinal . ' ' . $waktuWita,
            'updated_at' => Carbon::now('Asia/Makassar'),
        ]);
    }
}
