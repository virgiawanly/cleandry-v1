@extends('layouts.main')

@push('head')
    <meta name="outlet-id" content="{{ $outlet->id }}">
    @include('layouts.datatable_styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5 class="mb-0">Data layanan</h5>
                        <div class="text-sm">Outlet : {{ $outlet->name }}</div>
                    </div>
                    <div class="card-tools">
                        @if (Auth::user()->is_super)
                            <a href="/select-outlet" class="btn btn-info"><i class="fas fa-exchange-alt mr-1"></i>Ganti
                                Outlet</a>
                        @endif
                        <button class="btn btn btn-primary"
                            onclick="createHandler('{{ route('services.store', [$outlet->id]) }}')">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah layanan</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table id="services-table" class="table table-hover table-striped w-100">
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
    @include('layouts.datatable_scripts')
    <!-- Select2 -->
    <script src="{{ asset('adminlte') }}/plugins/select2/js/select2.full.min.js"></script>
    <!-- Page Script -->
    <script>
        let table;

        $(function() {
            const outletId = $("meta[name='outlet-id']").attr("content");
            const tableOptions =
                table = $('#services-table').DataTable({
                    ...DATATABLE_OPTIONS,
                    ajax: `/o/${outletId}/services/datatable`,
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
                            render: (unit) => {
                                switch (unit) {
                                    case 'm':
                                        return 'Meter';
                                    case 'pcs':
                                        return 'Pcs';
                                    default:
                                        return 'Kilogram';
                                }
                            }
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
                });
            //Initialize Select2 Elements
            $('.select2').select2({
                placeholder: "Pilih jenis",
                theme: 'bootstrap4'
            });
        });

        const createHandler = (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Buat Layanan Baru');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('post');
        }

        const editHandler = async (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Edit layanan');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('put');
            modal.find('input').attr('disabled', true);
            modal.find('select').attr('disabled', true);

            try {
                let res = await fetchData(url);
                modal.find('[name=name]').val(res.service.name);
                modal.find('[name=price]').val(res.service.price);
                modal.find(`[name=type_id]`).val(res.service.type_id).trigger('change');
                modal.find(`[name=unit][value='${res.service.unit}']`).prop('checked', true);
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
                title: 'Hapus Layanan',
                text: 'Anda yakin ingin menghapus layanan ini?',
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
