<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <h3><i class="fas fa-list"></i> Blood Requests</h3>
    <hr>

    @if ($requests->isEmpty())
        <div class="alert alert-info">No requests yet.</div>
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
                            <td>{{ $req->blood_type }}</td>
                            <td>{{ $req->quantity }}ml</td>
                            <td>{{ ucfirst($req->urgency) }}</td>
                            <td>{{ ucfirst($req->status) }}</td>
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
