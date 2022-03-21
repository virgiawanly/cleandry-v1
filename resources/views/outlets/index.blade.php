@extends('layouts.main')

@push('head')
    @include('layouts.datatable_styles')
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data outlet</h3>
                    <div class="card-tools">
                        <div class="dropdown d-inline">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-upload mr-1"></i>
                                <span>Export</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('outlets.export.excel') }}">XLSX</a>
                                <a class="dropdown-item" href="{{ route('outlets.export.pdf') }}">PDF</a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#import-modal">
                            <i class="fas fa-download mr-1"></i><span>Import</span>
                        </button>
                        <button class="btn btn btn-primary" onclick="createHandler('{{ route('outlets.store') }}')">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah Outlet</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table id="outlets-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Outlet</th>
                                <th>Nomor Telepon</th>
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
                        <h5 class="modal-title">Tambah Outlet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama outlet</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Outlet">
                        </div>
                        <div class="form-group">
                            <label for="phone">Nomor telepon</label>
                            <input type="tel" name="phone" class="form-control" id="phone" placeholder="08XX XXXX XXXX">
                        </div>
                        <div class="form-group">
                            <label for="address">Alamat</label>
                            <textarea name="address" id="address" rows="4" class="form-control" placeholder="Alamat lengkap oulet"></textarea>
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
            <form action="{{ route('outlets.import.excel') }}" method="POST" enctype="multipart/form-data"
                id="import-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="import-modal-label">Import Data Outlet</h5>
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
                            <div>Klik <a href="{{ route('outlets.template.download') }}">disini</a> untuk
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
    <script>
        let table;

        $(function() {
            table = $('#outlets-table').DataTable({
                ...DATATABLE_OPTIONS,
                ajax: '/outlets/datatable',
                columns: [{
                        data: 'DT_RowIndex',
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: 'phone',
                    },
                    {
                        data: 'address',
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false,
                    }
                ]
            });

            $("#import-form").on("submit", importHandler);

            $('[name="file_import"]').on("change", () => {
                let filename = $("#file-import")[0].files[0].name;
                let filesize = $("#file-import")[0].files[0].size;
                $(".filename").text(filename ?? "");
                $(".filesize").text(formatBytes(filesize) ?? "");
                $("#import-file-card").removeClass("d-none");
                $("#import-file-card").addClass("d-flex");
                $("#select-import-file").addClass("d-none");
                $("#select-import-file").removeClass("d-block");
            });

            $("#remove-import-file").on("click", removeImportFile);
        });

        const createHandler = (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Buat Outlet Baru');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('post');
        }

        const editHandler = async (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Edit Outlet');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('put');
            modal.find('input').attr('disabled', true);
            modal.find('select').attr('disabled', true);

            try {
                let res = await fetchData(url);
                modal.find('[name=name]').val(res.outlet.name);
                modal.find('[name=phone]').val(res.outlet.phone);
                modal.find('[name=address]').val(res.outlet.address);
            } catch (err) {
                toast('Tidak dapat mengambil data', 'error');
            }

            modal.find('input').attr('disabled', false);
            modal.find('select').attr('disabled', false);
        }

        const submitHandler = async () => {
            event.preventDefault();
            const url = $('#form-modal form').attr('action');
            const formData = $('#form-modal form').serialize();
            try {
                let res = await $.post(url, formData);
                $('#form-modal').modal('hide');
                toast(res.message, 'success');
                table.ajax.reload();
            } catch (err) {
                if (err.status === 422) validationErrorHandler(err.responseJSON.errors);
                toast('Terjadi kesalahan', 'error');
            }
        }

        const deleteHandler = async (url) => {
            let result = await Swal.fire({
                title: 'Hapus Outlet',
                text: 'Anda yakin ingin menghapus outlet ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6C757D',
                cancelButtonColor: '#037AFC',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            });
            if (result.isConfirmed) {
                try {
                    let res = await $.post(url, {
                        '_token': $('[name=_token]').val(),
                        '_method': 'delete'
                    });
                    toast(res.message, 'success');
                    table.ajax.reload();
                } catch (err) {
                    toast('Terjadi kesalahan', 'error');
                }
            }
        }

        const importHandler = async () => {
            event.preventDefault();
            let url = $("#import-form").attr("action");
            let formData = new FormData();
            formData.append("file_import", $("#file-import")[0].files[0]);
            $("#import-modal").modal("hide");
            try {
                let res = await $.ajax({
                    method: "post",
                    headers: {
                        "X-CSRF-Token": $("[name=_token]").val(),
                    },
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: formData,
                    enctype: "multipart/form-data",
                    url: url,
                });
                toast(res.message, "success");
                table.ajax.reload();
                removeImportFile();
            } catch (err) {
                console.log(err.responseJSON);
                toast(
                    err.status === 422 ? "File tidak valid" : "Terjadi kesalahan",
                    "error"
                );
            }
        };

        const removeImportFile = () => {
            $("#file-import").val(null);
            $(".filename").val("");
            $(".filesize").val("");
            $("#import-file-card").addClass("d-none");
            $("#import-file-card").removeClass("d-flex");
            $("#select-import-file").removeClass("d-none");
            $("#select-import-file").addClass("d-block");
        };
    </script>
@endpush
