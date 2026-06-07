<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <span class="brand-icon"><i class="fas fa-droplet"></i></span>
            BloodLink
        </a>
        @auth
            <div class="d-flex align-items-center gap-2 d-lg-none">
                <button class="sidebar-toggle" type="button" id="mobileSidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                    <i class="fas fa-ellipsis-v" style="font-size: 1.2rem;"></i>
                </button>
            </div>
        @else
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
        @endauth
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#features') }}">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#how-it-works') }}">How It Works</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn-login" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn-register" href="{{ route('register') }}"><i class="fas fa-user-plus me-1"></i> Register</a>
                    </li>
                @endguest
                @auth
                    <li class="nav-item dropdown user-dropdown">
                        <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            <span class="d-none d-md-inline fw-semibold" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-muted ps-3">{{ Auth::user()->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            @if (Auth::user()->role === 'donor')
                                <li><a class="dropdown-item" href="{{ route('donor.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('donor.requests') }}"><i class="fas fa-list"></i> Requests</a></li>
                                <li><a class="dropdown-item" href="{{ route('donor.history') }}"><i class="fas fa-history"></i> History</a></li>
                            @elseif (Auth::user()->role === 'hospital')
                                <li><a class="dropdown-item" href="{{ route('hospital.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('hospital.requests') }}"><i class="fas fa-list"></i> Requests</a></li>
                            @elseif (Auth::user()->role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.analytics') }}"><i class="fas fa-chart-bar"></i> Analytics</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user-cog"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('friends') }}"><i class="fas fa-user-friends"></i> Friends</a></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('messages') }}">
                                    <span><i class="fas fa-envelope"></i> Messages</span>
                                    @php $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count(); @endphp
                                    @if ($unreadCount > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
