<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="/" class="nav-link">Dashboard</a>
        </li>
        @if (Auth::user()->is_super === 1 &&
        request()->session()->has('outlet'))
        <li class="nav-item d-none d-sm-inline-block">
            <a href="/select-outlet" class="nav-link">Outlet</a>
        </li>
        @endif
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                @auth
                    <div>
                        <img src="https://picsum.photos/200/200" class="img-circle" alt="User Image"
                            style="width: 30px">
                        <span class="ml-1"> {{ Auth::user()->name }}</span>
                    </div>
                @endauth
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Edit Profile
                </a>
                <form id="logoutForm" action="/logout" method="POST" class="d-inline">
                    @csrf
                    <button type="button" class="dropdown-item" onclick="logoutHandler()">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
