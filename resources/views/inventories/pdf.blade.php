<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Cleandry {{ date('d M, Y') }}</title>
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
    <h1>Barang Inventaris Cleandry</h1>
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
                <th>Merek</th>
                <th>Kuantitas</th>
                <th>Kondisi</th>
                <th>Tgl Pengadaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventories as $inventory)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $inventory->name }}</td>
                    <td>{{ $inventory->brand }}</td>
                    <td>{{ $inventory->qty }}</td>
                    <td>
                        @switch($inventory->condition)
                            @case('good')
                                <span>Layak pakai</span>
                            @break

                            @case('damaged')
                                <span>Rusak ringan</span>
                            @break

                            @default
                                <span>Rusak</span>
                        @endswitch
                    </td>
                    <td>{{ date('d/m/Y', strtotime($inventory->procurement_date)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
