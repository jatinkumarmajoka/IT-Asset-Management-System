<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsTemplateExport implements FromCollection, ShouldAutoSize, WithColumnWidths, WithHeadings, WithStyles
{
    public function collection(): Collection
    {
        // Return sample data rows (3 empty rows for user to fill)
        return collect([
            [
                'ASSET-001', 'Laptop', 'Dell', 'Latitude 5520', 'Laptops', 'IT Department',
                'IT Section', 'Active', 'New', 1, 1, 'SN-001', 'BC-001',
                '2024-01-15', 'John Doe', 'IT Department', 'IT Section',
                'Room 101', '101', '—', '—', '—',
                'New laptop for development team', '',
            ],
            [
                'ASSET-002', 'Monitor', 'LG', '27UP850-W', 'Monitors', 'IT Department',
                '—', 'Active', 'New', 2, 2, 'SN-002', 'BC-002',
                '2024-01-20', '', '', '—',
                'Meeting Room A', 'A-101', '—', '—', '—',
                'Monitors for meeting room', '',
            ],
            [
                'ASSET-003', 'Printer', 'HP', 'LaserJet Pro M404dn', 'Printers', 'Admin Department',
                'Admin Section', 'Active', 'Good', 1, 0, 'SN-003', 'BC-003',
                '2024-02-01', 'Jane Smith', 'Admin Department', 'Admin Section',
                'Print Room', 'PR-01', '—', '—', '—',
                'Shared printer for admin', '',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'asset_code*',
            'item_name*',
            'make',
            'model',
            'category*',
            'department*',
            'sub_department',
            'asset_status',
            'condition',
            'issued_quantity',
            'physically_verified_quantity',
            'asset_serial_number',
            'barcode_number',
            'date_of_issue (YYYY-MM-DD)',
            'present_occupant_name',
            'department',
            'sub_department',
            'present_location',
            'present_room_number',
            'previous_department_name',
            'previous_location',
            'previous_room_number',
            'remarks',
            'sub_department',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '08203C'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
            // Data rows
            'A2:X'.($sheet->getHighestRow()) => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'CCCCCC'],
                    ],
                ],
            ],
            // Add comments/notes row
            5 => [
                'font' => [
                    'italic' => true,
                    'color' => ['argb' => '666666'],
                    'size' => 9,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,  // asset_code
            'B' => 30,  // item_name
            'C' => 15,  // make
            'D' => 25,  // model
            'E' => 20,  // category
            'F' => 25,  // department
            'G' => 20,  // sub_department
            'H' => 18,  // asset_status
            'I' => 14,  // condition
            'J' => 16,  // issued_quantity
            'K' => 22,  // physically_verified_quantity
            'L' => 22,  // asset_serial_number
            'M' => 20,  // barcode_number
            'N' => 22,  // date_of_issue
            'O' => 25,  // present_occupant_name
            'P' => 25,  // department
            'Q' => 20,  // sub_department
            'R' => 25,  // present_location
            'S' => 18,  // present_room_number
            'T' => 25,  // previous_department_name
            'U' => 25,  // previous_location
            'V' => 18,  // previous_room_number
            'W' => 40,  // remarks
            'X' => 20,  // sub_department (duplicate removed)
        ];
    }
}
