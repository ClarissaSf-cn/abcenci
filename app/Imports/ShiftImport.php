<?php

namespace App\Imports;

use App\Models\Shift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use Throwable;

class ShiftImport implements ToCollection, WithHeadingRow, SkipsOnError, WithChunkReading
{

    /*
    public function headingRow(): int
    {
        return 9;
    }
    */

    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {

            $normalizedRow = [];
            foreach ($row as $key => $value) {
                $normalizedKey = $this->normalizeHeader($key);
                $normalizedRow[$normalizedKey] = $value;
            }

            $get = function(array $candidates) use ($normalizedRow) {
                foreach ($candidates as $candidate) {
                    $key = $this->normalizeHeader($candidate);
                    if (array_key_exists($key, $normalizedRow)) {
                        $value = $normalizedRow[$key];
                        if ($this->isNotBlank($value)) return $value;
                    }
                }
                return null;
            };

            $wstCode = trim((string) ($get(['wst_code', 'kode_jam_kerja', 'kode_jam_shift', 'kode_wst', 'kode_shift', 'kode', 'code', 'kode_ws', 'wst_code']) ?? ''));

            if (!$wstCode) {
                continue;
            }

            $parseTime = function ($value) {
                if ($value instanceof \DateTimeInterface) return $value->format('H:i');
                $val = trim((string) ($value ?? ''));
                $val = str_replace([',', ';'], [':', ':'], $val);
                if ($val === '' || $val === '-' || strtolower($val) === 'null') return null;

                try {
                    if (is_numeric($val)) {
                        if ((float) $val > 0 && (float) $val < 1) return Date::excelToDateTimeObject((float) $val)->format('H:i');
                        if (preg_match('/^\d{1,2}$/', $val) && (int) $val <= 23) return sprintf('%02d:00', (int) $val);
                        if (preg_match('/^(\d{1,2})(\d{2})$/', $val, $matches)) {
                            $hour = (int) $matches[1]; $minute = (int) $matches[2];
                            if ($hour <= 23 && $minute <= 59) return sprintf('%02d:%02d', $hour, $minute);
                        }
                        return Date::excelToDateTimeObject((float) $val)->format('H:i');
                    }
                    $cleaned = preg_replace('/[^0-9:]/', '', $val);
                    $timestamp = strtotime($cleaned);
                    return $timestamp ? date('H:i', $timestamp) : null;
                } catch (Throwable $e) {
                    return null;
                }
            };

            $validateTime = function($time) {
                if (!$time) return null;
                if (preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
                    return date('H:i', strtotime($time));
                }
                return null;
            };

            $timeStart = $validateTime($parseTime($get(['time_start', 'awal_jam_kerja', 'start', 'jam_mulai', 'awal_jam', 'mulai', 'jam_awal'])));
            $timeEnd = $validateTime($parseTime($get(['time_end', 'akhir_jam_kerja', 'end', 'jam_selesai', 'akhir_jam', 'selesai', 'jam_akhir'])));

            $timeStart = $timeStart ?: '00:00';
            $timeEnd = $timeEnd ?: '00:00';

            $break1Start = $validateTime($parseTime($get(['break1_start', 'mulai_istirahat', 'mulai_istirahat_1', 'start_break', 'break_start'])));
            $break1End = $validateTime($parseTime($get(['break1_end', 'akhir_istirahat', 'akhir_istirahat_1', 'end_break', 'break_end'])));
            $break2Start = $validateTime($parseTime($get(['break2_start', 'mulai_istirahat_2', 'mulai_istirahat_dua'])));
            $break2End = $validateTime($parseTime($get(['break2_end', 'akhir_istirahat_2', 'akhir_istirahat_dua'])));

            $groupWs = $get(['group_ws_type', 'group', 'tipe_group_ws', 'group_ws', 'kode_istirahat', 'kode_grup_ws', 'kode_grup', 'group_type']);
            if ($groupWs && strlen(trim($groupWs)) > 10) {
                $groupWs = null;
            }

            $rawWstText = $get(['wst_text', 'description', 'keterangan', 'keterangan_wst', 'nama_shift']);
            $wstText = $rawWstText;
            if ($rawWstText === null || $rawWstText === '' || $rawWstText === '-' || (is_numeric($rawWstText) && (float)$rawWstText >= 0 && (float)$rawWstText <= 1)) {
                if ($break1Start && $break1End) {
                    $prefix = substr($wstCode, 0, 2);
                    $wstText = $prefix . ' ' . str_replace(':', '.', $break1Start) . '-' . str_replace(':', '.', $break1End);
                } elseif ($timeStart && $timeEnd && $timeStart !== '00:00') {
                    $prefix = substr($wstCode, 0, 2);
                    $wstText = $prefix . ' ' . str_replace(':', '.', $timeStart) . '-' . str_replace(':', '.', $timeEnd);
                } else {
                    $wstText = $wstText ?? '-';
                }
            }

            Shift::updateOrCreate(
                ['wst_code' => $wstCode],
                [
                    'wst_text' => $wstText,
                    'time_start' => $timeStart,
                    'time_end' => $timeEnd,
                    'is_overnight' => $this->determineOvernight(
                        $get(['is_overnight', 'overnight', 'lintas_hari']),
                        $timeStart,
                        $timeEnd
                    ),
                    'break1_start' => $break1Start,
                    'break1_end' => $break1End,
                    'break1_duration' => $this->calculateDuration($break1Start, $break1End),
                    'break2_start' => $break2Start,
                    'break2_end' => $break2End,
                    'break2_duration' => $this->calculateDuration($break2Start, $break2End),
                    'group_ws_type' => $groupWs,
                ]
            );
        }
    }

    public function onError(Throwable $e)
    {
        throw $e;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function normalizeHeader($header)
    {
        $header = trim((string) $header);
        $header = trim($header, '"\'');
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', '_', $header);
        return strtolower(trim(preg_replace('/_+/', '_', $normalized), '_'));
    }

    private function isNotBlank($value)
    {
        if ($value === null) return false;
        $text = trim((string) $value);
        return $text !== '' && $text !== '-' && $text !== '--' && strtolower($text) !== 'null';
    }

    private function normalizeBool($value)
    {
        $val = trim(strtolower((string) ($value ?? '')));
        return in_array($val, ['ya', 'yes', 'true', '1'], true);
    }

    private function determineOvernight($rawOvernight, $timeStart, $timeEnd)
    {
        if ($rawOvernight !== null && trim((string) $rawOvernight) !== '') return $this->normalizeBool($rawOvernight);
        try {
            $start = Carbon::createFromFormat('H:i', $timeStart ?: '00:00');
            $end = Carbon::createFromFormat('H:i', $timeEnd ?: '00:00');
            return $end->lessThan($start);
        } catch (Throwable $e) {
            return false;
        }
    }

    private function calculateDuration($start, $end)
    {
        if (!$start || !$end) return 0;
        try {
            $startTime = \DateTime::createFromFormat('H:i', $start);
            $endTime = \DateTime::createFromFormat('H:i', $end);
            if (!$startTime || !$endTime) return 0;
            if ($endTime < $startTime) $endTime->modify('+1 day');
            return (int) $startTime->diff($endTime)->format('%i') + ((int) $startTime->diff($endTime)->format('%h') * 60);
        } catch (Throwable $e) {
            return 0;
        }
    }
}
