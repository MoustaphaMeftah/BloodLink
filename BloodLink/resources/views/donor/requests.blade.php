<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Requests - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <h3><i class="fas fa-list"></i> Available Blood Requests</h3>
    <hr>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($requests->isEmpty())
        <div class="alert alert-info">No matching requests at this time.</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-danger">
                    <tr>
                        <th>Blood Type</th>
                        <th>Quantity</th>
                        <th>Urgency</th>
                        <th>Location</th>
                        <th>Hospital</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr>
                            <td><span class="badge bg-danger">{{ $request->blood_type }}</span></td>
                            <td>{{ $request->quantity }}ml</td>
                            <td>
                                <span class="badge {{ $request->urgency === 'critical' ? 'bg-danger' : ($request->urgency === 'high' ? 'bg-warning' : 'bg-info') }}">
                                    {{ ucfirst($request->urgency) }}
                                </span>
                            </td>
                            <td>{{ $request->location }}</td>
                            <td>{{ $request->hospital->name ?? 'N/A' }}</td>
                            <td>
                                <form method="POST" action="{{ route('donor.respond', $request->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="accepted" value="1">
                                    <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form method="POST" action="{{ route('donor.respond', $request->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="accepted" value="0">
                                    <button type="submit" class="btn btn-secondary btn-sm">Decline</button>
                                </form>
                            </td>
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
