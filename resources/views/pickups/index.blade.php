@extends('layouts.main')

@push('head')
    <meta name="outlet-id" content="{{ $outlet->id }}">
    <meta name="data-pickups-url" content="{{ route('pickups.datatable', $outlet->id) }}">
    @include('layouts.datatable_styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

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
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Penjemputan Outlet : {{ $outlet->name }}</h3>
            <div class="card-tools">
                <div class="dropdown d-inline">
                    <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-upload mr-1"></i>
                        <span>Export</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('pickups.export.excel', $outlet->id) }}">XLSX</a>
                        <a class="dropdown-item" href="{{ route('pickups.export.pdf', $outlet->id) }}">PDF</a>
                    </div>
                </div>
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#import-modal">
                    <i class="fas fa-download mr-1"></i><span>Import</span>
                </button>
                <button id="add-pickup-button" class="btn btn-primary"
                    data-create-pickups-url="{{ route('pickups.store', $outlet->id) }}"><i
                        class="far fa-plus-square mr-1"></i><span>Tambah
                        Penjemputan</span></button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="pickups-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat Pelanggan</th>
                        <th>Nomor Handphone</th>
                        <th>Petugas Penjemputan</th>
                        <th>Status</th>
                        <th>Ditambahkan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data goes here -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('bottom')
    <div class="modal fade" role="dialog" id="form-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="#" id="pickup-form" method="POST">
                    @method('post')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Penjemputan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="member">Pelanggan</label>
                            <select name="member_id" class="form-control select2" id="">
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} - {{ $member->address }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="courier">Nama Petugas Penjemputan</label>
                            <input type="text" class="form-control" name="courier" id="courier">
                        </div>
                        <div class="form-group">
                            <label for="status">Status Pengiriman</label>
                            <div class="row">
                                <div class="col-sm-4">
                                    <input class="d-none" type="radio" name="status" id="status-noted" value="noted"
                                        checked>
                                    <label for="status-noted" class="card">
                                        <div class="card-body">
                                            <i class="fas fa-file mr-1"></i>
                                            <span>Tercatat</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <input class="d-none" type="radio" name="status" id="status-process"
                                        value="process">
                                    <label for="status-process" class="card">
                                        <div class="card-body">
                                            <i class="fas fa-truck mr-1"></i>
                                            <span>Penjemputan</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <input class="d-none" type="radio" name="status" id="status-done" value="done">
                                    <label for="status-done" class="card">
                                        <div class="card-body">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <span>Selesai</span>
                                        </div>
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

    <!-- Modal -->
    <div class="modal fade" id="import-modal" tabindex="-1" aria-labelledby="import-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('pickups.import.excel', $outlet->id) }}" method="POST" enctype="multipart/form-data"
                id="import-form">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="import-modal-label">Import Data Penjemputan</h5>
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
                            <div>Klik <a href="{{ route('pickups.template.download') }}">disini</a> untuk
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

    <script>
        const dataUrl = $('meta[name="data-pickups-url"]').attr('content');

        $(function() {
            $(".select2").select2({
                placeholder: "Pilih jenis",
                theme: "bootstrap4",
            });

            table = $("#pickups-table").DataTable({
                responsive: true,
                pageLength: 25,
                ajax: dataUrl,
                columns: [{
                        data: "DT_RowIndex",
                    },
                    {
                        data: "member.name",
                    },
                    {
                        data: "member.address",
                    },
                    {
                        data: "member.phone",
                    },
                    {
                        data: "courier",
                    },
                    {
                        data: "update_status",
                    },
                    {
                        data: "created_at",
                    },
                    {
                        data: "actions",
                        searchable: false,
                        sortable: false,
                    },
                ],
            });

            // Event handlers
            $("#add-pickup-button").on("click", function() {
                let url = $(this).data("create-pickups-url");
                createHandler(url);
            });

            $("#pickups-table").on("click", ".edit-pickup-button", function() {
                let url = $(this).data("update-url");
                editHandler(url);
            });

            $("#pickups-table").on("click", ".delete-pickup-button", function() {
                let url = $(this).data("delete-url");
                deleteHandler(url);
            });

            $("#pickups-table").on("change", "select.pickup-status", async function() {
                let url = $(this).data("update-url");
                try {
                    let res = await $.post(url, {
                        _token: $("[name=_token]").val(),
                        _method: "PUT",
                        status: $(this).val()
                    });
                    toast(res.message, "success");
                } catch (err) {
                    toast("Terjadi kesalahan", "error");
                }
            });

            $('#pickup-form').on('submit', submitHandler);

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
            const modal = $("#form-modal");
            modal.modal("show");
            modal.find(".modal-title").text("Tambah Penjemputan Baru");
            modal.find("form")[0].reset();
            modal.find("form").attr("action", url);
            modal.find("[name=_method]").val("post");
        };

        const editHandler = async (url) => {
            clearErrors();
            const modal = $("#form-modal");
            modal.modal("show");
            modal.find(".modal-title").text("Edit data penjemputan");
            modal.find("form")[0].reset();
            modal.find("form").attr("action", url);
            modal.find("[name=_method]").val("put");
            modal.find("input").attr("disabled", true);
            modal.find("select").attr("disabled", true);

            try {
                let res = await fetchData(url);
                modal.find("[name=courier]").val(res.pickup.courier);
                modal.find("[name=member_id]").val(res.pickup.member_id).trigger('change');
                modal
                    .find(`[name=status][value='${res.pickup.status}']`)
                    .prop("checked", true);
            } catch (err) {
                toast("Tidak dapat mengambil data", "error");
                $("#form-modal").modal("hide");
            }

            modal.find("input").attr("disabled", false);
            modal.find("select").attr("disabled", false);
        };

        const submitHandler = async () => {
            event.preventDefault();
            clearErrors();
            let url = $("#form-modal form").attr("action");
            let formData = $("#form-modal form").serialize();
            try {
                let res = await $.post(url, formData);
                $("#form-modal").modal("hide");
                toast(res.message, "success");
                table.ajax.reload();
            } catch (err) {
                console.log(err);
                if (err.status === 422) validationErrorHandler(err.responseJSON.errors);
                toast(err.responseJSON.message ?? "Terjadi kesalahan", "error");
            }
        };

        const deleteHandler = async (url) => {
            let result = await Swal.fire({
                title: "Hapus Layanan",
                text: "Anda yakin ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#6C757D",
                cancelButtonColor: "#037AFC",
                confirmButtonText: "Hapus",
                cancelButtonText: "Batal",
            });
            if (result.isConfirmed) {
                try {
                    let res = await $.post(url, {
                        _token: $("[name=_token]").val(),
                        _method: "delete",
                    });
                    toast(res.message, "success");
                    table.ajax.reload();
                } catch (err) {
                    toast("Terjadi kesalahan", "error");
                }
            }
        };

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
