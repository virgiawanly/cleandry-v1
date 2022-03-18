@extends('layouts.main')

@push('head')
    @include('layouts.datatable_styles')
    <meta name="datatable-url" content="{{ route('service-types.datatable') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Jenis Cucian</h3>
            <div class="card-tools">
                <button class="btn btn-primary" id="add-type-button" data-create-url={{ route('service-types.store') }}><i
                        class="far fa-plus-square mr-1"></i>Tambah</button>
            </div>
        </div>
        <div class="card-body p-0">
            <table id="service-types-table" class="table table-hover table-striped w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jenis Cucian</th>
                        <th></th>
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
                <form action="#" method="POST">
                    @method('post')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jenis Cucian</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Jenis Cucian</label>
                            <input type="text" name="name" class="form-control" id="name">
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
            let datatableUrl = $('meta[name="datatable-url"]').attr('content');
            table = $('#service-types-table').DataTable({
                ...DATATABLE_OPTIONS,
                ajax: datatableUrl,
                columns: [{
                        data: 'DT_RowIndex',
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false,
                    }
                ]
            });

            $('#add-type-button').on('click', function() {
                let url = $(this).data('create-url');
                createHandler(url);
            });

            $('#service-types-table').on('click', '.update-button', function() {
                let url = $(this).data('update-url');
                editHandler(url);
            });

            $('#service-types-table').on('click', '.delete-button', function() {
                let url = $(this).data('delete-url');
                deleteHandler(url);
            });

            $('#form-modal form').on('submit', function() {
                submitHandler();
            });
        });

        const createHandler = (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Tambah Jenis Cucian');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('post');
        }

        const editHandler = async (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Edit Jenis Cucian');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('put');
            modal.find('input').attr('disabled', true);

            try {
                let res = await fetchData(url);
                modal.find('[name=name]').val(res.service_type.name);
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
                text: 'Anda yakin ingin menghapus data ini?',
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
