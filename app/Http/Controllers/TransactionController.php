<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionCollection;
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
     * Display a listing of the resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function index(Outlet $outlet)
    {
        return view('transactions.index', [
            'title' => 'Kelola Transaksi',
            'breadcrumbs' => [
                [
                    'href' => '/o/' . $outlet->id . '/transactions',
                    'label' => 'Member'
                ]
            ],
            'outlet' => $outlet,
        ]);
    }

    /**
     * Return data for DataTables.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request, Outlet $outlet)
    {
        $status = null;
        if ($request->has('status')) {
            switch ($request->status) {
                case 'new':
                    $status = 'new';
                    break;
                case 'process':
                    $status = 'process';
                    break;
                case 'done':
                    $status = 'done';
                    break;
                case 'taken':
                    $status = 'taken';
                    break;
                default:
                    $status = null;
            }
        }

        $transactions = Transaction::with(['user', 'member', 'details'])->where('outlet_id', $outlet->id)->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })->get();

        return DataTables::of($transactions)
            ->addIndexColumn()
            ->addColumn('total_item', function ($transaction) {
                return $transaction->details()->count();
            })
            ->addColumn('actions', function ($transaction) {
                $buttons = '';
                if ($transaction->payment_status === 'unpaid') {
                    $buttons .= '<button class="btn btn-success m-1 pay-button"><i class="fas fa-cash-register mr-1"></i><span>Bayar</span></button>';
                }
                $buttons .= '<button class="btn btn-info m-1 detail-button" data-id="' . $transaction->id . '"><i class="fas fa-eye mr-1"></i><span>Detail</span></button>';
                return $buttons;
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet, Transaction $transaction)
    {
        $transaction->load(['details', 'outlet', 'user', 'member']);
        $transaction['total_discount'] = $transaction->getTotalDiscount();
        $transaction['total_price'] = $transaction->getTotalPrice();
        $transaction['total_tax'] = $transaction->getTotalTax();
        $transaction['total_payment'] = $transaction->getTotalPayment();
        return response()->json([
            'message' => 'Data Transaksi',
            'transaction' => $transaction
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function invoice(Request $request, Outlet $outlet, Transaction $transaction)
    {
        $transaction->load(['details', 'outlet', 'user', 'member']);
        $transaction['total_discount'] = $transaction->getTotalDiscount();
        $transaction['total_price'] = $transaction->getTotalPrice();
        $transaction['total_tax'] = $transaction->getTotalTax();
        $transaction['total_payment'] = $transaction->getTotalPayment();

        if ($request->has('print') && $request->print == true) {
            return view('transactions.invoice_print', [
                'transaction' => $transaction,
            ]);
        } else {
            return view('transactions.invoice', [
                'title' => 'Invoice',
                'breadcrumbs' => [
                    [
                        'href' => '/o/' . $outlet->id . '/transactions',
                        'label' => 'Transaksi'
                    ],
                    [
                        'href' => '/o/' . $outlet->id . '/transactions/' . $transaction->id . '/invoice',
                        'label' => 'Invoice'
                    ],
                ],
                'transaction' => $transaction
            ]);
        }
    }

    public function updatePayment(Request $request){

    }

    public function faktur(Request $request, $id){
        $transaksi = Transaction::findOrFail($id);
    }
}
