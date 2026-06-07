<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - BloodLink</title>
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
        <a href="{{ route('admin.users') }}" class="sidebar-link active">
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
        <a href="{{ route('profile') }}" class="sidebar-link">
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
                <h3><i class="fas fa-users"></i> Manage Users</h3>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="donor" {{ request('role') == 'donor' ? 'selected' : '' }}>Donor</option>
                            <option value="hospital" {{ request('role') == 'hospital' ? 'selected' : '' }}>Hospital</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        @if (request('search') || request('role'))
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary w-100">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Verified</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-size:0.8rem;font-weight:700;flex-shrink:0;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <strong>{{ $user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'dark' : ($user->role === 'hospital' ? 'info' : ($user->role === 'patient' ? 'secondary' : 'success')) }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if ($user->email_verified_at)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            @if (!$user->email_verified_at)
                                                <form method="POST" action="{{ route('admin.user.approve', $user) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('messages.show', $user) }}" class="btn btn-outline-primary btn-sm" title="Send Message">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($users->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No users found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
