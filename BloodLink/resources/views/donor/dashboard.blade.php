<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - BloodLink</title>
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
        <a href="{{ route('donor.dashboard') }}" class="sidebar-link active">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('donor.requests') }}" class="sidebar-link">
            <i class="fas fa-list"></i> Browse Requests
        </a>
        <a href="{{ route('donor.history') }}" class="sidebar-link">
            <i class="fas fa-history"></i> Donation History
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
            <i class="fas fa-user-cog"></i> My Profile
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
                <h3><i class="fas fa-tachometer-alt"></i> Donor Dashboard</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('donor.requests') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-search me-1"></i> Browse Requests
                </a>
            </div>
        </div>

        @php
            $donor = Auth::user()->donor;
            $donationsCount = $donor ? $donor->donations()->count() : 0;
            $totalMl = $donor ? $donor->donations()->sum('quantity') : 0;
            $livesSaved = $donationsCount * 3;
            $matchingRequests = $donor ? \App\Models\BloodRequest::where('blood_type', $donor->blood_type)->where('status', 'open')->count() : 0;
            $isEligible = $donor ? $donor->isDonationEligible() : false;
            $daysUntilEligible = $donor ? $donor->getDaysUntilEligible() : 0;
            $recentDonations = $donor ? $donor->donations()->with('bloodRequest.hospital')->latest()->take(3)->get() : collect();
        @endphp

        <div class="row g-3">
            <div class="col-md-4 col-6">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-droplet"></i></div>
                    <div class="stat-label">Blood Type</div>
                    <div class="stat-value">{{ $donor?->blood_type ?? 'N/A' }}</div>
                    <i class="fas fa-droplet stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="stat-card {{ $isEligible ? 'success' : 'warning' }}">
                    <div class="stat-icon"><i class="fas {{ $isEligible ? 'fa-check-circle' : 'fa-clock' }}"></i></div>
                    <div class="stat-label">Eligibility</div>
                    <div class="stat-value" style="font-size:1.2rem;">{{ $isEligible ? 'Eligible' : $daysUntilEligible . ' days' }}</div>
                    <i class="fas fa-heartbeat stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <div class="stat-label">Matching Requests</div>
                    <div class="stat-value">{{ $matchingRequests }}</div>
                    <i class="fas fa-bell stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-heart"></i></div>
                    <div class="stat-label">Donations</div>
                    <div class="stat-value">{{ $donationsCount }}</div>
                    <i class="fas fa-heart stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-tint"></i></div>
                    <div class="stat-label">Total Donated</div>
                    <div class="stat-value" style="font-size:1.3rem;">{{ $totalMl }}ml</div>
                    <i class="fas fa-droplet stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="stat-label">Lives Saved</div>
                    <div class="stat-value">{{ $livesSaved }}</div>
                    <i class="fas fa-heartbeat stat-bg-icon"></i>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card {{ $donor?->availability ? 'success' : 'secondary' }}">
                    <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                    <div class="stat-label">Availability</div>
                    <div class="stat-value" style="font-size:1.2rem;">{{ $donor?->availability ? 'Available' : 'Unavailable' }}</div>
                    <i class="fas fa-user-check stat-bg-icon"></i>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-bullhorn me-2 text-danger"></i> Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('donor.requests') }}" class="btn btn-outline-danger py-3 text-start">
                                <i class="fas fa-hand-holding-heart me-2"></i> View Blood Requests
                                @if ($matchingRequests > 0)
                                    <span class="badge bg-danger float-end mt-1">{{ $matchingRequests }} open</span>
                                @endif
                            </a>
                            <a href="{{ route('donor.history') }}" class="btn btn-outline-primary py-3 text-start">
                                <i class="fas fa-history me-2"></i> My Donation History
                                <span class="float-end text-muted small mt-1">{{ $donationsCount }} donations</span>
                            </a>
                            <a href="{{ route('profile') }}" class="btn btn-outline-secondary py-3 text-start">
                                <i class="fas fa-user-cog me-2"></i> Update Profile
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
                        <span><i class="fas fa-history me-2 text-danger"></i> Recent Donations</span>
                        <a href="{{ route('donor.history') }}" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body">
                        @if ($recentDonations->isEmpty())
                            <div class="empty-state" style="padding:1.5rem;">
                                <div class="empty-icon" style="font-size:2rem;"><i class="fas fa-droplet"></i></div>
                                <h5 style="font-size:1rem;">No Donations Yet</h5>
                                <p style="font-size:0.85rem;">Your donation history will appear here once you make your first donation.</p>
                                <a href="{{ route('donor.requests') }}" class="btn btn-danger btn-sm">Browse Requests</a>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach ($recentDonations as $d)
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $d->quantity }}ml</strong>
                                            <span class="badge bg-danger ms-2">{{ $d->bloodRequest?->blood_type ?? 'N/A' }}</span>
                                            <div class="small text-muted">{{ $d->bloodRequest?->hospital?->name ?? 'N/A' }}</div>
                                        </div>
                                        <small class="text-muted">{{ $d->donation_date?->format('M d, Y') ?? $d->created_at->format('M d, Y') }}</small>
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
