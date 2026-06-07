<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

@php
    $hospitalUser = Auth::user();
    $hospital = $hospitalUser->hospital;
    $allHospitalReqs = $hospital ? \App\Models\BloodRequest::where('hospital_id', $hospital->id)->get() : collect();
    $totalReqs = $allHospitalReqs->count();
    $fulfilledReqs = $allHospitalReqs->where('status', 'fulfilled')->count();
    $openReqs = $allHospitalReqs->where('status', 'open')->count();
    $cancelledReqs = $allHospitalReqs->where('status', 'cancelled')->count();
    $criticalReqs = $allHospitalReqs->where('urgency', 'critical')->where('status', 'open')->count();
    $highReqs = $allHospitalReqs->where('urgency', 'high')->where('status', 'open')->count();
    $totalDonorsResponded = $hospital ? \App\Models\DonorResponse::whereIn('blood_request_id', $allHospitalReqs->pluck('id'))->count() : 0;
    $recentResponses = $hospital ? \App\Models\DonorResponse::whereIn('blood_request_id', $allHospitalReqs->pluck('id'))->with(['donor.user', 'bloodRequest'])->latest()->take(5)->get() : collect();
@endphp

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div class="sidebar-title">Main Menu</div>
        <a href="{{ route('hospital.dashboard') }}" class="sidebar-link active">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('hospital.requests') }}" class="sidebar-link">
            <i class="fas fa-list"></i> My Requests
        </a>
        <a href="{{ route('hospital.request.create') }}" class="sidebar-link">
            <i class="fas fa-plus-circle"></i> Create Request
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
            <i class="fas fa-user-cog"></i> Hospital Profile
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
                <h3><i class="fas fa-hospital"></i> {{ $hospital->name ?? 'Hospital' }} Dashboard</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('hospital.request.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus me-1"></i> New Request
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-tint"></i></div>
                    <div class="stat-label">Total Requests</div>
                    <div class="stat-value">{{ $totalReqs }}</div>
                    <i class="fas fa-droplet stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-label">Fulfilled</div>
                    <div class="stat-value">{{ $fulfilledReqs }}</div>
                    <i class="fas fa-check stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-label">Open</div>
                    <div class="stat-value">{{ $openReqs }}</div>
                    <i class="fas fa-hourglass stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
                    <div class="stat-label">Donor Responses</div>
                    <div class="stat-value">{{ $totalDonorsResponded }}</div>
                    <i class="fas fa-user-friends stat-bg-icon"></i>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card" style="border-left: 4px solid #dc3545;">
                    <div class="stat-label text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i>Critical Open</div>
                    <div class="stat-value" style="font-size:1.5rem; color:#dc3545;">{{ $criticalReqs }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card" style="border-left: 4px solid #ffc107;">
                    <div class="stat-label text-warning fw-bold"><i class="fas fa-exclamation-triangle me-1"></i>High Open</div>
                    <div class="stat-value" style="font-size:1.5rem; color:#b8860b;">{{ $highReqs }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card" style="border-left: 4px solid #6c757d;">
                    <div class="stat-label text-secondary fw-bold"><i class="fas fa-times-circle me-1"></i>Cancelled</div>
                    <div class="stat-value" style="font-size:1.5rem;">{{ $cancelledReqs }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card" style="border-left: 4px solid #28a745;">
                    <div class="stat-label text-success fw-bold"><i class="fas fa-percentage me-1"></i>Fulfillment Rate</div>
                    <div class="stat-value" style="font-size:1.5rem; color:#28a745;">{{ $totalReqs > 0 ? round(($fulfilledReqs / $totalReqs) * 100) : 0 }}%</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-bolt me-2 text-danger"></i> Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('hospital.request.create') }}" class="btn btn-danger py-3 text-start">
                                <i class="fas fa-plus-circle me-2"></i> New Blood Request
                            </a>
                            <a href="{{ route('hospital.requests') }}" class="btn btn-outline-primary py-3 text-start">
                                <i class="fas fa-list me-2"></i> Manage Requests
                                @if ($openReqs > 0)
                                    <span class="badge bg-danger float-end mt-1">{{ $openReqs }} open</span>
                                @endif
                            </a>
                            <a href="{{ route('profile') }}" class="btn btn-outline-secondary py-3 text-start">
                                <i class="fas fa-building me-2"></i> Hospital Profile
                            </a>
                            <a href="{{ route('messages') }}" class="btn btn-outline-info py-3 text-start">
                                <i class="fas fa-envelope me-2"></i> Messages
                                @if ($unreadCount > 0)
                                    <span class="badge bg-danger float-end mt-1">{{ $unreadCount }} unread</span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-friends me-2 text-danger"></i> Recent Donor Responses</span>
                        <a href="{{ route('hospital.requests') }}" class="btn btn-sm btn-link">All Requests</a>
                    </div>
                    <div class="card-body">
                        @if ($recentResponses->isEmpty())
                            <div class="empty-state" style="padding:1.5rem;">
                                <div class="empty-icon" style="font-size:2rem;"><i class="fas fa-user-friends"></i></div>
                                <h5 style="font-size:1rem;">No Responses Yet</h5>
                                <p style="font-size:0.85rem;">Donor responses to your requests will appear here.</p>
                                <a href="{{ route('hospital.request.create') }}" class="btn btn-danger btn-sm">Create a Request</a>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach ($recentResponses as $resp)
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $resp->donor?->user?->name ?? 'Anonymous' }}</strong>
                                            <span class="badge bg-danger ms-2">{{ $resp->donor?->blood_type ?? '?' }}</span>
                                            <div class="small text-muted">
                                                <i class="fas fa-tint me-1"></i>{{ $resp->bloodRequest?->blood_type ?? '?' }} request
                                                <span class="badge bg-{{ $resp->bloodRequest?->status === 'open' ? 'success' : 'secondary' }} ms-1" style="font-size:0.65rem;">{{ $resp->bloodRequest?->status ?? '?' }}</span>
                                            </div>
                                        </div>
                                        <span class="badge bg-{{ $resp->status === 'accepted' ? 'success' : 'warning' }}">
                                            {{ ucfirst($resp->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
