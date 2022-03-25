@extends('layouts.main')

@push('head')
    @include('layouts.datatable_styles')
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Barang</h3>
                    <div class="card-tools">
                        <div class="dropdown d-inline">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-upload mr-1"></i>
                                <span>Export</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('items.export.excel') }}">XLSX</a>
                                <a class="dropdown-item" href="{{ route('items.export.pdf') }}">PDF</a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#import-modal">
                            <i class="fas fa-download mr-1"></i><span>Import</span>
                        </button>
                        <button class="btn btn btn-primary" id="add-item-button"
                            data-create-item-url="{{ route('items.store') }}">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah Barang</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table id="items-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Waktu Beli</th>
                                <th>Supplier</th>
                                <th>Status Barang</th>
                                <th>Waktu Update Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data goes here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('bottom')
    <div class="modal fade" role="dialog" id="form-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="#" id="item-form" method="POST">
                    @method('post')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Barang</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama Barang</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Nama barang">
                        </div>
                        <div class="form-group">
                            <label for="qty">Kuantitas</label>
                            <input type="number" min="0" name="qty" class="form-control" id="qty"
                                placeholder="Jumlah barang">
                        </div>
                        <div class="form-group">
                            <label for="price">Harga</label>
                            <input type="number" min="0" name="price" class="form-control" id="price"
                                placeholder="Harga barang">
                        </div>
                        <div class="form-group">
                            <label for="buy-date">Waktu Beli</label>
                            <input type="date" name="buy_date" class="form-control" id="buy-date">
                        </div>
                        <div class="form-group">
                            <label for="supplier">Supplier</label>
                            <input type="text" name="supplier" class="form-control" id="supplier" placeholder="Supplier">
                        </div>
                        <div class="form-group">
                            <label for="">Kondisi Barang</label>
                            <div class="d-flex align-items-center" style="gap: 15px">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusSubmission"
                                        value="submission">
                                    <label class="form-check-label" for="statusSubmission">
                                        Diajukan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusOutOfStock"
                                        value="out_of_stock">
                                    <label class="form-check-label" for="statusOutOfStock">
                                        Habis
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusAvailable"
                                        value="available">
                                    <label class="form-check-label" for="statusAvailable">
                                        Tersedia
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="submit" class="btn btn-primary modal-submit-button">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="import-modal" tabindex="-1" aria-labelledby="import-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('items.import.excel') }}" method="POST" enctype="multipart/form-data"
                id="import-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="import-modal-label">Import Barang Inventaris</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="d-none align-items-center justify-content-between bg-white p-2 shadow-md mb-3"
                            id="import-file-card">
                            <div class="d-flex align-items-center">
                                <div class="h1 p-3 mb-0"><i class="fa fa-file-excel"></i></div>
                                <div>
                                    <h6 class="mb-0 filename">File.xlsx</h6>
                                    <div class="text-sm filesize">30kb</div>
                                </div>
                            </div>
                            <div class="p-3">
                                <button type="button" class="close" id="remove-import-file">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="text-center py-5" id="select-import-file">
                            <div>Upload file</div>
                            <label for="file-import" class="btn btn-info mt-1 font-weight-normal">
                                <span>Pilih file</span>
                            </label>
                            <div>Klik <a href="{{ route('items.template.download') }}">disini</a> untuk
                                mengunduh
                                template</div>
                        </div>
                        <input type="file" class="custom-file-input" id="file-import" name="file_import" hidden>
                        <button class="btn btn-primary w-100"><i class="fas fa-download mr-2"></i>Import data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endpush

@push('script')
    @include('layouts.datatable_scripts')

    <!-- Page Script -->
    <script src="{{ asset('js/pages/items.js') }}"></script>
@endpush
