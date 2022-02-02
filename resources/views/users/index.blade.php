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
                    <h3 class="card-title">Data user</h3>
                    <div class="card-tools">
                        <button class="btn btn btn-primary" onclick="createHandler('{{ route('users.store') }}')">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah user</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="usersTable" class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Nomor Telepon</th>
                                <th>Outlet</th>
                                <th>Role</th>
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
                            <input type="text" name="name" class="form-control" id="name" placeholder="Nama Lengkap">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" name="email" class="form-control" id="email" placeholder="name@domain.com">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control" id="password">
                        </div>
                        <div class="form-group">
                            <label for="passwordConfirmation">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                id="passwordConfirmation">
                        </div>
                        <div class="form-group">
                            <label for="phone">Nomor telepon</label>
                            <input type="tel" name="phone" class="form-control" id="phone" placeholder="08XX XXXX XXXX">
                        </div>
                        <div class="form-group">
                            <label for="outletId">Outlet</label>
                            <select name="outlet_id" class="form-control select2" id="outletId">
                                <option value="" selected></option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <div class="d-flex align-items-center" style="gap: 15px">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="roleOwner" value="owner">
                                    <label class="form-check-label" for="roleOwner">
                                        Owner
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin">
                                    <label class="form-check-label" for="roleAdmin">
                                        Admin
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="roleCashier"
                                        value="cashier">
                                    <label class="form-check-label" for="roleCashier">
                                        Kasir
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
                    url: '/users/datatable'
                },
                columns: [{
                        data: 'DT_RowIndex',
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: 'email',
                    },
                    {
                        data: 'phone',
                    },
                    {
                        data: 'outlet',
                        render: (outlet) => outlet && outlet.name ? outlet.name : '-',
                    },
                    {
                        data: 'role',
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false,
                    }
                ]
            }
            table = $('#usersTable').DataTable(tableOptions);
            //Initialize Select2 Elements
            $('.select2').select2({
                placeholder: "Pilih outlet",
                theme: 'bootstrap4'
            });
        });

        const createHandler = function(url) {
            clearErrors();
            const modal = $('#formModal');
            modal.modal('show');
            modal.find('.modal-title').text('Buat user baru');
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
