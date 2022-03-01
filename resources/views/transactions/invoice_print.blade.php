<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cleandry Invoice</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('adminlte') }}/dist/css/adminlte.min.css?v=3.2.0">
</head>

<body>
    <div class="wrapper">
        <div class="invoice">
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
        </div>
    </div>


    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script>
        window.addEventListener("load", window.print());
    </script>
</body>

</html>
