<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nearby Donors - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        #map { height: 450px; border-radius: 12px; overflow: hidden; }
        .donor-card { transition: transform 0.2s; }
        .donor-card:hover { transform: translateY(-2px); }
        .distance-badge { position: absolute; top: 10px; right: 10px; }
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
                <h3><i class="fas fa-map-marker-alt"></i> Nearby Donors</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('hospital.request.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus me-1"></i> Create Request
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}</div>
        @endif

        @if (!$hospital->latitude || !$hospital->longitude)
            @if (Auth::user()->role === 'admin')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> Showing all available donors.
                </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i> Set your hospital location to find nearby donors.
                <button class="btn btn-sm btn-outline-warning ms-2" onclick="getLocation()">Use My Location</button>
            </div>
            <form method="POST" action="{{ route('hospital.location.update') }}" id="locationForm" class="mb-4">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small">Latitude</label>
                        <input type="number" step="any" name="latitude" id="lat" class="form-control" placeholder="Latitude" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Longitude</label>
                        <input type="number" step="any" name="longitude" id="lng" class="form-control" placeholder="Longitude" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-danger w-100"><i class="fas fa-save me-1"></i> Save Location</button>
                    </div>
                </div>
            </form>
            @endif
        @endif

        @if ($hospital->latitude && $hospital->longitude)
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <div id="map"></div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header"><i class="fas fa-info-circle me-2 text-danger"></i> Your Hospital</div>
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-hospital" style="font-size:2rem;color:var(--danger-color);"></i>
                            </div>
                            <p class="fw-bold mb-1">{{ $hospital->name ?? 'Hospital' }}</p>
                            <p class="small text-muted mb-1">{{ $hospital->address ?? '' }}</p>
                            <p class="small text-muted mb-0">Lat: {{ $hospital->latitude }}, Lng: {{ $hospital->longitude }}</p>
                            <hr>
                            <button class="btn btn-sm btn-outline-danger" onclick="refreshLocation()">
                                <i class="fas fa-sync me-1"></i> Update Location
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-hand-holding-heart me-2 text-danger"></i> Available Donors Near You</span>
                <span class="badge bg-danger">{{ count($nearbyDonors) }} nearby</span>
            </div>
            <div class="card-body p-0">
                @if (empty($nearbyDonors))
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-hand-holding-heart" style="font-size:2.5rem;opacity:0.3;margin-bottom:1rem;display:block;"></i>
                        <h5>No Nearby Donors</h5>
                        <p>There are no available donors near your location right now.</p>
                        <a href="{{ route('hospital.requests') }}" class="btn btn-danger btn-sm">View Your Requests</a>
                    </div>
                @else
                    <div class="row g-3 p-3">
                        @foreach ($nearbyDonors as $d)
                            <div class="col-md-6 col-lg-4">
                                <div class="card donor-card position-relative h-100">
                                    <span class="badge bg-success distance-badge">
                                        <i class="fas fa-location-arrow me-1"></i>{{ $d->distance }} km
                                    </span>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;">
                                                {{ strtoupper(substr($d->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0">{{ $d->user->name ?? 'Anonymous' }}</h6>
                                                <span class="badge bg-danger" style="font-size:0.6rem;">{{ $d->blood_type }}</span>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-1">
                                            <i class="fas fa-city me-1"></i>{{ $d->city ?? 'N/A' }}
                                        </p>
                                        <p class="small text-muted mb-0">
                                            <i class="fas fa-phone me-1"></i>{{ $d->user->phone ?? 'N/A' }}
                                        </p>
                                        <div class="mt-2">
                                            <a href="{{ route('messages.show', $d->user) }}" class="btn btn-sm btn-outline-primary w-100">
                                                <i class="fas fa-envelope me-1"></i> Message
                                            </a>
                                        </div>
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
@if ($hospital->latitude && $hospital->longitude)
    const hospitalLat = {{ $hospital->latitude }};
    const hospitalLng = {{ $hospital->longitude }};

    const map = L.map('map').setView([hospitalLat, hospitalLng], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const hospitalIcon = L.divIcon({
        html: '<div style="background:#dc3545;color:white;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-hospital"></i></div>',
        className: '',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
    });

    L.marker([hospitalLat, hospitalLng], { icon: hospitalIcon }).addTo(map)
        .bindPopup('<strong>{{ $hospital->name }}</strong>');

    const circle = L.circle([hospitalLat, hospitalLng], {
        radius: 25000,
        color: '#dc3545',
        fillColor: '#dc3545',
        fillOpacity: 0.08,
        weight: 2,
        dashArray: '5, 10',
    }).addTo(map);

    const donors = @json($nearbyDonors);
    const donorIcon = L.divIcon({
        html: '<div style="background:#dc3545;color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.2);"><i class="fas fa-tint"></i></div>',
        className: '',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
    });

    donors.forEach(function(d) {
        if (d.latitude && d.longitude) {
            const marker = L.marker([d.latitude, d.longitude], {icon: donorIcon}).addTo(map);
            marker.bindPopup(`
                <strong>${d.user?.name ?? 'Donor'}</strong><br>
                Blood Type: ${d.blood_type}<br>
                Distance: ${d.distance} km<br>
                City: ${d.city ?? 'N/A'}
            `);
        }
    });
@endif

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('lat').value = pos.coords.latitude;
            document.getElementById('lng').value = pos.coords.longitude;
        });
    } else {
        alert('Geolocation is not supported by your browser.');
    }
}

function refreshLocation() {
    if (navigator.geolocation) {
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
        });
    }
}
</script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
