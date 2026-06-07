<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nearby Requests - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        #map { height: 420px; border-radius: 12px; overflow: hidden; z-index: 1; }
        .request-card { transition: transform 0.2s; border-left: 4px solid var(--danger-color); }
        .request-card:hover { transform: translateY(-2px); }
        .dist-badge { font-size: 0.75rem; }
        .donor-pin { color: var(--danger-color); }
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
                <h3><i class="fas fa-map-marker-alt"></i> Nearby Matching Requests</h3>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}</div>
        @endif

        @if (!$donor->latitude || !$donor->longitude)
            @if (Auth::user()->role === 'admin')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Showing all open blood requests.
                </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i> Set your location to find nearby matching requests.
                <button class="btn btn-sm btn-outline-warning ms-2" onclick="getLocation()">Use My Location</button>
            </div>
            <form method="POST" action="{{ route('donor.location.update') }}" id="locationForm" class="mb-4">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small">Latitude</label>
                        <input type="number" step="any" name="latitude" id="lat" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Longitude</label>
                        <input type="number" step="any" name="longitude" id="lng" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-danger w-100"><i class="fas fa-save me-1"></i> Save Location</button>
                    </div>
                </div>
            </form>
            @endif
        @endif

        @if ($donor->latitude && $donor->longitude)
            <div class="card mb-4">
                <div class="card-body p-2">
                    <div id="map"></div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span class="badge bg-danger fs-6 px-3 py-2">
                            <i class="fas fa-marker me-1"></i> You{{ $donor->blood_type ? ': ' . $donor->blood_type : '' }}
                        </span>
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fas fa-hand-holding-heart me-1"></i> {{ count($nearbyRequests) }} matching requests within 25km
                        </span>
                        <span class="badge bg-secondary fs-6 px-3 py-2">
                            <i class="fas fa-hospital me-1"></i> {{ count($allOnMap) }} total compatible on map
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2 text-danger"></i> Matching Blood Requests Near You</span>
                <span class="badge bg-danger">{{ count($nearbyRequests) }}</span>
            </div>
            <div class="card-body p-0">
                @if (empty($nearbyRequests))
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-map-marker-alt" style="font-size:2.5rem;opacity:0.3;margin-bottom:1rem;display:block;"></i>
                        <h5>No Nearby Matches</h5>
                        <p>{{ $donor->blood_type ? 'No blood requests matching your type (' . $donor->blood_type . ') within 25km.' : 'No open blood requests found.' }}</p>
                        <a href="{{ route('donor.requests') }}" class="btn btn-danger btn-sm">Browse All Compatible Requests</a>
                        @if (!$donor->latitude || !$donor->longitude)
                            @if (Auth::user()->role !== 'admin')
                            <button class="btn btn-outline-danger btn-sm ms-2" onclick="getLocation()">Set My Location</button>
                            @endif
                        @endif
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($nearbyRequests as $req)
                            <div class="list-group-item request-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-danger fs-6">{{ $req->blood_type }}</span>
                                            <strong>{{ $req->quantity }}ml</strong>
                                            <span class="badge bg-{{ $req->urgency === 'critical' ? 'danger' : ($req->urgency === 'high' ? 'warning' : 'secondary') }}">{{ ucfirst($req->urgency) }}</span>
                                            <span class="badge bg-light text-dark dist-badge">
                                                <i class="fas fa-location-arrow text-danger me-1"></i>{{ $req->distance }} km
                                            </span>
                                        </div>
                                        <p class="mb-1 small">
                                            <i class="fas fa-hospital text-muted me-1"></i>{{ $req->hospital->name ?? 'Hospital' }}
                                            &middot; <i class="fas fa-map-pin text-muted me-1"></i>{{ $req->location }}
                                        </p>
                                    </div>
                                    <div class="d-flex gap-1 flex-shrink-0">
                                        <form method="POST" action="{{ route('donor.respond', $req->id) }}">
                                            @csrf
                                            <input type="hidden" name="accepted" value="1">
                                            <input type="hidden" name="redirect_to" value="{{ route('donor.nearby') }}">
                                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Accept</button>
                                        </form>
                                        <form method="POST" action="{{ route('donor.respond', $req->id) }}">
                                            @csrf
                                            <input type="hidden" name="accepted" value="0">
                                            <input type="hidden" name="redirect_to" value="{{ route('donor.nearby') }}">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
@if ($donor->latitude && $donor->longitude)
    const lat = {{ $donor->latitude }};
    const lng = {{ $donor->longitude }};

    const map = L.map('map').setView([lat, lng], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    const youIcon = L.divIcon({
        html: '<div style="background:#dc3545;color:white;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-user"></i></div>',
        className: '',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
    });

    L.marker([lat, lng], { icon: youIcon }).addTo(map)
        .bindPopup('<strong>You</strong><br>{{ $donor->blood_type }} donor');

    const circle = L.circle([lat, lng], {
        radius: 25000,
        color: '#dc3545',
        fillColor: '#dc3545',
        fillOpacity: 0.08,
        weight: 2,
        dashArray: '5, 10',
    }).addTo(map);

    const requests = @json($allOnMap);
    const hospitalIcon = L.divIcon({
        html: '<div style="background:#0d6efd;color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.2);"><i class="fas fa-hospital"></i></div>',
        className: '',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
    });

    requests.forEach(function(req) {
        const h = req.hospital;
        if (h && h.latitude && h.longitude) {
            const marker = L.marker([h.latitude, h.longitude], { icon: hospitalIcon }).addTo(map);
            const isNearby = req.distance <= 25;
            marker.bindPopup(`
                <div style="min-width:200px;">
                    <strong style="color:#dc3545;">${req.blood_type} &mdash; ${req.quantity}ml</strong><br>
                    <span class="badge bg-${req.urgency === 'critical' ? 'danger' : req.urgency === 'high' ? 'warning' : 'secondary'}">${req.urgency}</span>
                    <hr class="my-1">
                    <small>${h.name ?? 'Hospital'}</small><br>
                    <small>${req.location}</small><br>
                    <small><strong>${req.distance} km</strong> from you ${isNearby ? '✅' : '⛔'}</small>
                </div>
            `);
        }
    });
@endif

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const latEl = document.getElementById('lat');
            const lngEl = document.getElementById('lng');
            if (latEl && lngEl) {
                latEl.value = pos.coords.latitude;
                lngEl.value = pos.coords.longitude;
            } else {
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
            }
        });
    } else {
        alert('Geolocation is not supported by your browser.');
    }
}
</script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
