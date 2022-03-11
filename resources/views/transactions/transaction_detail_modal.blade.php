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
                                    <th>Kode Faktur</th>
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
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
