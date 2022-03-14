@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-body row">
            <div class="col-md-5">
                <div class="form-group row">
                    <div class="col-sm-2 d-flex align-items-center justify-content-end">
                        <label for="date-start">Tgl Awal</label>
                    </div>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="date-start">
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group row">
                    <div class="col-sm-2 d-flex align-items-center justify-content-end">
                        <label for="date-end">Tgl Akhir</label>
                    </div>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="date-end">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="transactions-table" class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Invoice</th>
                        <th>Total Biaya</th>
                        <th>Jumlah Cucian</th>
                        <th>Tgl. Pemberian</th>
                        <th>Est. Selesai</th>
                        <th>Status Cucian</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Transaction data goes here -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        table = $("#transactions-table").DataTable({
            columns: [{
                    data: "DT_RowIndex",
                },
                {
                    data: "invoice",
                },
                {
                    data: "member",
                    render: (member) => member.name,
                },
                {
                    data: "total_payment",
                },
                {
                    data: "total_item",
                },
                {
                    data: "date",
                },
                {
                    data: "deadline",
                },
                {
                    data: "status",
                    render: (status) => {
                        let text;
                        switch (status) {
                            case "new":
                                text = "Baru";
                                break;
                            case "process":
                                text = "Diproses";
                                break;
                            case "done":
                                text = "Selesai";
                                break;
                            default:
                                text = "Diambil";
                                break;
                        }
                        return text;
                    },
                },
                {
                    data: "payment_status",
                    render: (status) => {
                        let type;
                        let label;
                        switch (status) {
                            case "paid":
                                type = "success";
                                label = "Dibayar";
                                break;
                            default:
                                type = "warning";
                                label = "Belum Dibayar";
                                break;
                        }
                        return `<div class="badge badge-${type}">${label}</div>`;
                    },
                },
            ],
        });
    </script>
@endpush
