<!--
    Pengurutan data di halaman ini menggunakan metode selection sort
-->

@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">SImulasi Gaji Karyawan</h3>
        </div>
        <div class="card-body">
            <form action="" id="add-data-form">
                <div class="form-group row">
                    <div class="col-2">
                        <label for="id">ID</label>
                    </div>
                    <div class="col-10">
                        <input type="number" class="form-control" name="id" id="id">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label for="name">Nama Karyawan</label>
                    </div>
                    <div class="col-10">
                        <input type="text" class="form-control" name="name" id="name">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label for="name">Jenis Kelamin</label>
                    </div>
                    <div class="col-10 d-flex" style="gap: 20px">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Laki-laki"
                                checked>
                            <label class="form-check-label" for="genderMale">
                                Laki-laki
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Perempuan">
                            <label class="form-check-label" for="genderFemale">
                                Perempuan
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label for="marriage-status">Status Menikah</label>
                    </div>
                    <div class="col-10">
                        <select name="marriage_status" id="marriage-status" class="form-control">
                            <option value="Single" selected>Single</option>
                            <option value="Couple">Couple (Menikah)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label for="total-childrens">Jumlah Anak</label>
                    </div>
                    <div class="col-10">
                        <input type="number" class="form-control" name="total_childrens" id="total-childrens" value="0"
                            readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label for="start-working-date">Mulai Bekerja</label>
                    </div>
                    <div class="col-10">
                        <input type="date" class="form-control" name="start_working_date" id="start-working-date">
                    </div>
                </div>
                <button class="btn btn-primary">Submit</button>
                <button class="btn btn-warning" type="reset">Reset</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Data Karyawan
            </h3>
            <div class="card-tools" id="reset-data-button">
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
                                <option value="total_childrens">Total Anak</option>
                                <option value="marriage_status">Status Menikah</option>
                                <option value="start_working_date">Awal Bekerja</option>
                                <option value="total_bonus">Total Tunjangan</option>
                                <option value="total_fee">Total Gaji</option>
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
            <table class="table" id="employees-fee-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Karyawan</th>
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                        <th>Jml Anak</th>
                        <th>Mulai Bekerja</th>
                        <th>Gaji Awal</th>
                        <th>Tunjangan</th>
                        <th>Total Gaji</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data goes here -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">TOTAL</th>
                        <td id="total-fee"></td>
                        <td id="total-bonus"></td>
                        <td id="total-fee-with-bonus"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const INITIAL_FEE = 2000000;
        const BONUS_PER_YEAR = 150000;
        const BONUS_COUPLE = 250000;
        const BONUS_PER_CHILDREN = 150000;
        const MAX_CHILDREN_BONUS = 2;

        let employees;
        let filteredEmployees;

        $(function() {
            // Load data saat halaman dimuat
            employees = filteredEmployees = JSON.parse(localStorage.getItem('employees-fee')) ?? [];
            sortEmployees();
            renderEmployees();

            // Event ketika form tambah data di submit
            $('#add-data-form').on('submit', function() {
                event.preventDefault();
                let data = {};
                let formData = $(this).serializeArray();
                // Validasi semua inputan agar tidak blank
                if (formData.every((item) => item.value && item.value !== '')) {
                    // Mapping data dari serializeArray menjadi bentuk bentuk yang valid
                    formData.forEach((item) => {
                        data[item.name] = item.name === 'id' ||
                            item.name === 'total_childrens' ?
                            parseInt(item.value) : item.value;
                    });
                    // Menambahkan gaji, total bonus & total gaji
                    data.initial_fee = INITIAL_FEE;
                    data.total_bonus = calculateBonus(data);
                    data.total_fee = INITIAL_FEE + data.total_bonus;
                    // Menambahkan data baru ke dalam array
                    pushData(data);
                    // Reset form tambah data
                    $(this).trigger('reset');
                }
            });

            // Event ketika dropdown sort-by diubah
            $('#sort-by').on('change', function() {
                sortEmployees();
                renderEmployees();
            });

            // Event ketika dropdown sort-direction diubah
            $('#sort-direction').on('change', function() {
                sortEmployees();
                renderEmployees();
            });

            // Event ketika inputan filter diubah
            $('#filter').on('change keydown', function() {
                setTimeout(() => {
                    filterEmployees($(this).val());
                    sortEmployees();
                    renderEmployees();
                });
            });

            // Event ketika inputan filter diubah
            $('[name="marriage_status"]').on('change', function() {
                if ($(this).val() === 'Single') {
                    $('[name="total_childrens"]').val(0);
                    $('[name="total_childrens"]').attr('readonly', true);
                } else {
                    $('[name="total_childrens"]').attr('readonly', false);
                }
            });

            // Event ketika tombol reset karyawan diklik
            $('#reset-data-button').on('click', resetEmployees);
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
            let totalFee = 0;
            let totalBonus = 0;
            let totalFeeWithBonus = 0;
            let rows = '';
            if (filteredEmployees.length > 0) {
                for (let i = 0; i < filteredEmployees.length; i++) {
                    rows += `<tr>
                        <td>${filteredEmployees[i].id}</td>
                        <td>${filteredEmployees[i].name}</td>
                        <td>${filteredEmployees[i].gender}</td>
                        <td>${filteredEmployees[i].marriage_status}</td>
                        <td>${filteredEmployees[i].total_childrens}</td>
                        <td>${filteredEmployees[i].start_working_date}</td>
                        <td>${formatter.format(filteredEmployees[i].initial_fee)}</td>
                        <td>${formatter.format(filteredEmployees[i].total_bonus)}</td>
                        <td>${formatter.format(filteredEmployees[i].total_fee)}</td>
                    </tr>`;
                    totalFee += filteredEmployees[i].initial_fee;
                    totalBonus += filteredEmployees[i].total_bonus;
                    totalFeeWithBonus += filteredEmployees[i].total_fee;
                }
            } else {
                rows = `<tr>
                    <td colspan="3">Tidak ada data</td>
                </tr>`
            }
            $('#employees-fee-table tbody').html(rows);
            $('#total-fee').html(formatter.format(totalFee));
            $('#total-bonus').html(formatter.format(totalBonus));
            $('#total-fee-with-bonus').html(formatter.format(totalFeeWithBonus));
        }

        // Menambahkan data baru ke dalam array
        const pushData = (employee) => {
            employees.push(employee);
            filteredEmployees = employees;
            localStorage.setItem('employees-fee', JSON.stringify(employees));
            sortEmployees();
            renderEmployees();
        }

        // Menghapus data karyawan di variabel dan localStorage
        const resetEmployees = () => {
            if (confirm('Hapus semua data?')) {
                employees = filteredEmployees = [];
                localStorage.removeItem('employees-fee');
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
                if (employees[i].id.toString().includes(keyword) ||
                    employees[i].name.toLowerCase().includes(keyword) ||
                    employees[i].gender.toLowerCase().includes(keyword) ||
                    employees[i].marriage_status.toLowerCase().includes(keyword) ||
                    employees[i].total_childrens.toString().includes(keyword) ||
                    employees[i].start_working_date.toLowerCase().includes(keyword) ||
                    employees[i].initial_fee.toString().includes(keyword) ||
                    employees[i].total_bonus.toString().includes(keyword) ||
                    employees[i].total_fee.toString().includes(keyword)
                ) {
                    // Jika kondisi terpenuhi, maka data array dengan index ke-i dimasukan ke dalam array penampung
                    arr.push(employees[i]);
                }
            }
            filteredEmployees = arr;
        }

        // Menghitung lama bekerja dalam satuan tahun
        const calculateTotalYear = (startDate) => {
            startDate = new Date(startDate);
            let ageDifMs = Date.now() - startDate.getTime();
            if (ageDifMs > 0) {
                let ageDate = new Date(ageDifMs);
                return Math.abs(ageDate.getUTCFullYear() - 1970);
            }
            return 0;
        }

        // Mengithung total tunjangan karyawan
        const calculateBonus = (employee) => {
            let bonus = 0;
            let totalYear = calculateTotalYear(employee.start_working_date);
            bonus += totalYear * BONUS_PER_YEAR;
            bonus += employee.total_childrens <= MAX_CHILDREN_BONUS ?
                employee.total_childrens * BONUS_PER_CHILDREN :
                BONUS_PER_CHILDREN * MAX_CHILDREN_BONUS;
            bonus += employee.marriage_status === 'Couple' ? BONUS_COUPLE : 0;
            return bonus;
        }
    </script>
@endpush
