<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\AssetDocument;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsExport implements FromCollection, ShouldAutoSize, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Asset::query()->with([
            'department',
            'category',
            'occupant',
            'presentSubDepartment',
        ]);

        // Apply the same filters as index
        if (! empty($this->filters['search'])) {
            $term = $this->filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('asset_code', 'like', "%{$term}%")
                    ->orWhere('item_name', 'like', "%{$term}%")
                    ->orWhere('make', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
                    ->orWhere('barcode_number', 'like', "%{$term}%")
                    ->orWhere('asset_serial_number', 'like', "%{$term}%")
                    ->orWhere('present_occupant_name', 'like', "%{$term}%");
            });
        }

        if (! empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        if (! empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (! empty($this->filters['asset_status'])) {
            $query->where('asset_status', $this->filters['asset_status']);
        }

        if (! empty($this->filters['present_sub_department_id'])) {
            $query->where('present_sub_department_id', $this->filters['present_sub_department_id']);
        }

        if (! empty($this->filters['condition'])) {
            $query->where('condition', $this->filters['condition']);
        }

        if (! empty($this->filters['assigned_only'])) {
            $query->whereNotNull('present_occupant_id');
        }

        if (! empty($this->filters['unassigned_only'])) {
            $query->whereNull('present_occupant_id');
        }

        if (! empty($this->filters['has_image'])) {
            $query->whereHas('documents', fn ($q) => $q->where('document_type', AssetDocument::TYPE_ASSET_IMAGE));
        }

        if (! empty($this->filters['has_bill'])) {
            $query->whereHas('documents', fn ($q) => $q->where('document_type', AssetDocument::TYPE_BILL_INVOICE));
        }

        if (! empty($this->filters['verified'])) {
            $query->whereRaw('physically_verified_quantity = issued_quantity');
        }

        if (! empty($this->filters['unverified'])) {
            $query->whereRaw('physically_verified_quantity < issued_quantity');
        }

        if (! empty($this->filters['date_from'])) {
            $query->whereDate('date_of_issue', '>=', $this->filters['date_from']);
        }

        if (! empty($this->filters['date_to'])) {
            $query->whereDate('date_of_issue', '<=', $this->filters['date_to']);
        }

        if (! empty($this->filters['qty_min'])) {
            $query->where('issued_quantity', '>=', $this->filters['qty_min']);
        }

        if (! empty($this->filters['qty_max'])) {
            $query->where('issued_quantity', '<=', $this->filters['qty_max']);
        }

        if (! empty($this->filters['verified_qty_min'])) {
            $query->where('physically_verified_quantity', '>=', $this->filters['verified_qty_min']);
        }

        if (! empty($this->filters['verified_qty_max'])) {
            $query->where('physically_verified_quantity', '<=', $this->filters['verified_qty_max']);
        }

        if (! empty($this->filters['employee_id'])) {
            $query->where('present_occupant_id', $this->filters['employee_id']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'S.No.',
            'Asset Code',
            'Item Name',
            'Make',
            'Model',
            'Category',
            'Department',
            'Sub Department',
            'Asset Status',
            'Condition',
            'Issued Quantity',
            'Physically Verified Qty',
            'Asset Serial Number',
            'Barcode Number',
            'Date of Issue',
            'Present Occupant (Employee)',
            'Present Occupant (Department)',
            'Sub Department',
            'Present Location',
            'Present Room Number',
            'Previous Department',
            'Previous Location',
            'Previous Room Number',
            'Remarks',
            'Created By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }

    public function map($asset): array
    {
        static $counter = 0;
        $counter++;

        $occupantName = $asset->occupant?->name
            ?? $asset->present_occupant_name
            ?? $asset->department?->name
            ?? 'Unassigned';

        $occupantDept = $asset->occupant?->department?->name
            ?? ($asset->present_occupant_id ? null : $asset->department?->name)
            ?? '—';

        return [
            $counter,
            $asset->asset_code,
            $asset->item_name,
            $asset->make ?? '—',
            $asset->model ?? '—',
            $asset->category?->name ?? '—',
            $asset->department?->name ?? '—',
            $asset->presentSubDepartment?->name ?? '—',
            $asset->asset_status,
            $asset->condition,
            $asset->issued_quantity,
            $asset->physically_verified_quantity,
            $asset->asset_serial_number ?? '—',
            $asset->barcode_number ?? '—',
            $asset->date_of_issue?->format('Y-m-d') ?? '—',
            $occupantName,
            $occupantDept,
            $asset->presentSubDepartment?->name ?? '—',
            $asset->present_location ?? $asset->location ?? '—',
            $asset->present_room_number ?? '—',
            $asset->previous_department_name ?? '—',
            $asset->previous_location ?? '—',
            $asset->previous_room_number ?? '—',
            $asset->remarks ?? '—',
            $asset->creator?->name ?? '—',
            $asset->updater?->name ?? '—',
            $asset->created_at?->format('Y-m-d H:i:s') ?? '—',
            $asset->updated_at?->format('Y-m-d H:i:s') ?? '—',
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
            'A2:Z'.($sheet->getHighestRow()) => [
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
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // S.No.
            'B' => 18,  // Asset Code
            'C' => 30,  // Item Name
            'D' => 20,  // Make
            'E' => 20,  // Model
            'F' => 25,  // Category
            'G' => 25,  // Department
            'H' => 25,  // Sub Department
            'I' => 18,  // Asset Status
            'J' => 14,  // Condition
            'K' => 16,  // Issued Qty
            'L' => 20,  // Verified Qty
            'M' => 22,  // Serial Number
            'N' => 20,  // Barcode
            'O' => 14,  // Date of Issue
            'P' => 30,  // Present Occupant
            'Q' => 25,  // Occupant Dept
            'R' => 25,  // Sub Dept
            'S' => 25,  // Present Location
            'T' => 18,  // Present Room
            'U' => 25,  // Prev Department
            'V' => 25,  // Prev Location
            'W' => 18,  // Prev Room
            'X' => 40,  // Remarks
            'Y' => 18,  // Created By
            'Z' => 18,  // Updated By
            'AA' => 22, // Created At
            'AB' => 22, // Updated At
        ];
    }
}
