@extends('layouts.main')

@push('head')
    <meta name="outlet-id" content="{{ $outlet->id }}">
    @include('layouts.datatable_styles')
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="mb-0">Data Member</h5>
                        <div class="text-sm">Outlet : {{ $outlet->name }}</div>
                    </div>
                    <div class="card-tools">
                        @if (Auth::user()->role === 'admin')
                            <a href="/select-outlet" class="btn btn-info"><i class="fas fa-exchange-alt mr-1"></i>Ganti
                                Outlet</a>
                        @endif
                        <div class="dropdown d-inline">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-upload mr-1"></i>
                                <span>Export</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('members.export.excel', $outlet->id) }}">XLSX</a>
                                <a class="dropdown-item" href="{{ route('members.export.pdf', $outlet->id) }}">PDF</a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#import-modal">
                            <i class="fas fa-download mr-1"></i><span>Import</span>
                        </button>
                        <button class="btn btn btn-primary"
                            onclick="createHandler('{{ route('members.store', [$outlet->id]) }}')">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah Member</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table id="members-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Nomor Telepon</th>
                                <th>Email</th>
                                <th>Jenis Kelamin</th>
                                <th>Alamat</th>
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
                <form action="#" onsubmit="submitHandler()" method="POST">
                    @method('post')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Member</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Nama lengkap">
                        </div>
                        <div class="form-group">
                            <label for="phone">Nomor telepon</label>
                            <input type="tel" name="phone" class="form-control" id="phone" placeholder="name@domain.com">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" name="email" class="form-control" id="email" placeholder="name@domain.com">
                        </div>
                        <div class="form-group">
                            <label for="gender">J.K</label>
                            <div class="d-flex align-items-center" style="gap: 15px">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genderMale" value="M">
                                    <label class="form-check-label" for="genderMale">
                                        Laki-laki
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="F">
                                    <label class="form-check-label" for="genderFemale">
                                        Perempuan
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <textarea name="address" class="form-control" id="address" rows="3" placeholder="Alamat lengkap"></textarea>
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

    <!-- Modal -->
    <div class="modal fade" id="import-modal" tabindex="-1" aria-labelledby="import-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('members.import.excel', $outlet->id) }}" method="POST" enctype="multipart/form-data"
                id="import-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="import-modal-label">Import Member</h5>
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
                            <div>Klik <a href="{{ route('members.template.download') }}">disini</a> untuk
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
    <script src="{{ asset('js/pages/members.js') }}"></script>
@endpush
