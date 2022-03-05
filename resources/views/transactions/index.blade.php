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
                    <a href="{{ 'transactions/new-transaction' }}" class="btn btn-primary">Transaksi Baru</a>
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
                            <th>Status Cucian</th>
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
    <div class="modal fade" id="transaction-detail-modal" tabindex="-1" aria-labelledby="transaction-detail-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transaction-detail-modalLabel">Detail Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="card shadow-none w-100">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs">
                                <li class="nav-item">
                                    <label for="modal-tab-items">
                                        <span class="nav-link tab-items" href="#">Detail Pesanan</span>
                                    </label>
                                    <input type="radio" hidden id="modal-tab-items" name="modal_tab" value="items">
                                </li>
                                <li class="nav-item">
                                    <label for="modal-tab-member">
                                        <span class="nav-link tab-member" href="#">Detail Pelanggan</span>
                                    </label>
                                    <input type="radio" hidden id="modal-tab-member" name="modal_tab" value="member">
                                </li>
                            </ul>
                        </div>
                        <div class="card-body pb-0">
                            <div id="transaction-items-info" class="d-block">
                                <table class="table table-borderless table-sm mb-3" id="transaction-info-table">
                                    <tr>
                                        <th>Kode Invoice</th>
                                        <td class="td-transaction-invoice"></td>
                                    </tr>
                                    <tr>
                                        <th>Nama Pelanggan</th>
                                        <td class="td-transaction-member-name"></td>
                                    </tr>
                                    <tr>
                                        <th>Tgl Diterima</th>
                                        <td class="td-transaction-date">{{ date('d M, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estimasi Selesai</th>
                                        <td class="td-transaction-deadline">{{ date('d M, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status Pembayaran</th>
                                        <td class="td-transaction-payment-status"></td>
                                    </tr>
                                </table>
                                <table class="table mb-3" id="transaction-items-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Layanan</th>
                                            <th>Harga</th>
                                            <th>Qty</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data goes here -->
                                    </tbody>
                                </table>
                                <div id="button-container">

                                </div>
                            </div>
                            <div id="transaction-member-info" class="d-none">
                                <table class="table table-borderless">
                                    <tr>
                                        <th colspan="2">Detail Pelanggan</th>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td class="td-member-name"></td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Telepon</th>
                                        <td class="td-member-phone"></td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td class="td-member-address"></td>
                                    </tr>
                                    <tr>
                                        <th>Member Sejak</th>
                                        <td class="td-member-since"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('script')
    @include('layouts.datatable_scripts')
    <!-- Page Script -->
    <script>
        let table;
        const outletId = $("meta[name='outlet-id']").attr("content");
        let datatableUrl = `/o/${outletId}/transactions/datatable`;

        $(function() {
            table = $('#transactions-table').DataTable({
                ...DATATABLE_OPTIONS,
                ajax: {
                    url: `${datatableUrl}?status=new`,
                },
                columns: [{
                        data: 'DT_RowIndex',
                    },
                    {
                        data: 'invoice',
                    },
                    {
                        data: 'member',
                        render: (member) => member.name
                    },
                    {
                        data: 'total_item',
                    },
                    {
                        data: 'date',
                    },
                    {
                        data: 'deadline',
                    },
                    {
                        data: 'status',
                        render: (status) => {
                            let type;
                            let label;
                            switch (status) {
                                case 'new':
                                    type = 'info';
                                    label = 'Baru'
                                    break;
                                case 'process':
                                    type = 'warning';
                                    label = 'Diproses'
                                    break;
                                case 'done':
                                    type = 'success';
                                    label = 'Selesai'
                                    break;
                                default:
                                    type = 'secondary';
                                    label = 'Diambil';
                                    break;
                            }
                            return `<div class="badge badge-${type}">${label}</div>`
                        }
                    },
                    {
                        data: 'payment_status',
                        render: (status) => {
                            let type;
                            let label;
                            switch (status) {
                                case 'paid':
                                    type = 'success';
                                    label = 'Dibayar'
                                    break;
                                default:
                                    type = 'warning';
                                    label = 'Belum Dibayar';
                                    break;
                            }
                            return `<div class="badge badge-${type}">${label}</div>`
                        }
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false,
                    }
                ]
            });
        });

        $('#transactions-table').on('click', '.detail-button', async function() {
            let transactionId = $(this).data('id');
            try {
                let {
                    transaction
                } = await fetchData(`/o/${outletId}/transactions/${transactionId}`);
                let rows = '';
                transaction.details.forEach((item, index) => {
                    rows += `<tr>
                            <td>${++index}</td>
                            <td>${item.service_name_history}</td>
                            <td>${item.price_history}</td>
                            <td>${item.qty}</td>
                            <td>${item.qty * item.price_history}</td>
                        </tr>`;
                });

                rows += `<tr>
                            <td colspan="4">Total Harga</td>
                            <td>${formatter.format(transaction.total_price)}</td>
                        </tr>`;

                if (transaction.payment_status == 'paid') {
                    rows += `<tr>
                            <td colspan="4">Diskon</td>
                            <td>${formatter.format(transaction.total_discount)}</td>
                        </tr>`;
                    rows += `<tr>
                            <td colspan="4">Pajak</td>
                            <td>${formatter.format(transaction.total_tax)}</td>
                        </tr>`;
                    rows += `<tr>
                            <td colspan="4">Biaya Tambahan</td>
                            <td>${formatter.format(transaction.additional_cost)}</td>
                        </tr>`;
                    rows += `<tr>
                            <th colspan="4">Total</th>
                            <td>${formatter.format(transaction.total_payment)}</td>
                        </tr>`;
                }

                $('#transaction-info-table').find('.td-transaction-invoice').text(transaction.invoice);
                $('#transaction-info-table').find('.td-transaction-member-name').html(
                    `<label for="modal-tab-member" class="mb-0 text-primary">${transaction.member.name}<i class="fas fa-external-link-alt ml-1"></i></label>`
                );
                $('#transaction-info-table').find('.td-transaction-date').text(transaction.date);
                $('#transaction-info-table').find('.td-transaction-deadline').text(transaction.deadline);
                let paymentStatusType = transaction.payment_status == 'paid' ? 'success' : 'secondary';
                let paymentStatusText = transaction.payment_status == 'paid' ? 'Dibayar' : 'Belum dibayar';
                $('#transaction-info-table').find('.td-transaction-payment-status').html(
                    `<span class="font-weight-bold text-${paymentStatusType}">${paymentStatusText}</span>`);
                $('#transaction-member-info').find('.td-member-name').text(transaction.member.name);
                $('#transaction-member-info').find('.td-member-phone').text(transaction.member.phone);
                $('#transaction-member-info').find('.td-member-address').text(transaction.member.address);
                $('#transaction-member-info').find('.td-member-since').text(transaction.member.created_at);
                $('#transaction-items-table tbody').html(rows);

                if (transaction.payment_status == 'paid') {
                    $('#transaction-detail-modal').find('#button-container').html(
                        `<a href="/o/${outletId}/transactions/${transaction.id}/invoice" class="btn btn-success w-100 mt-2 mb-0 print-invoice-button">
                            <i class="fas fa-print mr-1"></i>
                            <span>Cetak Invoice</span>
                        </a>`);
                } else {

                }
                $('[name="modal_tab"][value="items"]').attr('checked', true).trigger('change');

                $('#transaction-detail-modal').modal('show');
            } catch (err) {
                toast('error', 'Terjadi kesalahan');
                $('#transaction-detail-modal').modal('hide');
            }
        });

        $('[name="modal_tab"]').on('change', function() {
            if ($(this).val() == 'member') {
                $('.nav-link.tab-member').addClass('active');
                $('.nav-link.tab-items').removeClass('active');
                $('#transaction-member-info').removeClass('d-none');
                $('#transaction-items-info').removeClass('d-block');
                $('#transaction-member-info').addClass('d-block');
                $('#transaction-items-info').addClass('d-none');
            } else {
                $('.nav-link.tab-member').removeClass('active');
                $('.nav-link.tab-items').addClass('active');
                $('#transaction-member-info').addClass('d-none');
                $('#transaction-member-info').removeClass('d-block');
                $('#transaction-items-info').addClass('d-block');
                $('#transaction-items-info').removeClass('d-none');
            }
        });

        $('[name="status_tab"]').on('change', function() {
            console.log('ok');
            let status = $(this).val();
            console.log(status);
            $.each($('.nav-link-status'), (i, el) => {
                $(el).removeClass('active');
            });

            switch (status) {
                case 'process':
                    $('.nav-link.status-process').addClass('active');
                    break;
                case 'done':
                    $('.nav-link.status-done').addClass('active');
                    break;
                case 'taken':
                    $('.nav-link.status-taken').addClass('active');
                    break;
                default:
                    status = 'new';
                    $('.nav-link.status-new').addClass('active');
                    break;
            }
            table.ajax.url(`${datatableUrl}?status=${status}`).load();
        });
    </script>
@endpush
