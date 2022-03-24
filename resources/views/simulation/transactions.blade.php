@extends('layouts.main')

@section('content')
    <div class="card">
        <form action="#" id="add-data-form">
            <div class="card-body row">
                <div class="col-md-6">
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="id">ID</label>
                        </div>
                        <div class="col-9">
                            <input type="number" id="id" name="id" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="product-name">Nama Barang</label>
                        </div>
                        <div class="col-9">
                            <select name="product_name" id="product-name" class="form-control">
                                <!-- Product goes here-->
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="qty">Jumlah</label>
                        </div>
                        <div class="col-9">
                            <input type="number" id="qty" name="qty" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="date">Tanggal Beli</label>
                        </div>
                        <div class="col-9">
                            <input type="date" id="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="price-display">Harga Barang</label>
                        </div>
                        <div class="col-9">
                            <input type="text" id="price-display" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label>Jenis Pembayaran</label>
                        </div>
                        <div class="col-9">
                            <div class="d-flex align-items-center" style="gap: 15px">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentCash"
                                        value="cash" checked>
                                    <label class="form-check-label" for="paymentCash">
                                        Cash
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentTransfer"
                                        value="transfer">
                                    <label class="form-check-label" for="paymentTransfer">
                                        E-Money/Transfer
                                    </label>
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
                Data Transaksi Barang
            </h3>
            <div class="card-tools" id="reset-data-button">
                <button class="btn btn-danger">Hapus Semua</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row align-items-center">
                        <div class="col-3"><label for="sort-by">Urutkan</label></div>
                        <div class="col-6">
                            <select name="filter_by" id="sort-by" class="form-control">
                                <option value="id" selected>ID</option>
                                <option value="product_name">Nama Barang</option>
                                <option value="price">Harga</option>
                                <option value="qty">Qty</option>
                                <option value="discount">Diskon</option>
                                <option value="total_payment">Total Harga</option>
                                <option value="payment_method">Jenis Pembayaran</option>
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
                <div class="col-md-3 d-flex" style="gap: 20px">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="filter_payment_method" value="cash"
                            id="filterPaymentCash" checked>
                        <label class="form-check-label" for="filterPaymentCash">
                            Cash
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="filter_payment_method" value="transfer"
                            id="filterPaymentTransfer" checked>
                        <label class="form-check-label" for="filterPaymentTransfer">
                            E-Money/Transfer
                        </label>
                    </div>
                </div>
                <div class="col-md-5">
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
                        <th>Tgl Beli</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Diskon</th>
                        <th>Total Harga</th>
                        <th>Jenis Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data goes here -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">TOTAL</th>
                        <th id="total-price"></th>
                        <th id="total-qty"></th>
                        <th id="total-discount"></th>
                        <th colspan="2" id="total-payment"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const MIN_PRICE_DISCOUNT = 50000;
        const DISCOUNT_PERCENT = 15;
        const AVAILABLE_PRODUCTS = [{
                name: 'Deterjen Sepatu',
                price: 25000,
            },
            {
                name: 'Deterjen',
                price: 15000,
            },
            {
                name: 'Pewangi',
                price: 10000,
            },
        ];

        let transactions;
        let filteredTransactions;

        // Event ketika halaman dimuat
        $(function() {
            // Load data transaksi
            transactions = filteredTransactions = JSON.parse(localStorage.getItem('transactions')) ?? [];

            // Urutkan data transaksi
            sortTransactions();

            // Tampilkan data transaksi
            renderTransactions();

            // Load data produk
            loadProducts();

            // Event ketika dropdown barang diubah
            $('[name="product_name"]').on('change', function() {
                let price = getProductPrice($(this).val());
                $('#price-display').val(formatter.format(price));
            });

            // Event ketika form tambah data di submit
            $('#add-data-form').on('submit', function() {
                event.preventDefault();
                let data = {};
                let formData = $(this).serializeArray();

                // Mapping data dari serializeArray menjadi bentuk bentuk objek transaksi
                formData.forEach((item) => {
                    data[item.name] = item.name === 'id' ||
                        item.name === 'qty' ?
                        Number(item.value) : item.value;
                });

                // Menambahkan harga, total diskon & total harga setelah diskon ke dalam objek
                data.price = getProductPrice(data.product_name);
                data.discount = getTotalDiscount(data.price * data.qty);
                data.total_payment = (data.price * data.qty) - data.discount;

                // Menambahkan objek baru ke dalam array transactions
                pushTransaction(data);

                // Reset form tambah data dan filter
                $(this).trigger('reset');
                $('#filter').val('');
                $('#filter').trigger('reset');
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

            // Event ketika checkbox filter metode pembayaram diubah
            $('[name="filter_payment_method"]').on('change', function() {
                setTimeout(() => {
                    filterTransactions($('#filter').val());
                    sortTransactions();
                    renderTransactions();
                });
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
            $('#reset-data-button').on('click', resetTransactions);
        });

        // Menampilkan data transaksi ke dalam tabel
        const renderTransactions = () => {
            let totalPrice = totalQty = totalDiscount = totalPayment = 0;
            let rows = '';
            if (filteredTransactions.length > 0) {
                for (let i = 0; i < filteredTransactions.length; i++) {
                    rows += `<tr>
                        <td>${filteredTransactions[i].id}</td>
                        <td>${filteredTransactions[i].date}</td>
                        <td>${filteredTransactions[i].product_name}</td>
                        <td>${formatter.format(filteredTransactions[i].price)}</td>
                        <td>${filteredTransactions[i].qty}</td>
                        <td>${formatter.format(filteredTransactions[i].discount)}</td>
                        <td>${formatter.format(filteredTransactions[i].total_payment)}</td>
                        <td>${filteredTransactions[i].payment_method}</td>
                    </tr>`;
                    totalPrice += filteredTransactions[i].price;
                    totalQty += filteredTransactions[i].qty;
                    totalPayment += filteredTransactions[i].total_payment;
                    totalDiscount += filteredTransactions[i].discount;
                }
            } else {
                rows = `<tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>`
            }
            $('#transactions-table tbody').html(rows);
            $('#total-price').html(formatter.format(totalPrice));
            $('#total-qty').html(totalQty);
            $('#total-discount').html(formatter.format(totalDiscount));
            $('#total-payment').html(formatter.format(totalPayment));
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
            transactions.push(transaction);
            filteredTransactions = transactions;
            localStorage.setItem('transactions', JSON.stringify(transactions));
            filterTransactions($('#filter').val());
            sortTransactions();
            renderTransactions();
        }

        // Mendapatkan harga barang
        const getProductPrice = (product_name) => {
            for (let i = 0; i < AVAILABLE_PRODUCTS.length; i++) {
                if (AVAILABLE_PRODUCTS[i].name === product_name) {
                    return AVAILABLE_PRODUCTS[i].price;
                }
            }
            return 0;
        }

        // Menghitung diskon barang
        const getTotalDiscount = (price) => {
            return price >= MIN_PRICE_DISCOUNT ? (DISCOUNT_PERCENT / 100) * price : 0;
        }

        // Memuat data barang di dropdown
        const loadProducts = () => {
            let options = ''
            AVAILABLE_PRODUCTS.forEach(product => {
                options += `<option value="${product.name}">${product.name}</option>`
            });
            $('[name=product_name]').html(options);
        }

        // Mendapatkan data filter metode pembayaran yang di ceklis
        const getPaymentMethodValues = () => {
            let values = $('[name="filter_payment_method"]:checked').map((i, row) => $(row).val()).get();
            return values;
        }

        // Memfilter data transaksi berdasarkan kata kunci tertentu
        const filterTransactions = (keyword) => {
            // Array untuk menampung data yang difilter
            let arr = [];
            // Checkbox filter data berdasarkan metode pembayaran
            let payment_methods = getPaymentMethodValues();
            // Mengecek apakah data di dalam array transactions mengandung kata kunci yang dikirim menggunakan method .includes()
            // Karena method .includes() membandingkan string secara case sensitive, maka string diubah ke huruf kecil terlebih dahulu
            keyword = keyword.toLowerCase();
            for (let i = 0; i < transactions.length; i++) {
                if (payment_methods.includes(transactions[i].payment_method)) {
                    if (
                        transactions[i].id.toString().includes(keyword) ||
                        transactions[i].date.toLowerCase().includes(keyword) ||
                        transactions[i].product_name.toLowerCase().includes(keyword) ||
                        transactions[i].price.toString().includes(keyword) ||
                        transactions[i].qty.toString().includes(keyword) ||
                        transactions[i].discount.toString().includes(keyword) ||
                        transactions[i].total_payment.toString().includes(keyword) ||
                        transactions[i].payment_method.toLowerCase().includes(keyword)
                    ) {
                        // Jika kondisi terpenuhi, maka data array dengan index ke-i dimasukan ke dalam array penampung
                        arr.push(transactions[i]);
                    }
                }
            }
            filteredTransactions = arr;
        }

        // Menghapus data transaksi di variabel dan localStorage
        const resetTransactions = () => {
            if (confirm('Hapus semua data?')) {
                transaction = filteredTransactions = [];
                localStorage.removeItem('transactions');
                renderTransactions();
            }
        }
    </script>
@endpush
