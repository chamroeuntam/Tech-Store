<?php
namespace App\Exports;

use App\Models\SalesReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    protected $totalRevenue = 0;
    protected $id;
    protected $filterType;

    public function __construct($id, $filterType = 'daily')
    {
        $this->id = $id;
        $this->filterType = ucfirst($filterType);
    }

    public function collection()
    {
        $report = SalesReport::with('productSales.product')->find($this->id);
        if ($report && $report->productSales->count() > 0) {
            $this->totalRevenue = $report->productSales->sum('total_revenue');
            return $report->productSales;
        } else {
            // Return a dummy row if no data
            return collect([
                (object)[
                    'id' => '',
                    'product' => (object)['name' => 'No data'],
                    'quantity_sold' => 0,
                    'total_revenue' => 0,
                ]
            ]);
        }
    }

    public function headings(): array
    {
        return [
            'No.',
            'Product',
            'Quantity Sold',
            'Total Revenue',
        ];
    }

    private $rowNumber = 0;
    public function map($item): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $item->product->name,
            $item->quantity_sold,
            $item->total_revenue,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                // Add border to all cells
                $sheet->getStyle("A1:{$highestColumn}{$rowCount}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                // Format revenue as currency
                $sheet->getStyle("D3:D{$rowCount}")
                    ->getNumberFormat()->setFormatCode('[$$-409]#,##0.00');
                // Freeze header row
                $sheet->freezePane('A3');
                // Add autofilter
                $sheet->setAutoFilter("A2:{$highestColumn}2");
                // Add total revenue row
                $totalRow = $rowCount + 1;
                $sheet->setCellValue("C{$totalRow}", 'Total Revenue:');
                $sheet->setCellValue("D{$totalRow}", $this->totalRevenue);
                $sheet->getStyle("C{$totalRow}:D{$totalRow}")->getFont()->setBold(true);
                $sheet->getStyle("D{$totalRow}")->getNumberFormat()->setFormatCode('[$$-409]#,##0.00');
            }
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function styles(Worksheet $sheet)
    {
        // Custom header at top
        $sheet->setCellValue('A1', 'Tech-Store Report (' . $this->filterType . ')');
        $sheet->mergeCells('A1:D1');
        return [
            'A1:D1' => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
