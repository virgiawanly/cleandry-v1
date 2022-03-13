<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MembersImport implements WithValidation, ToModel,  WithHeadingRow
{
    private $outletId;

    public function __construct()
    {
        $this->outletId = Auth::user()->role === 'admin' ? session()->get('outlet')->id : Auth::user()->outlet_id;
    }

    /**
     * @param array $row
     *
     * @return Member|null
     */
    public function model(array $row)
    {
        $gender = ($row['jenis_kelamin'] === 'Laki-laki') ? 'M' : 'F';
        return new Member([
            'name' => $row['nama_pelanggan'],
            'phone' => $row['nomor_telepon'],
            'email' => $row['email'],
            'address' => $row['alamat'],
            'gender' => $gender,
            'outlet_id' => $this->outletId
        ]);
    }

    public function rules(): array
    {
        return [
            'gender' => Rule::in(['Laki-laki', 'Perempuan']),
        ];
    }
}
