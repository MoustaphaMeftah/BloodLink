<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Requests - BloodLink</title>
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
        <a href="{{ route('donor.dashboard') }}" class="sidebar-link">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('donor.requests') }}" class="sidebar-link active">
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
                <h3><i class="fas fa-list"></i> Available Blood Requests</h3>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $donor = Auth::user()->donor;
            $respondedRequestIds = $donor ? $donor->responses()->pluck('blood_request_id')->toArray() : [];
            $respondedStatuses = $donor ? $donor->responses()->pluck('status', 'blood_request_id')->toArray() : [];
        @endphp

        @if ($requests->isEmpty())
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                        <h5>No Matching Requests</h5>
                        <p>There are currently no blood requests matching your profile. Check back later or update your availability.</p>
                        <a href="{{ route('donor.dashboard') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Desktop Table -->
            <div class="card d-none d-md-block">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Blood Type</th>
                                    <th>Quantity</th>
                                    <th>Urgency</th>
                                    <th>Location</th>
                                    <th>Hospital</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $req)
                                    @php $responded = in_array($req->id, $respondedRequestIds); @endphp
                                    <tr class="{{ $responded ? 'table-light' : '' }}">
                                        <td><span class="badge bg-danger">{{ $req->blood_type }}</span></td>
                                        <td><strong>{{ $req->quantity }}ml</strong></td>
                                        <td>
                                            @if ($req->urgency === 'critical')
                                                <span class="badge urgency-critical pulse-critical"><i class="fas fa-exclamation-circle me-1"></i>Critical</span>
                                            @elseif ($req->urgency === 'high')
                                                <span class="badge urgency-high">High</span>
                                            @elseif ($req->urgency === 'medium')
                                                <span class="badge urgency-medium">Medium</span>
                                            @else
                                                <span class="badge urgency-low">Low</span>
                                            @endif
                                        </td>
                                        <td><i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $req->location }}</td>
                                        <td>{{ $req->hospital->name ?? 'N/A' }}</td>
                                        <td>
                                            @php $hospitalUser = $req->hospital?->user; @endphp
                                            @if ($responded)
                                                @php $status = $respondedStatuses[$req->id] ?? ''; @endphp
                                                <div class="d-flex gap-1 align-items-center">
                                                    @if ($status === 'accepted')
                                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Accepted</span>
                                                    @else
                                                        <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Declined</span>
                                                    @endif
                                                    @if ($hospitalUser)
                                                        <a href="{{ route('messages.show', $hospitalUser) }}" class="btn btn-outline-primary btn-sm" title="Message Hospital">
                                                            <i class="fas fa-envelope"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="d-flex gap-1">
                                                    <form method="POST" action="{{ route('donor.respond', $req->id) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="accepted" value="1">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check me-1"></i> Accept
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('donor.respond', $req->id) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="accepted" value="0">
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                            Decline
                                                        </button>
                                                    </form>
                                                    @if ($hospitalUser)
                                                        <a href="{{ route('messages.show', $hospitalUser) }}" class="btn btn-outline-primary btn-sm" title="Message Hospital">
                                                            <i class="fas fa-envelope"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($requests->hasPages())
                    <div class="card-footer">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>

            <!-- Mobile Cards -->
            <div class="d-md-none">
                <div class="row g-3">
                    @foreach ($requests as $req)
                        @php $responded = in_array($req->id, $respondedRequestIds); $status = $respondedStatuses[$req->id] ?? ''; @endphp
                        <div class="col-12">
                            <div class="request-card" style="{{ $responded ? 'opacity:0.75;' : '' }}">
                                <div class="request-header">
                                    <div>
                                        <div class="request-blood-type">{{ $req->blood_type }}</div>
                                        <div style="font-size:0.85rem; color:var(--text-secondary);">{{ $req->hospital->name ?? 'N/A' }}</div>
                                    </div>
                                    @if ($req->urgency === 'critical')
                                        <span class="badge urgency-critical pulse-critical"><i class="fas fa-exclamation-circle me-1"></i>Critical</span>
                                    @elseif ($req->urgency === 'high')
                                        <span class="badge urgency-high">High</span>
                                    @elseif ($req->urgency === 'medium')
                                        <span class="badge urgency-medium">Medium</span>
                                    @else
                                        <span class="badge urgency-low">Low</span>
                                    @endif
                                </div>
                                <div class="request-info">
                                    <div class="info-item">
                                        <span class="label">Quantity</span>
                                        {{ $req->quantity }}ml
                                    </div>
                                    <div class="info-item">
                                        <span class="label">Location</span>
                                        {{ $req->location }}
                                    </div>
                                </div>
                                <div class="request-actions">
                                    @php $hospitalUser = $req->hospital?->user; @endphp
                                    @if ($responded)
                                        @if ($status === 'accepted')
                                            <span class="badge bg-success py-2"><i class="fas fa-check me-1"></i>Accepted</span>
                                        @else
                                            <span class="badge bg-secondary py-2"><i class="fas fa-times me-1"></i>Declined</span>
                                        @endif
                                        @if ($hospitalUser)
                                            <a href="{{ route('messages.show', $hospitalUser) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-envelope me-1"></i> Message
                                            </a>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('donor.respond', $req->id) }}" class="flex-grow-1">
                                            @csrf
                                            <input type="hidden" name="accepted" value="1">
                                            <button type="submit" class="btn btn-success w-100 btn-sm">
                                                <i class="fas fa-check me-1"></i> Accept
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('donor.respond', $req->id) }}">
                                            @csrf
                                            <input type="hidden" name="accepted" value="0">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                Decline
                                            </button>
                                        </form>
                                        @if ($hospitalUser)
                                            <a href="{{ route('messages.show', $hospitalUser) }}" class="btn btn-outline-primary btn-sm" title="Message Hospital">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($requests->hasPages())
                    <div class="mt-3">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        @endif
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
