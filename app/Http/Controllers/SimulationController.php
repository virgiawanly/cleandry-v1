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
                    'href' => '/simluation/employee',
                    'label' => 'Simulasi'
                ],
                [
                    'href' => '/simluation/employee',
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
                    'href' => '/simluation/employee',
                    'label' => 'Simulasi'
                ],
                [
                    'href' => '/simluation/employee',
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
                    'href' => '/simluation/books',
                    'label' => 'Simulasi'
                ],
                [
                    'href' => '/simluation/books',
                    'label' => 'Data Buku'
                ],
            ],
        ]);
    }
}
