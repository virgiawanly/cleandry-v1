<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Pickup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PickupsImport implements WithValidation, ToModel,  WithHeadingRow
{
    private $outletId;

    public function __construct()
    {
        $this->outletId = Auth::user()->role === 'admin' ? session()->get('outlet')->id : Auth::user()->outlet_id;
    }

    /**
     * @param array $row
     *
     * @return Pickup|null
     */
    public function model(array $row)
    {
        $gender = $row['jenis_kelamin'] === 'Laki-laki' ? 'M' : 'F';
        $memberId = null;
        $member = Member::where('name', $row['nama_pelanggan'])->where('phone', $row['nomor_telepon'])->where('address', $row['alamat_pelanggan'])->where('gender', $gender)->first();
        if ($member) {
            $memberId = $member->id;
        } else {
            $member = Member::create([
                'name' => $row['nama_pelanggan'],
                'phone' => $row['nomor_telepon'],
                'address' => $row['alamat_pelanggan'],
                'email' => $row['email_pelanggan'],
                'gender' => $gender,
                'outlet_id' => $this->outletId
            ]);
            $memberId = $member->id;
        }

        $status = '';
        switch ($row['status_penjemputan']) {
            case 'tercatat':
                $status = 'noted';
                break;
            case 'penjemputan':
                $status = 'process';
                break;
            case 'selesai':
                $status = 'done';
                break;
        }

        return new Pickup([
            'member_id' => $memberId,
            'status' => $status,
            'courier' => $row['nama_petugas_penjemputan'],
            'outlet_id' => $this->outletId
        ]);
    }

    public function rules(): array
    {
        return [
            'status' => Rule::in(['tercatat', 'penjemputan', 'selesai']),
        ];
    }
}
