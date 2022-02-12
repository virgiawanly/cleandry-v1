@extends('layouts.main')

@push('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endpush

@section('content')
    <form action="#" id="addItemForm" onsubmit="addItemHandler()">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Transaksi Baru</div>
            </div>
            <div class="card-body">
                <!-- row -->
                <div class="row align-items-end" id="addItemContainer">
                    <div class="col-md-3">
                        <label>Layanan</label>
                        <select class="form-control select2bs4 input-service-id" onchange="loadServiceDetail(this.value)">
                            <option disabled selected>Silahkan pilih layanan</option>
                            @foreach ($outlet->services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>Jenis</label>
                        <input type="text" class="form-control input-service-type" placeholder="Jenis" value="-" readonly>
                    </div>
                    <div class="col-md-1">
                        <label>Satuan</label>
                        <input type="text" class="form-control input-service-unit" placeholder="Satuan" value="-" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Harga</label>
                        <input type="number" class="form-control input-service-price" placeholder="Rp" value="0" readonly>
                    </div>
                    <div class="col-md-1">
                        <label>Kuantitas</label>
                        <input type="number" class="form-control input-qty" placeholder="Jumlah Paket" value="1" min="1"
                            onkeypress="updateTotalPrice()">
                    </div>
                    <div class="col-md-2">
                        <label>Total Harga</label>
                        <input type="number" class="form-control input-total-price" placeholder="Jumlah Paket" value="0"
                            readonly>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" id="addItemBtn" class="d-block w-100 btn btn-success">Tambah</button>
                    </div>
                </div>
                <!-- !.row -->
            </div>
        </div>
    </form>

    <form action="/transactions" id="transactionForm" onsubmit="submitHandler()" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <!-- table-container -->
                <div class="table-container">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Paket</th>
                                <th>Harga Paket</th>
                                <th>Kuantitas</th>
                                <th>Total Harga</th>
                                <th>Keterangan / Catatan</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Transaction item goes here -->
                        </tbody>
                    </table>
                </div>
                <!-- !.table-container -->
            </div>
        </div>

        <div class="card">
            <!-- !.card-header -->
            <div class="card-body">
                <!-- row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label>Pelanggan</label>
                            <div class="input-group" style="gap: 5px">
                                <input type="text" class="form-control" placeholder="Cari pelanggan" disabled>
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                    data-target="#modalMember">
                                    <i class="fas fa-search mr-1"></i>
                                    <span>Cari</span>
                                </button>
                            </div>
                            <input type="hidden" name="member_id" value="">
                            @include('transactions.member_info_table')
                        </div>
                        <div style="font-family: consolas">
                            <div class="bg-secondary px-4 py-2">
                                <b class="mb-0">TOTAL</b>
                            </div>
                            <div class="bg-dark p-4">
                                <p class="h1 display-total-payment">Rp0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Transaksi</label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="date">
                        </div>
                        <div class="form-group">
                            <label>Deadline</label>
                            <input type="date" class="form-control" name="deadline">
                        </div>
                        <div class="form-group">
                            <label>Biaya Tambahan</label>
                            <input type="number" class="form-control" placeholder="Rp0,-"
                                onkeydown="updateTotalPayment()" name="additional_cost" value="0">
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-8">
                                <label>Diskon</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" placeholder="0"
                                        onkeydown="updateTotalPayment()" name="discount" value="0">
                                    <select class="form-control" name="discount_type" onchange="updateTotalPayment()">
                                        <option value="percent">Persen (%)</option>
                                        <option value="nominal">Nominal (Rp)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Pajak</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="tax" onkeydown="updateTotalPayment()"
                                        placeholder="0" value="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status Pembayaran</label>
                            <div class="d-flex align-items-center" style="gap: 15px">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_status" id="lunas"
                                        value="paid" checked>
                                    <label class="form-check-label" for="lunas">
                                        Lunas
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_status" id="belumLunas"
                                        value="unpaid">
                                    <label class="form-check-label" for="belumLunas">
                                        Belum Lunas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- !.row -->

            </div>
            <!-- !.card-body -->

            <div class="card-footer row justify-content-end">
                <div class="col-md-3">
                    <button class="btn btn-primary w-100">Proses Transaksi</button>
                </div>
            </div>

        </div>
    </form>
@endsection

@push('bottom')
    @include('transactions.member_modal')
@endpush

@push('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('adminlte') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- Select2 -->
    <script src="{{ asset('adminlte') }}/plugins/select2/js/select2.full.min.js"></script>
    <!-- Page script -->
    <script src="{{ asset('js/pages/transactions.js') }}"></script>
@endpush
