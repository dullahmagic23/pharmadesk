<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting,ShouldAutoSize,WithEvents
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return collect($this->sales)->map(function($sale){
            return [
                'date' => $sale->date,
                'buyer' => $sale->buyer->name,
                'items' => $sale->items->count(),
                'total' => $sale->total,
                'paid' => $sale->paid,
                'balance' => $sale->balance,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'DATE',
            'BUYER',
            'ITEMS',
            'TOTAL',
            'PAID',
            'BALANCE',

        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '"TSh. "#,##0.00',
            'E' => '"TSh. "#,##0.00',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $firstRow = $event->sheet->getStartRow(1);
                $event->sheet->getStyle($firstRow)->getFont()->setBold(true);

                // Example: Add a TOTAL row for Amount and Balance
                $event->sheet->setCellValue('C' . ($highestRow + 1), 'TOTAL:');
                $event->sheet->setCellValue('D' . ($highestRow + 1), '=SUM(D2:D' . $highestRow . ')');
                $event->sheet->setCellValue('E' . ($highestRow + 1), '=SUM(E2:E' . $highestRow . ')');

                // Bold total row
                $event->sheet->getStyle('C' . ($highestRow + 1) . ':D' . ($highestRow + 1))->getFont()->setBold(true);
                $event->sheet->getStyle('D' . ($highestRow + 1) . ':E' . ($highestRow + 1))->getFont()->setBold(true);
            },
        ];
    }
}
