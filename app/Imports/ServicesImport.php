<?php

namespace App\Imports;

use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ServicesImport implements WithValidation, ToModel,  WithHeadingRow
{
    private $outletId;

    public function __construct()
    {
        $this->outletId = Auth::user()->is_super ? session()->get('outlet')->id : Auth::user()->outlet_id;
    }

    /**
     * @param array $row
     *
     * @return Service|null
     */
    public function model(array $row)
    {
        $type = ServiceType::where('name', $row['jenis_cucian'])->first();
        $typeId = 0;

        if ($type) {
            $typeId = $type->id;
        } else {
            $newType = ServiceType::create(['name' => $row['jenis_cucian']]);
            $typeId = $newType->id;
        }

        return new Service([
            'name' => $row['nama_layanan'],
            'type_id' => $typeId,
            'unit' => $row['satuan'],
            'price' => $row['harga'],
            'outlet_id' => $this->outletId
        ]);
    }

    public function rules(): array
    {
        return [
            'satuan' => Rule::in(['kg', 'm', 'pcs']),
        ];
    }
}
