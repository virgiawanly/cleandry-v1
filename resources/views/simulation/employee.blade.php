<!--
    Pengurutan data di halaman ini menggunakan metode selection sort
-->

@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Data Karyawan</h3>
        </div>
        <div class="card-body">
            <form action="" id="add-employee-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id">ID Karyawan</label>
                            <input type="number" class="form-control" name="id" id="id">
                        </div>
                        <div class="form-group">
                            <label for="name">Nama Karyawan</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                        <div class="form-group">
                            <label for="name">Nama Karyawan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Laki-laki"
                                    checked>
                                <label class="form-check-label" for="genderMale">
                                    Laki-laki
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderFemale"
                                    value="Perempuan">
                                <label class="form-check-label" for="genderFemale">
                                    Perempuan
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-primary">Submit</button>
                        <button class="btn btn-warning" type="reset">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Data Karyawan
            </h3>
            <div class="card-tools" id="reset-employees-button">
                <button class="btn btn-danger">Hapus Semua</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row align-items-center">
                        <div class="col-3"><label for="sort-by">Urutkan</label></div>
                        <div class="col-6">
                            <select name="filter_by" id="sort-by" class="form-control">
                                <option value="id" selected>ID</option>
                                <option value="name">Nama</option>
                                <option value="gender">Jenis Kelamin</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <select name="sort_direction" id="sort-direction" class="form-control">
                                <option value="ASC" selected>ASC</option>
                                <option value="DESC">DESC</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row align-items-center">
                        <div class="col-3 text-right">
                            <label for="filter">Filter</label>
                        </div>
                        <div class="col-9">
                            <input class="form-control" id="filter" placeholder="Cari...">
                        </div>
                    </div>
                </div>
            </div>
            <table class="table" id="employees-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Karyawan</th>
                        <th>Jenis Kelamin</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Employees goes here -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let employees;
        let filteredEmployees;

        $(function() {
            // Load data karyawan saat halaman dimuat
            employees = filteredEmployees = JSON.parse(localStorage.getItem('employees')) ?? [];
            sortEmployees();
            renderEmployees();

            // Event ketika form tambah karyawan di submit
            $('#add-employee-form').on('submit', function() {
                event.preventDefault();
                let data = {};
                let formData = $(this).serializeArray();
                // Validasi semua inputan agar tidak blank
                if (formData.every((item) => item.value && item.value !== '')) {
                    // Mapping data dari serializeArray menjadi bentuk valid objek karyawan
                    formData.forEach((item) => data[item.name] = item.name === 'id' ? parseInt(item.value) :
                        item
                        .value);
                    // Menambahkan data baru ke dalam array karyawan
                    pushEmployees(data);
                    // Reset form tambah karyawan
                    $(this).trigger('reset');
                }
            });

            // Event ketika dropdown sort-by diubah
            $('#sort-by').on('change', function(e) {
                sortEmployees();
                renderEmployees();
            });

            // Event ketika dropdown sort-direction diubah
            $('#sort-direction').on('change', function(e) {
                sortEmployees();
                renderEmployees();
            });

            // Event ketika inputan filter diubah
            $('#filter').on('change keydown', function(e) {
                setTimeout(() => {
                    filterEmployees($(this).val());
                    sortEmployees();
                    renderEmployees();
                });
            });

            // Event ketika tombol reset karyawan diklik
            $('#reset-employees-button').on('click', resetEmployees);
        });

        // Function untuk mengurutkan array 2 dimensi menggunakan metode selection sort
        const selectionSort = (arr, sortBy, sortDirection) => {
            let swapIdx;
            for (let i = 0; i < arr.length; i++) {
                swapIdx = i;
                // Mecari index array dengan data terbesar / terkecil
                for (let j = i + 1; j < arr.length; j++) {
                    if (sortDirection === 'ASC' && arr[swapIdx][sortBy] > arr[j][sortBy]) {
                        swapIdx = j;
                    } else if (sortDirection === 'DESC' && arr[swapIdx][sortBy] < arr[j][sortBy]) {
                        swapIdx = j;
                    }
                }
                // Jika ada data yang lebih kecil / besar dari data dengan index ke-i, maka tukar data
                if (swapIdx != i) {
                    let temp = arr[swapIdx];
                    arr[swapIdx] = arr[i];
                    arr[i] = temp;
                }
            }
        }

        // Mengurutkan karyawan dengan metode selection sort
        const sortEmployees = () => {
            selectionSort(filteredEmployees, $('#sort-by').val(), $('#sort-direction').val());
        }

        // Menampilkan / mengupdate isi tabel karyawan
        const renderEmployees = () => {
            let rows = '';
            if (filteredEmployees.length > 0) {
                for (let i = 0; i < filteredEmployees.length; i++) {
                    rows += `<tr>
                        <td>${filteredEmployees[i].id}</td>
                        <td>${filteredEmployees[i].name}</td>
                        <td>${filteredEmployees[i].gender}</td>
                    </tr>`;
                }
            } else {
                rows = `<tr>
                    <td colspan="3">Tidak ada data</td>
                </tr>`
            }
            $('#employees-table tbody').html(rows);
        }

        // Menambahkan data baru ke array employees / karyawan
        const pushEmployees = (employee) => {
            employees.push(employee);
            filteredEmployees = employees;
            localStorage.setItem('employees', JSON.stringify(employees));
            sortEmployees();
            renderEmployees();
        }

        // Menghapus data karyawan di variabel dan localStorage
        const resetEmployees = () => {
            if (confirm('Hapus semua karyawan?')) {
                employees = filteredEmployees = [];
                localStorage.removeItem('employees');
                renderEmployees();
            }
        }

        // Memfilter karyawan berdasarkan kata kunci tertentu
        const filterEmployees = (keyword) => {
            // Array untuk menampung data yang difilter
            let arr = [];
            // Mengecek apakah data di dalam array employees / karyawan mengandung kata kunci yang dikirim menggunakan method .includes()
            // Karena method .includes() membandingkan string secara case sensitive, maka string diubah ke huruf kecil terlebih dahulu
            keyword = keyword.toLowerCase();
            for (let i = 0; i < employees.length; i++) {
                // Khusus untuk id, karena berupa number maka harus diubah ke bentuk string ".toString()" agar bisa menggunakan method .includes()
                if (employees[i].id.toString().toLowerCase().includes(keyword) ||
                    employees[i].name.toLowerCase().includes(keyword) ||
                    employees[i].gender.toLowerCase().includes(keyword)
                ) {
                    // Jika kondisi terpenuhi, maka data array dengan index ke-i dimasukan ke dalam array penampung
                    arr.push(employees[i]);
                }
            }
            filteredEmployees = arr;
        }
    </script>
@endpush
