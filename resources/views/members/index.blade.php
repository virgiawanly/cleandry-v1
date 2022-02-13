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
                        @if (Auth::user()->is_super)
                            <a href="/select-outlet" class="btn btn-info"><i class="fas fa-exchange-alt mr-1"></i>Ganti
                                Outlet</a>
                        @endif
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
    @include('layouts.datatable_scripts')
    <!-- Page Script -->
    <script>
        let table;

        $(function() {
            const outletId = $("meta[name='outlet-id']").attr("content");
            table = $('#members-table').DataTable({
                ...DATATABLE_OPTIONS,
                ajax: `/o/${outletId}/members/datatable`,
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
            });
        });

        const createHandler = (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Register Member Baru');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('post');
        }

        const editHandler = async (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Edit member');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('put');
            modal.find('input').attr('disabled', true);

            try {
                let res = await fetchData(url);
                modal.find('[name=name]').val(res.member.name);
                modal.find('[name=phone]').val(res.member.phone);
                modal.find('[name=email]').val(res.member.email);
                modal.find('[name=address]').val(res.member.address);
                modal.find(`[name=gender][value='${res.member.gender}']`).prop('checked', true);
            } catch (err) {
                toast('Tidak dapat mengambil data', 'error');
                $('#form-modal').modal('hide');
            }

            modal.find('input').attr('disabled', false);
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
                title: 'Hapus Member',
                text: 'Anda yakin ingin menghapus member ini?',
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
