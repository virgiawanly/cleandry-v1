<?php

namespace App\Imports;

use App\Models\Inventory;
use App\Models\Service;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class InventoriesImport implements WithValidation, ToModel,  WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Service|null
     */
    public function model(array $row)
    {
        $condition = '';
        switch ($row['kondisi_barang']) {
            case 'bagus':
                $condition = 'good';
                break;
            case 'rusak ringan':
                $condition = 'damaged';
                break;
            default:
                $condition = 'broken';
        }

        $procurementDate =  date('Y-m-d H:i:s', ($row['tgl_pengadaan'] - 25569) * 86400);

        return new Inventory([
            'name' => $row['nama_barang'],
            'brand' => $row['merek'],
            'qty' => $row['kuantitas'],
            'condition' => $condition,
            'procurement_date' => $procurementDate
        ]);
    }

    public function rules(): array
    {
        return [
            'condition' => Rule::in(['bagus', 'rusak ringan', 'rusak berat']),
        ];
    }
}
