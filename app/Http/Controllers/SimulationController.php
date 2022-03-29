<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimulationController extends Controller
{
    /**
     * Menampilkan halaman simulasi karyawan.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Menampilkan halaman simulasi gaji karyawan.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Menampilkan halaman simulasi data buku.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Menampilkan halaman simulasi transaksi barang.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Menampilkan halaman simulasi transaksi cucian.
     *
     * @return \Illuminate\Http\Response
     */
    public function serviceTransactions()
    {
        return view('simulation.service_transactions', [
            'title' => 'Simulasi Transaksi Cucian',
            'breadcrumbs' => [
                [
                    'href' => '/simulation/service-transactions',
                    'label' => 'Simulasi'
                ],
                [
                    'href' => '/simulation/service-transactions',
                    'label' => 'Transaksi Cucian'
                ],
            ],
        ]);
    }
}
