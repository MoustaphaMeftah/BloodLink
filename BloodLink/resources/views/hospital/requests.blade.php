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

<div class="dashboard-wrapper">
    <div class="dashboard-sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div class="sidebar-title">Main Menu</div>
        <a href="{{ route('hospital.dashboard') }}" class="sidebar-link">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('hospital.requests') }}" class="sidebar-link active">
            <i class="fas fa-list"></i> My Requests
        </a>
        <a href="{{ route('hospital.request.create') }}" class="sidebar-link">
            <i class="fas fa-plus-circle"></i> Create Request
        </a>
        <div class="sidebar-title">Communication</div>
        <a href="{{ route('messages') }}" class="sidebar-link d-flex align-items-center justify-content-between">
            <span><i class="fas fa-envelope"></i> Messages</span>
            @php $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->whereNull('read_at')->count(); @endphp
            @if ($unreadCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
            @endif
        </a>
        <div class="sidebar-title">Account</div>
        <a href="{{ route('profile') }}" class="sidebar-link">
            <i class="fas fa-user-cog"></i> Hospital Profile
        </a>
        <div class="mt-4 px-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="dashboard-content">
        <div class="page-header">
            <div>
                <button class="sidebar-toggle me-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h3><i class="fas fa-list"></i> Blood Requests</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('hospital.request.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus me-1"></i> New Request
                </a>
            </div>
        </div>

        @if ($requests->isEmpty())
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                        <h5>No Requests Yet</h5>
                        <p>You haven't created any blood requests. Click below to create your first one.</p>
                        <a href="{{ route('hospital.request.create') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-plus me-1"></i> Create Request
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
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
                                        <td><strong>{{ $req->quantity }}ml</strong></td>
                                        <td>
                                            @if ($req->urgency === 'critical')
                                                <span class="badge urgency-critical pulse-critical"><i class="fas fa-exclamation-circle me-1"></i>Critical</span>
                                            @elseif ($req->urgency === 'high')
                                                <span class="badge urgency-high">High</span>
                                            @elseif ($req->urgency === 'medium')
                                                <span class="badge urgency-medium">Medium</span>
                                            @else
                                                <span class="badge urgency-low">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $req->status === 'open' ? 'success' : ($req->status === 'fulfilled' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('hospital.request.show', $req) }}" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($requests->hasPages())
                    <div class="card-footer">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        @endif
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
