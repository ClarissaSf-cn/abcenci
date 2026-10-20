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

        $nik = $row['nik'] ?? null;
        if (!$nik) return null;

        $formatDate = function($value) {
            $val = trim((string) $value);
            if ($val === '' || $val === '-' || $val === '0000-00-00') return null;
            try {

                if (is_numeric($val)) return Date::excelToDateTimeObject($val)->format('Y-m-d');

                $time = strtotime($val);
                return $time ? date('Y-m-d', $time) : null;
            } catch (Throwable $e) {

                return null;
            }
        };

        $formatInt = function($value) {
            $val = trim((string) $value);
            if ($val === '' || $val === '-') return 0;
            if (preg_match('/[0-9]+/', $val, $matches)) return (int) $matches[0];
            return 0;
        };

        $tipeHk = trim((string)($row['tipe_hari_kerja'] ?? ''));
        $rawJam = trim((string)($row['jam_kerja'] ?? ''));
        if (str_starts_with($rawJam, '=') || empty($rawJam) || str_contains(strtoupper($rawJam), 'VLOOKUP')) {
            $jamKerja = str_contains($tipeHk, '5') ? '8 Jam' : '7 Jam';
        } elseif (is_numeric($rawJam)) {
            $jamKerja = $rawJam . ' Jam';
        } else {
            $jamKerja = $rawJam;
        }

        return Employee::updateOrCreate(
            ['nik' => (string)$nik],
            [
                'nama'                => $row['employee_name'] ?? $row['nama'] ?? '-',
                'user'                => $row['user'] ?? null,
                'jumlah'              => $row['jumlah'] ?? 1,
                'jabatan'             => $row['position_text'] ?? $row['jabatan'] ?? '-',
                'employee_group'      => $row['employee_group'] ?? null,
                'pers_subarea_text'   => $row['pers_subarea_text'] ?? null,
                'sub_subarea'         => $row['subsubarea'] ?? $row['sub_subarea'] ?? null,
                'tmk'                 => $formatDate($row['tmk'] ?? null),
                'length_of_service'   => $row['length_of_service'] ?? null,
                'gender'              => $row['gender'] ?? null,
                'marital_status'      => $row['marital_status'] ?? null,
                'birthplace'          => $row['birthplace'] ?? null,
                'birthdate'           => $formatDate($row['birthdate'] ?? null),
                'age'                 => $formatInt($row['age'] ?? 0),
                'range_umur'          => $row['range_umur'] ?? null,
                'religion'            => $row['religion'] ?? null,
                'jamsostek_id'        => $row['jamsostek_id'] ?? null,
                'bpjs_id'             => $row['bpjs_id'] ?? null,
                'tax_id'              => $row['personal_tax_id'] ?? $row['tax_id'] ?? null,
                'tax_status'          => $row['tax_status'] ?? null,
                'number_of_child'     => $formatInt($row['number_of_child'] ?? 0),
                'ktp_nomor'           => $row['ktp_nomor_ktp'] ?? $row['ktp_nomor'] ?? null,
                'ktp_alamat'          => $row['ktp_alamat_ktp'] ?? $row['ktp_alamat'] ?? null,
                'region'              => $row['region'] ?? null,
                'postal_code'         => $row['ktp_postal_code'] ?? $row['postal_code'] ?? null,
                'no_hp'               => $row['no_hp'] ?? null,
                'email'               => $row['email_0030'] ?? $row['email'] ?? null,
                'esgrp_text'          => $row['esgrp_text'] ?? null,
                'level'               => $row['level'] ?? null,
                'position_id'         => $row['position_id'] ?? null,
                'org_unit_id'         => $row['org_unit_id'] ?? null,
                'org_unit_text'       => $row['org_unit_text'] ?? null,
                'pers_subarea_id'     => $row['pers_subarea_id'] ?? null,
                'org_key_id'          => $row['org_key_id'] ?? null,
                'org_key_text'        => $row['org_key_text'] ?? null,
                'bantu_opr_hlp'       => $row['bantu_oprhlp_prod_direct'] ?? $row['bantu_oprhlpprod_direct'] ?? null,
                'regu_line'           => $row['regu_line'] ?? $row['reguline'] ?? null,
                'line'                => $row['line'] ?? null,
                'regu'                => $row['regu'] ?? null,
                'area_prov_marketing' => $row['area_prov_marketing'] ?? null,
                'distrik_rayon_marketing' => $row['distrik_rayon_marketing'] ?? $row['distrik_rayon'] ?? null,
                'vendor_os'           => $row['vendor_os'] ?? null,
                'cost_center'         => $row['cost_center'] ?? null,
                'cost_center_text'    => $row['cost_center_text'] ?? null,
                'emp_status_text'     => $row['emp_status_text'] ?? 'Active',
                'excuse'              => $row['excuse'] ?? null,
                'tanggal_akhir_kerja' => $formatDate($row['tanggal_akhir_kerja'] ?? null),
                'pendidikan_level'    => $row['last_education_level'] ?? null,
                'pendidikan_institusi'=> $row['last_education_institution'] ?? null,
                'pendidikan_studi'    => $row['last_education_branch_of_study'] ?? null,
                'ps_type_text'        => $row['ps_type_0008_text'] ?? $row['ps_type0008_text'] ?? null,
                'status_incentive'    => $row['status_incentive'] ?? null,
                'bank_keys'           => $row['bank_keys'] ?? null,
                'bank_account_no'     => $row['bank_account_no'] ?? null,
                'vaksin1_tanggal'     => $formatDate($row['vaksin1_tanggal'] ?? null),
                'vaksin1_merk'        => $row['vaksin1_merk'] ?? null,
                'vaksin2_tanggal'     => $formatDate($row['vaksin2_tanggal'] ?? null),
                'vaksin2_merk'        => $row['vaksin2_merk'] ?? null,
                'vaksin3_tanggal'     => $formatDate($row['vaksin3_tanggal'] ?? null),
                'vaksin3_merk'        => $row['vaksin3_merk'] ?? null,
                'workschedule_id'     => $row['workschedule_id'] ?? null,
                'tipe_hari_kerja'     => $tipeHk ?: ($row['tipe_hari_kerja'] ?? null),
                'shift_type'          => $row['shiftnon_shift'] ?? $row['shift_non_shift'] ?? null,
                'no_sidik_jari'       => $row['no_sidik_jari'] ?? null,
                'jam_kerja'           => $jamKerja,
            ]
        );
    }

    public function onError(Throwable $e)
    {

    }
}
