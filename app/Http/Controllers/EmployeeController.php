<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

class EmployeeImport implements ToModel, WithHeadingRow, SkipsOnError
{
    public function model(array $row)
    {

        $cleanRow = [];
        foreach ($row as $key => $value) {
            $cleanRow[trim((string)$key)] = $value;
        }

        $formatDate = function($value) {
            $val = trim((string) $value);
            if ($val === '' || $val === '-' || $val === '0000-00-00') return null;
            try {
                if (is_numeric($val)) return Date::excelToDateTimeObject($val)->format('Y-m-d');
                $time = strtotime($val);
                return $time ? date('Y-m-d', $time) : null;
            } catch (\Exception $e) { return null; }
        };

        $formatInt = function($value) {
            $val = trim((string) $value);
            if ($val === '' || $val === '-') return null;
            if (preg_match('/[0-9]+/', $val, $matches)) return (int) $matches[0];
            return null;
        };

        $nik = $cleanRow['NIK'] ?? null;
        if (!$nik) return null;

        $tipeHk = trim((string)($cleanRow['Tipe Hari Kerja'] ?? ''));
        $rawJam = trim((string)($cleanRow['Jam Kerja'] ?? ''));
        if (str_starts_with($rawJam, '=') || empty($rawJam) || str_contains(strtoupper($rawJam), 'VLOOKUP')) {
            $jamKerja = str_contains($tipeHk, '5') ? '8 Jam' : '7 Jam';
        } elseif (is_numeric($rawJam)) {
            $jamKerja = $rawJam . ' Jam';
        } else {
            $jamKerja = $rawJam;
        }

        return new Employee([
            'nik'               => $nik,
            'nama'              => $cleanRow['Employee Name'] ?? null,
            'user'              => $cleanRow['User'] ?? $cleanRow['user'] ?? null,
            'jumlah'            => $cleanRow['Jumlah'] ?? 1, 
            'jabatan'           => $cleanRow['Position text'] ?? null,
            'employee_group'    => $cleanRow['Employee Group'] ?? null,
            'pers_subarea_text' => $cleanRow['Pers subarea text'] ?? null,
            'sub_subarea'       => $cleanRow['SubSubarea'] ?? null,
            'tmk'               => $formatDate($cleanRow['TMK'] ?? null),
            'length_of_service' => $cleanRow['Length of Service'] ?? null,
            'gender'            => $cleanRow['Gender'] ?? null,
            'marital_status'    => $cleanRow['Marital Status'] ?? null,
            'birthplace'        => $cleanRow['Birthplace'] ?? null,
            'birthdate'         => $formatDate($cleanRow['Birthdate'] ?? null),
            'age'               => $formatInt($cleanRow['Age'] ?? null),
            'range_umur'        => $cleanRow['Range Umur'] ?? null,
            'religion'          => $cleanRow['Religion'] ?? null,
            'jamsostek_id'      => $cleanRow['Jamsostek ID'] ?? null,
            'bpjs_id'           => $cleanRow['BPJS ID'] ?? null,
            'tax_id'            => $cleanRow['Personal Tax ID'] ?? null,
            'tax_status'        => $cleanRow['Tax Status'] ?? null,
            'number_of_child'   => $formatInt($cleanRow['Number Of Child'] ?? null),
            'ktp_nomor'         => $cleanRow['KTP - Nomor KTP'] ?? null,
            'ktp_alamat'        => $cleanRow['KTP - Alamat KTP'] ?? null,
            'region'            => $cleanRow['Region'] ?? null,
            'postal_code'       => $cleanRow['KTP - Postal Code'] ?? null,
            'no_hp'             => $cleanRow['No. HP'] ?? null,
            'email'             => $cleanRow['Email-0030'] ?? null,
            'esgrp_text'        => $cleanRow['ESGrp text'] ?? null,
            'level'             => $cleanRow['Level'] ?? null,
            'position_id'       => $cleanRow['Position ID'] ?? null,
            'org_unit_id'       => $cleanRow['Org unit ID'] ?? null,
            'org_unit_text'     => $cleanRow['Org unit text'] ?? null,
            'pers_subarea_id'   => $cleanRow['Pers subarea ID'] ?? null,
            'org_key_id'        => $cleanRow['Org key ID'] ?? null,
            'org_key_text'      => $cleanRow['Org key text'] ?? null,
            'bantu_opr_hlp'     => $cleanRow['Bantu OPR/HLP'] ?? null,
            'regu_line'         => $cleanRow['Regu / Line'] ?? null,
            'line'              => $cleanRow['Line'] ?? null,
            'regu'              => $cleanRow['Regu'] ?? null,
            'area_prov_marketing' => $cleanRow['Area Prov Marketing'] ?? null,
            'distrik_rayon_marketing' => $cleanRow['Distrik / Rayon Marketing'] ?? null,
            'vendor_os'         => $cleanRow['Vendor OS'] ?? null,
            'cost_center'       => $cleanRow['Cost Center'] ?? null,
            'cost_center_text'  => $cleanRow['Cost Center Text'] ?? null,
            'emp_status_text'   => $cleanRow['Emp status text'] ?? 'Active',
            'excuse'            => $cleanRow['Excuse'] ?? null,
            'tanggal_akhir_kerja' => $formatDate($cleanRow['Tanggal Akhir Kerja'] ?? null),
            'pendidikan_level'  => $cleanRow['Last Education - Level'] ?? null,
            'pendidikan_institusi' => $cleanRow['Last Education - Institution'] ?? null,
            'pendidikan_studi'  => $cleanRow['Last Education - Branch of Study'] ?? null,
            'ps_type_text'      => $cleanRow['PS Type-0008 Text'] ?? null,
            'status_incentive'  => $cleanRow['Status Incentive'] ?? null,
            'bank_keys'         => $cleanRow['Bank Keys'] ?? null,
            'bank_account_no'   => $cleanRow['Bank Account No'] ?? null,
            'vaksin1_tanggal'   => $formatDate($cleanRow['Vaksin1 - Tanggal'] ?? null),
            'vaksin1_merk'      => $cleanRow['Vaksin1 - Merk'] ?? null,
            'vaksin2_tanggal'   => $formatDate($cleanRow['Vaksin2 - Tanggal'] ?? null),
            'vaksin2_merk'      => $cleanRow['Vaksin2 - Merk'] ?? null,
            'vaksin3_tanggal'   => $formatDate($cleanRow['Vaksin3 - Tanggal'] ?? null),
            'vaksin3_merk'      => $cleanRow['Vaksin3 - Merk'] ?? null,
            'workschedule_id'   => $cleanRow['Workschedule ID'] ?? null,
            'tipe_hari_kerja'   => $tipeHk ?: ($cleanRow['Tipe Hari Kerja'] ?? null),
            'shift_type'        => $cleanRow['Shift/Non Shift'] ?? null,
            'no_sidik_jari'     => $cleanRow['No Sidik Jari'] ?? null,
            'jam_kerja'         => $jamKerja,
        ]);
    }

    public function onError(Throwable $e) { /* Abaikan error baris tunggal agar tidak crash */ }
}
