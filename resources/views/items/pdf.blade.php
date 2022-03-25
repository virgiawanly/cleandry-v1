<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang {{ date('d M, Y') }}</title>
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
    <h1>Data Barang</h1>
    <table cellspacing="0" cellpadding="5" style="margin-bottom: 1.5em;">
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ date('d-m-Y') }}</td>
        </tr>
    </table>
    <table class="main-table" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Kuantitas</th>
                <th>Harga</th>
                <th>Waktu Beli</th>
                <th>Supplier</th>
                <th>Status Barang</th>
                <th>Tgl Ubah Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->buy_date }}</td>
                    <td>{{ $item->supplier }}</td>
                    <td>
                        @switch($item->condition)
                            @case('submission')
                                <span>Diajukan</span>
                            @break

                            @case('out_of_stock')
                                <span>Habis</span>
                            @break

                            @default
                                <span>Tersedia</span>
                        @endswitch
                    </td>
                    <td>{{ date('d/m/Y', strtotime($item->status_updated_at)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
