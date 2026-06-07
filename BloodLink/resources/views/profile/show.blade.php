<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div class="sidebar-title">Main Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="sidebar-link">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="{{ route('admin.analytics') }}" class="sidebar-link">
            <i class="fas fa-chart-bar"></i> Analytics
        </a>
        <div class="sidebar-title">Communication</div>
        <a href="{{ route('messages') }}" class="sidebar-link d-flex align-items-center justify-content-between">
            <span><i class="fas fa-envelope"></i> Messages</span>
            @php $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count(); @endphp
            @if ($unreadCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
            @endif
        </a>
        <div class="sidebar-title">Account</div>
        <a href="{{ route('profile') }}" class="sidebar-link active">
            <i class="fas fa-user-shield"></i> Admin Profile
        </a>
        <div class="mt-4 px-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <button class="sidebar-toggle me-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h3><i class="fas fa-user-cog"></i> My Profile</h3>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div style="width:80px;height:80px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;color:white;font-weight:700;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <div class="text-muted small mb-3">{{ $user->email }}</div>
                        <span class="badge bg-{{ $user->role === 'admin' ? 'dark' : ($user->role === 'hospital' ? 'info' : 'success') }}" style="font-size:0.8rem;">
                            {{ ucfirst($user->role) }}
                        </span>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Phone</span>
                                <span class="small fw-medium">{{ $user->phone ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">City</span>
                                <span class="small fw-medium">{{ $user->city ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted small">Joined</span>
                                <span class="small fw-medium">{{ $user->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-edit me-2 text-danger"></i> Edit Profile
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $user->city) }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" id="role" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save me-1"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
