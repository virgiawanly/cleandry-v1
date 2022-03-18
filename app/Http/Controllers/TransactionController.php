<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Http\Resources\TransactionCollection;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Service;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
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
                    'label' => 'Transactions'
                ]
            ],
            'outlet' => $outlet,
        ]);
    }

    /**
     * Return data for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
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
        })->orderBy('id', 'desc')->get();

        return DataTables::of($transactions)
            ->addIndexColumn()
            ->editColumn('date', function ($transaction) {
                return date('d/m/Y', strtotime($transaction->date));
            })
            ->editColumn('deadline', function ($transaction) {
                return date('d/m/Y', strtotime($transaction->deadline));
            })
            ->addColumn('total_item', function ($transaction) {
                return $transaction->details()->count();
            })
            ->addColumn('actions', function ($transaction) use ($outlet) {
                $buttons = '';
                if ($transaction->payment_status === 'unpaid') {
                    $buttons .= '<button class="btn btn-success btn-sm m-1 update-payment-button" data-detail-url="' . route('transactions.show', [$outlet->id, $transaction->id]) . '" data-update-payment-url="' . route('transactions.updatePayment', [$outlet->id, $transaction->id]) . '"><i class="fas fa-cash-register mr-1"></i><span>Bayar</span></button>';
                }
                if ($transaction->status !== 'taken') {
                    $buttons .= '<button class="btn btn-primary btn-sm m-1 update-status-button" data-update-url="' . route('transactions.updateStatus', [$outlet->id, $transaction->id]) . '" data-status="' . $transaction->status . '"><i class="fas fa-arrow-circle-right mr-1"></i><span>Proses</span></button>';
                }
                $buttons .= '<button class="btn btn-info btn-sm m-1 detail-button" data-detail-url="' . route('transactions.show', [$outlet->id, $transaction->id]) . '"><i class="fas fa-eye mr-1"></i><span>Detail</span></button>';
                return $buttons;
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * Show new transaction form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
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
                'outlet_id' => Auth::user()->role === 'admin' ? session()->get('outlet')->id : Auth::user()->outlet_id,
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
        $transaction['date'] = date('d/m/Y', strtotime($transaction->date));
        $transaction['deadline'] = date('d/m/Y', strtotime($transaction->deadline));
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
     * @param  \Illuminate\Http\Request  $request
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function invoicePDF(Outlet $outlet, Transaction $transaction)
    {
        $pdf = Pdf::loadView('transactions.invoice_pdf', [
            'transaction' => $transaction
        ]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream();
    }

    /**
     * Update transaction status.
     *
     * @param  \App\Models\Outlet
     * @param  \App\Models\Transaction
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Outlet $outlet, Transaction $transaction)
    {
        switch ($transaction->status) {
            case 'new':
                $transaction->status = 'process';
                break;
            case 'process':
                $transaction->status = 'done';
                break;
            case 'done':
                $transaction->status = 'taken';
                break;
            default:
                $transaction->status = 'new';
        }
        $transaction->save();

        return response()->json([
            'message' => 'Status transaksi berhasil diupdate',
            'status' => $transaction->status
        ], Response::HTTP_OK);
    }

    /**
     * Update transaction payment status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function updatePayment(Request $request, Outlet $outlet, Transaction $transaction)
    {
        $request->validate([
            'discount' => 'required|min:0',
            'discount_type' => 'in:percent,nominal',
            'tax' => 'required|min:0',
            'additional_cost' => 'required|min:0',
        ]);

        if ($transaction->payment_status === 'unpaid') {
            $transaction->update([
                'payment_status' => 'paid',
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,
                'tax' => $request->tax,
                'additional_cost' => $request->additional_cost,
            ]);
        }

        return response()->json([
            'message' => 'Pembayaran berhasil',
        ], Response::HTTP_OK);
    }


    /**
     * Send transaction invoice via whatsapp.
     *
     * @param  \App\Models\Outlet  $outlet
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function sendWhatsapp(Outlet $outlet, Transaction $transaction)
    {
        $transaction->load('details', 'details.service');

        $text = 'Yth. Pelanggan Cleandry,
        Kami informasikan bahwa cucian anda yang kami terima pada tanggal *' . date('d-m-Y', strtotime($transaction->date)) . '*';

        switch ($transaction->status) {
            case 'new' || 'process':
                $text .= ' sedang dalam proses pencucian.';
                break;
            case 'done':
                $text .= ' *siap untuk diambil*.';
                break;
            default:
                $text .= ' sudah diambil.';
                break;
        }

        $text .= ' Dengan rincian layanan sebagai berikut :';

        foreach ($transaction->details as $detail) {
            $text .= $detail->qty . 'x ' . $detail->service->name;
        }

        $redirectTo = 'https://wa.me/' . $transaction->member->phone;
        // dd($redirectTo);
        return redirect()->to($redirectTo)->with('text', $text);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function report(Outlet $outlet)
    {
        return view('transactions.report', [
            'title' => 'Laporan Transaksi',
            'breadcrumbs' => [
                [
                    'href' => '/o/' . $outlet->id . '/report',
                    'label' => 'Report'
                ],
            ],
            'outlet' => $outlet,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function reportDatatable(Request $request, Outlet $outlet)
    {
        $dateStart = ($request->has('date_start') && $request->date_start != "") ? $request->date_start : date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $dateEnd = ($request->has('date_end') && $request->date_end != "") ? $request->date_end : date('Y-m-d');

        $transactions = Transaction::whereBetween('date', [$dateStart, $dateEnd])->get();

        return DataTables::of($transactions)
            ->addIndexColumn()
            ->editColumn('date', function ($transaction) {
                return date('d/m/Y', strtotime($transaction->date));
            })
            ->editColumn('deadline', function ($transaction) {
                return date('d/m/Y', strtotime($transaction->deadline));
            })
            ->addColumn('total_payment', function ($transaction) {
                return $transaction->getTotalPayment();
            })
            ->addColumn('total_item', function ($transaction) {
                return $transaction->details()->count();
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * Save transactions data as excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \App\Exports\MembersExport
     */
    public function exportExcel(Request $request, Outlet $outlet)
    {
        $dateStart = ($request->has('date_start') && $request->date_start != "") ? $request->date_start : date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $dateEnd = ($request->has('date_end') && $request->date_end != "") ? $request->date_end : date('Y-m-d');

        return (new TransactionsExport)->whereOutlet($outlet->id)->setDateStart($dateStart)->setDateEnd($dateEnd)->download('Transaksi-' . $dateStart . '-' . $dateEnd . '.xlsx');
    }

    /**
     * Save transactions data as pdf file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Barryvdh\DomPDF\Facade\Pdf
     */
    public function exportPDF(Request $request, Outlet $outlet)
    {
        $dateStart = ($request->has('date_start') && $request->date_start != "") ? $request->date_start : date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $dateEnd = ($request->has('date_end') && $request->date_end != "") ? $request->date_end : date('Y-m-d');

        $transactions = Transaction::where('outlet_id', $outlet->id)->whereBetween('date', [$dateStart, $dateEnd])->with('details')->get();

        $pdf = Pdf::loadView('transactions.pdf', ['transactions' => $transactions, 'outlet' => $outlet, 'dateStart' => $dateStart, 'dateEnd' => $dateEnd]);
        return $pdf->stream('Transaksi-' . $dateStart . '-' . $dateEnd . '.pdf');
    }
}
