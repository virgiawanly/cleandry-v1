<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="{{ asset('adminlte') }}/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Cleandry</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        @if (request()->session()->has('outlet'))
            <!-- Sidebar user (optional) -->
            <div class="user-panel p-3">
                <div class="text-sm text-secondary">Outlet</div>
                <a href="#" class="d-block">{{ request()->session()->get('outlet')->name }}</a>
            </div>
        @endif

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
       with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-header">ADMINISTRATOR</li>
                <li class="nav-item">
                    <a href="/outlets" class="nav-link">
                        <i class="nav-icon fas fa-store"></i>
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
                <li class="nav-header">OUTLET</li>
                @if (Auth::user()->is_super === 1 &&
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
                    @if (Auth::user()->is_super)
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
                                <i class="nav-icon fas fa-shopping-basket"></i>
                                <p>
                                    Layanan
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/services" class="nav-link">
                            <i class="nav-icon fas fa-shopping-basket"></i>
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
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Member
                                </p>
                            </a>
                        @else
                            <a href="/select-outlet" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Member
                                </p>
                            </a>
                        @endif
                    @elseif(Auth::user()->outlet_id)
                        <a href="/o/{{ Auth::user()->outlet_id }}/members" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Member
                            </p>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tshirt"></i>
                        <p>
                            Cucian
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Diproses</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Selesai</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dibatalkan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">TRANSAKSI</li>
                <li class="nav-item">
                    @if (Auth::user()->is_super)
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
                    @elseif(Auth::user()->outlet_id)
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
                    <a href="/transactions/report" class="nav-link">
                        <i class="nav-icon fas fa-file"></i>
                        <p>
                            Laporan
                        </p>
                    </a>
                </li>
                <li class="nav-header">SETTINGS</li>
                <li class="nav-item">
                    <a href="/profile/edit" class="nav-link">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>
                            Edit Profile
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
