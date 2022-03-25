<?php

namespace App\Imports;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Service;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemsImport implements WithValidation, ToModel,  WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Service|null
     */
    public function model(array $row)
    {
        $status = '';
        switch ($row['status_barang']) {
            case 'diajukan':
                $status = 'submission';
                break;
            case 'habis':
                $status = 'out_of_stock';
                break;
            default:
                $status = 'available';
        }

        $buyDate =  date('Y-m-d H:i:s', ($row['waktu_beli'] - 25569) * 86400);

        return new Item([
            'name' => $row['nama_barang'],
            'qty' => $row['kuantitas'],
            'price' => $row['harga'],
            'buy_date' => $buyDate,
            'supplier' => $row['supplier'],
            'status' => $status
        ]);
    }

    public function rules(): array
    {
        return [
            'condition' => Rule::in(['diajukan', 'habis', 'tersedia']),
        ];
    }
}
