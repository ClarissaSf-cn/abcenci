<?php

namespace App\Exports;

use App\Models\Shift;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShiftExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    private int $rowNumber = 0;

    public function collection()
    {
        return Shift::orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Jam Kerja',
            'Keterangan',
            'Awal Jam Kerja',
            'Akhir Jam Kerja',
            'Kode Istirahat',
            'Mulai Istirahat',
            'Akhir Istirahat',
            'Total Istirahat',
            'Total Jam Kerja',
        ];
    }

    public function map($shift): array
    {
        $breakDuration = ($shift->break1_duration ?? 0) + ($shift->break2_duration ?? 0);
        $start = $shift->time_start ? \Carbon\Carbon::parse($shift->time_start) : null;
        $end = $shift->time_end ? \Carbon\Carbon::parse($shift->time_end) : null;
        if ($start && $end && $end->lt($start)) {
            $end->addDay();
        }

        $totalWorkingMinutes = 0;
        if ($start && $end) {
            $totalWorkingMinutes = $start->diffInMinutes($end) - $breakDuration;
        }

        $totalWorkingHours = intdiv(max($totalWorkingMinutes, 0), 60);
        $totalWorkingRemaining = max($totalWorkingMinutes, 0) % 60;

        return [
            ++$this->rowNumber,
            $shift->wst_code,
            $shift->wst_text ?? '-',
            $shift->time_start ? $start->format('H:i') : '-',
            $shift->time_end ? $end->format('H:i') : '-',
            $shift->group_ws_type ?? '-',
            $shift->break1_start ? \Carbon\Carbon::parse($shift->break1_start)->format('H:i') : '-',
            $shift->break1_end ? \Carbon\Carbon::parse($shift->break1_end)->format('H:i') : '-',
            $breakDuration . ' menit',
            $totalWorkingHours . ' jam ' . sprintf('%02d', $totalWorkingRemaining) . ' mnt',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2B8'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);
                $sheet->getStyle("A1:{$highestColumn}1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFF2B8');
                $sheet->getStyle("A1:{$highestColumn}1")->getFont()->getColor()->setRGB('000000');
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('EAF4FB');
                    }
                }
            },
        ];
    }
}
