@extends('layouts.main')

@push('head')
    @include('layouts.datatable_styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <style>
        /* Labels for checked inputs */
        input[name="role"]+label {
            border: 1px solid #CED4DA;
            cursor: pointer;
            transition: .3s;
            box-shadow: none;
        }

        input[name="role"]:checked+label {
            background-color: #007BFF;
            color: #ffffff;
            border: 1px solid #007BFF;
            box-shadow: 1px 0 8px rgba(0, 0, 0, 0.1);
        }

    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data user</h3>
                    <div class="card-tools">
                        <button class="btn btn btn-primary" onclick="createHandler('{{ route('users.store') }}')">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah User</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table id="users-table" class="table table-hover table-striped w-100">
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
    <div class="modal fade" role="dialog" id="form-modal">
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
                            <label for="phone">Nomor telepon</label>
                            <input type="tel" name="phone" class="form-control" id="phone" placeholder="08XX XXXX XXXX">
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password-confirmation">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    id="password-confirmation">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="outlet-id">Outlet</label>
                            <select name="outlet_id" class="form-control select2" id="outlet-id">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <div class="row">
                                <div class="col-sm-4">
                                    <input class="d-none" type="radio" name="role" id="role-admin" value="admin">
                                    <label for="role-admin" class="card">
                                        <div class="card-body">
                                            <i class="fas fa-user-secret mr-1"></i>
                                            <span>Admin</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <input class="d-none" type="radio" name="role" id="role-owner" value="owner">
                                    <label for="role-owner" class="card">
                                        <div class="card-body">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            <span>Owner</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <input class="d-none" type="radio" name="role" id="role-cashier"
                                        value="cashier">
                                    <label for="role-cashier" class="card">
                                        <div class="card-body">
                                            <i class="fas fa-user mr-1"></i>
                                            <span>Kasir</span>
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
@endpush

@push('script')
    @include('layouts.datatable_scripts')
    <!-- Select2 -->
    <script src="{{ asset('adminlte') }}/plugins/select2/js/select2.full.min.js"></script>
    <!-- Page Script -->
    <script>
        let table;
        let outletOptions;

        $(function() {
            table = $('#users-table').DataTable({
                ...DATATABLE_OPTIONS,
                ajax: '/users/datatable',
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
                        render: (role) => {
                            let type;
                            let text;
                            switch (role) {
                                case 'admin':
                                    type = 'primary';
                                    text = 'Admin';
                                    break;
                                case 'owner':
                                    type = 'info';
                                    text = 'Owner';
                                    break;
                                default:
                                    type = 'success';
                                    text = 'Kasir';
                            }
                            return `<span class="badge badge-${type}">${text}</span>`;
                        }
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false,
                    }
                ]
            });
            //Initialize Select2 Elements
            fetchOutletOptions();
        });

        const fetchOutletOptions = async () => {
            try {
                let res = await fetchData('/outlets/data');
                let outletOptions = res.outlets.map((outlet, index) => {
                    return {
                        id: outlet.id,
                        text: outlet.name,
                    }
                });
                $('#outlet-id').select2({
                    placeholder: "Pilih outlet",
                    theme: 'bootstrap4',
                    data: outletOptions,
                });
            } catch (err) {
                toast('Tidak dapat mengambil data outlet', 'error');
            }
        }

        const createHandler = function(url) {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Register User Baru');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('post');
        }

        const editHandler = async (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Edit User');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('put');
            modal.find('input').attr('disabled', true);
            modal.find('select').attr('disabled', true);

            try {
                let res = await fetchData(url);
                modal.find('[name=name]').val(res.user.name);
                modal.find('[name=phone]').val(res.user.phone);
                modal.find('[name=email]').val(res.user.email);
                modal.find(`[name=outlet_id]`).val(res.user.outlet_id).trigger('change');
                modal.find(`[name=role][value='${res.user.role}']`).prop('checked', true);
            } catch (err) {
                toast('Tidak dapat mengambil data', 'error');
            }

            modal.find('input').attr('disabled', false);
            modal.find('select').attr('disabled', false);
        }

        const submitHandler = async () => {
            event.preventDefault();
            let url = $('#form-modal form').attr('action');
            let formData = $('#form-modal form').serialize();
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
                title: 'Hapus User',
                text: 'Anda yakin ingin menghapus user ini?',
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
    </script>
@endpush
