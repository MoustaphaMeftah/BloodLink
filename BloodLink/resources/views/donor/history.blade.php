<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History - BloodLink</title>
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
                <h3><i class="fas fa-history"></i> Donation History</h3>
            </div>
            <div class="page-actions">
                <a href="{{ route('donor.requests') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus me-1"></i> New Donation
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($donations->isEmpty())
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-history"></i></div>
                        <h5>No Donations Yet</h5>
                        <p>You haven't made any donations yet. Browse available blood requests and start saving lives.</p>
                        <a href="{{ route('donor.requests') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-search me-1"></i> Browse Requests
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
                                    <th>Date</th>
                                    <th>Quantity</th>
                                    <th>Hospital</th>
                                    <th>Blood Request</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($donations as $donation)
                                    <tr>
                                        <td>
                                            <i class="fas fa-calendar text-muted me-1"></i>
                                            {{ $donation->donation_date?->format('Y-m-d') ?? $donation->created_at->format('Y-m-d') }}
                                        </td>
                                        <td><strong>{{ $donation->quantity }}ml</strong></td>
                                        <td>{{ $donation->bloodRequest?->hospital?->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-danger">{{ $donation->bloodRequest?->blood_type ?? 'N/A' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($donations->hasPages())
                    <div class="card-footer">
                        {{ $donations->links() }}
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
