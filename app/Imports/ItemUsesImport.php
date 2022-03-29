<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemUses;
use App\Models\Service;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemUsesImport implements WithValidation, ToModel,  WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Service|null
     */
    public function model(array $row)
    {
        $status = $row['status_penggunaan'] === 'selesai' ? 'finish' : 'in_use';

        $startUse =  date('Y-m-d H:i:s', ($row['waktu_mulai_pakai'] - 25569) * 86400);

        $endUse = null;
        if ($row['status_penggunaan'] === 'selesai') {
            $endUse =  $row['waktu_selesai_pakai'] ? date('Y-m-d H:i:s', ($row['waktu_selesai_pakai'] - 25569) * 86400) : now();
        }

        return new ItemUses([
            'item_name' => $row['nama_barang'],
            'user_name' => $row['nama_pemakai'],
            'start_use' => $startUse,
            'end_use' => $endUse,
            'status' => $status
        ]);
    }

    public function rules(): array
    {
        return [
            'status_penggunaan' => Rule::in(['selesai', 'belum selesai']),
        ];
    }
}
