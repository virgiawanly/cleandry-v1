@extends('layouts.main')

@push('head')
    <meta name="outlet-id" content="{{ $outlet->id }}">
    @include('layouts.datatable_styles')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between my-1">
                    <h3>Data Transaksi</h3>
                    <a href="{{ 'transactions/new-transaction' }}" class="btn btn-primary"><i class="fas fa-cash-register mr-1"></i><span>Transaksi Baru</span></a>
                </div>
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item active">
                        <label for="status-tab-new">
                            <span class="nav-link nav-link-status status-new active" href="#">Baru</span>
                        </label>
                        <input type="radio" hidden id="status-tab-new" name="status_tab" value="new">
                    </li>
                    <li class="nav-item">
                        <label for="status-tab-process">
                            <span class="nav-link nav-link-status status-process" href="#">Diproses</span>
                        </label>
                        <input type="radio" hidden id="status-tab-process" name="status_tab" value="process">
                    </li>
                    <li class="nav-item">
                        <label for="status-tab-done">
                            <span class="nav-link nav-link-status status-done" href="#">Selesai</span>
                        </label>
                        <input type="radio" hidden id="status-tab-done" name="status_tab" value="done">
                    </li>
                    <li class="nav-item">
                        <label for="status-tab-taken">
                            <span class="nav-link nav-link-status status-taken" href="#">Diambil</span>
                        </label>
                        <input type="radio" hidden id="status-tab-taken" name="status_tab" value="taken">
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped w-100" id="transactions-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Invoice</th>
                            <th>Nama Pelanggan</th>
                            <th>Item Cucian</th>
                            <th>Tgl Pemberian</th>
                            <th>Est. Selesai</th>
                            <th>Status</th>
                            <th>Status Pembayaran</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('bottom')
    <!-- Transaction detail modal -->
    @include('transactions.transaction_detail_modal')

    <!-- Update payment modal -->
    @include('transactions.update_payment_modal')
@endpush

@push('script')
    @include('layouts.datatable_scripts')
    <!-- Page Script -->
    <script src="{{ asset('js/pages/transactions.js') }}"></script>
@endpush
