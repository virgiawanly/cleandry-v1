<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Outlet;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TransactionController extends Controller
{
    /**
     * Return data for DataTables.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function datatable(Outlet $outlet)
    {
        $transactions = Transaction::with(['user', 'member', 'details'])->where('outlet_id', $outlet->id)->get();

        return DataTables::of($transactions)
            ->addIndexColumn()
            ->addColumn('total_item', function ($transaction) {
                return $transaction->details()->count();
            })
            ->addColumn('actions', function ($transaction) use ($outlet) {
                return '<button class="btn btn-info"><i class="fas fa-eye"></i></button>';
            })->rawColumns(['actions'])->make(true);
    }

    public function newTransaction(Request $request, Outlet $outlet)
    {
        return view('transactions.new_transaction', [
            'title' => 'Transaksi Baru',
            'breadcrumbs' => [
                [
                    'href' => '/o/' . $outlet->id . '/transactions',
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
        $response = DB::transaction(function () use ($request) {
            $rules = [
                'service_id' => 'required|array',
                'qty' => 'required|array',
                'member_id' => 'required|exists:members,id',
                'deadline' => 'required',
                'payment_status' => 'required|in:paid,unpaid',
            ];

            if ($request->payment_status == 'paid') {
                $rules = array_merge($rules, [
                    'discount' => 'required|min:0',
                    'discount_type' => 'in:percent,nominal',
                    'tax' => 'required|min:0',
                    'additional_cost' => 'required|min:0',
                ]);
            }

            $request->validate($rules);

            $transactionDate = date('Y-m-d');
            $payload = [
                'outlet_id' => Auth::user()->is_super ? session()->get('outlet')->id : Auth::user()->outlet_id,
                'user_id' => Auth::id(),
                'member_id' => $request->member_id,
                'invoice' => Transaction::createInvoice(),
                'date' => $transactionDate,
                'deadline' => $request->deadline,
                'status' => 'new',
                'payment_status' => $request->payment_status
            ];

            if ($request->payment_status == 'paid') {
                $payload = array_merge($payload, [
                    'discount' => $request->discount,
                    'discount_type' => $request->discount_type,
                    'additional_cost' => $request->additional_cost,
                    'tax' => $request->tax,
                    'payment_date' => $transactionDate
                ]);
            }

            $transaction = Transaction::create($payload);

            $services = Service::whereIn('id', $request->service_id)->get();

            $transactionDetails = [];
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

            return [
                'success' => true,
                'message' => 'Transaksi berhasil',
                'transaction' => $transaction,
            ];
        });

        if ($response && $response['success']) {
            return response()->json($response, Response::HTTP_OK);
        }
    }
}
