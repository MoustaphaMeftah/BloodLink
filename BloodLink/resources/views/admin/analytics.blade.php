<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - BloodLink</title>
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
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="sidebar-link">
            <i class="fas fa-users"></i> Manage Users
        </a>
        <a href="{{ route('admin.analytics') }}" class="sidebar-link active">
            <i class="fas fa-chart-bar"></i> Analytics
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
            <i class="fas fa-user-shield"></i> Admin Profile
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
                <h3><i class="fas fa-chart-bar"></i> Analytics</h3>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt me-2 text-danger"></i> Monthly Donations ({{ date('Y') }})
                    </div>
                    <div class="card-body">
                        @if ($monthlyDonations->isEmpty())
                            <div class="empty-state" style="padding:2rem;">
                                <div class="empty-icon" style="font-size:2.5rem;"><i class="fas fa-chart-line"></i></div>
                                <h5 style="font-size:1rem;">No Data Available</h5>
                                <p style="font-size:0.85rem;">Donation data will appear here once donations are made.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Donations</th>
                                            <th>Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $maxDonation = max($monthlyDonations->toArray() ?: [1]); @endphp
                                        @foreach ($monthlyDonations as $month => $count)
                                            <tr>
                                                <td><strong>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</strong></td>
                                                <td><span class="fw-bold text-danger">{{ $count }}</span></td>
                                                <td style="width:40%;">
                                                    <div class="progress" style="height:6px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $maxDonation > 0 ? ($count / $maxDonation) * 100 : 0 }}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-tint me-2 text-danger"></i> Requests by Blood Type
                    </div>
                    <div class="card-body">
                        @if ($requestsByType->isEmpty())
                            <div class="empty-state" style="padding:2rem;">
                                <div class="empty-icon" style="font-size:2.5rem;"><i class="fas fa-droplet"></i></div>
                                <h5 style="font-size:1rem;">No Data Available</h5>
                                <p style="font-size:0.85rem;">Request data will appear here once requests are created.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Blood Type</th>
                                            <th>Requests</th>
                                            <th>Distribution</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $maxRequests = max($requestsByType->toArray() ?: [1]); @endphp
                                        @foreach ($requestsByType as $type => $count)
                                            <tr>
                                                <td><span class="badge bg-danger">{{ $type }}</span></td>
                                                <td><span class="fw-bold">{{ $count }}</span></td>
                                                <td style="width:40%;">
                                                    <div class="progress" style="height:6px;">
                                                        <div class="progress-bar bg-{{ $loop->index % 2 == 0 ? 'danger' : 'warning' }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $maxRequests > 0 ? ($count / $maxRequests) * 100 : 0 }}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
