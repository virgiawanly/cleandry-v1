@extends('layouts.main')

@push('head')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data outlet</h3>
                    <div class="card-tools">
                        <button class="btn btn btn-primary" onclick="createHandler('{{ route('outlets.store') }}')">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah outlet</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="outletTable" class="table table-hover table-striped">
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
    <div class="modal fade" role="dialog" id="formModal">
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
                            <textarea name="address" id="address" rows="4" class="form-control"
                                placeholder="Alamat lengkap oulet"></textarea>
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
    <!-- Page Script -->
    <script>
        let table;
        $(function() {
            const tableOptions = {
                responsive: true,
                ajax: {
                    url: '/outlets/datatable'
                },
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
            }
            table = $('#outletTable').DataTable(tableOptions);
        });

        const createHandler = function(url) {
            clearErrors();
            const modal = $('#formModal');
            modal.modal('show');
            modal.find('.modal-title').text('Buat outlet baru');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('post');
            modal.on('shown.bs.modal', function() {
                modal.find('[name=name]').focus();
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
                        title: 'Terjadi kesalahan'
                    });
                });
        }
    </script>
@endpush
