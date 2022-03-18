<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Cleandry {{ date('d M, Y') }}</title>
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
    <h1>Layanan Cleandry</h1>
    <table cellspacing="0" cellpadding="5" style="margin-bottom: 1.5em;">
        <tr>
            <td>Outlet</td>
            <td>:</td>
            <td>{{ $outlet->name }}</td>
        </tr>
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
                <th>Outlet</th>
                <th>Nama Layanan</th>
                <th>Jenis</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Tgl Ditambahkan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($services as $service)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $service->outlet->name }}</td>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->type ? $service->type->name : '-' }}</td>
                    <td>{{ $service->unit }}</td>
                    <td>{{ $service->price }}</td>
                    <td>{{ date('d/m/Y', strtotime($service->created_at)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
