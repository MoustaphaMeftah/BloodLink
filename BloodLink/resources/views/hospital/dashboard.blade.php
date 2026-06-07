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
    $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count();
    $pendingFriendRequests = \App\Models\Friend::where('friend_id', Auth::id())->where('status', 'pending')->with('requester')->get();
    $notificationCount = \App\Models\Notification::where('user_id', Auth::id())->where('read_status', false)->count();
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
    @include('partials.sidebar')

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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
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
                            @if ($pendingFriendRequests->isNotEmpty())
                            <a href="{{ route('friends') }}" class="btn btn-outline-warning py-3 text-start">
                                <i class="fas fa-user-friends me-2"></i> Friend Requests
                                <span class="badge bg-danger float-end mt-1">{{ $pendingFriendRequests->count() }} pending</span>
                            </a>
                            @endif
                            @if ($notificationCount > 0)
                            <a href="{{ route('friends') }}" class="btn btn-outline-warning py-3 text-start">
                                <i class="fas fa-bell me-2"></i> Notifications
                                <span class="badge bg-warning text-dark float-end mt-1">{{ $notificationCount }}</span>
                            </a>
                            @endif
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

        @php
            $recentActivity = $hospital ? \App\Models\BloodRequest::where('hospital_id', $hospital->id)->with('responses.donor.user')->latest()->take(5)->get() : collect();
        @endphp
        @if ($recentActivity->isNotEmpty())
        <div class="row g-3 mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clock me-2 text-danger"></i> Recent Requests Activity
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach ($recentActivity as $reqAct)
                                <div class="list-group-item px-3 d-flex align-items-center gap-3">
                                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="fas fa-tint" style="color:var(--primary);font-size:0.8rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="small">
                                            <span class="badge bg-danger" style="font-size:0.6rem;">{{ $reqAct->blood_type }}</span>
                                            {{ $reqAct->quantity }}ml —
                                            <span class="badge bg-{{ $reqAct->status === 'open' ? 'success' : ($reqAct->status === 'fulfilled' ? 'primary' : 'secondary') }}" style="font-size:0.6rem;">{{ ucfirst($reqAct->status) }}</span>
                                            ({{ $reqAct->responses->count() }} response{{ $reqAct->responses->count() !== 1 ? 's' : '' }})
                                        </div>
                                        <small class="text-muted">{{ $reqAct->created_at->diffForHumans() }}</small>
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
                <h6>Find Donors Near Your Hospital</h6>
                <p class="small text-muted mb-0">Allow location access to find nearby available donors. Your location helps us match you with donors in your area.</p>
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
            fetch('{{ route('hospital.location.update') }}', {
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
