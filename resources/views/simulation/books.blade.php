<!--
    Pengurutan data di halaman ini menggunakan metode insertion sort
-->

@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Data</h3>
        </div>
        <div class="card-body">
            <form action="#" class="row" id="add-book-form">
                <div class="col-lg-6">
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="id">ID</label>
                        </div>
                        <div class="col-9">
                            <input type="number" name="id" class="form-control" id="id">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="title">Judul Buku</label>
                        </div>
                        <div class="col-9">
                            <input type="text" name="title" class="form-control" id="title">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="author">Pengarang</label>
                        </div>
                        <div class="col-9">
                            <input type="text" name="author" class="form-control" id="author">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="publication-year">Tahun Terbit</label>
                        </div>
                        <div class="col-9">
                            <select name="year" id="publication-year" class="form-control">
                                @for ($i = 1800; $i <= (int) date('Y'); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="price">Harga</label>
                        </div>
                        <div class="col-9">
                            <input type="number" name="price" class="form-control" id="price">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <label for="qty">Kuantitas</label>
                        </div>
                        <div class="col-9">
                            <input type="number" name="qty" class="form-control" id="qty">
                        </div>
                    </div>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Data Karyawan
            </h3>
            <div class="card-tools" id="reset-books-button">
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
                                <option value="title">Nama</option>
                                <option value="author">Pengarang</option>
                                <option value="year">Tahun Terbit</option>
                                <option value="price">Harga</option>
                                <option value="qty">Kuantitas</option>
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
                            <input type="text" class="form-control" id="filter" placeholder="Cari...">
                        </div>
                    </div>
                </div>
            </div>
            <table class="table" id="books-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Tahun Terbit</th>
                        <th>Harga</th>
                        <th>Kuantitas</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Books goes here -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let books;
        let filteredBooks;

        $(function() {
            // Load data buku saat halaman dimuat
            books = filteredBooks = JSON.parse(localStorage.getItem('books')) ?? [];
            sortBooks();
            renderBooks();

            $('#add-book-form').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();
                let book = {};
                // Mapping data dari serializeArray menjadi bentuk valid objek karyawan
                formData.forEach((item) => {
                    book[item.name] = (item.name === 'id' ||
                            item.name === 'price' ||
                            item.name === 'year' ||
                            item.name === 'qty') ?
                        Number(item.value) :
                        item.value
                });
                // Tambah data baru ke dalam array books / buku
                addBook(book);
            });

            // Event ketika dropdown sort direction diubah
            $('#sort-direction').on('change', function(e) {
                sortBooks()
                renderBooks();
            });

            // Event ketika dropdown sort by diubah
            $('#sort-by').on('change', function(e) {
                sortBooks();
                renderBooks();
            });

            // Event ketika inputan filter diubah / diisi
            $('#filter').on('change keydown', function() {
                setTimeout(() => {
                    filterBooks($(this).val());
                    renderBooks();
                });
            })

            // Event ketika tombol reset buku di klik
            $('#reset-books-button').on('click', resetBooks);
        });

        // Function untuk mengurutkan data array 2 dimensi menggunakan metode insertion sort
        const insertionSort = (arr, sortBy, sortDirection) => {
            for (let i = 1; i < arr.length; i++) {
                // Menampung data array dengan index ke-i
                let curr = arr[i];
                let j = i - 1;
                // Membandingkan data dengan index ke-i dengan data sebelumnya (j)
                if (sortDirection === 'ASC') {
                    while (j >= 0 && arr[j][sortBy] > curr[sortBy]) {
                        arr[j + 1] = arr[j];
                        j--;
                    }
                } else {
                    while (j >= 0 && arr[j][sortBy] < curr[sortBy]) {
                        arr[j + 1] = arr[j];
                        j--;
                    }
                }
                arr[j + 1] = curr;
            }
        }

        // Mengurutkan data buku dengan menggunakan metode insertion sort
        const sortBooks = () => {
            insertionSort(filteredBooks, $('#sort-by').val(), $('#sort-direction').val());
        }

        // Menambakan data baru ke dalam array book / buku
        const addBook = (book) => {
            books.push(book);
            filteredBooks = books;
            $('#filter').val('').trigger('change');
            $('#add-book-form').trigger('reset');
            localStorage.setItem('books', JSON.stringify(books));
            renderBooks();
        }

        // Menampilkan atau mengupdate tabel buku
        const renderBooks = () => {
            let row = '';
            filteredBooks.forEach((book) => {
                row += `<tr>
                        <td>${book.id}</td>
                        <td>${book.title}</td>
                        <td>${book.author}</td>
                        <td>${book.year}</td>
                        <td>${book.price}</td>
                        <td>${book.qty}</td>
                    </tr>`;
            });
            $('#books-table tbody').html(row);
        }

        const filterBooks = (keyword) => {
            // Array untuk menampung data yang difilter
            let arr = [];
            // Mengecek apakah data di dalam array books / buku mengandung kata kunci yang dikirim menggunakan method .includes()
            // Karena method .includes() membandingkan string secara case sensitive, maka string diubah ke huruf kecil terlebih dahulu
            keyword = keyword.toLowerCase();
            for (let i = 0; i < books.length; i++) {
                // Khusus untuk id, qty, price, dan year karena berupa number maka harus diubah ke bentuk string ".toString()" agar bisa menggunakan method .includes()
                if (books[i].id.toString().toLowerCase().includes(keyword) ||
                    books[i].title.toLowerCase().includes(keyword) ||
                    books[i].author.toLowerCase().includes(keyword) ||
                    books[i].year.toString().toLowerCase().includes(keyword) ||
                    books[i].price.toString().toLowerCase().includes(keyword) ||
                    books[i].qty.toString().toLowerCase().includes(keyword)
                ) {
                    // Jika kondisi terpenuhi, maka data array dengan index ke-i dimasukan ke dalam array penampung
                    arr.push(books[i]);
                }
            }
            filteredBooks = arr;
        }

        // Menghapus data buku di dalam variabel books dan localStorage
        const resetBooks = () => {
            if (confirm('Hapus semua buku?')) {
                books = filteredBooks = [];
                localStorage.removeItem('books');
                renderBooks();
            }
        }
    </script>
@endpush
