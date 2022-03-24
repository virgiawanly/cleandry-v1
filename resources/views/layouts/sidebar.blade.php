<aside class="main-sidebar sidebar-dark-success elevation-1">
    <!-- Brand Logo -->
    <a href="/" class="brand-link d-flex align-items-center text-align-center py-4">
        <img src="{{ asset('img/logo_white.svg') }}" class="brand-image" style="opacity: .8">
        <span class="brand-text font-weight-light">
            <img src="{{ asset('img/logo_text_white.svg') }}" alt="" style="width: 120px">
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
       with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="/" class="nav-link active">
                        <i class="nav-icon fas fa-tv"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if (Auth::user()->role === 'admin')
                    <li class="nav-header mt-2">ADMINISTRATOR</li>
                    <li class="nav-item">
                        <a href="/outlets" class="nav-link">
                            <i class="nav-icon fas fa-store-alt"></i>
                            <p>
                                Kelola Outlet
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/users" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Kelola Users
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/service-types" class="nav-link">
                            <i class="nav-icon fas fa-tshirt"></i>
                            <p>
                                Kelola Jenis Cucian
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/inventories" class="nav-link">
                            <i class="nav-icon fas fa-box-open"></i>
                            <p>
                                Kelola Inventaris
                            </p>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'cashier')
                    <li class="nav-header">OUTLET</li>
                @endif
                @if (Auth::user()->role === 'admin' &&
    request()->session()->has('outlet'))
                    <li class="nav-item">
                        <a href="/select-outlet" class="nav-link">
                            <i class="nav-icon fas fa-store-alt"></i>
                            <p>
                                Pilih Outlet
                            </p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    @if (Auth::user()->role === 'admin')
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/services"
                                class="nav-link">
                                <i class="nav-icon fas fa-shopping-basket"></i>
                                <p>
                                    Layanan
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <i class="nav-icon fas fa-tshirt"></i>
                                <p>
                                    Layanan
                                </p>
                            </a>
                        @endif
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->role === 'admin')
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/members" class="nav-link">
                                <i class="nav-icon fas fa-address-book"></i>
                                <p>
                                    Member
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <i class="nav-icon fas fa-address-book"></i>
                                <p>
                                    Member
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->role === 'cashier' && Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/members" class="nav-link">
                            <i class="nav-icon fas fa-address-book"></i>
                            <p>
                                Member
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->role === 'admin')
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/pickups" class="nav-link">
                                <i class="nav-icon fas fa-truck"></i>
                                <p>
                                    Penjemputan
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <i class="nav-icon fas fa-truck"></i>
                                <p>
                                    Penjemputan
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->role === 'cashier' && Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/pickups" class="nav-link">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>
                                Penjemputan
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-header">TRANSAKSI</li>
                <li class="nav-item">
                    @if (Auth::user()->role === 'admin')
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/transactions"
                                class="nav-link">
                                <i class="nav-icon fas fa-archive"></i>
                                <p>
                                    Data Pesanan
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <i class="nav-icon fas fa-archive"></i>
                                <p>
                                    Data Pesanan
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->role === 'cashier' && Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/transactions" class="nav-link">
                            <i class="nav-icon fas fa-archive"></i>
                            <p>
                                Data Pesanan
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->role === 'admin')
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/transactions/new-transaction"
                                class="nav-link">
                                <i class="nav-icon fas fa-cash-register"></i>
                                <p>
                                    Transaksi Baru
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <i class="nav-icon fas fa-cash-register"></i>
                                <p>
                                    Transaksi Baru
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->role === 'cashier' && Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/transactions/new-transaction"
                            class="nav-link">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>
                                Transaksi Baru
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->role === 'admin')
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/report" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>
                                    Laporan
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>
                                    Laporan
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/report" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>
                                Laporan
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-header">SETTINGS</li>
                <li class="nav-item">
                    <a href="/edit-profile" class="nav-link">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>
                            Edit Profile
                        </p>
                    </a>
                </li>
                @if (Auth::user()->role === 'admin')
                    <li class="nav-header">SIMULASI</li>
                    <li class="nav-item">
                        <a href="/simulation/employee" class="nav-link">
                            <i class="nav-icon fas fa-address-book"></i>
                            <p>
                                Simulasi Karyawan
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/simulation/books" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Simulasi Data Buku
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/simulation/fee" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Simulasi Gaji Karyawan
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/simulation/transactions" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Simulasi Transaksi Barang
                            </p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
