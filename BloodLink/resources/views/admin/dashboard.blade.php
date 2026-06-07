<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

@php
    $pendingApprovals = \App\Models\User::whereNull('email_verified_at')->count();
    $totalDonations = \App\Models\Donation::count();
    $totalDonatedMl = \App\Models\Donation::sum('quantity');
    $totalMessages = \App\Models\Message::count();
    $unreadMessages = \App\Models\Message::whereNull('read_at')->count();
    $donorsCount = \App\Models\Donor::count();
    $hospitalsCount = \App\Models\Hospital::count();
    $fulfilledRequests = \App\Models\BloodRequest::where('status', 'fulfilled')->count();
    $recentUsers = \App\Models\User::latest()->take(5)->get();
    $recentDonations = \App\Models\Donation::with(['donor.user', 'bloodRequest'])->latest()->take(5)->get();

    $bloodTypeCounts = \App\Models\Donor::selectRaw('blood_type, COUNT(*) as count')
        ->groupBy('blood_type')
        ->pluck('count', 'blood_type')
        ->toArray();
    $allTypes = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
    $maxCount = max($bloodTypeCounts ?: [1]);
@endphp

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    @include('partials.sidebar')

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <button class="sidebar-toggle me-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h3><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.users') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-users me-1"></i> Manage Users
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value">{{ $stats['users_count'] }}</div>
                    <div class="stat-change text-danger small">{{ $donorsCount }} donors, {{ $hospitalsCount }} hospitals</div>
                    <i class="fas fa-users stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <div class="stat-label">Donors</div>
                    <div class="stat-value">{{ $stats['donors_count'] }}</div>
                    <i class="fas fa-hand-holding-heart stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-hospital"></i></div>
                    <div class="stat-label">Hospitals</div>
                    <div class="stat-value">{{ $stats['hospitals_count'] }}</div>
                    <i class="fas fa-hospital stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-tint"></i></div>
                    <div class="stat-label">Requests</div>
                    <div class="stat-value">{{ $stats['requests_count'] }}</div>
                    <i class="fas fa-tint stat-bg-icon"></i>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(220,53,69,0.12);color:#dc3545;"><i class="fas fa-clock"></i></div>
                    <div class="stat-label text-danger">Pending Approvals</div>
                    <div class="stat-value">{{ $pendingApprovals }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(23,162,184,0.12);color:#17a2b8;"><i class="fas fa-envelope-open-text"></i></div>
                    <div class="stat-label text-info">Messages</div>
                    <div class="stat-value">{{ $totalMessages }}</div>
                    <div class="stat-change text-muted small">{{ $unreadMessages }} unread</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(40,167,69,0.12);color:#28a745;"><i class="fas fa-heart"></i></div>
                    <div class="stat-label text-success">Donations</div>
                    <div class="stat-value">{{ $totalDonations }}</div>
                    <div class="stat-change text-muted small">{{ $totalDonatedMl }}ml total</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(255,193,7,0.12);color:#b8860b;"><i class="fas fa-check-double"></i></div>
                    <div class="stat-label text-warning">Fulfilled</div>
                    <div class="stat-value">{{ $fulfilledRequests }}</div>
                    <div class="stat-change text-muted small">of {{ $stats['requests_count'] }} requests</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-tint me-2 text-danger"></i> Donors by Blood Type
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            @foreach ($allTypes as $type)
                                @php $count = $bloodTypeCounts[$type] ?? 0; @endphp
                                <div>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-bold">{{ $type }}</span>
                                        <span class="text-muted">{{ $count }} donors</span>
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $maxCount > 0 ? ($count / $maxCount) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-user-plus me-2 text-danger"></i> Recent Registrations
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach ($recentUsers as $u)
                                <div class="list-group-item px-0 d-flex align-items-center gap-2">
                                    <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-size:0.75rem;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="small fw-bold text-truncate">{{ $u->name }}</div>
                                        <div class="small text-muted">
                                            <span class="badge bg-{{ $u->role === 'admin' ? 'dark' : ($u->role === 'hospital' ? 'info' : 'success') }}" style="font-size:0.6rem;">{{ $u->role }}</span>
                                            {{ $u->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer text-center p-2">
                        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-link">View All Users</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-droplet me-2 text-danger"></i> Recent Donations
                    </div>
                    <div class="card-body">
                        @if ($recentDonations->isEmpty())
                            <div class="text-center text-muted small py-3">No donations yet</div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach ($recentDonations as $d)
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="small">{{ $d->donor?->user?->name ?? 'Anonymous' }}</strong>
                                            <span class="badge bg-danger ms-1" style="font-size:0.6rem;">{{ $d->bloodRequest?->blood_type ?? '?' }}</span>
                                            <div class="small text-muted">{{ $d->quantity }}ml &middot; {{ $d->donation_date?->format('M d') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center p-2">
                        <a href="{{ route('admin.analytics') }}" class="btn btn-sm btn-link">View Analytics</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-download me-2 text-danger"></i> Export Data
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.export', ['type' => 'users']) }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-users me-1"></i> Export Users (CSV)
                            </a>
                            <a href="{{ route('admin.export', ['type' => 'donations']) }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-heart me-1"></i> Export Donations (CSV)
                            </a>
                            <a href="{{ route('admin.export', ['type' => 'requests']) }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-tint me-1"></i> Export Requests (CSV)
                            </a>
                        </div>
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
