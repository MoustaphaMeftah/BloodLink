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

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    @include('partials.sidebar')

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <button class="sidebar-toggle me-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h3><i class="fas fa-users"></i> Manage Users</h3>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-1"></i> Please fix the errors below.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="donor" {{ request('role') == 'donor' ? 'selected' : '' }}>Donor</option>
                            <option value="hospital" {{ request('role') == 'hospital' ? 'selected' : '' }}>Hospital</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        @if (request('search') || request('role'))
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary w-100">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
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
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-size:0.8rem;font-weight:700;flex-shrink:0;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <strong>{{ $user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'dark' : ($user->role === 'hospital' ? 'info' : ($user->role === 'patient' ? 'secondary' : 'success')) }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if ($user->email_verified_at)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <button type="button" class="btn btn-outline-info btn-sm" title="View Details" data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $user->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if (!$user->email_verified_at)
                                                <form method="POST" action="{{ route('admin.user.approve', $user) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('messages.show', $user) }}" class="btn btn-outline-primary btn-sm" title="Send Message">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-warning btn-sm" title="Edit" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if (Auth::id() !== $user->id)
                                                <button type="button" class="btn btn-outline-danger btn-sm" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($users->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No users found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        @foreach ($users as $user)
            @php
                $donorProfile = $user->role === 'donor' ? $user->donor : null;
                $hospitalProfile = $user->role === 'hospital' ? $user->hospital : null;
            @endphp
            <div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-user me-2 text-danger"></i>{{ $user->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header py-2"><strong>Account Info</strong></div>
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between py-1"><small>Email</small><span>{{ $user->email }}</span></div>
                                            <div class="d-flex justify-content-between py-1"><small>Phone</small><span>{{ $user->phone ?? 'N/A' }}</span></div>
                                            <div class="d-flex justify-content-between py-1"><small>City</small><span>{{ $user->city ?? 'N/A' }}</span></div>
                                            <div class="d-flex justify-content-between py-1"><small>Role</small><span class="badge bg-{{ $user->role === 'admin' ? 'dark' : ($user->role === 'hospital' ? 'info' : 'success') }}">{{ ucfirst($user->role) }}</span></div>
                                            <div class="d-flex justify-content-between py-1"><small>Verified</small>@if ($user->email_verified_at) <span class="badge bg-success">Yes</span> @else <span class="badge bg-warning">No</span> @endif</div>
                                            <div class="d-flex justify-content-between py-1"><small>Joined</small><span>{{ $user->created_at->format('M d, Y') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if ($donorProfile)
                                        <div class="card mb-3">
                                            <div class="card-header py-2"><strong>Donor Profile</strong></div>
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between py-1"><small>Blood Type</small><span class="badge bg-danger">{{ $donorProfile->blood_type }}</span></div>
                                                <div class="d-flex justify-content-between py-1"><small>Availability</small>@if ($donorProfile->availability) <span class="badge bg-success">Available</span> @else <span class="badge bg-secondary">Unavailable</span> @endif</div>
                                                <div class="d-flex justify-content-between py-1"><small>Last Donation</small><span>{{ $donorProfile->last_donation_date?->format('M d, Y') ?? 'Never' }}</span></div>
                                                <div class="d-flex justify-content-between py-1"><small>Donations</small><span>{{ $donorProfile->donations()->count() }}</span></div>
                                            </div>
                                        </div>
                                    @elseif ($hospitalProfile)
                                        <div class="card mb-3">
                                            <div class="card-header py-2"><strong>Hospital Profile</strong></div>
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between py-1"><small>Name</small><span>{{ $hospitalProfile->name }}</span></div>
                                                <div class="d-flex justify-content-between py-1"><small>Address</small><span>{{ $hospitalProfile->address ?? 'N/A' }}</span></div>
                                                <div class="d-flex justify-content-between py-1"><small>Phone</small><span>{{ $hospitalProfile->phone ?? 'N/A' }}</span></div>
                                                <div class="d-flex justify-content-between py-1"><small>Contact</small><span>{{ $hospitalProfile->contact_person ?? 'N/A' }}</span></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="card mb-3">
                                            <div class="card-header py-2"><strong>Profile</strong></div>
                                            <div class="card-body py-2 text-muted small">No additional profile data for this role.</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="{{ route('admin.user.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-edit me-2 text-danger"></i>Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                                        <option value="donor" {{ old('role', $user->role) === 'donor' ? 'selected' : '' }}>Donor</option>
                                        <option value="hospital" {{ old('role', $user->role) === 'hospital' ? 'selected' : '' }}>Hospital</option>
                                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="patient" {{ old('role', $user->role) === 'patient' ? 'selected' : '' }}>Patient</option>
                                    </select>
                                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-save me-1"></i> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if (Auth::id() !== $user->id)
            <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-danger">
                        <div class="modal-body text-center py-4">
                            <div style="width:64px;height:64px;border-radius:50%;background:rgba(220,53,69,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                                <i class="fas fa-exclamation-triangle" style="font-size:1.8rem;color:#dc3545;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Delete User</h5>
                            <p class="text-muted small mb-3">Are you sure you want to delete <strong>{{ $user->name }}</strong>?<br>This action cannot be undone.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                                <form method="POST" action="{{ route('admin.user.delete', $user) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm px-4"><i class="fas fa-trash me-1"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
