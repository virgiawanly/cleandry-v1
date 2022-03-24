<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimulationController extends Controller
{
    public function employee()
    {
        return view('simulation.employee', [
            'title' => 'Simulasi Data Karyawan',
            'breadcrumbs' => [
                [
                    'href' => '/simulation/employee',
                    'label' => 'Simulasi'
                ],
                [
                    'href' => '/simulation/employee',
                    'label' => 'Karyawan'
                ],
            ],
        ]);
    }

    public function fee()
    {
        return view('simulation.fee', [
            'title' => 'Simulasi Gaji Karyawan',
            'breadcrumbs' => [
                [
                    'href' => '/simulation/employee',
                    'label' => 'Simulasi'
                ],
                [
                    'href' => '/simulation/employee',
                    'label' => 'Karyawan'
                ],
            ],
        ]);
    }

    public function books()
    {
        return view('simulation.books', [
            'title' => 'Simulasi Data Buku',
            'breadcrumbs' => [
                [
                    'href' => '/simulation/books',
                    'label' => 'Simulasi'
                ],
                [
                    'href' => '/simulation/books',
                    'label' => 'Data Buku'
                ],
            ],
        ]);
    }

    public function transactions()
    {
        return view('simulation.transactions', [
            'title' => 'Simulasi Transaksi Barang',
            'breadcrumbs' => [
                [
                    'href' => '/simulation/transactions',
                    'label' => 'Simulasi'
                ],
                [
                    'href' => '/simulation/transactions',
                    'label' => 'Transaksi Barang'
                ],
            ],
        ]);
    }
}
