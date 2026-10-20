<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    /**
     * Menangkap parameter tanggal dari Controller
     */
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
    * Mengambil data berdasarkan filter rentang tanggal
    */
    public function collection()
    {
        $query = Attendance::whereNotNull('catatan');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
    * Menentukan header kolom di Excel
    */
    public function headings(): array
    {
        return [
            "NAMA KARYAWAN (ID - NAMA)",
            "TANGGAL",
            "WAKTU INPUT",
            "STATUS KEHADIRAN"
        ];
    }

    /**
    * Transformasi data sebelum masuk ke Excel
    */
    public function map($data): array
    {
        return [

            str_replace('Assorting: ', '', $data->catatan),
            $data->created_at->format('d/m/Y'),
            $data->created_at->format('H:i:s'),
            strtoupper($data->status)
        ];
    }

    /**
     * Memberikan style tebal pada header Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
