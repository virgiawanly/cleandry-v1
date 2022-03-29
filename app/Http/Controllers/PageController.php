<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Menampilkan halaman dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view('dashboard', [
            'title' => 'Dashboard',
            'breadcrumbs' => [
                [
                    'href' => '/',
                    'label' => 'Dashboard'
                ],
            ]
        ]);
    }
}
