<?php

namespace App\Exports;

use App\Models\Member;
use App\Models\Outlet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class MembersExport implements FromQuery, WithMapping, WithHeadings, WithEvents
{
    use Exportable;

    private $rowNumber = 0;
    private $outletId;
    private $outle;

    public function whereOutlet(int $outletId)
    {
        $this->outletId = $outletId;
        $this->outlet = Outlet::where('id', $outletId)->first();
        return $this;
    }

    public function query()
    {
        return Member::query()->where('outlet_id', $this->outletId);
    }

    public function headings(): array
    {
        return ["No", "Nama Member", "Nomor Telepon", "Email",  "Jenis Kelamin", "Alamat", "Ditambahkan pada"];
    }

    public function map($member): array
    {
        $gender = $member->gender === 'M' ? 'Laki-laki' : 'Perempuan';
        return [
            ++$this->rowNumber,
            $member->name,
            $member->phone,
            $member->email,
            $gender,
            $member->address,
            $member->created_at,
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

                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->mergeCells('A2:B2');
                $event->sheet->mergeCells('C2:D2');
                $event->sheet->setCellValue('A1', 'Data Member Laundry');
                $event->sheet->setCellValue('A2', 'Outlet : ' . $this->outlet->name);
                $event->sheet->setCellValue('C2', 'Tgl : ' . date('d/m/Y'));
                $event->sheet->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getStyle('A3:G3')->getFont()->setBold(true);
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->getStyle('A3:G' . $event->sheet->getHighestRow())->applyFromArray([
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
