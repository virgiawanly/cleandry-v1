<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penggunaan Barang {{ date('d M, Y') }}</title>
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
    <h1>Data Penggunaan Barang</h1>
    <table cellspacing="0" cellpadding="5" style="margin-bottom: 1.5em;">
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ date('d-m-Y') }}</td>
        </tr>
    </table>
    <table class="main-table" cellspacing="0" cellpadding="2" style="font-size: 0.85em">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Nama Pengguna</th>
                <th>Waktu Mulai Pakai</th>
                <th>Waktu Selesai Pakai</th>
                <th>Status Penggunaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($itemUses as $uses)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $uses->item_name }}</td>
                    <td>{{ $uses->user_name }}</td>
                    <td>{{ $uses->start_use }}</td>
                    <td>{{ $uses->end_use ?? "-" }}</td>
                    <td>
                        @switch($uses->status)
                            @case('in_use')
                                <span>Belum Selesai</span>
                            @break

                            @case('finish')
                                <span>Selesai</span>
                            @break

                            @default
                                <span>-</span>
                        @endswitch
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
