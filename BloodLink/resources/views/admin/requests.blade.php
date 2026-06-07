<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests - BloodLink Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        #adminReqMap { height: 400px; border-radius: 8px; }
    </style>
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
                <h3><i class="fas fa-tint"></i> Blood Requests</h3>
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

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by hospital or location..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="blood_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach (['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $bt)
                                <option value="{{ $bt }}" {{ request('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="urgency" class="form-select">
                            <option value="">All Urgency</option>
                            <option value="critical" {{ request('urgency') == 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="high" {{ request('urgency') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('urgency') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('urgency') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100"><i class="fas fa-filter me-1"></i> Filter</button>
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
                                <th>Blood Type</th>
                                <th>Quantity</th>
                                <th>Urgency</th>
                                <th>Status</th>
                                <th>Hospital</th>
                                <th>Location</th>
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
                                            <span class="badge urgency-critical pulse-critical">Critical</span>
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
                                    <td>{{ $req->hospital?->user?->name ?? 'N/A' }}</td>
                                    <td><i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $req->location }}</td>
                                    <td><small class="text-muted">{{ $req->created_at->format('M d, Y') }}</small></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                        @if ($req->status === 'open')
                                            <form method="POST" action="{{ route('admin.request.status', $req) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="fulfilled">
                                                <button type="submit" class="btn btn-success btn-sm" title="Mark Fulfilled"><i class="fas fa-check"></i></button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.request.status', $req) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm" title="Cancel"><i class="fas fa-times"></i></button>
                                            </form>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                        @if ($req->hospital && $req->hospital->latitude && $req->hospital->longitude)
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="showAdminReqMap({{ $req->hospital->latitude }}, {{ $req->hospital->longitude }}, '{{ addslashes($req->hospital->name ?? 'Hospital') }}')" title="View on Map">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </button>
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($requests->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No requests found</td>
                                </tr>
                            @endif
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
    </main>
</div>

<div class="modal fade" id="adminReqMapModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt text-danger me-2"></i>Hospital Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2">
                <div id="adminReqMap"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    var adminReqMapInstance = null;

    function showAdminReqMap(lat, lng, name) {
        var modal = new bootstrap.Modal(document.getElementById('adminReqMapModal'));
        modal.show();

        setTimeout(function() {
            if (adminReqMapInstance) {
                adminReqMapInstance.remove();
                adminReqMapInstance = null;
            }

            adminReqMapInstance = L.map('adminReqMap').setView([lat, lng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(adminReqMapInstance);

            var icon = L.divIcon({
                html: '<div style="background:#dc3545;color:white;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-hospital"></i></div>',
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
            });

            L.marker([lat, lng], { icon: icon }).addTo(adminReqMapInstance)
                .bindPopup('<strong>' + name + '</strong>')
                .openPopup();
        }, 300);
    }
</script>
</body>
</html>
