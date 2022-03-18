<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjemputan Cleandry {{ date('d M, Y') }}</title>
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif
        }

        .main-table {
            width: 100%;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #727272;
        }

    </style>
</head>

<body>
    <h1>Riwayat Transaksi Laundry</h1>
    <table cellspacing="0" cellpadding="3" style="margin-bottom: 1.5em;">
        <tr>
            <td>Outlet</td>
            <td>:</td>
            <td>{{ $outlet->name }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $dateStart . ' s.d' . $dateEnd }}</td>
        </tr>
    </table>
    <table class="main-table" cellspacing="0" cellpadding="5" style="font-size: 0.8em; border: 1px solid #a5a5a5">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Invoice</th>
                <th>Jml. Layanan</th>
                <th>Tgl. Pemberian</th>
                <th>Est. Selesai</th>
                <th>Status Cucian</th>
                <th>Status Pembayaran</th>
                <th>Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $transaction->invoice }}</td>
                    <td>{{ $transaction->details()->count() }}</td>
                    <td>{{ $transaction->date }}</td>
                    <td>{{ $transaction->deadline }}</td>
                    <td>
                        @switch($transaction->status)
                            @case('new')
                                Baru
                            @break

                            @case('process')
                                Diproses
                            @break

                            @case('done')
                                Selesai
                            @break

                            @default
                                Diambil
                        @endswitch
                    </td>
                    <td>
                        @switch($transaction->payment_status)
                            @case('paid')
                                Dibayar
                            @break

                            @default
                                Belum Dibayar
                        @endswitch
                    </td>
                    <td>{{ 'Rp' . number_format($transaction->getTotalPayment()) }}</td>
                </tr>
                @php
                    $total += $transaction->getTotalPayment();
                @endphp
            @endforeach
            <tr>
                <td colspan="7">Total Pendapatan</td>
                <td>{{ 'Rp' . number_format($total) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
