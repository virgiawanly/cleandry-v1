<div class="modal fade" id="update-payment-modal" tabindex="-1" aria-labelledby="update-payment-modal-label"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="update-payment-modal-label">Update Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="card shadow-none w-100">
                    <div class="card-body pb-0">
                        <table class="table table-borderless table-sm mb-3" id="payment-info-table">
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
                        <table class="table mb-3" id="payment-items-table">
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
                        <hr>
                        <form action="#" id="update-payment-form">
                            @csrf
                            @method('put')
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Total Harga</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control o-total-price" readonly>
                                    <input type="number" class="int-total-price" hidden>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Diskon</label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="number" name="discount" class="form-control">
                                        <select name="discount_type" class="form-control" style="max-width: 25%">
                                            <option value="percent">Persen (%)</option>
                                            <option value="nominal">Nominal (Rp)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Pajak</label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="number" name="tax" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Biaya Tambahan</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="number" name="additional_cost" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label>Total Bayar</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control o-total-payment" readonly>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary w-100">Bayar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
