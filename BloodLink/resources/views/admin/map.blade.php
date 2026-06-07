<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map View - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        #map { height: 600px; border-radius: 8px; }
        .legend { background: white; padding: 10px 14px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.15); font-size: 13px; line-height: 2; color: #212529; }
        .legend i { width: 18px; height: 18px; display: inline-block; border-radius: 50%; margin-right: 6px; vertical-align: middle; }
        .stats-card { background: white; border-radius: 8px; padding: 16px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); text-align: center; }
        .stats-card .num { font-size: 1.5rem; font-weight: 700; }
        [data-theme="dark"] .legend { background: #2a2a3e; color: #e4e6eb; }
        [data-theme="dark"] .stats-card { background: #2a2a3e; color: #e4e6eb; }
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
                <h3><i class="fas fa-map-marked-alt"></i> Map View</h3>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stats-card">
                    <div class="text-muted small">Available Donors</div>
                    <div class="num text-success">{{ $donors->count() }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stats-card">
                    <div class="text-muted small">Open Requests</div>
                    <div class="num text-danger">{{ $requests->count() }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stats-card">
                    <div class="text-muted small">Donors on Map</div>
                    <div class="num text-success">{{ $donors->count() }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stats-card">
                    <div class="text-muted small">Hospitals on Map</div>
                    <div class="num text-danger">{{ $requests->count() }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-2">
                <div id="map"></div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const donors = @json($donors);
    const requests = @json($requests);

    const map = L.map('map');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    const donorIcon = L.divIcon({
        html: '<div style="background:#198754;color:white;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.2);"><i class="fas fa-tint"></i></div>',
        className: '',
        iconSize: [30, 30],
        iconAnchor: [15, 15],
    });

    const hospitalIcon = L.divIcon({
        html: '<div style="background:#dc3545;color:white;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.2);"><i class="fas fa-hospital"></i></div>',
        className: '',
        iconSize: [30, 30],
        iconAnchor: [15, 15],
    });

    const msgBase = '{{ route("messages.show", "__ID__") }}';

    donors.forEach(function(d) {
        if (d.latitude && d.longitude) {
            const m = L.marker([d.latitude, d.longitude], { icon: donorIcon }).addTo(map);
            m.bindPopup(`
                <strong>${d.user?.name ?? 'Donor'}</strong><br>
                Blood Type: <span class="badge bg-danger">${d.blood_type ?? '?'}</span><br>
                City: ${d.city ?? 'N/A'}<br>
                <a href="${msgBase.replace('__ID__', d.user_id)}" class="btn btn-sm btn-outline-primary mt-1"><i class="fas fa-envelope"></i> Message</a>
            `);
        }
    });

    requests.forEach(function(r) {
        const h = r.hospital;
        if (h && h.latitude && h.longitude) {
            const m = L.marker([h.latitude, h.longitude], { icon: hospitalIcon }).addTo(map);
            m.bindPopup(`
                <strong>${h.name ?? 'Hospital'}</strong><br>
                Requesting: <span class="badge bg-danger">${r.blood_type}</span> ${r.quantity}ml<br>
                Urgency: <span class="badge bg-${r.urgency === 'critical' ? 'danger' : r.urgency === 'high' ? 'warning' : 'secondary'}">${r.urgency}</span><br>
                Status: ${r.status}
            `);
        }
    });

    const allCoords = [];
    donors.forEach(function(d) { if (d.latitude && d.longitude) allCoords.push([d.latitude, d.longitude]); });
    requests.forEach(function(r) { if (r.hospital?.latitude && r.hospital?.longitude) allCoords.push([r.hospital.latitude, r.hospital.longitude]); });

    if (allCoords.length > 0) {
        map.fitBounds(allCoords, { padding: [40, 40] });
    } else {
        map.setView([34.0, 9.0], 6);
    }

    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function() {
        const div = L.DomUtil.create('div', 'legend');
        div.innerHTML = '<strong>Legend</strong><br>';
        div.innerHTML += '<i style="background:#198754;"></i> Available Donor<br>';
        div.innerHTML += '<i style="background:#dc3545;"></i> Hospital Request';
        return div;
    };
    legend.addTo(map);
</script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
