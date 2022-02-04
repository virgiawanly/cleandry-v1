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
                        <button class="btn btn btn-primary">
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
    </script>
@endpush
