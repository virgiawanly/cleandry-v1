@extends('layouts.main')

@push('head')
    <meta name="outlet-id" content="{{ $outlet->id }}">
    @include('layouts.datatable_styles')
    <link rel="stylesheet" href="{{ asset('css/pages/new_transaction.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <form action="{{ route('transactions.store', $outlet->id) }}" method="POST" id="transaction-form">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Transaksi Baru</h5>
                </div>
                <!-- card-body -->
                <div class="card-body">
                    <!-- Input item -->
                    <div class="row mt-5">
                        <div class="col-md-8 mx-auto">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search-item-input"
                                        placeholder="Cari Layanan">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" id="open-add-item-modal-button"><i
                                                class="fas fa-plus-circle mr-1"></i><span>Tambah
                                                Item</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- !. input item -->
                    <!-- Item list table -->
                    <table class="table table-striped" id="transaction-items-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Layanan</th>
                                <th>Jenis</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items goes here -->
                        </tbody>
                    </table>
                    <!-- !. item list table -->
                    <!-- Transaction details -->
                    <div class="row">
                        <!-- Left col -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Member</label>
                                </div>
                                <div class="col-sm-9 form-group mb-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control o-member-info" placeholder="-" disabled>
                                        <div class="input-group-append">
                                            <button type="button" class="input-group-text"
                                                id="open-member-info-modal-button" disabled><i
                                                    class="fas fa-eye"></i></button>
                                            <button type="button" class="btn btn-info" id="open-member-modal-button"><i
                                                    class="fas fa-search mr-1"></i><span>Pilih</span></button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="member_id">
                                </div>
                            </div>
                            <!-- !.row -->
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Tanggal Pesanan</label>
                                </div>
                                <div class="col-sm-9 form-group mb-0">
                                    <input type="date" readonly value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                            <!-- !.row -->
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Estimasi Selesai</label>
                                </div>
                                <div class="col-sm-9 form-group mb-0">
                                    <input type="date" name="deadline" class="form-control">
                                </div>
                            </div>
                            <!-- !.row -->
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="">Pembayaran</label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="radio" name="payment_status" value="paid" id="payment-status-paid"
                                                hidden checked>
                                            <label for="payment-status-paid">
                                                <div class="card mb-0">
                                                    <div class="card-body">
                                                        <span>Bayar Sekarang</span>
                                                        <div class="float-right">
                                                            <i class="fas fa-check-circle"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" name="payment_status" value="unpaid"
                                                id="payment-status-unpaid" hidden>
                                            <label for="payment-status-unpaid">
                                                <div class="card mb-0">
                                                    <div class="card-body">
                                                        <span>Bayar Nanti</span>
                                                        <div class="float-right">
                                                            <i class="fas fa-check-circle"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- !.row -->
                        </div>
                        <!-- !. left col -->
                        <!-- Right col -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-sm-3 text-right">
                                    <label for="">Total Harga (Rp)</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control o-total-price" placeholder="Rp0,-" readonly>
                                </div>
                            </div>
                            <div id="pay-now">
                                <div class="form-group row">
                                    <div class="col-sm-3 text-right">
                                        <label for="">Diskon</label>
                                    </div>
                                    <div class="col-sm-9 form-group mb-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="discount" value="0">
                                            <div class="input-group-append">
                                                <select name="discount_type" class="form-control">
                                                    <option value="nominal" selected>Nominal (Rp)</option>
                                                    <option value="percent">Persen (%)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3 text-right">
                                        <label for="">Pajak (%)</label>
                                    </div>
                                    <div class="col-sm-9 form-group mb-0">
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="tax" value="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3 text-right">
                                        <label for="">Biaya Tambahan (Rp)</label>
                                    </div>
                                    <div class="col-sm-9 form-group mb-0">
                                        <input type="text" class="form-control" name="additional_cost" placeholder="Rp0,-"
                                            value="0">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3 text-right">
                                        <label for="">Total Bayar (Rp)</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control o-total-payment" readonly
                                            placeholder="Rp0,-">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- !. right col -->
                    </div>
                    <!-- !. transaction details -->
                </div>
                <!-- !. card-body -->
                <div class="card-footer bg-light text-right">
                    <button class="btn btn-primary submit-transaction-button">Proses Transaksi</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('bottom')
    <!-- Select member modal -->
    @include('transactions.select_member_modal')

    <!-- Member info modal -->
    @include('transactions.member_info_modal')

    <!-- Add service item modal -->
    @include('transactions.add_item_modal')
@endpush

@push('script')
    @include('layouts.datatable_scripts')
    <script src="{{ asset('js/pages/new_transaction.js') }}"></script>
@endpush
