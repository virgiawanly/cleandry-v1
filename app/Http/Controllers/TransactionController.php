<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'date' => 'required',
            'deadline' => 'required',
            'discount_type' => 'in:percent,nominal',
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        DB::transaction(function () use ($request) {
            $payload = [
                'outlet_id' => Auth::user()->outlet->id,
                'user_id' => Auth::id(),
                'member_id' => $request->member_id,
                'invoice' => Transaction::createInvoice(),
                'date' => $request->date,
                'deadline' => $request->deadline,
                'additional_cost' => $request->additional_cost ?? 0,
                'discount' => $request->discount ?? 0,
                'discount_type' => $request->discount_type,
                'tax' => $request->tax ?? 0,
                'status' => 'received',
                'payment_status' => $request->payment_status
            ];

            if ($request->payment_status == 'paid') $payload['payment_date'] = $request->date;

            $transaction = Transaction::create($payload);

            $transactionDetails = [];
            $services = Service::whereIn('id', $request->service_id)->get();
            for ($i = 0; $i < count($request->service_id); $i++) {
                $service =  $services->where('id',  $request->service_id[$i])->first();
                $transactionDetails[] = [
                    'service_id' => $request->service_id[$i],
                    'qty' => $request->qty[$i],
                    'service_name_history' => $service->name,
                    'price_history' => $service->price,
                    'description' => $request->description[$i] ?? ''
                ];
            }

            $transaction->details()->createMany($transactionDetails);
            session()->put('transaction', $transaction);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil'
            ], Response::HTTP_OK);
        });
    }
}
