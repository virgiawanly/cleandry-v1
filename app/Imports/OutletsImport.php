<?php

namespace App\Imports;

use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OutletsImport implements ToModel,  WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Service|null
     */
    public function model(array $row)
    {
        return new Outlet([
            'name' => $row['nama_outlet'],
            'phone' => $row['nomor_telepon'],
            'address' => $row['alamat'],
        ]);
    }
}
