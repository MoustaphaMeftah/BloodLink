<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Details - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        #reqDetailMap { height: 300px; border-radius: 8px; }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    @include('partials.sidebar')

    @php $hospital = Auth::user()->hospital; @endphp

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <button class="sidebar-toggle me-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h3><i class="fas fa-file-medical"></i> Request #{{ $request->id }}</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('hospital.requests') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-edit me-2 text-danger"></i> Edit Request</span>
                        <span class="badge bg-{{ $request->status === 'open' ? 'success' : ($request->status === 'fulfilled' ? 'primary' : 'secondary') }}" style="font-size:0.8rem;">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('hospital.request.update', $request) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="blood_type" class="form-label">Blood Type</label>
                                    <select name="blood_type" id="blood_type" class="form-select" required>
                                        @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $bt)
                                            <option value="{{ $bt }}" {{ $request->blood_type == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="quantity" class="form-label">Quantity (ml)</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $request->quantity }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="urgency" class="form-label">Urgency</label>
                                    <select name="urgency" id="urgency" class="form-select" required>
                                        @foreach(['low', 'medium', 'high', 'critical'] as $urg)
                                            <option value="{{ $urg }}" {{ $request->urgency == $urg ? 'selected' : '' }}>{{ ucfirst($urg) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        @foreach(['open', 'fulfilled', 'cancelled'] as $st)
                                            <option value="{{ $st }}" {{ $request->status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ $request->location }}" required>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save me-1"></i> Update Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                @if ($hospital->latitude && $hospital->longitude)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i> Your Location
                    </div>
                    <div class="card-body p-2">
                        <div id="reqDetailMap"></div>
                    </div>
                </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users me-2 text-danger"></i> Responding Donors
                    </div>
                    <div class="card-body">
                        @if ($request->responses->isEmpty())
                            <div class="empty-state" style="padding:1.5rem;">
                                <div class="empty-icon" style="font-size:2rem;"><i class="fas fa-user-friends"></i></div>
                                <h5 style="font-size:1rem;">No Responses Yet</h5>
                                <p style="font-size:0.85rem;">Donors haven't responded to this request yet.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach ($request->responses as $resp)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <strong>{{ $resp->donor->user->name ?? 'Unknown' }}</strong>
                                            <span class="badge bg-danger ms-2" style="font-size:0.7rem;">{{ $resp->donor->blood_type }}</span>
                                            <div style="font-size:0.8rem; color:var(--text-secondary);">{{ $resp->donor->user->city ?? '' }}</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($resp->confirmed_at)
                                                <span class="badge bg-primary"><i class="fas fa-check-circle me-1"></i>Confirmed</span>
                                            @elseif ($resp->status === 'accepted')
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal{{ $resp->id }}">
                                                    <i class="fas fa-check me-1"></i> Confirm
                                                </button>
                                                <span class="badge bg-success">{{ ucfirst($resp->status) }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($resp->status) }}</span>
                                            @endif
                                        </div>
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

@foreach ($request->responses as $resp)
    @if ($resp->status === 'accepted' && !$resp->confirmed_at)
        <div class="modal fade" id="confirmModal{{ $resp->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('hospital.response.confirm', $resp) }}">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title"><i class="fas fa-calendar-check text-danger me-2"></i>Schedule Donation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body py-4">
                            <div class="mb-3 text-center">
                                <div style="width:64px;height:64px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                                    <i class="fas fa-user-check" style="font-size:1.5rem;color:#dc3545;"></i>
                                </div>
                                <h6>Confirm <strong>{{ $resp->donor->user->name ?? 'Donor' }}</strong></h6>
                                <p class="small text-muted">Set the appointment date and time for this donor.</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Appointment Date & Time</label>
                                <input type="datetime-local" name="scheduled_date" class="form-control" required min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Notes (optional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Any instructions for the donor..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-check-circle me-1"></i> Confirm & Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    @if ($hospital->latitude && $hospital->longitude)
    var detailMap = L.map('reqDetailMap').setView([{{ $hospital->latitude }}, {{ $hospital->longitude }}], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(detailMap);
    var hospitalIcon = L.divIcon({
        html: '<div style="background:#dc3545;color:white;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-hospital"></i></div>',
        className: '',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
    });
    L.marker([{{ $hospital->latitude }}, {{ $hospital->longitude }}], { icon: hospitalIcon }).addTo(detailMap)
        .bindPopup('<strong>{{ addslashes($hospital->name ?? 'Your Hospital') }}</strong>');
    @endif
</script>
</body>
</html>
