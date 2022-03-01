@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="invoice p-4">
                    <div class="row p-3">
                        <div class="col d-flex flex-column justify-content-between">
                            <div>
                                <h2><i class="fas fa-globe mr-1"></i><span>Cleandry</span></h2>
                                <div class="text-sm">JL Jend Sudirman Kav 44-46 BRI 2 Building 9th
                                    Floor,Jakarta,Indonesia</div>
                                <div class="text-sm">Telepon : 089540898123 - Email : contact@cleandry.id</div>
                            </div>
                            <div>
                                <div>Operator : {{ $transaction->user->name }}</div>
                                <div>Outlet : {{ $transaction->outlet->name }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <b>FAKTUR no. {{ $transaction->invoice }}</b><br>
                            <div>{{ date('d/m/Y', strtotime($transaction->date)) }}</div>
                            <div>Kepada Yth :</div>
                            <div>{{ $transaction->member->name }}<br></div>
                            <div>{{ $transaction->member->address }}</div>
                            <div>{{ $transaction->member->phone }}</div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Layanan</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->details as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->service_name_history }}</td>
                                            <td>Rp {{ number_format($item->price_history) }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>Rp {{ number_format($item->price_history * $item->qty) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-6 text-sm">
                            <b>PERHATIAN</b>
                            <ol>
                                <li>Pengambilan barang dibayar tunai.</li>
                                <li>Jika terjadi kehilangan/kerusakan kami hanya mengganti tidak lebih dari 2x ongkos cuci.
                                </li>
                                <li>Hak claim yang kami terima tidak lebih dari 24 jam dari pengambilan.</li>
                            </ol>
                            <b>KAMI TIDAK BERTANGGUNG JAWAB</b>
                            <ol>
                                <li>Susut/luntur karena sifat bahannya.</li>
                                <li>Cucian yang tidak diambil dalam tempo 1 bulan hilang/rusak.</li>
                                <li>Bila terjadi kebakaran</li>
                            </ol>
                        </div>
                        <div class="col-6">
                            <p class="lead">Dibayar Pada
                                {{ date('d/m/Y', strtotime($transaction->payment_date)) }}</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td>Rp {{ number_format($transaction->getTotalPrice()) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pajak ({{ $transaction->tax }}%)</th>
                                        <td>Rp {{ number_format($transaction->getTotalTax()) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Diskon</th>
                                        <td>Rp {{ number_format($transaction->getTotalDiscount()) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Pembayaran</th>
                                        <td>Rp {{ number_format($transaction->getTotalPayment()) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row no-print">
                        <div class="col-12">
                            <a href="?print=true" rel="noopener" target="_blank" class="btn btn-default"><i
                                    class="fas fa-print"></i> Print</a>
                            <button type="button" class="btn btn-success float-right" style="margin-right: 5px;">
                                <i class="fab fa-whatsapp"></i> Kirim Whatsapp
                            </button>
                            <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
