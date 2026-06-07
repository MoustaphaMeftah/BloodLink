<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - BloodLink</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
@include('partials.navbar')

<div class="container mt-5 pt-4">
    <h3><i class="fas fa-users"></i> Manage Users</h3>
    <hr>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-control">
                <option value="">All Roles</option>
                <option value="donor" {{ request('role') == 'donor' ? 'selected' : '' }}>Donor</option>
                <option value="hospital" {{ request('role') == 'hospital' ? 'selected' : '' }}>Hospital</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Patient</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-danger w-100">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-danger">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Verified</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-{{ $user->role === 'admin' ? 'dark' : ($user->role === 'hospital' ? 'info' : 'success') }}">{{ ucfirst($user->role) }}</span></td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>
                            @if ($user->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if (!$user->email_verified_at)
                                <form method="POST" action="{{ route('admin.user.approve', $user) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
