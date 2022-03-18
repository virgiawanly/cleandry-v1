<?php

namespace App\Exports;

use App\Models\Outlet;
use App\Models\Service;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class TransactionsExport implements FromQuery, WithMapping, WithHeadings, WithEvents
{
    use Exportable;

    private $rowNumber = 0;

    public function whereOutlet(int $outletId)
    {
        $this->outlet = Outlet::find($outletId);
        $this->outletId = $outletId;
        return $this;
    }

    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function query()
    {
        return Transaction::query()->with(['details'])->where('outlet_id', $this->outletId)->whereBetween('date', [$this->dateStart, $this->dateEnd]);
    }

    public function headings(): array
    {
        return ["No", "Kode Invoice", "Jumlah Layanan", "Tgl Pemberian", "Estimasi Selesai", "Status Cucian", "Status Pembayaran", "Total Biaya"];
    }

    public function map($transaction): array
    {
        switch ($transaction->status) {
            case 'new':
                $transaction->status = 'Baru';
                break;
            case 'process':
                $transaction->status = 'Diproses';
                break;
            case 'done':
                $transaction->status = 'Selesai';
                break;
            case 'taken':
                $transaction->status = 'Diambil';
                break;
            default:
                $transaction->status = '';
                break;
        }

        switch ($transaction->payment_status) {
            case 'paid':
                $transaction->payment_status = 'Dibayar';
                break;
            default:
                $transaction->payment_status = 'Belum Dibayar';
                break;
        }

        return [
            ++$this->rowNumber,
            $transaction->invoice,
            $transaction->details()->count(),
            date('d/m/Y', strtotime($transaction->date)),
            date('d/m/Y', strtotime($transaction->deadline)),
            $transaction->status,
            $transaction->payment_status,
            $transaction->getTotalPayment(),
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
                $event->sheet->mergeCells('G2:H2');
                $event->sheet->setCellValue('A1', 'Riwayat Transaksi Laundry');
                $event->sheet->setCellValue('A2', 'Outlet : ' . $this->outlet->name);
                $event->sheet->setCellValue('G2', 'Tgl : ' . date('d/m/Y', strtotime($this->dateStart)) . ' - ' . date('d/m/Y', strtotime($this->dateEnd)));
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
