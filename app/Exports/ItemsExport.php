<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ItemsExport implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    use Exportable;

    private $rowNumber = 0;

    public function collection()
    {
        return Item::all();
    }

    public function headings(): array
    {
        return ["No", "Nama Barang", "Kuantitas", "Harga", "Waktu Beli", "Supplier", "Status Barang", "Tgl Ubah Status"];
    }

    public function map($item): array
    {
        $status = '';
        switch ($item->status) {
            case 'submission':
                $status = 'Diajukan';
                break;
            case 'out_of_stok':
                $status = 'Habis';
                break;
            default:
                $status = 'Tersedia';
        }

        return [
            ++$this->rowNumber,
            $item->name,
            $item->qty,
            $item->price,
            $item->buy_date,
            $item->supplier,
            $status,
            $item->status_updated_at,
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getColumnDimension('F')->setAutoSize(true);
                $event->sheet->getColumnDimension('G')->setAutoSize(true);
                $event->sheet->getColumnDimension('H')->setAutoSize(true);

                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->mergeCells('A1:H1');
                $event->sheet->mergeCells('A2:B2');
                $event->sheet->setCellValue('A1', 'Data Barang');
                $event->sheet->setCellValue('A2', 'Tgl : ' . date('d/m/Y'));
                $event->sheet->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getStyle('A3:H3')->getFont()->setBold(true);
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->getStyle('A3:H' . $event->sheet->getHighestRow())->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }
}
