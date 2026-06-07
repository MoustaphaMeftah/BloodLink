<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Details - BloodLink</title>
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
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users me-2 text-danger"></i> Responding Donors
                    </div>
                    <div class="card-body">
                        @if ($request->donors->isEmpty())
                            <div class="empty-state" style="padding:1.5rem;">
                                <div class="empty-icon" style="font-size:2rem;"><i class="fas fa-user-friends"></i></div>
                                <h5 style="font-size:1rem;">No Responses Yet</h5>
                                <p style="font-size:0.85rem;">Donors haven't responded to this request yet.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach ($request->donors as $donor)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <strong>{{ $donor->user->name ?? 'Unknown' }}</strong>
                                            <span class="badge bg-danger ms-2" style="font-size:0.7rem;">{{ $donor->blood_type }}</span>
                                            <div style="font-size:0.8rem; color:var(--text-secondary);">{{ $donor->user->city ?? '' }}</div>
                                        </div>
                                        <span class="badge bg-{{ $donor->pivot->status === 'accepted' ? 'success' : 'warning' }}">
                                            {{ ucfirst($donor->pivot->status) }}
                                        </span>
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
