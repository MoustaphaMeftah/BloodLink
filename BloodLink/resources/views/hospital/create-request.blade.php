<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Request - BloodLink</title>
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
                <h3><i class="fas fa-plus-circle"></i> Create Blood Request</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('hospital.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-medical me-2 text-danger"></i> Request Details
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('hospital.request.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="blood_type" class="form-label">Blood Type</label>
                                    <select name="blood_type" id="blood_type" class="form-select" required>
                                        <option value="">Select Blood Type</option>
                                        @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $bt)
                                            <option value="{{ $bt }}">{{ $bt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="quantity" class="form-label">Quantity (ml)</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" placeholder="e.g. 500" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="urgency" class="form-label">Urgency</label>
                                    <select name="urgency" id="urgency" class="form-select" required>
                                        <option value="low">Low - Routine</option>
                                        <option value="medium">Medium - Scheduled</option>
                                        <option value="high">High - Urgent</option>
                                        <option value="critical">Critical - Emergency</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" name="location" id="location" class="form-control" placeholder="City or address" required>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-paper-plane me-1"></i> Create Request
                                </button>
                                <a href="{{ route('hospital.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2 text-danger"></i> Tips
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0" style="font-size:0.9rem;">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Be specific with blood type</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Set the right urgency level</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Include exact location</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Update status when fulfilled</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Respond to donor inquiries</li>
                        </ul>
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
