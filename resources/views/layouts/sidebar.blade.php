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
                        <ion-icon class="nav-icon" name="desktop-outline"></ion-icon>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-header mt-2">ADMINISTRATOR</li>
                <li class="nav-item">
                    <a href="/outlets" class="nav-link">
                        <ion-icon class="nav-icon" name="storefront-outline"></ion-icon>
                        <p>
                            Kelola Outlet
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/users" class="nav-link">
                        <ion-icon class="nav-icon" name="people-outline"></ion-icon>
                        <p>
                            Kelola Users
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/inventories" class="nav-link">
                        <ion-icon class="nav-icon" name="cube-outline"></ion-icon>
                        <p>
                            Kelola Inventaris
                        </p>
                    </a>
                </li>
                <li class="nav-header">OUTLET</li>
                @if (Auth::user()->is_super === 1 &&
    request()->session()->has('outlet'))
                    <li class="nav-item">
                        <a href="/select-outlet" class="nav-link">
                            <ion-icon class="nav-icon" name="storefront-outline"></ion-icon>
                            <p>
                                Pilih Outlet
                            </p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    @if (Auth::user()->is_super)
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/services"
                                class="nav-link">
                                <ion-icon class="nav-icon" name="shirt-outline"></ion-icon>
                                <p>
                                    Layanan
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <ion-icon class="nav-icon" name="shirt-outline"></ion-icon>
                                <p>
                                    Layanan
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/services" class="nav-link">
                            <ion-icon class="nav-icon" name="shirt-outline"></ion-icon>
                            <p>
                                Layanan
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->is_super)
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/members" class="nav-link">
                                <ion-icon class="nav-icon" name="people-circle-outline"></ion-icon>
                                <p>
                                    Member
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <ion-icon class="nav-icon" name="people-circle-outline"></ion-icon>
                                <p>
                                    Member
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/members" class="nav-link">
                            <ion-icon class="nav-icon" name="people-circle-outline"></ion-icon>
                            <p>
                                Member
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-header">TRANSAKSI</li>
                <li class="nav-item">
                    @if (Auth::user()->is_super)
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/transactions"
                                class="nav-link">
                                <ion-icon class="nav-icon" name="document-text-outline"></ion-icon>
                                <p>
                                    Data Pesanan
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <ion-icon class="nav-icon" name="document-text-outline"></ion-icon>
                                <p>
                                    Data Pesanan
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/transactions" class="nav-link">
                            <ion-icon class="nav-icon" name="document-text-outline"></ion-icon>
                            <p>
                                Data Pesanan
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (Auth::user()->is_super)
                        @if (request()->session()->has('outlet'))
                            <a href="/o/{{ request()->session()->get('outlet')->id }}/transactions/new-transaction"
                                class="nav-link">
                                <ion-icon class="nav-icon" name="cart-outline"></ion-icon>
                                <p>
                                    Transaksi Baru
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <ion-icon class="nav-icon" name="cart-outline"></ion-icon>
                                <p>
                                    Transaksi Baru
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/transactions/new-transaction"
                            class="nav-link">
                            <ion-icon class="nav-icon" name="cart-outline"></ion-icon>
                            <p>
                                Transaksi Baru
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    <a href="/transactions/report" class="nav-link">
                        <ion-icon class="nav-icon" name="bar-chart-outline"></ion-icon>
                        <p>
                            Laporan
                        </p>
                    </a>
                </li>
                <li class="nav-header">SETTINGS</li>
                <li class="nav-item">
                    <a href="/profile/edit" class="nav-link">
                        <ion-icon class="nav-icon" name="bar-chart-outline"></ion-icon>
                        <p>
                            Edit Profile
                        </p>
                    </a>
                </li>
                <li class="nav-header">SIMULASI</li>
                <li class="nav-item">
                    <a href="/simulation/employee" class="nav-link">
                        <ion-icon class="nav-icon" name="people-circle-outline"></ion-icon>
                        <p>
                            Simulasi Karyawan
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/simulation/books" class="nav-link">
                        <ion-icon class="nav-icon" name="file-tray-stacked-outline"></ion-icon>
                        <p>
                            Simulasi Data Buku
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
