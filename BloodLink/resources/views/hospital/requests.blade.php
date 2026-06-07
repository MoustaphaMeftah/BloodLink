<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        #requestMap { height: 400px; border-radius: 8px; }
    </style>
</head>
<body>
@include('partials.navbar')

@php $hospital = Auth::user()->hospital; @endphp

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    @include('partials.sidebar')

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <button class="sidebar-toggle me-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h3><i class="fas fa-list"></i> Blood Requests</h3>
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

        @if ($requests->isEmpty())
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                        <h5>No Requests Yet</h5>
                        <p>You haven't created any blood requests. Click below to create your first one.</p>
                        <a href="{{ route('hospital.request.create') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-plus me-1"></i> Create Request
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Blood Type</th>
                                    <th>Quantity</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $req)
                                    <tr>
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
                                        <td>
                                            <span class="badge bg-{{ $req->status === 'open' ? 'success' : ($req->status === 'fulfilled' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                            <a href="{{ route('hospital.request.show', $req) }}" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            @if ($hospital->latitude && $hospital->longitude)
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="showRequestMap({{ $hospital->latitude }}, {{ $hospital->longitude }}, '{{ $hospital->name }}')" title="View on Map">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </button>
                                            @endif
                                            </div>
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
        @endif
    </main>
</div>

<div class="modal fade" id="requestMapModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt text-danger me-2"></i>Request Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2">
                <div id="requestMap"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    var requestMapInstance = null;

    function showRequestMap(lat, lng, name) {
        var modal = new bootstrap.Modal(document.getElementById('requestMapModal'));
        modal.show();

        setTimeout(function() {
            if (requestMapInstance) {
                requestMapInstance.remove();
                requestMapInstance = null;
            }

            requestMapInstance = L.map('requestMap').setView([lat, lng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(requestMapInstance);

            var icon = L.divIcon({
                html: '<div style="background:#dc3545;color:white;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-hospital"></i></div>',
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
            });

            L.marker([lat, lng], { icon: icon }).addTo(requestMapInstance)
                .bindPopup('<strong>' + name + '</strong>')
                .openPopup();
        }, 300);
    }
</script>
</body>
</html>
