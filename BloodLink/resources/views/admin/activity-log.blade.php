<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log - BloodLink</title>
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
                <h3><i class="fas fa-history"></i> Activity Log</h3>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.activity-log') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small">Action</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">All Actions</option>
                            @foreach ($actions as $a)
                                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $a)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">User ID</label>
                        <input type="number" name="user_id" class="form-control form-control-sm" placeholder="User ID" value="{{ request('user_id') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-danger btn-sm w-100"><i class="fas fa-search"></i> Filter</button>
                        <a href="{{ route('admin.activity-log') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2 text-danger"></i> Log Entries</span>
                <span class="badge bg-secondary">{{ $logs->total() }} total</span>
            </div>
            <div class="card-body p-0">
                @if ($logs->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-history" style="font-size:2.5rem;opacity:0.3;margin-bottom:1rem;display:block;"></i>
                        <h5>No Activity Logs</h5>
                        <p>No log entries match your criteria.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                    <tr>
                                        <td class="text-muted small">{{ $log->id }}</td>
                                        <td>
                                            @if ($log->user)
                                                <span class="fw-semibold small">{{ $log->user->name }}</span>
                                                <span class="badge bg-{{ $log->user->role === 'admin' ? 'dark' : ($log->user->role === 'hospital' ? 'info' : 'success') }}" style="font-size:0.6rem;">{{ $log->user->role }}</span>
                                            @else
                                                <span class="text-muted small">System</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                                        <td class="small">{{ $log->description }}</td>
                                        <td class="small text-muted">{{ $log->ip_address ?? '-' }}</td>
                                        <td class="small text-muted">{{ $log->created_at->format('M d, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if ($logs->hasPages())
                <div class="card-footer">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
