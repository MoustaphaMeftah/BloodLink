<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-hospital"></i> Hospital Dashboard</h3>
        <a href="{{ route('hospital.request.create') }}" class="btn btn-danger">
            <i class="fas fa-plus"></i> New Request
        </a>
    </div>
    <hr>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($requests->isEmpty())
        <div class="alert alert-info">No blood requests yet.</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-danger">
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
                            <td>{{ $req->quantity }}ml</td>
                            <td>
                                <span class="badge {{ $req->urgency === 'critical' ? 'bg-danger' : ($req->urgency === 'high' ? 'bg-warning' : 'bg-info') }}">
                                    {{ ucfirst($req->urgency) }}
                                </span>
                            </td>
                            <td><span class="badge bg-{{ $req->status === 'open' ? 'success' : ($req->status === 'fulfilled' ? 'primary' : 'secondary') }}">{{ ucfirst($req->status) }}</span></td>
                            <td>{{ $req->created_at->format('Y-m-d') }}</td>
                            <td><a href="{{ route('hospital.request.show', $req) }}" class="btn btn-sm btn-outline-danger">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $requests->links() }}
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
