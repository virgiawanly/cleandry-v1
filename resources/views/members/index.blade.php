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
                    <h3 class="card-title">Data member</h3>
                    <div class="card-tools">
                        <button class="btn btn btn-primary" onclick="createHandler('{{ route('members.store') }}')">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah member</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="membersTable" class="table table-hover table-striped">
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
    <div class="modal fade" role="dialog" id="formModal">
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
                            <label for="gender">Jenis Kelamin</label>
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
                            <textarea name="address" class="form-control" id="address" rows="3"
                                placeholder="Alamat lengkap"></textarea>
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
                    url: '/members/datatable'
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
                        data: 'email',
                    },
                    {
                        data: 'gender',
                        render: (gender) => gender == 'M' ? 'Laki-laki' : 'Perempuan'
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
            table = $('#membersTable').DataTable(tableOptions);
        });

        const createHandler = function(url) {
            clearErrors();
            const modal = $('#formModal');
            modal.modal('show');
            modal.find('.modal-title').text('Tambah member baru');
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
            modal.find('.modal-title').text('Edit member');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('put');
            modal.find('input').attr('disabled', true);
            modal.on('shown.bs.modal', function() {
                modal.find('[name=name]').focus();
            });

            $.get(url)
                .done((res) => {
                    const member = res.member;
                    modal.find('[name=name]').val(member.name);
                    modal.find('[name=phone]').val(member.phone);
                    modal.find('[name=email]').val(member.email);
                    modal.find('[name=address]').val(member.address);
                    modal.find(`[name=gender][value='${member.gender}']`).prop('checked', true);
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
                        title: 'Terjadi kesalahan'
                    });
                });
        }
    </script>
@endpush
