<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function newTransaction()
    {
        $outlet = Auth::user()->outlet;
        return view('transactions.new_transaction', [
            'title' => 'Transaksi Baru',
            'breadcrumbs' => [
                [
                    'href' => '/transactions',
                    'label' => 'Transaksi'
                ],
                [
                    'href' => '/transactions/new-transactions',
                    'label' => 'Transaksi Baru'
                ],
            ],
            'outlet' => $outlet
        ]);
    }
}
