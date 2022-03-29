@extends('layouts.main')

@section('content')
    <div class="card">
        <form action="#" id="add-data-form">
            <div class="card-body row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="id">No. Transaksi</label>
                        </div>
                        <div class="col-9">
                            <input type="number" id="id" name="id" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="phone">No. HP/WA</label>
                        </div>
                        <div class="col-9">
                            <input type="tel" id="phone" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="service-type">Jenis Cucian</label>
                        </div>
                        <div class="col-9">
                            <select name="service_type" id="service-type" class="form-control">
                                <!-- Product goes here-->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="customer_name">Nama Pelanggan</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="customer_name" name="customer_name" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="date">Tanggal Cuci</label>
                        </div>
                        <div class="col-9">
                            <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="weight">Berat</label>
                        </div>
                        <div class="col-9">
                            <div class="input-group">
                                <input type="number" id="weight" name="weight" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text">Kg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Data Transaksi Cucian
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
                                <option value="customer_name">Nama Pelanggan</option>
                                <option value="phone">Kontak Pelanggan</option>
                                <option value="date">Tanggal Cucian</option>
                                <option value="service_type">Jenis Cucian</option>
                                <option value="weight">Berat</option>
                                <option value="discount">Diskon</option>
                                <option value="total_price">Total Harga</option>
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
            <table class="table table-striped" id="transactions-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelanggan</th>
                        <th>Kontak</th>
                        <th>Tanggal Cuci</th>
                        <th>Jenis Cucian</th>
                        <th>Berat (Kg)</th>
                        <th>Diskon</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data goes here -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5">TOTAL</th>
                        <th id="total-weight"></th>
                        <th id="total-discount"></th>
                        <th id="total-price"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const MIN_PRICE_DISCOUNT = 50000;
        const DISCOUNT_PERCENT = 10;
        const SERVICE_TYPES = [{
                name: 'Standar',
                price: 7500,
            },
            {
                name: 'Express',
                price: 10000,
            },
        ];

        let transactions;
        let filteredTransactions;

        // Event ketika halaman dimuat
        $(function() {
            // Load data transaksi
            transactions = filteredTransactions = JSON.parse(localStorage.getItem('service-transactions')) ?? [];

            // Urutkan data transaksi
            sortTransactions();

            // Tampilkan data transaksi
            renderTransactions();

            // Load data layanan
            loadServices();

            // Event ketika form tambah data di submit
            $('#add-data-form').on('submit', function() {
                event.preventDefault();
                let data = {};
                let formData = $(this).serializeArray();

                // Validasi semua input tidak boleh kosong
                if (formData.every((item) => item.value && item.value !== '')) {
                    // Mapping data dari serializeArray menjadi bentuk bentuk objek transaksi
                    formData.forEach((item) => {
                        data[item.name] = item.name === 'id' ||
                            item.name === 'weight' ?
                            Number(item.value) : item.value;
                    });

                    // Menambahkan harga, total diskon & total harga setelah diskon ke dalam objek
                    data.price = getServicePrice(data.service_type);
                    data.discount = getTotalDiscount(data.price * data.weight);
                    data.total_price = (data.price * data.weight) - data.discount;

                    // Menambahkan objek baru ke dalam array transactions
                    pushTransaction(data);

                    // Reset form tambah data dan filter
                    $(this).trigger('reset');
                    $('#filter').val('');
                    $('#filter').trigger('change');
                }
            });

            // Event ketika dropdown sort-by diubah
            $('#sort-by').on('change', function() {
                sortTransactions();
                renderTransactions();
            });

            // Event ketika dropdown sort-direction diubah
            $('#sort-direction').on('change', function() {
                sortTransactions();
                renderTransactions();
            });

            // Event ketika inputan filter diubah
            $('#filter').on('change keydown', function() {
                setTimeout(() => {
                    filterTransactions($('#filter').val());
                    sortTransactions();
                    renderTransactions();
                });
            });

            // Event ketika tombol reset data diklik
            $('#reset-data-button').on('click', resetData);
        });

        // Menampilkan data transaksi ke dalam tabel
        const renderTransactions = () => {
            let totalPrice = totalWeight = totalDiscount = 0;
            let rows = '';
            if (filteredTransactions.length > 0) {
                for (let i = 0; i < filteredTransactions.length; i++) {
                    rows += `<tr>
                        <td>${filteredTransactions[i].id}</td>
                        <td>${filteredTransactions[i].customer_name}</td>
                        <td>${filteredTransactions[i].phone}</td>
                        <td>${filteredTransactions[i].date}</td>
                        <td>${filteredTransactions[i].service_type}</td>
                        <td>${filteredTransactions[i].weight}</td>
                        <td>${formatter.format(filteredTransactions[i].discount)}</td>
                        <td>${formatter.format(filteredTransactions[i].total_price)}</td>
                    </tr>`;
                    totalWeight += filteredTransactions[i].weight;
                    totalDiscount += filteredTransactions[i].discount;
                    totalPrice += filteredTransactions[i].total_price;
                }
            } else {
                rows = `<tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>`
            }
            $('#transactions-table tbody').html(rows);
            $('#total-weight').html(totalWeight);
            $('#total-discount').html(formatter.format(totalDiscount));
            $('#total-price').html(formatter.format(totalPrice));
        }

        // Mengurutkan array 2 dimensi menggunakan metode selection sort
        const selectionSort = (arr, sortBy, sortDirection) => {
            let swapIdx;
            for (let i = 0; i < arr.length; i++) {
                // Index array terkecil / terbesar
                swapIdx = i;
                // Mecari index array dengan data terbesar / terkecil
                for (let j = i + 1; j < arr.length; j++) {
                    if (sortDirection === 'ASC' && arr[swapIdx][sortBy] > arr[j][sortBy]) {
                        swapIdx = j;
                    } else if (sortDirection === 'DESC' && arr[swapIdx][sortBy] < arr[j][sortBy]) {
                        swapIdx = j;
                    }
                }
                // Jika index array terkecil / terbesar berubah, tukar data
                if (swapIdx != i) {
                    let temp = arr[swapIdx];
                    arr[swapIdx] = arr[i];
                    arr[i] = temp;
                }
            }
        }

        // Mengurutkan data transaksi dengan metode selection sort
        const sortTransactions = () => {
            selectionSort(filteredTransactions, $('#sort-by').val(), $('#sort-direction').val());
        }

        // Menambahkan data baru ke dalam array
        const pushTransaction = (transaction) => {
            transactions = JSON.parse(localStorage.getItem('service-transactions')) ?? [];
            transactions.push(transaction);
            filteredTransactions = transactions;
            localStorage.setItem('service-transactions', JSON.stringify(transactions));
            filterTransactions($('#filter').val());
            sortTransactions();
            renderTransactions();
        }

        // Mendapatkan harga layanan per kg
        const getServicePrice = (service_type) => {
            for (let i = 0; i < SERVICE_TYPES.length; i++) {
                if (SERVICE_TYPES[i].name === service_type) {
                    return SERVICE_TYPES[i].price;
                }
            }
            return 0;
        }

        // Menghitung diskon cucian
        const getTotalDiscount = (price) => {
            return price > MIN_PRICE_DISCOUNT ? (DISCOUNT_PERCENT / 100) * price : 0;
        }

        // Memuat data layanan di dropdown
        const loadServices = () => {
            let options = ''
            SERVICE_TYPES.forEach(service => {
                options += `<option value="${service.name}">${service.name}</option>`
            });
            $('[name=service_type]').html(options);
        }

        // Memfilter data transaksi berdasarkan kata kunci tertentu
        const filterTransactions = (keyword) => {
            // Array untuk menampung data yang difilter
            let arr = [];
            // Mengecek apakah data di dalam array transactions mengandung kata kunci yang dikirim menggunakan method .includes()
            // Karena method .includes() membandingkan string secara case sensitive, maka string diubah ke huruf kecil terlebih dahulu
            keyword = keyword.toLowerCase();
            for (let i = 0; i < transactions.length; i++) {
                if (
                    transactions[i].id.toString().includes(keyword) ||
                    transactions[i].customer_name.toLowerCase().includes(keyword) ||
                    transactions[i].phone.toLowerCase().includes(keyword) ||
                    transactions[i].date.toLowerCase().includes(keyword) ||
                    transactions[i].service_type.toLowerCase().includes(keyword) ||
                    transactions[i].weight.toString().includes(keyword) ||
                    transactions[i].discount.toString().includes(keyword) ||
                    transactions[i].total_price.toString().includes(keyword)
                ) {
                    // Jika kondisi terpenuhi, maka data array dengan index ke-i dimasukan ke dalam array penampung
                    arr.push(transactions[i]);
                }
            }
            filteredTransactions = arr;
        }

        // Menghapus data transaksi di variabel dan localStorage
        const resetData = () => {
            if (confirm('Hapus semua data?')) {
                transaction = filteredTransactions = [];
                localStorage.removeItem('service-transactions');
                renderTransactions();
            }
        }
    </script>
@endpush
