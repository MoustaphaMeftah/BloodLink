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

<div class="container mt-5 pt-4">
    <a href="{{ route('hospital.requests') }}" class="btn btn-outline-secondary mb-3">&larr; Back</a>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between">
            <h5 class="mb-0">Request #{{ $request->id }} - {{ $request->blood_type }}</h5>
            <span class="badge bg-light text-dark">{{ ucfirst($request->status) }}</span>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('hospital.request.update', $request) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="blood_type" class="form-label fw-medium">Blood Type</label>
                        <select name="blood_type" id="blood_type" class="form-control" required>
                            @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $bt)
                                <option value="{{ $bt }}" {{ $request->blood_type == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label fw-medium">Quantity (ml)</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $request->quantity }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="urgency" class="form-label fw-medium">Urgency</label>
                        <select name="urgency" id="urgency" class="form-control" required>
                            @foreach(['low', 'medium', 'high', 'critical'] as $urg)
                                <option value="{{ $urg }}" {{ $request->urgency == $urg ? 'selected' : '' }}>{{ ucfirst($urg) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-medium">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            @foreach(['open', 'fulfilled', 'cancelled'] as $st)
                                <option value="{{ $st }}" {{ $request->status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label fw-medium">Location</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ $request->location }}" required>
                </div>
                <button type="submit" class="btn btn-danger fw-bold">Update</button>
            </form>

            <hr>
            <h6>Responding Donors</h6>
            @if ($request->donors->isEmpty())
                <p class="text-muted">No responses yet.</p>
            @else
                <ul class="list-group">
                    @foreach ($request->donors as $donor)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $donor->user->name ?? 'Unknown' }} ({{ $donor->blood_type }})</span>
                            <span class="badge bg-{{ $donor->pivot->status === 'accepted' ? 'success' : 'warning' }}">
                                {{ ucfirst($donor->pivot->status) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
