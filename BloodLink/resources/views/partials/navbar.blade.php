<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="fas fa-droplet text-danger"></i> BloodLink
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#features') }}">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#about') }}">About</a></li>
                @endguest
                @auth
                    @if (Auth::user()->role === 'donor')
                        <li class="nav-item"><a class="nav-link" href="{{ route('donor.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('donor.requests') }}">Requests</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('donor.history') }}">History</a></li>
                    @elseif (Auth::user()->role === 'hospital')
                        <li class="nav-item"><a class="nav-link" href="{{ route('hospital.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('hospital.requests') }}">Requests</a></li>
                    @elseif (Auth::user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.analytics') }}">Analytics</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ route('profile') }}">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('messages') }}">Messages</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger ms-2">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-danger text-white ms-2" href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
