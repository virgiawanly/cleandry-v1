@extends('layouts.main')

@push('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data layanan</h3>
                    <div class="card-tools">
                        <button class="btn btn btn-primary" onclick="createHandler('{{ route('services.store') }}')">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah layanan</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="servicesTable" class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Layanan</th>
                                <th>Jenis</th>
                                <th>Satuan</th>
                                <th>Harga</th>
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
    <div class="modal fade" role="dialog" id="formModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="#" onsubmit="submitHandler()" method="POST">
                    @method('post')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Register User Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Nama layanan">
                        </div>
                        <div class="form-group">
                            <label for="typeId">Jenis</label>
                            <select name="type_id" class="form-control select2" id="typeId">
                                <option value=""></option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role">Satuan</label>
                            <div class="d-flex align-items-center" style="gap: 15px">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="unit" id="unitKg" value="kg">
                                    <label class="form-check-label" for="unitKg">
                                        Kg
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="unit" id="unitMeter" value="m">
                                    <label class="form-check-label" for="unitMeter">
                                        Meter<sup>2</sup>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="unit" id="unitPcs" value="pcs">
                                    <label class="form-check-label" for="unitPcs">
                                        Pcs
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="price">Harga</label>
                            <input type="text" name="price" class="form-control" id="price" placeholder="Harga">
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
@endpush

@push('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('adminlte') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js">
    </script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js">
    </script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js">
    </script>
    <!-- Select2 -->
    <script src="{{ asset('adminlte') }}/plugins/select2/js/select2.full.min.js"></script>
    <!-- Page Script -->
    <script>
        let table;
        $(function() {
            const tableOptions = {
                responsive: true,
                ajax: {
                    url: '/services/datatable'
                },
                columns: [{
                        data: 'DT_RowIndex',
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: 'type',
                        render: (type) => type && type.name ? type.name : '-',
                    },
                    {
                        data: 'unit',
                    },
                    {
                        data: 'price',
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false,
                    }
                ]
            }
            table = $('#servicesTable').DataTable(tableOptions);
            //Initialize Select2 Elements
            $('.select2').select2({
                placeholder: "Pilih jenis",
                theme: 'bootstrap4'
            });
        });

        const createHandler = function(url) {
            clearErrors();
            const modal = $('#formModal');
            modal.modal('show');
            modal.find('.modal-title').text('Buat layanan baru');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('post');
            modal.on('shown.bs.modal', function() {
                modal.find('[name=name]').focus();
            });
        }

        const editHandler = (url) => {
            clearErrors();
            const modal = $('#formModal');
            modal.modal('show');
            modal.find('.modal-title').text('Edit layanan');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('put');
            modal.find('input').attr('disabled', true);
            modal.find('select').attr('disabled', true);
            modal.on('shown.bs.modal', function() {
                modal.find('[name=name]').focus();
            });

            $.get(url)
                .done((res) => {
                    const service = res.service;
                    modal.find('[name=name]').val(service.name);
                    modal.find('[name=price]').val(service.price);
                    modal.find(`[name=type_id]`).val(service.type_id).trigger('change');
                    modal.find(`[name=unit][value='${service.unit}']`).prop('checked', true);
                })
                .fail((err) => {
                    toaster.fire({
                        icon: 'error',
                        title: 'Tidak dapat mengambil data'
                    });
                }).always(() => {
                    modal.find('input').attr('disabled', false);
                    modal.find('select').attr('disabled', false);
                });
        }

        const submitHandler = function() {
            event.preventDefault();
            const url = $('#formModal form').attr('action');
            const formData = $('#formModal form').serialize();
            $.post(url, formData)
                .done((res) => {
                    $('#formModal').modal('hide');
                    table.ajax.reload();
                    toaster.fire({
                        icon: 'success',
                        title: res.message
                    });
                }).fail((err) => {
                    if (err.status === 422) validationErrorHandler(err.responseJSON.errors);
                    toaster.fire({
                        icon: 'error',
                        title: 'Data gagal disimpan'
                    });
                });
        }

        const deleteHandler = function(url) {
            Swal.fire({
                title: 'Hapus Layanan',
                text: 'Anda yakin ingin menghapus layanan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6C757D',
                cancelButtonColor: '#037AFC',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (!result.isConfirmed) return;
                return $.post(url, {
                    '_token': $('[name=_token]').val(),
                    '_method': 'delete'
                }).then((res) => {
                    table.ajax.reload();
                    toaster.fire({
                        icon: 'success',
                        title: res.message
                    });
                }).catch((err) => {
                    toaster.fire({
                        icon: 'error',
                        title: err.responseJSON.message ?? 'Error'
                    });
                });
            });
        }
    </script>
@endpush
