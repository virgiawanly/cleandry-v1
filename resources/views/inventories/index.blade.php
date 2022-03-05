@extends('layouts.main')

@push('head')
    @include('layouts.datatable_styles')
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Barang Inventaris</h3>
                    <div class="card-tools">
                        <button class="btn btn btn-primary" id="add-inventory-button"
                            data-create-inventory-url="{{ route('inventories.store') }}">
                            <i class="far fa-plus-square mr-1"></i><span>Tambah Barang</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table id="inventories-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Merek Barang</th>
                                <th>Kuantitas</th>
                                <th>Kondisi Barang</th>
                                <th>Tgl Pengadaan</th>
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
                <form action="#" id="inventory-form" method="POST">
                    @method('post')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Barang</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama Barang</label>
                            <input type="text" name="name" class="form-control" id="name"
                                placeholder="Nama barang inventaris">
                        </div>
                        <div class="form-group">
                            <label for="brand">Nama Merek</label>
                            <input type="text" name="brand" class="form-control" id="brand" placeholder="Merek barang">
                        </div>
                        <div class="form-group">
                            <label for="qty">Kuantitas</label>
                            <input type="number" min="0" name="qty" class="form-control" id="qty"
                                placeholder="Jumlah barang">
                        </div>
                        <div class="form-group">
                            <label for="">Kondisi Barang</label>
                            <div class="d-flex align-items-center" style="gap: 15px">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="conditionGood"
                                        value="good">
                                    <label class="form-check-label" for="conditionGood">
                                        Bagus / Layak dipakai
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="conditionDamaged"
                                        value="damaged">
                                    <label class="form-check-label" for="conditionDamaged">
                                        Rusak ringan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="condition" id="conditionBroken"
                                        value="broken">
                                    <label class="form-check-label" for="conditionBroken">
                                        Rusak berat
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="procurement-date">Tanggal Pengadaan</label>
                            <input type="date" name="procurement_date" class="form-control" id="procurement-date"
                                placeholder="Jumlah barang">
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
            table = $('#inventories-table').DataTable({
                ...DATATABLE_OPTIONS,
                ajax: '/inventories/datatable',
                columns: [{
                        data: 'DT_RowIndex',
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: 'brand',
                    },
                    {
                        data: 'qty',
                    },
                    {
                        data: 'condition',
                        render: (condition) => {
                            let text;
                            let type;
                            switch (condition) {
                                case 'good':
                                    text = 'Bagus / layak pakai';
                                    type = 'success';
                                    break;
                                case 'damaged':
                                    text = 'Rusak ringan';
                                    type = 'warning';
                                    break;
                                default:
                                    text = 'Rusak';
                                    type = 'danger';
                                    break;
                            }
                            return `<span class="badge badge-${type}">${text}</span>`;
                        }
                    },
                    {
                        data: 'procurement_date',
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
            modal.find('.modal-title').text('Tambah Barang Inventaris');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('post');
        }

        const editHandler = async (url) => {
            clearErrors();
            const modal = $('#form-modal');
            modal.modal('show');
            modal.find('.modal-title').text('Edit Inventaris');
            modal.find('form')[0].reset();
            modal.find('form').attr('action', url);
            modal.find('[name=_method]').val('put');
            modal.find('input').attr('disabled', true);
            modal.find('select').attr('disabled', true);

            try {
                let res = await fetchData(url);
                modal.find('[name=name]').val(res.inventory.name);
                modal.find('[name=brand]').val(res.inventory.brand);
                modal.find('[name=qty]').val(res.inventory.qty);
                modal.find(`[name=condition][value='${res.inventory.condition}']`).prop('checked', true);
                modal.find('[name=procurement_date]').val(res.inventory.procurement_date);
            } catch (err) {
                console.log(err);
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

        // Event handlers
        $('#add-inventory-button').on('click', function() {
            let url = $(this).data('create-inventory-url');
            createHandler(url);
        });

        $('#inventories-table').on('click', '.edit-inventory-button', function() {
            let url = $(this).data('edit-inventory-url');
            editHandler(url);
        });

        $('#inventories-table').on('click', '.delete-inventory-button', function() {
            let url = $(this).data('delete-inventory-url');
            deleteHandler(url);
        });

        $('#inventory-form').on('submit', submitHandler);
    </script>
@endpush
