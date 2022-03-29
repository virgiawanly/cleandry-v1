@extends('layouts.main')

@push('head')
    @include('layouts.datatable_styles')
    <style>
        /* Labels for checked inputs */
        input[name="status"]+label {
            border: 2px solid #CED4DA;
            cursor: pointer;
            transition: .3s;
            box-shadow: none;
        }

        input[name="status"]:checked+label {
            background-color: rgba(23, 162, 184, 0.1);
            color: rgba(23, 162, 184);
            border: 1px solid rgba(23, 162, 184);
            box-shadow: none;
        }

    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Penggunaan Barang</h3>
                    <div class="card-tools">
                        <div class="dropdown d-inline">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-upload mr-1"></i>
                                <span>Export</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('uses.export.excel') }}">XLSX</a>
                                <a class="dropdown-item" href="{{ route('uses.export.pdf') }}">PDF</a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#import-modal">
                            <i class="fas fa-download mr-1"></i><span>Import</span>
                        </button>
                        <button class="btn btn btn-primary" id="add-item-button"
                            data-create-item-url="{{ route('uses.store') }}">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah Data</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table id="items-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Waktu Mulai Pakai</th>
                                <th>Waktu Beres</th>
                                <th>Nama Pemakai</th>
                                <th>Status</th>
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
                        <h5 class="modal-title">Tambah Data Penggunaan Barang</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="item-name">Nama Barang</label>
                            <input type="text" name="item_name" class="form-control" id="item-name">
                        </div>
                        <div class="form-group">
                            <label for="username">Nama Pemakai</label>
                            <input type="text" name="user_name" class="form-control" id="username">
                        </div>
                        <div class="form-group">
                            <label for="start-use">Waktu Mulai Pakai</label>
                            <input type="datetime-local" name="start_use" class="form-control" id="start-use">
                        </div>
                        <div class="form-group">
                            <label for="status">Status Pemakaian</label>
                            <div class="row">
                                <div class="col-6">
                                    <input class="d-none" type="radio" name="status" id="status-in-use"
                                        value="in_use" checked>
                                    <label for="status-in-use" class="card">
                                        <div class="card-body">
                                            <i class="fas fa-user-secret mr-1"></i>
                                            <span>Digunakan / Belum Selesai</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input class="d-none" type="radio" name="status" id="status-finish"
                                        value="finish">
                                    <label for="status-finish" class="card">
                                        <div class="card-body">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            <span>Selesai</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end-use">Waktu Selesai Pakai</label>
                            <input type="datetime-local" name="end_use" class="form-control" id="end-use" disabled>
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
            <form action="{{ route('uses.import.excel') }}" method="POST" enctype="multipart/form-data" id="import-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="import-modal-label">Import Penggunaan Barang</h5>
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
                            <div>Klik <a href="{{ route('uses.template.download') }}">disini</a> untuk
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
    <!-- Select2 -->
    <script src="{{ asset('adminlte') }}/plugins/select2/js/select2.full.min.js"></script>
    <!-- Page Script -->
    <script src="{{ asset('js/pages/item_uses.js') }}"></script>
@endpush
