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
    @include('partials.sidebar')

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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count();
            $pendingFriendRequests = \App\Models\Friend::where('friend_id', Auth::id())->where('status', 'pending')->with('requester')->get();
            $donationsCount = $donor ? $donor->donations()->count() : 0;
            $totalMl = $donor ? $donor->donations()->sum('quantity') : 0;
            $livesSaved = $donationsCount * 3;
            $matchingRequests = $donor ? \App\Models\BloodRequest::where('blood_type', $donor->blood_type)->where('status', 'open')->count() : 0;
            $isEligible = $donor ? $donor->isDonationEligible() : false;
            $daysUntilEligible = $donor ? $donor->getDaysUntilEligible() : 0;
            $recentDonations = $donor ? $donor->donations()->with('bloodRequest.hospital')->latest()->take(3)->get() : collect();
            $badges = [];
            if ($donationsCount >= 1) $badges[] = ['icon' => 'fa-droplet', 'label' => 'First Donation', 'color' => '#6c757d'];
            if ($donationsCount >= 3) $badges[] = ['icon' => 'fa-heart', 'label' => '3 Donations', 'color' => '#28a745'];
            if ($donationsCount >= 5) $badges[] = ['icon' => 'fa-star', 'label' => '5 Donations', 'color' => '#17a2b8'];
            if ($donationsCount >= 10) $badges[] = ['icon' => 'fa-crown', 'label' => '10 Donations', 'color' => '#ffc107'];
            if ($totalMl >= 1000) $badges[] = ['icon' => 'fa-trophy', 'label' => '1L+ Donated', 'color' => '#dc3545'];
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

        @if (!empty($badges))
        <div class="row g-3 mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-award me-2 text-danger"></i> Badges
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3 flex-wrap">
                            @foreach ($badges as $b)
                                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:{{ $b['color'] }}15;border:1px solid {{ $b['color'] }}30;">
                                    <i class="fas {{ $b['icon'] }}" style="color:{{ $b['color'] }};"></i>
                                    <span style="color:{{ $b['color'] }};font-weight:600;font-size:0.85rem;">{{ $b['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                            @if ($pendingFriendRequests->isNotEmpty())
                            <a href="{{ route('friends') }}" class="btn btn-outline-warning py-3 text-start">
                                <i class="fas fa-user-friends me-2"></i> Friend Requests
                                <span class="badge bg-danger float-end mt-1">{{ $pendingFriendRequests->count() }} pending</span>
                            </a>
                            @endif
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

        @php
            $recentActivity = $donor ? $donor->responses()->with('bloodRequest.hospital')->latest()->take(5)->get() : collect();
        @endphp
        @if ($recentActivity->isNotEmpty())
        <div class="row g-3 mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clock me-2 text-danger"></i> Recent Activity
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach ($recentActivity as $act)
                                <div class="list-group-item px-3 d-flex align-items-center gap-3">
                                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $act->status === 'accepted' ? 'rgba(40,167,69,0.1)' : 'rgba(108,117,125,0.1)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="fas {{ $act->status === 'accepted' ? 'fa-check' : 'fa-times' }}" style="color:{{ $act->status === 'accepted' ? '#28a745' : '#6c757d' }};font-size:0.8rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="small">
                                            <strong>{{ ucfirst($act->status) }}</strong> request for
                                            <span class="badge bg-danger" style="font-size:0.6rem;">{{ $act->bloodRequest?->blood_type ?? '?' }}</span>
                                            @if ($act->bloodRequest?->hospital)
                                                at {{ $act->bloodRequest->hospital->name }}
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $act->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>

<div class="modal fade" id="locationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt text-danger me-2"></i>Enable Location</h5>
            </div>
            <div class="modal-body text-center py-4">
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-crosshairs" style="font-size:1.8rem;color:#dc3545;"></i>
                </div>
                <h6>Find Requests Near You</h6>
                <p class="small text-muted mb-0">Allow location access to see nearby blood requests that match your type. We only use your location to show relevant opportunities.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Skip</button>
                <button type="button" class="btn btn-danger" id="enableLocationBtn" onclick="enableLocation()">
                    <i class="fas fa-crosshairs me-1"></i> Use My Location
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
@if ($needsLocation)
    const locModal = new bootstrap.Modal(document.getElementById('locationModal'));
    locModal.show();
@endif

function enableLocation() {
    if (navigator.geolocation) {
        document.getElementById('enableLocationBtn').disabled = true;
        document.getElementById('enableLocationBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Locating...';
        navigator.geolocation.getCurrentPosition(function(pos) {
            fetch('{{ route('donor.location.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: pos.coords.latitude,
                    longitude: pos.coords.longitude
                })
            }).then(function() { location.reload(); });
        }, function() {
            document.getElementById('enableLocationBtn').disabled = false;
            document.getElementById('enableLocationBtn').innerHTML = '<i class="fas fa-crosshairs me-1"></i> Use My Location';
            alert('Could not get your location. Please enable location access in your browser settings.');
        });
    } else {
        alert('Geolocation is not supported by your browser.');
    }
}
</script>
</body>
</html>
